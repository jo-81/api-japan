<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Theme;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Theme>
 */
class ThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('name', 'Nom');

        yield SlugField::new('slug', 'Permalien')
            ->setTargetFieldName('name')
            ->hideOnForm()
        ;

        yield DateField::new('createdAt', 'Date de création')
            ->onlyOnIndex()
            ->setFormat(DateTimeField::FORMAT_FULL)
        ;

        yield DateTimeField::new('createdAt', 'Date de création')
            ->onlyOnDetail()
            ->setFormat(DateTimeField::FORMAT_FULL, DateTimeField::FORMAT_SHORT)
        ;

        yield DateTimeField::new('updatedAt', 'Dernière modification')
            ->onlyOnDetail()
            ->setFormat(DateTimeField::FORMAT_FULL, DateTimeField::FORMAT_SHORT)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('thèmes')
            ->setEntityLabelInSingular('thème')

            ->setDefaultSort(['id' => 'DESC'])

            ->setSearchFields(['name'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('new', 'Ajouter un %entity_label_singular%')
            ->setPageTitle('detail', fn (Theme $theme) => 'Consulter le %entity_label_singular% : <b>'.$theme->getName().'</b>')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter');
            })
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::EDIT, Action::INDEX])
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('createdAt')
        ;
    }
}
