<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Api\Response;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use SpecialCharacterSearch\Entity\SearchValue;
use SpecialCharacterSearch\Api\Representation\SearchValueRepresentation;

class SearchValueAdapter extends AbstractEntityAdapter
{

    public function getResourceName()
    {
        return "search_values";
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $data = $request->getContent();
        if (isset($data['value_id'])) {
            $entity->setValueId($data['value_id']);
        }
        if (isset($data['resource'])) {
            $entity->setResource($data['resource']);
        }
        if (isset($data['property_id'])) {
            $entity->setPropertyId($data['property_id']);
        }
//         if (isset($data['value_resource_id'])) {
//             $entity->setSearchCharacter($data['value_resource_id']);
//         }
        if (isset($data['search_value'])) {
            $entity->setSearchValue($data['search_value']);
        }
        if (isset($data['lang'])) {
            $entity->setLang($data['lang']);
        }
        if (isset($data['type'])) {
            $entity->setType($data['type']);
        }
        if (isset($data['uri'])) {
            $entity->setUri($data['uri']);
        }
    }
    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['value_id'])) {
            $qb->andWhere($qb->expr()->eq($this->getEntityClass() . '.valueId', $query['value_id']));
        }
    }
    public function getRepresentationClass()
    {
        return SearchValueRepresentation::class;
    }

    public function getEntityClass()
    {
        return SearchValue::class;
    }
    /**
     *
     * {@inheritDoc}
     * @see \Omeka\Api\Adapter\AbstractEntityAdapter::create()
     * add new related item
     */
    public function create(Request $request)
    {
        $entity = new SearchValue();
        $this->authorize($entity, Request::CREATE);
        $connection = $this->serviceLocator->get('Omeka\Connection');
        $content = $request->getContent();
        $connection->insert('search_value', $content);
        return new Response($entity);
    }
    /**
     *
     * {@inheritDoc}
     * @see \Omeka\Api\Adapter\AbstractEntityAdapter::delete()
     * Delete all related items
     */
    public function delete(Request $request)
    {
        $entity = new SearchValue();
        $this->authorize($entity, Request::BATCH_DELETE);
        $connection = $this->serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
truncate table search_value
SQL;
        $connection->exec($sql);
        return new Response($entity);
    }
}

