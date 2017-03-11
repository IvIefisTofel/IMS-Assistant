<?php
namespace Clients\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Files\Entity\Files;

class ClientsUpload extends Form
{
    const NAME       = 'clientName';
    const ADDONS     = 'clientAddons';
    const NEW_ADDONS = 'clientNewAddons';

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $this->add(new Element\Text(self::NAME));
        $this->add(new Element\File(self::ADDONS));
        $this->add(new Element\File(self::NEW_ADDONS));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter();

        $name = new Input(self::NAME);
        $name->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\StringLength(3));
        $inputFilter->add($name);

        $addons = new Input(self::ADDONS);
        $addons->setRequired(false)
            ->getFilterChain()
            ->attachByName('filerenameupload', [
                    'target'          => Files::UPLOAD_DIR,
                    'randomize'       => true,
                    'overwrite'       => true,
                    'use_upload_name' => false,
                ]
            );
        $inputFilter->add($addons);

        $newAddons = new Input(self::NEW_ADDONS);
        $newAddons->setRequired(false)
            ->getFilterChain()
            ->attachByName('filerenameupload', [
                    'target'          => Files::UPLOAD_DIR,
                    'randomize'       => true,
                    'overwrite'       => true,
                    'use_upload_name' => false,
                ]
            );
        $inputFilter->add($newAddons);

        return $inputFilter;
    }
}