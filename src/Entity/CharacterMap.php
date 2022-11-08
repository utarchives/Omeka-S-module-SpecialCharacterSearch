<?php
namespace SpecialCharacterSearch\Entity;

use Omeka\Entity\AbstractEntity;
/**
 *
 * @Entity
 * @Table(name="character_map",indexes={@Index(name="character_search_idx", columns={"search_character"}),
 * @Index(name="character_convert_idx", columns={"original_character"})})
 */
class CharacterMap extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="string", nullable=true)
     */
    protected $originalCharacter;
    /**
     * @Column(type="string", nullable=true)
     */
    protected $searchCharacter;


    /**
     * @return mixed
     */
    public function getOriginalCharacter()
    {
        return $this->originalCharacter;
    }

    /**
     * @return mixed
     */
    public function getSearchCharacter()
    {
        return $this->searchCharacter;
    }

    /**
     * @param mixed $originalCharacter
     */
    public function setOriginalCharacter($originalCharacter)
    {
        $this->originalCharacter = $originalCharacter;
    }

    /**
     * @param mixed $searchCharacter
     */
    public function setSearchCharacter($searchCharacter)
    {
        $this->searchCharacter = $searchCharacter;
    }

    public function getId()
    {
        return $this->id;
    }
}

