<?php

namespace App\EasyAdmin;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class VotesField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return VotesField
     */
    public static function new(string $propertyName, ?string $label = null): VotesField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            //this template is used in the index and details pages
            ->setTemplatePath('admin/crud/field/votes.html.twig')
            //this template is used in the edit and new pages
            ->setFormType(IntegerType::class)
            ->addCssClass('field-integer')
            ->setDefaultColumns('col-md-4 col-xxl-3');
    }
}