<?php
namespace Orders\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Files\Entity\Files;

class OrdersUpload extends Form
{
    const FILES = 'files';

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $this->add(new Element\File(self::FILES));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $files = new InputFilter\FileInput(self::FILES);
        $files->setRequired(false)->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => Files::UPLOAD_DIR,
                'randomize'       => true,
                'overwrite'       => true,
                'use_upload_name' => false,
            ]
        );
        $inputFilter->add($files);

        return $inputFilter;
    }
}