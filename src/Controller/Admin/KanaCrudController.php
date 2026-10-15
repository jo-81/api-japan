<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Kana;
use App\Enum\KanaType;
use App\Enum\KanaAccentType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Kana>
 */
class KanaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Kana::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('ideogram', 'Idéogramme');

        yield TextField::new('romaji');

        yield TextField::new('kunrei');

        yield ChoiceField::new('type');

        yield IntegerField::new('position');

        yield ChoiceField::new('accentType', 'Accent');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('kana')
            ->setEntityLabelInSingular('kana')

            ->setDefaultSort(['position' => 'ASC'])

            ->setSearchFields(['romaji', 'kunrei', 'position'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('romaji')
            ->add('kunrei')
            ->add('position')
            ->add(ChoiceFilter::new('type')->setChoices(KanaType::getChoices()))
            ->add(ChoiceFilter::new('accentType')->setChoices(KanaAccentType::getChoices()))
        ;
    }
}
