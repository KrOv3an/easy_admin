<?php

namespace App\Controller\Admin;

use App\EasyAdmin\VotesField;
use App\Entity\Answer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class AnswerCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield Field::new('answer');
        yield VotesField::new('votes');
        yield AssociationField::new('question')
            ->autocomplete()
            ->hideOnIndex();
        yield AssociationField::new('answeredBy')
            ->autocomplete();
        yield Field::new('createdAt')
            ->hideOnForm();
        yield Field::new('updatedAt')
            ->hideOnDetail();
    }
}
