<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Kanji;
use App\Entity\Signification;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Kanji>
 */
class KanjiCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Kanji::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(6);
        yield TextField::new('ideogram', 'Idéogramme');
        yield IntegerField::new('position', 'Classement');
        yield IntegerField::new('numberStroke', 'Traits');
        yield ChoiceField::new('jlpt');
        yield BooleanField::new('published', 'Publié ?');

        yield FormField::addColumn(6);
        yield CollectionField::new('significations')
            ->useEntryCrudForm(SignificationCrudController::class)
            ->setEntryIsComplex(true)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms()
        ;

        yield ArrayField::new('significations', 'Sens')
            ->formatValue(fn (Collection $value) => implode(' / ', array_map(
                fn (Signification $s) => $s->getMeaning(), // @phpstan-ignore-line
                $value->toArray(),
            )))
            ->hideOnForm()
        ;

        yield FormField::addColumn(12);
        yield FormField::addColumn(6);
        yield AssociationField::new('similars', 'Kanji similaire')
            ->onlyWhenCreating()
        ;
        yield AssociationField::new('similars', 'Kanji similaire')
            ->onlyWhenUpdating()
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $kana = $this->getCurrentInstance();

                return $qb
                    ->andWhere('entity.id <> :id')
                    ->setParameter('id', $kana?->getId())
                ;
            })
        ;
        yield FormField::addColumn(6);
        yield AssociationField::new('words', 'Exemple de mots')
            ->hideOnIndex()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('kanji')
            ->setEntityLabelInSingular('kanji')

            ->setDefaultSort(['id' => 'DESC'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('new', 'Ajouter un %entity_label_singular%')
            ->setPageTitle('edit', 'Modifier le %entity_label_singular%')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter');
            })
        ;
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        if (Action::NEW == $action || Action::EDIT == $action) {
            return $this->redirectToRoute('admin_kanji_detail', [
                'entityId' => $context->getEntity()->getPrimaryKeyValue(),
            ]);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    public function getCurrentInstance(): ?Kanji
    {
        return $this->getContext()?->getEntity()?->getInstance();
    }
}
