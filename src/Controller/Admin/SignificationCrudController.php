<?php

namespace App\Controller\Admin;

use App\Entity\Signification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SignificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Signification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnDetail()
        ;

        yield TextField::new('meaning', 'Sens')
            ->setColumns(12)
        ;

        yield TextField::new('kunyomi')
            ->setHelp('Lecture purement Japonaise.')
            ->setColumns(12)
        ;

        yield TextField::new('onyomi')
            ->setHelp('Lecture sino-japonaise.')
            ->setColumns(12)
        ;

        yield AssociationField::new('kanji', 'Associé au kanji')
            ->hideOnForm()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('significations')
            ->setEntityLabelInSingular('signification')

            ->setDefaultSort(['id' => 'DESC'])

            ->setSearchFields(['meaning', 'onyomi', 'kunyomi'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('new', 'Ajouter une %entity_label_singular%')
            ->setPageTitle('edit', 'Modifier la %entity_label_singular%')
            ->setPageTitle('detail', 'consulter la %entity_label_singular%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('meaning')
            ->add('onyomi')
            ->add('kunyomi')
            ->add('kanji')
        ;
    }
}
