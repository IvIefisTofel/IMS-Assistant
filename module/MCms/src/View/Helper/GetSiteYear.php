<?php
namespace MCms\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetSiteYear extends AbstractHelper
{
    /**
     * @return String
     */
    public function __invoke()
    {
        $siteYear = $this->getView()->getHelperPluginManager()->getServiceLocator()->get('config')['siteYear'];
        return($siteYear . (($siteYear == date('Y')) ? "" : "-" . date('Y')));
    }
}