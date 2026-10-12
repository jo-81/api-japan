<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ApiKey;
use App\Enum\ApiKeyStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<ApiKey>
 */
class ApiKeyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ApiKey::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom de la clé');

        yield ChoiceField::new('status', 'Statut')
            ->setFormTypeOption('choice_label', fn (ApiKeyStatus $value) => $value->getLabel())
        ;

        yield DateField::new('createdAt', 'Date de création')
            ->onlyOnIndex()
        ;

        yield DateField::new('lastUsedAt', 'Date de dernière utilisation')
            ->onlyOnIndex()
        ;

        yield DateField::new('expiredAt', "Date d'expiration")
            ->onlyOnIndex()
        ;

        yield TextField::new('user.email', 'Utilisateur');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('clés d\'api')
            ->setEntityLabelInSingular('clé d\'api')

            ->setDefaultSort(['createdAt' => 'DESC'])

            ->setSearchFields(['user.email', 'name'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('expiredAt')
            ->add('lastUsedAt')
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices(ApiKeyStatus::getChoices()))
            ->add(EntityFilter::new('user', 'Utilisateur')
                ->setFormTypeOption('value_type_options.choice_label', 'email')
                ->canSelectMultiple(false),
            )
        ;
    }
}
