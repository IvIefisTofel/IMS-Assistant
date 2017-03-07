<?php
namespace MCms\Logger;

use Zend\Mvc\MvcEvent;

class Formatter
{
    /**
     * @param string $path
     * @return string
     */
    private static function getPath($path)
    {
        return str_replace('\\', "/", str_replace(realpath('./'), ".", $path));
    }
    /**
     * @param string $trace
     * @param \Zend\View\Helper\EscapeHtml $escapeHtml
     * @return string
     */
    private static function getTrace($trace, $escapeHtml)
    {
        $result = $escapeHtml($trace);
        while (($pos1 = strpos($result, realpath('./')))) {
            $pos2 = strpos($result, '):', $pos1) + 2;
            $tmp = self::getPath(substr($result, $pos1, $pos2 - $pos1));
            $result = substr($result, 0, $pos1) . "<font color=\"#cc0000\">" . $tmp . "</font>" . substr($result, $pos2);
        }

        return $result;
    }

    /**
     * @param MvcEvent $e
     * @return string
     */
    public static function defaultException(MvcEvent $e) {
        $viewHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager');
        $translate = $viewHelper->get('translate');
        $escapeHtml = $viewHelper->get('escapeHtml');

        $ex = $e->getParam('exception');
        $requestUri = $e->getRequest()->getRequestUri();
        $msg  = "<h1>Ошибка на странице <a href=\"" . $requestUri . "\">" . $requestUri . "</a></h1>\n";
        $msg .= "<p>" .  $translate($ex->getMessage()) . "</p>\n";

        $message = '';
        if ($ex && ($ex instanceof \Exception || $ex instanceof \Error)) {
            $message = "<hr/>";
            $message .= "<h2>" .  $translate('Additional information') . ":</h2>\n";
            $message .= "<h3>Class: \"" .  get_class($ex) . "\"</h3>\n";
            $message .= "<dl>\n";
            $message .= "<dt>" .  $translate('File') . ":</dt>\n";
            $message .= "<dd>\n";
            $message .= "<pre class=\"prettyprint linenums\">" .  self::getPath($ex->getFile()) . " :" .  $ex->getLine() . "</pre>\n";
            $message .= "</dd>\n";
            $message .= "<dt>" .  $translate('Message') . ":</dt>\n";
            $message .= "<dd>\n";
            $message .= "<pre class=\"prettyprint linenums\">" .  $escapeHtml($ex->getMessage()) . "</pre>\n";
            $message .= "</dd>\n";
            $message .= "<dt>" .  $translate('Stack trace') . ":</dt>\n";
            $message .= "<dd>\n";
            $message .= "<pre class=\"prettyprint linenums\">" .  self::getTrace($ex->getTraceAsString(), $escapeHtml) . "</pre>\n";
            $message .= "</dd>\n";
            $message .= "</dl>\n";

            $ex = $ex->getPrevious();
            if ($ex) {
                $message .= "<hr/>\n";
                $message .= "<h2>" .  $translate('Previous exceptions') . ":</h2>\n";
                $message .= "<ul class=\"unstyled\">\n";
                do {
                    $message .= "<li>\n";
                    $message .= "<h3>" .  get_class($ex) . "</h3>\n";
                    $message .= "<dl>\n";
                    $message .= "<dt>" .  $translate('File') . ":</dt>\n";
                    $message .= "<dd>\n";
                    $message .= "<pre class=\"prettyprint linenums\">" .  self::getPath($ex->getFile()) . ":" .  $ex->getLine() . "</pre>\n";
                    $message .= "</dd>\n";
                    $message .= "<dt>" .  $translate('Message') . ":</dt>\n";
                    $message .= "<dd>\n";
                    $message .= "<pre class=\"prettyprint linenums\">" .  $escapeHtml($ex->getMessage()) . "</pre>\n";
                    $message .= "</dd>\n";
                    $message .= "<dt>" .  $translate('Stack trace') . ":</dt>\n";
                    $message .= "<dd>\n";
                    $message .= "<pre class=\"prettyprint linenums\">" .  self::getTrace($ex->getTraceAsString(), $escapeHtml) . "</pre>\n";
                    $message .= "</dd>\n";
                    $message .= "</dl>\n";
                    $message .= "</li>\n";
                } while ($ex = $ex->getPrevious());
                $message .= "</ul>\n";
            }

        } else {
            $message .= "<h3>" .  $translate('No Exception available') . "</h3>\n";
        }

        return $msg . $message;
    }

    /**
     * @param MvcEvent $e
     * @return string | null
     */
    public static function routeNoMatchException(MvcEvent $e) {
        $viewHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager');
        $translate = $viewHelper->get('translate');

        $res = $e->getResult()->getVariables();

        $requestUri = $e->getRequest()->getRequestUri();
        if ($requestUri == '/favicon.ico') {
            return null;
        }
        $msg  = "<h1>Ошибка на странице <a href=\"" . $requestUri . "\">" . $requestUri . "</a></h1>\n";
        $msg .= "<p>" .  $translate($res->message) . "</p>\n";

        $message = "<hr/>";
        $message .= "<h2>" .  $translate('Additional information') . ":</h2>\n";
        $message .= "<h3>" .  $translate($res->reason) . "</h3>\n";

        if (isset($_SERVER['HTTP_REFERER'])) {
            $serverUrl = $viewHelper->get('serverUrl');

            $referer = '/' . str_replace($serverUrl('/',true), '', $_SERVER['HTTP_REFERER']);
            $message .= "<dt>Источник ссылки:</dt>\n<dd>\n";
            $message .= "<pre class=\"prettyprint linenums\"><a href=\"" . $referer . "\">" . $referer . "</a></pre>\n";
            $message .= "</dd>\n";
            $message .= "</dl>\n";
        }

//        return $requestUri . ' (' . $res->reason . ', ' . $res->message . ')';
        return $msg . $message;
    }

    /**
     * @param MvcEvent $e
     * @return string
     */
    public static function controllerException(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        $viewHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager');
        $translate = $viewHelper->get('translate');

        $res = $e->getResult()->getVariables();

        $requestUri = $e->getRequest()->getRequestUri();
        $msg  = "<h1>Ошибка на странице <a href=\"" . $requestUri . "\">" . $requestUri . "</a></h1>\n";
        $msg .= "<p>" .  $translate($res['message']) . "</p>\n";

        $message = "<hr/>";
        $message .= "<h2>" .  $translate('Additional information') . ":</h2>\n";
        $message .= "<h3>" .  $translate($res['reason']) . "</h3>\n";

        $message .= "<dt>Контроллер:</dt>\n<dd>\n";
        $message .= "<pre class=\"prettyprint linenums\">Controller: \"" . $controller . "\"\nAction: \"" . $action . "\"</pre>\n";
        $message .= "</dd>\n";
        $message .= "</dl>\n";

        if (isset($_SERVER['HTTP_REFERER'])) {
            $serverUrl = $viewHelper->get('serverUrl');

            $referer = '/' . str_replace($serverUrl('/',true), '', $_SERVER['HTTP_REFERER']);
            $message .= "<dt>Источник ссылки:</dt>\n<dd>\n";
            $message .= "<pre class=\"prettyprint linenums\"><a href=\"" . $referer . "\">" . $referer . "</a></pre>\n";
            $message .= "</dd>\n";
            $message .= "</dl>\n";
        }

//        return $requestUri . ' (' . $res->reason . ', ' . $res->message . ')';
        return $msg . $message;
    }
}