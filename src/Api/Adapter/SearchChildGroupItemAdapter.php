<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use SpecialCharacterSearch\Api\Representation\SearchChildItemRepresentation;
use SpecialCharacterSearch\Entity\SearchChildGroupItem;
use SpecialCharacterSearch\Entity\SearchChildItem;

class SearchChildGroupItemAdapter extends AbstractSearchItemEntityAdapter
{

    public function getResourceName()
    {
        return 'search_child_group_adapters';
    }

    public function getRepresentationClass()
    {
        return SearchChildItemRepresentation::class;
    }

    public function getEntityClass()
    {
        return SearchChildItem::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $query = $this->buildSiteFilter($query);
        $this->buildResourceQuery($qb, $query);
        if (isset($query['id'])) {
            $qb->andWhere($qb->expr()->eq($this->getEntityClass() . '.id', $query['id']));
        }
        parent::buildQuery($qb, $query);
        $this->buildSiteAttachmentFilter($qb, $query);
    }
    /**
     * {@inheritDoc}
     */
    public function buildResourceQuery(QueryBuilder $qb, array $query)
    {
        // configure collaborate with Resource Tree
        $serviceLocator = $this->getServiceLocator();
        $controllerPlugins = $serviceLocator->get('ControllerPluginManager');
        $relatedItemsData = $controllerPlugins->get('relatedItemsData');
        $qb->andWhere(" ((1=1 ");
        $this->buildPropertyQuery($qb, $query, $this->getEntityClass());
        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $this->getEntityClass());
        }
//         $qb->andWhere("$searchItem.id is not null");
        if (isset($query['media'])) {
            $mediaAlias = $this->createAlias();
            $qb->leftJoin(
                $this->getEntityClass() . '.media',
                $mediaAlias
                );
            $qb->andWhere("$mediaAlias.id is not null");
        }
        $qb->andWhere("1=1)) OR ((1=1");
        $childItem = 'child_group_item';
        // サブクエリを作成
        $subQb =  $this->getEntityManager()->createQueryBuilder()
        ->select("$childItem.id")
        ->from(SearchChildGroupItem::class, $childItem);
        $childSearchItem = $this->createAlias();
        $subQb->innerJoin(
            $childItem . '.searchItem',
            $childSearchItem
            );
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
        if (isset($query['parent_item_id'])) {
            $subQb->andWhere($subQb->expr()->eq("$childItem.targetParentId", $query['parent_item_id']));
        }
        $qb->andWhere("{$this->getEntityClass()}.id in ({$subQb->getDql()})");
        $qb->andWhere("1=1)) ");
        $resourceClassAlias = $this->createAlias();
        if (isset($query['parent_item_id'])) {
            $paretnItemAlias = $this->createAlias();
            $qb->innerJoin(
                $this->getEntityClass() . '.parentItem',
                $paretnItemAlias
                );
            $qb->andWhere($qb->expr()->eq($paretnItemAlias . '.id', $query['parent_item_id']));
        }
        $qb->innerJoin(
            $this->getEntityClass() . '.resourceClass',
            $resourceClassAlias
            );
        $qb->andWhere("($resourceClassAlias.id = ". $relatedItemsData->getFolderResourceClassId() . " OR
                            $resourceClassAlias.id = " . $relatedItemsData->getSearchFolderResourceClassId(). ')');
        $qb->andWhere($qb->expr()->eq($this->getEntityClass() . '.isHere', true));
    }
    /**
     * {@inheritDoc}
     */
    public function sortQuery(QueryBuilder $qb, array $query)
    {
        if ('parent' == $query['sort_by']) {
            $paretnItemAlias = $this->createAlias();
            $qb->innerJoin(
                $this->getEntityClass()  . '.parentItem',
                $paretnItemAlias
                );
            $property = $this->getPropertyByTerm('dcterms:title');
            if ($property) {
                $valuesAlias = $this->createAlias();
                $qb->leftJoin(
                    "$paretnItemAlias.values", $valuesAlias,
                    'WITH', $qb->expr()->eq("$valuesAlias.property", $property->getId())
                    );
                $qb->addOrderBy(
                    "GROUP_CONCAT($valuesAlias.value ORDER BY $valuesAlias.id)",
                    $query['sort_order']
                    );
            }
            $qb->addOrderBy(
                "GROUP_CONCAT($paretnItemAlias.id ORDER BY $paretnItemAlias.id)",
                $query['sort_order']
                );
        } else {
            parent::sortQuery($qb, $query);
        }
        $entity = $this->getEntityClass();
        $qb->groupBy("$entity.id,$entity.owner,$entity.resourceClass,$entity.resourceTemplate,$entity.isPublic,$entity.created,$entity.modified,$entity.searchItem,$entity.parentItem,$entity.relatedItem,$entity.depth,$entity.isParent");
    }
}

