<?php
namespace Clients\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Files\Entity\Files;

class AddForm extends Form
{
    const NAME    = 'clientName';
    const DESCRIPTION = 'clientDescription';
    const ADDITIONS = 'clientAdditions';

    public function __construct($options = [])
    {
        parent::__construct('client-add', $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $this->add(new Element\Text(self::NAME));
        $this->add(new Element\Textarea(self::DESCRIPTION));
        $this->add(new Element\File(self::ADDITIONS));
        $this->add(new Element\Submit('submit'));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter();

        $name = new Input(self::NAME);
        $name->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\StringLength(3));
        $inputFilter->add($name);

        $description = new Input(self::DESCRIPTION);
        $description->setRequired(false)
            ->getValidatorChain()
            ->attach(new Validator\StringLength(6));
        $inputFilter->add($description);

        $additions = new Input(self::ADDITIONS);
        $additions->setRequired(false)
            ->getFilterChain()
            ->attachByName('filerenameupload', [
                'target'          => Files::UPLOAD_DIR,
                'randomize'       => true,
                'overwrite'       => true,
                'use_upload_name' => false,
            ]
        );
        $inputFilter->add($additions);

        return $inputFilter;
    }
}