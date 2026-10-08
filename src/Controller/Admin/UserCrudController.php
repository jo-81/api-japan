<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<User>
 */
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();

        yield EmailField::new('email', 'Adresse email');

        yield BooleanField::new('emailVerified', 'Compte vérifié ?')
            ->renderAsSwitch(false)
            ->hideOnForm()
        ;

        yield DateField::new('createdAt', "Date d'inscription")
            ->onlyOnIndex()
            ->setFormat(DateTimeField::FORMAT_FULL)
        ;

        yield DateTimeField::new('createdAt', "Date d'inscription")
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
            ->setEntityLabelInPlural('utilisateurs')
            ->setEntityLabelInSingular('utilisateur')

            ->setSearchFields(['email'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('detail', 'Consulter l\'%entity_label_singular%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('emailVerified')
            ->add('createdAt')
        ;
    }
}
