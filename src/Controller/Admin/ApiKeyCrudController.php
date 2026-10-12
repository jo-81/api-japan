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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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

        yield DateTimeField::new('createdAt', 'Date de création')
            ->onlyOnDetail()
            ->setFormat(DateTimeField::FORMAT_FULL, DateTimeField::FORMAT_SHORT)
        ;

        yield DateField::new('lastUsedAt', 'Date de dernière utilisation')
            ->onlyOnIndex()
        ;

        yield DateTimeField::new('lastUsedAt', 'Date de dernière utilisation')
            ->onlyOnDetail()
            ->setFormat(DateTimeField::FORMAT_FULL, DateTimeField::FORMAT_SHORT)
        ;

        yield DateField::new('expiredAt', "Date d'expiration")
            ->onlyOnIndex()
        ;

        yield DateTimeField::new('expiredAt', "Date d'expiration")
            ->onlyOnDetail()
            ->setFormat(DateTimeField::FORMAT_FULL, DateTimeField::FORMAT_SHORT)
        ;

        yield TextField::new('user.email', 'Utilisateur')->onlyOnIndex();

        yield AssociationField::new('user', 'Utilisateur')->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('clés d\'api')
            ->setEntityLabelInSingular('clé d\'api')

            ->setDefaultSort(['createdAt' => 'DESC'])

            ->setSearchFields(['user.email', 'name'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('detail', fn (ApiKey $apiKey) => 'Consulter la %entity_label_singular% : <b>'.$apiKey->getName().'</b>')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT)
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::INDEX])
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
