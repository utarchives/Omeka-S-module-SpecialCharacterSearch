<?php
namespace SpecialCharacterSearch\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Api\Response;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use SpecialCharacterSearch\Entity\CharacterMap;
use SpecialCharacterSearch\Api\Representation\CharacterMapRepresentation;

class CharacterMapAdapter extends AbstractEntityAdapter
{

    public function getResourceName()
    {
        return "character_maps";
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $data = $request->getContent();
        if (isset($data['original_character'])) {
            $entity->setOriginalCharacter($data['original_character']);
        }
        if (isset($data['search_character'])) {
            $entity->setSearchCharacter($data['search_character']);
        }
    }
    /**
     * {@inheritDoc}
     */
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['original_character'])) {
            $qb->andWhere($qb->expr()->like($this->getEntityClass() . '.originalCharacter', $query['original_character']));
        }
    }
    public function getRepresentationClass()
    {
        return CharacterMapRepresentation::class;
    }

    public function getEntityClass()
    {
        return CharacterMap::class;
    }
    /**
     *
     * {@inheritDoc}
     * @see \Omeka\Api\Adapter\AbstractEntityAdapter::delete()
     * Delete all related items
     */
    public function delete(Request $request)
    {
        $entity = new CharacterMap();
        $this->authorize($entity, Request::BATCH_DELETE);
        $connection = $this->serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
truncate table character_map;
SQL;
        $connection->exec($sql);
        return new Response($entity);
    }
}

