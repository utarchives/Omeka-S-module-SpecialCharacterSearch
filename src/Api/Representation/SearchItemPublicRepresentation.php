<?php
namespace SpecialCharacterSearch\Api\Representation;

use Omeka\Api\Representation\ItemRepresentation;


class SearchItemPublicRepresentation extends AbstractSearchItemRepresentation
{
    /**
     * {@inheritDoc}
     */
    public function getResourceJsonLdType()
    {
        return 'o:SearchItem';
    }
    /**
     * Get Item.
     *
     * @return ItemRepresentation
     */
    public function parentItem()
    {
        return $this->getAdapter('items')
        ->getRepresentation($this->resource->getParentItem());
    }
}
