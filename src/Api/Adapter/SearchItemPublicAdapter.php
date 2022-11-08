<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use SpecialCharacterSearch\Api\Representation\SearchItemPublicRepresentation;
use SpecialCharacterSearch;

class SearchItemPublicAdapter extends AbstractSearchItemEntityAdapter
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
        return 'search_items';
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
        return SpecialCharacterSearch\Entity\SearchItemPublic::class;
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
        if (isset($query['media'])) {
            $mediaAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.media',
                $mediaAlias
                );
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
        $entity = 'omeka_root';
        $serviceLocator = $this->getServiceLocator();
        $controllerPlugins = $serviceLocator->get('ControllerPluginManager');
        $relatedItemsData = $controllerPlugins->get('relatedItemsData');
        $settings = $serviceLocator->get('Omeka\Settings');
        $searchFolderItem = isset($query['search_folder']) ? $query['search_folder'] : false;
        $this->buildPropertyQuery($qb, $query, $entity);
        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $entity);
        }

        if (isset($query['owner_id'])) {
            $userAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.owner',
                $userAlias
                );
            $qb->andWhere($qb->expr()->eq(
                "$userAlias.id",
                $this->createNamedParameter($qb, $query['owner_id']))
                );
        }
        if (isset($query['parent_item_id'])) {
            $qb->andWhere($qb->expr()->neq($entity . '.id', $query['parent_item_id']));
            $paretnItemAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.parentItem',
                $paretnItemAlias
                );
            $qb->andWhere($qb->expr()->eq($paretnItemAlias . '.id', $query['parent_item_id']));
            if (isset($query['is_here'])) {
                $qb->andWhere($qb->expr()->eq($entity . '.isHere', true));
            }
            return;
        }
        // configure escape resouce class
        $resourceClassAlias = $this->createAlias();
        $qb->innerJoin(
            $entity . '.resourceClass',
            $resourceClassAlias
            );
//         if (!$searchFolderItem) {
//             $qb->andWhere("$resourceClassAlias.id = ". $relatedItemsData->getDocumentResourceClassId());
//             if (isset($query['is_here'])) {
//                 $qb->andWhere($qb->expr()->eq($this->getEntityClass() . '.isHere', true));

//             } else {
//                 $qb->andWhere($qb->expr()->eq($this->getEntityClass() . '.isParent', true));
//             }
//         } else {
            $isGroup = false;
            $isItem = false;
            if (isset($query['targetItem'])) {
                foreach ($query['targetItem'] as $target) {
                    if (strcmp($target, 'item') == 0) {
                        $isItem =true;
                    }
                    if (strcmp($target, 'group') == 0) {
                        $isGroup =true;
                    }
                }
            }
            if (isset($query['target_all']) && $query['target_all']) {
                $qb->andWhere("($resourceClassAlias.id = ". $relatedItemsData->getDocumentResourceClassId() . " OR
                            $resourceClassAlias.id = " . $relatedItemsData->getSearchFolderResourceClassId(). " OR
                            $resourceClassAlias.id = " . $relatedItemsData->getFolderResourceClassId().')');
            } else if (!$isGroup && $isItem) {
                $qb->andWhere("$resourceClassAlias.id = ". $relatedItemsData->getDocumentResourceClassId());
            } else if ($isGroup && !$isItem) {
                $qb->andWhere("$resourceClassAlias.id = " . $relatedItemsData->getSearchFolderResourceClassId());
            } else {
                $qb->andWhere("($resourceClassAlias.id = ". $relatedItemsData->getDocumentResourceClassId() . " OR
                            $resourceClassAlias.id = " . $relatedItemsData->getSearchFolderResourceClassId(). ')');
            }
            $qb->andWhere($qb->expr()->eq($entity . '.isParent', true));
//         }

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
//                     "GROUP_CONCAT($valuesAlias.value ORDER BY $valuesAlias.value)",
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
        // $entity = $this->getEntityClass();
        $qb->groupBy("$entity.id,$entity.owner,$entity.resourceClass,$entity.resourceTemplate,$entity.isPublic,$entity.created,$entity.modified,$entity.depth,$entity.parentItem,$entity.isParent,$entity.isHere,$entity.targetResourceClassId,$entity.sort");
    }

}
