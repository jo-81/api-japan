<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\JlptLevel;
use App\Entity\Vocabulary;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Vocabulary>
 */
class VocabularyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Vocabulary::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('meaning', 'Signification');

        yield TextField::new('kana');

        yield TextField::new('romaji');

        yield TextField::new('kunrei');

        yield BooleanField::new('published', 'Publié ?');

        yield ChoiceField::new('jlpt', 'Niveau JLPT');

        yield AssociationField::new('theme', 'Thème associé');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('mots de vocabulaire')
            ->setEntityLabelInSingular('mot de vocabulaire')

            ->setDefaultSort(['id' => 'DESC'])

            ->setSearchFields(['romaji', 'kunrei', 'meaning', 'kana'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter');
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('meaning')
            ->add('kana')
            ->add('published')
            ->add(ChoiceFilter::new('jlpt')->setTranslatableChoices(JlptLevel::cases()))
            ->add('theme')
        ;
    }
}
