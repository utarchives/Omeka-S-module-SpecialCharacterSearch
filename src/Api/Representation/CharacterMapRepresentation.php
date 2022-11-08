<?php
namespace SpecialCharacterSearch\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class CharacterMapRepresentation extends AbstractEntityRepresentation
{

    public function originalCharacter()
    {
        return $this->resource->getOriginalCharacter();
    }
    public function searchCharacter()
    {
        return $this->resource->getSearchCharacter();
    }
    public function getJsonLdType()
    {
        return 'o:CharacterMap';
    }

    public function getJsonLd()
    {
        return [
            'o:id' => $this->id,
            'o:original_character' => $this->originalCharacter(),
            'o:search_character' => $this->searchCharacter(),
        ];
    }
}

