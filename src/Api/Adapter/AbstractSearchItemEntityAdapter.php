<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Request;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use Omeka\Api\Exception;

abstract class AbstractSearchItemEntityAdapter extends AbstractEntityAdapter
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        if (isset($query['item_set_id'])) {
            $itemSets = $query['item_set_id'];
            if (!is_array($itemSets)) {
                $itemSets = [$itemSets];
            }
            $itemSets = array_filter($itemSets, 'is_numeric');

            if ($itemSets) {
                $itemSetAlias = $this->createAlias();
                $qb->innerJoin(
                    $entity . '.searchItemSets',
                    $itemSetAlias, 'WITH',
                    $qb->expr()->in("$itemSetAlias.id", $this->createNamedParameter($qb, $itemSets))
                    );
            }
        }
        if (isset($query['resource_class_id']) && is_numeric($query['resource_class_id'])) {
            $resourceClassAlias = $this->createAlias();
            $qb->innerJoin(
                $entity . '.resourceClass',
                $resourceClassAlias
                );
            $qb->andWhere($qb->expr()->eq(
                "$resourceClassAlias.id",
                $this->createNamedParameter($qb, $query['resource_class_id']))
                );
        }
        if (isset($query['is_public'])) {
            $qb->andWhere($qb->expr()->eq(
                $entity . '.isPublic',
                $this->createNamedParameter($qb, (bool) $query['is_public'])
                ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sortQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        if (is_string($query['sort_by'])) {
            $property = $this->getPropertyByTerm($query['sort_by']);
            $entityClass = $entity;
            if ($property) {
                $valuesAlias = $this->createAlias();
                $qb->leftJoin(
                    "$entityClass.values", $valuesAlias,
                    'WITH', $qb->expr()->eq("$valuesAlias.property", $property->getId())
                );
                $qb->addOrderBy(
                    "GROUP_CONCAT($valuesAlias.value ORDER BY $valuesAlias.id)",
                    $query['sort_order']
                );
            } elseif ('resource_class_label' == $query['sort_by']) {
                $resourceClassAlias = $this->createAlias();
                $qb->leftJoin("$entityClass.resourceClass", $resourceClassAlias)
                    ->addOrderBy("$resourceClassAlias.label", $query['sort_order']);
            } elseif ('owner_name' == $query['sort_by']) {
                $ownerAlias = $this->createAlias();
                $qb->leftJoin("$entityClass.owner", $ownerAlias)
                    ->addOrderBy("$ownerAlias.name", $query['sort_order']);
            } else {
                parent::sortQuery($qb, $query);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(Request $request, EntityInterface $entity,
        ErrorStore $errorStore
    ) {
        $data = $request->getContent();
    }

    /**
     * Build query on value.
     *
     * Query format:
     *
     *   - property[{index}][joiner]: "and" OR "or" joiner with previous query
     *   - property[{index}][property]: property ID
     *   - property[{index}][text]: search text
     *   - property[{index}][type]: search type
     *     - eq: is exactly
     *     - neq: is not exactly
     *     - in: contains
     *     - nin: does not contain
     *     - ex: has any value
     *     - nex: has no value
     *
     * @param QueryBuilder $qb
     * @param array $query
     * @param target entity
     */
    protected function buildPropertyQuery(QueryBuilder $qb, array $query, $targetEntity, QueryBuilder $paramQb = null)
    {
        if (!$paramQb) {
            $paramQb = $qb;
        }
        if (!isset($query['property']) || !is_array($query['property'])) {
            return;
        }
        $valuesJoin = $targetEntity . '.searchValues';
        $where = '';
        foreach ($query['property'] as $queryRow) {
            if (!(is_array($queryRow)
                && array_key_exists('property', $queryRow)
                && array_key_exists('type', $queryRow)
                )) {
                    continue;
                }
                $propertyId = $queryRow['property'];
                $queryType = $queryRow['type'];
                $joiner = isset($queryRow['joiner']) ? $queryRow['joiner'] : null;
                $value = isset($queryRow['text']) ? $queryRow['text'] : null;
                $array = isset($queryRow['array']) ? $queryRow['array'] : null;
                if (!$value && $queryType !== 'nex' && $queryType !== 'ex' && !$array) {
                    continue;
                } else if ($array) {
                    $unitWhere = '';
                    foreach ($array as $value) {
                        $valuesAlias = $this->createAlias();
                        $positive = true;
                        $param = $this->createNamedParameter($paramQb, $value);
                        $predicateExpr = $qb->expr()->orX(
                            $qb->expr()->eq("$valuesAlias.searchValue", $param),
                            $qb->expr()->eq("$valuesAlias.uri", $param)
                            );
                        $joinConditions = [];
                        // Narrow to specific property, if one is selected
                        if ($propertyId) {
                            $joinConditions[] = $qb->expr()->eq("$valuesAlias.property", (int) $propertyId);
                        }

                        if ($positive) {
                            $whereClause = '(' . $predicateExpr . ')';
                        } else {
                            $joinConditions[] = $predicateExpr;
                            $whereClause = $qb->expr()->isNull("$valuesAlias.id");
                        }

                        if ($joinConditions) {
                            $qb->leftJoin($valuesJoin, $valuesAlias, 'WITH', $qb->expr()->andX(...$joinConditions));
                        } else {
                            $qb->leftJoin($valuesJoin, $valuesAlias);
                        }

                        if ($unitWhere == '') {
                            $unitWhere =  $whereClause;
                        } else {
                            $unitWhere .= " OR $whereClause";
                        }
                    }
                    if ($where == '') {
                        $where = ' (' . $unitWhere . ') ';
                    } else {
                        $where .= ' AND (' . $unitWhere . ') ';
                    }
                } else {
//                     $value = str_replace('　', ' ', $value);
                    //                 $singleValues = explode(' ', $value);
                    //                 foreach ($singleValues as $singleValue) {
                    $valuesAlias = $this->createAlias();
                    $positive = true;
                    switch ($queryType) {
                        case 'neq':
                            $positive = false;
                        case 'eq':
                            $param = $this->createNamedParameter($paramQb, $value);
                            $predicateExpr = $qb->expr()->orX(
                                $qb->expr()->eq("$valuesAlias.searchValue", $param),
                                $qb->expr()->eq("$valuesAlias.uri", $param)
                                );
                            break;
                        case 'nin':
                            $positive = false;
                        case 'in':
                            $param = $this->createNamedParameter($paramQb, "%$value%");
                            $predicateExpr = $qb->expr()->orX(
                                $qb->expr()->like("$valuesAlias.searchValue", $param),
                                $qb->expr()->like("$valuesAlias.uri", $param)
                                );
                            break;
                        case 'nres':
                            $positive = false;
                        case 'res':
                            $predicateExpr = $qb->expr()->eq(
                            "$valuesAlias.valueResource",
                            $this->createNamedParameter($paramQb, $value)
                            );
                            break;
                        case 'nex':
                            $positive = false;
                        case 'ex':
                            $predicateExpr = $qb->expr()->isNotNull("$valuesAlias.id");
                        default:
                            continue 2;
                    }
                    $joinConditions = [];
                    // Narrow to specific property, if one is selected
                    if ($propertyId) {
                        $joinConditions[] = $qb->expr()->eq("$valuesAlias.property", (int) $propertyId);
                    }

                    if ($positive) {
                        $whereClause = '(' . $predicateExpr . ')';
                    } else {
                        $joinConditions[] = $predicateExpr;
                        $whereClause = $qb->expr()->isNull("$valuesAlias.id");
                    }

                    if ($joinConditions) {
                        $qb->leftJoin($valuesJoin, $valuesAlias, 'WITH', $qb->expr()->andX(...$joinConditions));
                    } else {
                        $qb->leftJoin($valuesJoin, $valuesAlias);
                    }

                    if ($where == '') {
                        $where = $whereClause;
                    } elseif ($joiner == 'or') {
                        $where .= " OR $whereClause";
                    } else {
                        $where .= " AND $whereClause";
                    }
                }
//
            }
//         }

        if ($where) {
            $qb->andWhere($where);
        }
//         var_dump($qb->getQuery()->getSQL());
//         exit;
    }
    protected function buildSiteFilter($query) {
        $params = [];
        if (!isset($query['site_id'])) {
            return $query;
        }
        if (!empty($query['site_id'])) {
            $siteAdapter = $this->getAdapter('sites');
            try {
                $site = $siteAdapter->findEntity($query['site_id']);
                $params = $site->getItemPool();
                if (!is_array($params)) {
                    $params = [];
                }
                foreach ($params as $key => $param) {
                    if (strcmp('site_id', $key) == 0) {
                        continue;
                    }
                    if (strcmp($key, 'property') == 0) {
                        foreach ($param as $property) {
                            $query['property'][] = $property;
                        }
                    } else {
                        $query[$key] = $param;
                    }
                }
                // Avoid potential infinite recursion
//                 unset($params['site_id']);
            } catch (Exception\NotFoundException $e) {
                $site = null;
            }
        }
        return $query;
    }
    protected function buildSiteAttachmentFilter($qb, $query) {
        $entity = 'omeka_root';
        if (!isset($query['site_id'])) {
            return;
        }
        if (!empty($query['site_id'])) {
            if (isset($query['site_attachments_only']) && $query['site_attachments_only']) {
                $siteBlockAttachmentsAlias = $this->createAlias();
                $qb->innerJoin(
                    $entity . '.siteBlockAttachments',
                    $siteBlockAttachmentsAlias
                    );
                $sitePageBlockAlias = $this->createAlias();
                $qb->innerJoin(
                    "$siteBlockAttachmentsAlias.block",
                    $sitePageBlockAlias
                    );
                $sitePageAlias = $this->createAlias();
                $qb->innerJoin(
                    "$sitePageBlockAlias.page",
                    $sitePageAlias
                    );
                $siteAlias = $this->createAlias();
                $qb->innerJoin(
                    "$sitePageAlias.site",
                    $siteAlias
                    );
                $qb->andWhere($qb->expr()->eq(
                    "$siteAlias.id",
                    $this->createNamedParameter($qb, $query['site_id']))
                    );
            }
        }
    }

    /**
     * Get a property entity by JSON-LD term.
     *
     * @param string $term
     * @return EntityInterface
     */
    public function getPropertyByTerm($term)
    {
        if (!$this->isTerm($term)) {
            return null;
        }
        list($prefix, $localName) = explode(':', $term);
        $dql = 'SELECT p FROM Omeka\Entity\Property p
        JOIN p.vocabulary v WHERE p.localName = :localName
        AND v.prefix = :prefix';
        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters([
                'localName' => $localName,
                'prefix' => $prefix,
            ])->getOneOrNullResult();
    }

}
