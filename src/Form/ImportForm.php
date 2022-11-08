<?php
namespace SpecialCharacterSearch\Form;

use Laminas\Form\Form;

class ImportForm extends Form
{
    protected $mappingClasses;

    public function init()
    {
        $this->add([
                'name' => 'csv',
                'type' => 'file',
                'options' => [
                    'label' => 'CSV file', // @translate
                    'info' => 'The Character Map CSV file to upload', //@translate
                ],
                'attributes' => [
                    'id' => 'csv',
                    'required' => 'true',
                ],
        ]);
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'csv',
            'required' => true,
        ]);
    }
}
