<?php

class LitHelperPlugin
{
    private $cmpFuncName;

    private function asc($first, $second) {
        $funcName = $this->cmpFuncName;
        if ($first->$funcName() == $second->$funcName()) {
            return 0;
        }
        return ($first->$funcName() < $second->$funcName()) ? -1 : 1;
    }

    private function desc($first, $second) {
        $funcName = $this->cmpFuncName;
        if ($first->$funcName() == $second->$funcName()) {
            return 0;
        }
        return ($first->$funcName() > $second->$funcName()) ? -1 : 1;
    }

    /**
     * @param array $arrEntity
     * @param string $funcName
     * @param string $sort
     */
    public function sortEntities(&$arrEntity, $funcName, $sort = "asc")
    {
        $sort = strtolower($sort);
        if ($sort != "asc" && $sort != "desc")
            $sort = "asc";

        $this->cmpFuncName = $funcName;
        uasort($arrEntity, [$this, $sort]);
    }
}