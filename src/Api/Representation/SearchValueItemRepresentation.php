<?php
namespace SpecialCharacterSearch\Api\Representation;

use Omeka\Api\Representation\ItemRepresentation;


class SearchValueItemRepresentation extends AbstractSearchItemRepresentation
{
    /**
     * {@inheritDoc}
     */
    public function getResourceJsonLdType()
    {
        return 'o:SearchValueItem';
    }
}
