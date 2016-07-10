<?php
namespace Files\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class FileUpload extends Form
{
    const UPLOAD_DIR = './upload/';

    const FILES = 'files';

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $file = new Element\File(self::FILES);
        $file
            ->setLabel('Выбрать файл')
            ->setAttributes([
                'id' => 'file',
                'multiple' => true,
            ]);
        $this->add($file);

        $submit = new Element\Submit('submit');
        $submit
            ->setLabel('Загрузить')
            ->setAttributes([
                'class' => 'btn-primary',
            ]);
        $this->add($submit);
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $file = new InputFilter\FileInput(self::FILES);
        $file->setRequired(true);
        $file->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => self::UPLOAD_DIR,
                'randomize'       => true,
                'overwrite'       => true,
                'use_upload_name' => false,
            ]
        );
        $inputFilter->add($file);

        return $inputFilter;
    }
}