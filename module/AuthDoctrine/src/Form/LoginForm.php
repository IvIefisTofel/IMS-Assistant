<?php
namespace AuthDoctrine\Form;

use TwbBundle\View\Helper\TwbBundleButtonGroup;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class LoginForm extends Form
{
    const NAME    = 'name';
    const PASSWORD = 'password';

    public function __construct($options = [])
    {
        parent::__construct('login', $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $email = new Element\Text(self::NAME);
        $email->setLabel('Логин')
            ->setLabelAttributes(['class' => 'col-sm-2 control-label'])
            ->setAttributes([
                'id' => self::NAME,
                'placeholder' => 'Имя пользователя',
                'required' => true,
            ])->setOptions([
                'column-size' => 'sm-10',
            ]);
        $this->add($email);

        $password = new Element\Password(self::PASSWORD);
        $password->setLabel('Пароль')
            ->setLabelAttributes(['class' => 'col-sm-2 control-label'])
            ->setAttributes([
                'id' => self::PASSWORD,
                'placeholder' => 'Пароль',
                'required' => true,
            ])->setOptions([
                'column-size' => 'sm-10'
            ]);
        $this->add($password);

        new TwbBundleButtonGroup();
        $submit = new Element\Submit('submit');
        $submit
            ->setLabel('Вход')
            ->setAttributes([
                'class' => 'btn btn-default btn-auth',
            ])->setOptions([
                'column-size' => 'sm-offset-2 col-sm-10 btn-auth-container',
            ]);
        $this->add($submit);
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter();

        $email = new Input(self::NAME);
        $email->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\StringLength(3));
        $inputFilter->add($email);

        $password = new Input(self::PASSWORD);
        $password->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\StringLength(6));
        $inputFilter->add($password);

        return $inputFilter;
    }
}