<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use SpecialCharacterSearch\Api\Representation\SearchItemPublicRepresentation;
use SpecialCharacterSearch\Entity\SearchItemPublic;

class SearchChildItemPublicAdapter extends AbstractSearchItemEntityAdapter
{

    public function getResourceName()
    {
        return 'search_child_adapters';
    }

    public function getRepresentationClass()
    {
        return SearchItemPublicRepresentation::class;
    }

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
        if (isset($query['parent_item_id'])) {
            $paretnItemAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.parentItem',
                $paretnItemAlias
                );
            $qb->andWhere($qb->expr()->eq($paretnItemAlias . '.id', $query['parent_item_id']));
            $qb->andWhere($qb->expr()->neq($entity . '.id', $query['parent_item_id']));
        }
        if (isset($query['related_item_id'])) {
            $relatedItemAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.relatedItem',
                $relatedItemAlias
                );
            $qb->andWhere($qb->expr()->eq($relatedItemAlias . '.id', $query['related_item_id']));
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
        $qb->andWhere($qb->expr()->neq($entity . '.depth', 1));
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
        $settings = $serviceLocator->get('Omeka\Settings');
        $searchFolderItem = isset($query['search_folder']) ? $query['search_folder'] : false;
        $targetEntity = 'omeka_root';
        $this->buildPropertyQuery($qb, $query, $targetEntity);

        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $targetEntity);
        }

        if (isset($query['owner_id'])) {
            $userAlias = $this->createAlias();
            $qb->innerJoin(
                $targetEntity . '.owner',
                $userAlias
                );
            $qb->andWhere($qb->expr()->eq(
                "$userAlias.id",
                $this->createNamedParameter($qb, $query['owner_id']))
                );
        }
        if (isset($query['resource_class_id']) && is_numeric($query['resource_class_id'])) {
            $resourceClassAlias = $this->createAlias();
            $qb->innerJoin(
                $targetEntity . '.resourceClass',
                $resourceClassAlias
                );
            $qb->andWhere($qb->expr()->eq(
                "$resourceClassAlias.id",
                $this->createNamedParameter($qb, $query['resource_class_id']))
                );
        }
        // configure escape resouce class
        $resourceClassAlias = $this->createAlias();
        $qb->innerJoin(
            $targetEntity . '.resourceClass',
            $resourceClassAlias
            );
//         if ($searchFolderItem) {
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
//             $searchItemAlias = $this->createAlias();
//             $qb->innerJoin(
//                 $this->getEntityClass() . '.searchItem',
//                 $searchItemAlias
//                 );
//             $paretnItemAlias = $this->createAlias();
//             $qb->innerJoin(
//                 $searchItemAlias . '.parentItem',
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

