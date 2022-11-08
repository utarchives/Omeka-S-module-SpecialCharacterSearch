<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use SpecialCharacterSearch\Api\Representation\SearchItemPublicRepresentation;
use SpecialCharacterSearch\Entity\SearchGroupItemPublic;
use SpecialCharacterSearch\Entity\SearchItemPublic;


class SearchGroupItemPublicAdapter extends AbstractSearchItemEntityAdapter
{
    /**
     * {@inheritDoc}
     */
    protected $sortFields = [
        'id' => 'id',
        'is_public' => 'isPublic',
        'created' => 'created',
        'modified' => 'modified',
    ];

    /**
     * {@inheritDoc}
     */
    public function getResourceName()
    {
        return 'search_group_items';
    }

    /**
     * {@inheritDoc}
     */
    public function getRepresentationClass()
    {
        return SearchItemPublicRepresentation::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityClass()
    {
        return SearchItemPublic::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        $query = $this->buildSiteFilter($query);
        $this->buildResourceQuery($qb, $query);
        if (isset($query['id'])) {
            $qb->andWhere($qb->expr()->eq($entity . '.id', $query['id']));
        }
        parent::buildQuery($qb, $query);
        $this->buildSiteAttachmentFilter($qb, $query);
    }
    /**
     * {@inheritDoc}
     */
    public function buildResourceQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        // configure collaborate with Resource Tree
        $serviceLocator = $this->getServiceLocator();
        $controllerPlugins = $serviceLocator->get('ControllerPluginManager');
        $relatedItemsData = $controllerPlugins->get('relatedItemsData');
        $qb->andWhere(" ((1=1 ");
        $this->buildPropertyQuery($qb, $query, $entity);
        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $entity);
        }

//         $qb->andWhere("$searchItem.id is not null");
//         $qb->andWhere("1=1)) ");
        if (isset($query['media'])) {
            $mediaAlias = $this->createAlias();
            $qb->leftJoin(
                $entity . '.media',
                $mediaAlias
                );
            $qb->andWhere("$mediaAlias.id is not null");
        }
        $qb->andWhere("1=1)) OR ((1=1");
        $childItem = 'child_group_item';
        // サブクエリを作成
        $subQb =  $this->getEntityManager()->createQueryBuilder()
        ->select("$childItem.id")
        ->from(SearchGroupItemPublic::class, $childItem);
//         $childClassAlias = $this->createAlias();
//         $subQb->innerJoin(
//             $childItem . '.resourceClass',
//             $childClassAlias
//             );

        $childSearchItem = $this->createAlias();
        $subQb->innerJoin(
            $childItem . '.searchItemPublic',
            $childSearchItem
            );
        $subQb->andWhere("$childSearchItem.targetResourceClassId = ". $relatedItemsData->getDocumentResourceClassId());
        $this->buildPropertyQuery($subQb, $query, $childSearchItem, $qb);
        if (isset($query['search'])) {
            $this->buildPropertyQuery($subQb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $childSearchItem, $qb);
        }
        if (isset($query['media'])) {
            $mediaAlias = $this->createAlias();
            $subQb->innerJoin(
                $childSearchItem . '.media',
                $mediaAlias
                );
        }
        $qb->andWhere("{$entity}.id in ({$subQb->getDql()})");
        $qb->andWhere("1=1)) ");
        $qb->andWhere($qb->expr()->eq($entity . '.isHere', true));
        $resourceClassAlias = $this->createAlias();
        $qb->innerJoin(
            $entity . '.resourceClass',
            $resourceClassAlias
            );
        $qb->andWhere("$resourceClassAlias.id = " . $relatedItemsData->getSearchFolderResourceClassId());

    }
    /**
     * {@inheritDoc}
     */
    public function sortQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        if ('parent' == $query['sort_by']) {
            $qb->addOrderBy(
                $entity . '.sort',
                $query['sort_order']
                );
//             $paretnItemAlias = $this->createAlias();
//             $qb->innerJoin(
//                 $this->getEntityClass() . '.parentItem',
//                 $paretnItemAlias
//                 );
//             $property = $this->getPropertyByTerm('dcterms:title');
//             if ($property) {
//                 $valuesAlias = $this->createAlias();
//                 $qb->leftJoin(
//                     "$paretnItemAlias.values", $valuesAlias,
//                     'WITH', $qb->expr()->eq("$valuesAlias.property", $property->getId())
//                     );
//                 $qb->addOrderBy(
//                     "GROUP_CONCAT($valuesAlias.value ORDER BY $valuesAlias.id)",
//                     $query['sort_order']
//                     );
//             }
//             $qb->addOrderBy(
//                 "GROUP_CONCAT($paretnItemAlias.id ORDER BY $paretnItemAlias.id)",
//                 $query['sort_order']
//                 );
        } else {
            parent::sortQuery($qb, $query);
        }
        $qb->groupBy("$entity.id,$entity.owner,$entity.resourceClass,$entity.resourceTemplate,$entity.isPublic,$entity.created,$entity.modified,$entity.depth,$entity.parentItem,$entity.isParent,$entity.isHere,$entity.targetResourceClassId,$entity.sort");
    }
}
