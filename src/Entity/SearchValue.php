<?php
namespace SpecialCharacterSearch\Entity;

use Omeka\Entity\AbstractEntity;
/**
 *
 * @Entity
 */
class SearchValue extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $searchValue;
    /**
     * @Column(type="integer")
     */
    protected $valueId;
     /**
     * @ManyToOne(targetEntity="Omeka\Entity\Resource", inversedBy="values")
     * @JoinColumn(nullable=false)
     */
    protected $resource;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\Property", inversedBy="values")
     * @JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $property;
    /**
     * @Column(nullable=true)
     */
    protected $lang;
    /**
     * @Column(type="string")
     */
    protected $type;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $uri;
    /**
     * @Column(type="integer")
     */
    protected $valueResourceId;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSearchValue()
    {
        return $this->searchValue;
    }

    /**
     * @return mixed
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param mixed $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return mixed
     */
    public function getValueResourceId()
    {
        return $this->valueResourceId;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $searchValue
     */
    public function setSearchValue($searchValue)
    {
        $this->searchValue = $searchValue;
    }

    /**
     * @param mixed $valueId
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;
    }

    /**
     * @param mixed $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }


    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param mixed $valueResourceId
     */
    public function setValueResourceId($valueResourceId)
    {
        $this->valueResourceId = $valueResourceId;
    }

    
}

