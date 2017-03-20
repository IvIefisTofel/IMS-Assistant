<?php

namespace MCms\Entity;

class MCmsEntity
{
    public function toArray()
    {
        $vars = get_class_vars(get_class($this));
        $result = [];
        foreach ($vars as $var => $val) {
            $method = 'get' . ucfirst($var);
            if (method_exists($this, $method)) {
                $result[$var] = $this->$method();
            }
        }
        return $result;
    }
}

