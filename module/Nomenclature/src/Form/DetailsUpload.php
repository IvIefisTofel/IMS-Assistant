<?php
namespace Nomenclature\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Files\Entity\Files;

class DetailsUpload extends Form
{
    const MODELS = 'models';
    const PROJECTS = 'projects';

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $this->add(new Element\File(self::MODELS));
        $this->add(new Element\File(self::PROJECTS));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $models = new InputFilter\FileInput(self::MODELS);
        $models->setRequired(false)->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => Files::UPLOAD_DIR,
                'randomize'       => true,
                'overwrite'       => true,
                'use_upload_name' => false,
            ]
        );
        $inputFilter->add($models);

        $projects = new InputFilter\FileInput(self::PROJECTS);
        $projects->setRequired(false)->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => Files::UPLOAD_DIR,
                'randomize'       => true,
                'overwrite'       => true,
                'use_upload_name' => false,
            ]
        );
        $inputFilter->add($projects);

        return $inputFilter;
    }
}