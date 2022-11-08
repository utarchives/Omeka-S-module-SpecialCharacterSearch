<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Api\Response;
use Omeka\Entity\EntityInterface;
use Omeka\Entity\Value;
use Omeka\Stdlib\ErrorStore;
use SpecialCharacterSearch\Entity\SearchValue;
use SpecialCharacterSearch\Api\Representation\SearchValueRepresentation;
use SpecialCharacterSearch\Api\Representation\ValueDataRepresentation;

class ValueDataAdapter extends AbstractEntityAdapter
{

    public function getResourceName()
    {
        return "value_datas";
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
    }
    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $entity = $this->getEntityClass();
        $qb->expr()->orX(
            $qb->expr()->eq("$entity.type", 'literal'),
            $qb->expr()->eq("$entity.type", 'uri')
            );
    }
    public function getRepresentationClass()
    {
        return ValueDataRepresentation::class;
    }

    public function getEntityClass()
    {
        return Value::class;
    }
}

