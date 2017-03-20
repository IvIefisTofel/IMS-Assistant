<?php

namespace AuthDoctrine\Authentication;

use DoctrineModule\Authentication\Adapter\ObjectRepository;

class Adapter extends ObjectRepository
{
    /**
     * @param  array|Options $options
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
}
