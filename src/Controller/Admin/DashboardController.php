<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\QuestionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    /**
     * @param QuestionRepository $questionRepository
     * @param ChartBuilderInterface $chartBuilder
     */
    public function __construct(
        private readonly QuestionRepository $questionRepository,
        private readonly ChartBuilderInterface $chartBuilder
    ) {
    }

    /**
     * @return Response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $latestQuestions = $this->questionRepository->findLatest();
        $topVoted = $this->questionRepository->findTopVoted();
        return $this->render('admin/index.html.twig', [
            'latestQuestions' => $latestQuestions, 'topVoted' => $topVoted, 'chart' => $this->createChart()
        ]);
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Cauldron Overflow Admin');
    }

    /**
     * @param UserInterface $user
     * @return UserMenu
     * @throws Exception
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new Exception('Wrong User');
        }

        return parent::configureUserMenu($user)
            ->setAvatarUrl($user->getAvatarUrl())
            ->setMenuItems(
                [
                    MenuItem::linkToUrl('My Profile', 'fa fa-user', $this->generateUrl('app_profile_show'))
                ]
            );
    }


    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Homepage', 'fa fa-home', $this->generateUrl('app_homepage'));
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Questions', 'fa fa-question-circle', Question::class);
        yield MenuItem::linkToCrud('Answers', 'fa fa-comments', Answer::class);
        yield MenuItem::linkToCrud('Topics', 'fa fa-folder', Topic::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
    }

    /**
     * @return Actions
     */
    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /**
     * @return Assets
     */
    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }

    /**
     * @return Crud
     */
    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->setDefaultSort(
                [
                    'id' => 'DESC',
                ]
            );
    }

    /**
     * @return Chart
     */
    private function createChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData(
            [
                'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                'datasets' => [
                    [
                        'label' => 'My First dataset',
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => [0, 10, 5, 2, 20, 30, 45],
                    ],
                ],
            ]
        );

        $chart->setOptions(
            [
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 100,
                    ],
                ],
            ]
        );

        return $chart;
    }
}
