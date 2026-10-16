<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Kana;
use App\Enum\KanaType;
use App\Enum\KanaAccentType;
use Doctrine\ORM\QueryBuilder;
use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use App\Controller\Admin\Filter\HasComponentsFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
        yield TextField::new('ideogram', 'Idéogramme')->setColumns(4);

        yield TextField::new('romaji')->setColumns(4);

        yield TextField::new('kunrei')->setColumns(4);

        yield ChoiceField::new('type')->setColumns(4);

        yield IntegerField::new('position')->setColumns(4);

        yield ChoiceField::new('accentType', 'Accent')->setColumns(4);

        yield ArrayField::new('components')->onlyOnDetail();

        yield AssociationField::new('components', 'Combinaisons de kana')
            ->setHelp('Doit contenir exactement 2 kana.')
            ->setColumns(4)
            ->onlyWhenUpdating()
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $kana = $this->getCurrentInstance();

                return $qb
                    ->andWhere('entity.type = :type')
                    ->andWhere('entity.components IS EMPTY')
                    ->setParameter('type', $kana?->getType())
                ;
            });

        yield TextField::new('imageFile', 'Fichier')
            ->onlyWhenUpdating()
            ->setFormType(VichFileType::class)
            ->setFormTypeOptions([
                'allow_delete' => true,
                'download_uri' => true,
                'required' => false,
            ])
        ;

        yield ImageField::new('imageName', 'Fichier')
            ->onlyOnDetail()
            ->setBasePath('uploads/kana')
            ->setUploadDir('uploads/kana')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('kana')
            ->setEntityLabelInSingular('kana')

            ->setDefaultSort(['position' => 'ASC'])

            ->setSearchFields(['romaji', 'kunrei', 'position'])

            ->setPageTitle('index', 'Liste des %entity_label_plural%')
            ->setPageTitle('edit', fn (Kana $kana) => sprintf('Modifier le kana <b>%s</b>', $kana->getIdeogram()),
            )
            ->setPageTitle('detail', fn (Kana $kana) => sprintf('Consulter le kana <b>%s</b>', $kana->getIdeogram()),
            )
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE)
            ->remove(Action::EDIT, Action::SAVE_AND_RETURN)
            ->reorder(Crud::PAGE_EDIT, [Action::DETAIL, Action::SAVE_AND_CONTINUE])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('romaji')
            ->add('kunrei')
            ->add('position')
            ->add(HasComponentsFilter::new('components', 'Est une combinaison ?'))
            ->add(ChoiceFilter::new('type')->setChoices(KanaType::getChoices()))
            ->add(ChoiceFilter::new('accentType')->setChoices(KanaAccentType::getChoices()))
        ;
    }

    public function getCurrentInstance(): ?Kana
    {
        return $this->getContext()?->getEntity()?->getInstance();
    }
}
