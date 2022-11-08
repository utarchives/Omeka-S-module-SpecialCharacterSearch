<?php
namespace SpecialCharacterSearch\Api\Representation;

use Omeka\Api\Representation\ItemRepresentation;

class SearchGroupItemPublicRepresentation extends AbstractSearchItemRepresentation
{
    /**
     * {@inheritDoc}
     */
    public function getResourceJsonLdType()
    {
        return 'o:SearchGroupItem';
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
    /**
     * Get Search Item.
     *
     * @return SearchItemRepresentation
     */
    public function searchItem()
    {
        return $this->getAdapter('search_items')
        ->getRepresentation($this->resource->getSearchItem());
    }
}
