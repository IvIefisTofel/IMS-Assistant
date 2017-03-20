<?php

namespace AuthDoctrine\Authentication;

use DoctrineModule\Authentication\Storage\ObjectRepository;

class Storage extends ObjectRepository
{
    /**
     * @param  array | Options $options
     * @return ObjectRepository
     */
    public function setOptions($options)
    {
        if (!$options instanceof Options) {
            $options = new Options($options);
        }

        $this->options = $options;
        return $this;
    }

    /**
     * This function assumes that the storage only contains identifier values (which is the case if
     * the ObjectRepository authentication adapter is used).
     *
     * @return null|object
     */
    public function read()
    {
        if (($identity = $this->options->getStorage()->read())) {
            if (isset($identity['__rules'])) {
                $rules = $identity['__rules'];
                unset($identity['__rules']);
                $identity = $this->options->getObjectRepository()->find($identity);
                $setRules = 'set' . ucfirst($this->options->getRulesProperty());
                if (method_exists($identity, $setRules)) {
                    $identity->$setRules($rules);
                }
                return $identity;
            } else {
                return $this->options->getObjectRepository()->find($identity);
            }
        }

        return null;
    }
    
    /**
     * @param  object $identity
     * @return void
     */
    public function write($identity)
    {
        $metadataInfo     = $this->options->getClassMetadata();
        $identifierValues = $metadataInfo->getIdentifierValues($identity);

        $getRules = 'get' . ucfirst($this->options->getRulesProperty());
        if (method_exists($identity, $getRules)) {
            $identifierValues['__rules'] = $identity->$getRules();
        }

        $this->options->getStorage()->write($identifierValues);
    }
}
