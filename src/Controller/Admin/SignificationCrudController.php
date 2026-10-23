<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Signification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Signification>
 */
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
            ->setColumns(12)
            ->hideOnForm()
        ;

        if (SignificationCrudController::class == $this->getContext()?->getCrud()?->getControllerFqcn()) {
            yield AssociationField::new('kanji', 'Associé au kanji')
                ->setColumns(12)
                ->onlyWhenUpdating()
            ;
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('significations')
            ->setEntityLabelInSingular('signification')

            ->setDefaultSort(['id' => 'DESC'])

            ->setSearchFields(['meaning', 'onyomi', 'kunyomi', 'kanji.ideogram'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('new', 'Ajouter une %entity_label_singular%')
            ->setPageTitle('edit', 'Modifier la %entity_label_singular%')
            ->setPageTitle('detail', 'Consulter la %entity_label_singular%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::EDIT, Action::INDEX])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('meaning')
            ->add('onyomi')
            ->add('kunyomi')
        ;
    }
}
