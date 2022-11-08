<?php

namespace SpecialCharacterSearch\Form;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Form\Element\Checkbox;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;

class ConfigForm extends Form  implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;
    public function init()
    {
        $this->add([
            'name' => 'special_character_search_config',
            'type' => Fieldset::class,
            'options' => [
                'label' => 'Special Character Search Config', // @translate
                'info' => $this->translate('Configuration for using special character search.')
            ],
        ]);
        $specialCharacterSearchConfigFieldSet = $this->get('special_character_search_config');
        $specialCharacterSearchConfigFieldSet->add([
            'name' => 'special_character_search_folder',
            'type' => Checkbox::class,
            'options' => [
                'label' => 'Search Folder Item', // @translate
                'info' => $this->translate('If not checked not searching folder items .') // @translate
            ],
        ]);
        $inputFilter = $this->getInputFilter();
        $specialCharacterSearchConfigFilter = $inputFilter->get('special_character_search_config');
        $specialCharacterSearchConfigFilter->add([
            'name' => 'special_character_search_folder',
            'required' => false,
        ]);
    }
    /**
     *
     * @param $args
     * @return string
     */
    protected function translate($args)
    {
        $translator = $this->getTranslator();
        return $translator->translate($args);
    }
}