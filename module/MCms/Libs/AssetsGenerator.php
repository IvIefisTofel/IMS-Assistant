<?php

class AssetsGenerator
{
    const cssCorePath = './public/css/core';
    const jsCorePath = './public/js/core';

    const cssMinFilter  = [['filter' => 'CssMinFilter']];
    const jsMinFilter   = [['filter' => 'JSMin']];

    private static $staticParams = [
        'paths' => [
            './public'
        ],
        'aliases' => [
            'bower/' => './bower_components/',
            'fonts/' => './bower_components/font-awesome/fonts',
            'fonts/ionicons.eot' => './bower_components/Ionicons/fonts/ionicons.eot',
            'fonts/ionicons.svg' => './bower_components/Ionicons/fonts/ionicons.svg',
            'fonts/ionicons.ttf' => './bower_components/Ionicons/fonts/ionicons.ttf',
            'fonts/ionicons.woff' => './bower_components/Ionicons/fonts/ionicons.woff',
        ],
    ];

    private static $staticProduction = [
        'css/core.css' => ['cache' => 'FilesystemCache','options' => ['dir' => self::cssCorePath]],
        'js/core.js' => ['cache' => 'FilesystemCache', 'options' => ['dir' => self::jsCorePath]],
    ];

    /**
     * @var bool
     */
    private $development = false;

    /**
     * @var array
     */
    private $cssCollection = [];

    /**
     * @var array
     */
    private $jsCollection = [];

    /**
     * @var array
     */
    private $cssMin = [];

    /**
     * @var string
     */
    private $coreCss = null;

    /**
     * @var array
     */
    private $jsMin = [];

    /**
     * @var string
     */
    private $coreJs = null;

    /**
     * @var bool
     */
    private $alwaysMinCss = false;

    /**
     * @var bool
     */
    private $alwaysMinJs = false;

    public function __construct()
    {
        $this->development = (getenv('APP_ENV') == 'development') ? true : false;
        $assets = include './config/assets.php';

        $this->alwaysMinCss = $assets['alwaysMinCss'];
        $this->alwaysMinJs = $assets['alwaysMinJs'];

        $this->cssCollection = $assets['assetsCss'];
        $this->jsCollection = $assets['assetsJs'];

        foreach ($this->cssCollection as $css) {
            if (strpos($css, "min.css") === false) {
                $this->cssMin[$css] = self::cssMinFilter;
            }
        }

        foreach ($this->jsCollection as $js) {
            if (strpos($js, "min.js") === false) {
                $this->jsMin[$js] = self::jsMinFilter;
            }
        }

        $this->coreCss = str_replace('./public/', '', self::cssCorePath) . '.css';
        $this->coreJs = str_replace('./public/', '', self::jsCorePath) . '.js';
    }

    public function getAssets()
    {
        $result = [];
        if ($this->development) {
            $result['asset_manager']['resolver_configs'] = self::$staticParams;
            $result['asset_manager']['filters'] = [];
            if ($this->alwaysMinCss) {
                $result['asset_manager']['resolver_configs']['collections'][$this->coreCss] = $this->cssCollection;
                $result['asset_manager']['filters'] = array_merge(
                    $result['asset_manager']['filters'],
                    $this->cssMin
                );
                $result['asset_manager']['caching'] = self::$staticProduction;
            }
            if ($this->alwaysMinJs) {
                $result['asset_manager']['resolver_configs']['collections'][$this->coreJs] = array_reverse($this->jsCollection);
                $result['asset_manager']['filters'] = array_merge(
                    $result['asset_manager']['filters'],
                    $this->jsMin
                );
                $result['asset_manager']['caching'] = self::$staticProduction;
            }
        } else {
            $result['asset_manager']['resolver_configs'] = array_merge(self::$staticParams,
                ["collections" => [
                    $this->coreCss => $this->cssCollection,
                    $this->coreJs => array_reverse($this->jsCollection),
                ]]
            );
            $result['asset_manager']['filters'] = array_merge($this->jsMin, $this->cssMin);
            $result['asset_manager']['caching'] = self::$staticProduction;
        }
        return $result;
    }

    public function getCssCollection()
    {
        if ($this->alwaysMinCss || !$this->development) {
            return [$this->coreCss];
        }
        else {
            return $this->cssCollection;
        }
    }

    public function getJsCollection()
    {
        if ($this->alwaysMinJs || !$this->development) {
            return [$this->coreJs];
        }
        else {
            return $this->jsCollection;
        }
    }
}