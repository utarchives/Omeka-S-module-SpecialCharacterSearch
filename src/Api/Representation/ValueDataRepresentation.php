<?php
namespace SpecialCharacterSearch\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class ValueDataRepresentation extends AbstractEntityRepresentation
{

    /**
     * Get the resource representation.
     *
     * This is the subject of the RDF triple represented by this value.
     *
     * @return 
     */
    public function resource()
    {
        $resource = $this->resource->getResource();
        return $this->getAdapter($resource->getResourceName())
            ->getRepresentation($resource);
    }

    /**
     * Get the property representation.
     *
     * This is the predicate of the RDF triple represented by this value.
     *
     * @return 
     */
    public function property()
    {
        return $this->getAdapter('properties')
        ->getRepresentation($this->resource->getProperty());
    }

    /**
     * Get the value itself.
     *
     * This is the object of the RDF triple represented by this value.
     *
     * @return string
     */
    public function value()
    {
        return $this->resource->getValue();
    }
    /**
     * Get the value type.
     *
     * @return string
     */
    public function type()
    {
        // The data type resolved by the data type manager takes precedence over
        // the one stored in the database.
        return $this->resource->getType();
    }
    /**
     * Get the value language.
     *
     * @return string
     */
    public function lang()
    {
        return $this->resource->getLang();
    }

    /**
     * Get the URI.
     *
     * @return string
     */
    public function uri()
    {
        return $this->resource->getUri();
    }

    /**
     * Get the value resource representation.
     *
     * This is the object of the RDF triple represented by this value.
     *
     * @return 
     */
    public function valueResourceId()
    {
        $resource = $this->resource->getValueResource();
        if (!$resource) {
            return null;
        }
        $resourceAdapter = $this->getAdapter($resource->getResourceName());
        return $resourceAdapter->getRepresentation($resource);
    }
    public function getJsonLdType()
    {}

    public function getJsonLd()
    {}

}
