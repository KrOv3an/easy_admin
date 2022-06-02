<?php

namespace App\Controller\Admin;

use App\EasyAdmin\VotesField;
use App\Entity\Question;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_MODERATOR')]
class QuestionCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield Field::new('slug')
            ->hideOnIndex()
            ->setFormTypeOption('disabled', $pageName !== Crud::PAGE_NEW);
        yield Field::new('name')
            ->setSortable(false);
        yield AssociationField::new('topic');
        yield TextareaField::new('question')
            ->hideOnIndex()
            ->setFormTypeOptions(
                [
                    'row_attr' => [
                        'data-controller' => 'snarkdown',
                    ],
                    'attr' => [
                        'data-snarkdown-target' => 'input',
                        'data-action' => 'snarkdown#render',
                    ],
                ]
            )
            ->setHelp('Preview:');
        yield VotesField::new('votes')
            ->setPermission('ROLE_SUPER_ADMIN');
        yield AssociationField::new('askedBy')
            ->autocomplete()
            ->formatValue(static function ($value, Question $question) {
                if (!$user = $question->getAskedBy()) {
                    return null;
                }

                return sprintf('%s&nbsp;(%s)', $user->getEmail(), $user->getQuestions()->count());
            })
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere('entity.enabled = :enabled')
                    ->setParameter('enabled', true);
            });

        yield AssociationField::new('answers')
            ->autocomplete()
            ->setFormTypeOption('by_reference', false);
        yield Field::new('createdAt')
            ->hideOnForm();
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setDefaultSort(
                [
                    'askedBy.enabled' => 'DESC',
                    'createdAt' => 'DESC',
                ]
            );
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->setPermission(Action::INDEX, 'ROLE_MODERATOR')
            ->setPermission(Action::DETAIL, 'ROLE_MODERATOR')
            ->setPermission(Action::EDIT, 'ROLE_MODERATOR')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::BATCH_DELETE, 'ROLE_SUPER_ADMIN');
    }
}
