<?php

namespace MCms\Entity;

use Zend\Server\Reflection\ReflectionClass;

class MCmsEntity
{
    public function toArray()
    {
        $methods = get_class_methods($this);
        $result = [];
        foreach ($methods as $key => $val) {
            if (substr($val, 0, 3) == 'get' && strpos($val, 'Format') === false) {
                $result[lcfirst(substr($val, 3))] = $this->$val();
            }
        }
        return $result;
    }
}

