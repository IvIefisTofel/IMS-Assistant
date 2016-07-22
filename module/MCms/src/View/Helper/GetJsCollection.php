<?php
namespace MCms\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetJsCollection extends AbstractHelper
{
    /**
     * @return String
     */
    public function __invoke()
    {
        return $this->getView()->getHelperPluginManager()->getServiceLocator()->get('assets')->getJsCollection();
    }
}