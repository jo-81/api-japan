<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\JlptLevel;
use App\Entity\Vocabulary;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
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
        yield IdField::new('id')->onlyOnDetail();

        yield TextField::new('meaning', 'Signification');

        yield SlugField::new('slug', 'Permalien')->setTargetFieldName('romaji')->onlyOnDetail();

        yield TextField::new('kana');

        yield TextField::new('romaji');

        yield TextField::new('kunrei');

        yield BooleanField::new('published', 'Publié ?');

        yield ChoiceField::new('jlpt', 'Niveau JLPT');

        yield AssociationField::new('theme', 'Thème associé');

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
            ->setEntityLabelInPlural('mots de vocabulaire')
            ->setEntityLabelInSingular('mot de vocabulaire')

            ->setDefaultSort(['id' => 'DESC'])

            ->setSearchFields(['romaji', 'kunrei', 'meaning', 'kana'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('detail',
                fn (Vocabulary $vocabulary) => sprintf('Consulter le mot : <b>%s</b>', $vocabulary->getMeaning()),
            )
            ->setPageTitle('new', 'Ajouter un %entity_label_singular%')
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
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::EDIT, Action::INDEX])
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
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

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        if (Action::NEW == $action) {
            return $this->redirectToRoute('admin_vocabulary_detail', [
                'entityId' => $context->getEntity()->getPrimaryKeyValue(),
            ]);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }
}
