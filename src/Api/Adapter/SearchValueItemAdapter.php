<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use SpecialCharacterSearch\Api\Representation\SearchValueItemRepresentation;
use SpecialCharacterSearch;

class SearchValueItemAdapter extends AbstractSearchItemEntityAdapter
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
        return 'search_value_items';
    }

    /**
     * {@inheritDoc}
     */
    public function getRepresentationClass()
    {
        return SearchValueItemRepresentation::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityClass()
    {
        return SpecialCharacterSearch\Entity\SearchValueItem::class;
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
        if (isset($query['media'])) {
            $mediaAlias = $this->createAlias();
            $qb->innerJoin(
                $this->getEntityClass() . '.media',
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
        $this->buildPropertyQuery($qb, $query, $this->getEntityClass());
        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]], $this->getEntityClass());
        }
        if (isset($query['owner_id'])) {
            $userAlias = $this->createAlias();
            $qb->innerJoin(
                $this->getEntityClass() . '.owner',
                $userAlias
                );
            $qb->andWhere($qb->expr()->eq(
                "$userAlias.id",
                $this->createNamedParameter($qb, $query['owner_id']))
                );
        }
    }
    public function sortQuery(QueryBuilder $qb, array $query)
    {
        parent::sortQuery($qb, $query);
        $entity = $this->getEntityClass();
        $qb->groupBy("$entity.id,$entity.owner,$entity.resourceClass,$entity.resourceTemplate,$entity.isPublic,$entity.created,$entity.modified");
    }

}
