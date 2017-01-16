<?php

namespace MCms\Entity;

class MCmsEntity
{
    public function toArray()
    {
        $methods = get_class_methods($this);
        $result = [];
        foreach ($methods as $method) {
            if (substr($method, 0, 3) == 'get' && strpos($method, 'Format') === false) {
                $result[lcfirst(substr($method, 3))] = $this->$method();
            }
        }
        return $result;
    }
}

