<?php

namespace AuthDoctrine\Authentication;

use Zend\Authentication\Adapter\Exception;
use DoctrineModule\Options\Authentication as DoctrineOptions;

class Options extends DoctrineOptions
{
    /**
     * Property to use for the rules
     * @var string
     */
    protected $rulesProperty;

    /**
     * @param string $rulesProperty
     * @throws Exception\InvalidArgumentException
     * @return Options
     */
    public function setRulesProperty($rulesProperty)
    {
        $this->rulesProperty = $rulesProperty;
        if (!is_string($rulesProperty) || $rulesProperty === '') {
            throw new Exception\InvalidArgumentException(
                sprintf('Provided $rulesProperty is invalid, %s given', gettype($rulesProperty))
            );
        }

        $this->rulesProperty = $rulesProperty;

        return $this;
    }

    /**
     * @return string
     */
    public function getRulesProperty()
    {
        return $this->rulesProperty;
    }
}
