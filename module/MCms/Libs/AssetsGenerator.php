<?php

class AssetsGenerator
{
    const cssCoreName = 'cssCore.css';
    const cssLibsName = 'cssLibs.css';
    const jsCoreName  = 'jsCore.js';
    const jsLibsName  = 'jsLibs.js';

    const cssCorePath = './public/core/css';
    const jsCorePath  = './public/core/js';

    const cssMinFilter  = [['filter' => 'CssMinFilter']];
    const jsMinFilter   = [['filter' => 'JSMin']];

    private static $staticParams = [
        'paths' => [
            './public'
        ],
        'aliases' => [
            'bower/' => './bower_components/',
            'fonts/' => './bower_components/font-awesome/fonts',
            // Bootsrap
            'fonts/glyphicons-halflings-regular.eot' => './bower_components/bootstrap/fonts/glyphicons-halflings-regular.eot',
            'fonts/glyphicons-halflings-regular.svg' => './bower_components/bootstrap/fonts/glyphicons-halflings-regular.svg',
            'fonts/glyphicons-halflings-regular.ttf' => './bower_components/bootstrap/fonts/glyphicons-halflings-regular.ttf',
            'fonts/glyphicons-halflings-regular.woff' => './bower_components/bootstrap/fonts/glyphicons-halflings-regular.woff',
            'fonts/glyphicons-halflings-regular.woff2' => './bower_components/bootstrap/fonts/glyphicons-halflings-regular.woff2',
            // IonIcons
            'fonts/ionicons.eot' => './bower_components/Ionicons/fonts/ionicons.eot',
            'fonts/ionicons.svg' => './bower_components/Ionicons/fonts/ionicons.svg',
            'fonts/ionicons.ttf' => './bower_components/Ionicons/fonts/ionicons.ttf',
            'fonts/ionicons.woff' => './bower_components/Ionicons/fonts/ionicons.woff',
        ],
        'map' => [
            'pdfmake.min.js.map' => './bower_components/amcharts/dist/amcharts/plugins/export/libs/pdfmake/pdfmake.min.js.map',
            'responsive.min.js.map' => './bower_components/amcharts/dist/amcharts/plugins/responsive/responsive.min.js.map',
            'angular.min.js.map' => './bower_components/angular/angular.min.js.map',
            'angular-animate.min.js.map' => './bower_components/angular-animate/angular-animate.min.js.map',
            'angular-chart.min.js.map' => './bower_components/angular-chart.js/dist/angular-chart.min.js.map',
            'angular-route.min.js.map' => './bower_components/angular-route/angular-route.min.js.map',
            'smart-table.min.js.map' => './bower_components/angular-smart-table/dist/smart-table.min.js.map',
            'angular-touch.min.js.map' => './bower_components/angular-touch/angular-touch.min.js.map',
            'ng-tree-dnd.min.js.map' => './bower_components/angular-tree-dnd/dist/ng-tree-dnd.min.js.map',
            'select.min.css.map' => './bower_components/angular-ui-select/dist/select.min.css.map',
            'bootstrap-select.js.map' => './bower_components/bootstrap-select/dist/js/bootstrap-select.js.map',
            'bootstrap-tagsinput.min.js.map' => './bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js.map',
            'bootstrap-tagsinput-angular.min.js.map' => './bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput-angular.min.js.map',
            'jquery.min.map' => './bower_components/jquery/dist/jquery.min.map',
            'sizzle.min.map' => './bower_components/jquery/src/sizzle/dist/sizzle.min.map',
            'angular-idle.map' => './bower_components/ng-idle/angular-idle.map',
            'chartist.min.js.map' => './bower_components/chartist/dist/chartist.min.js.map',
            'toastr.js.map' => './bower_components/toastr/toastr.js.map',
        ],
    ];

    private static $staticProduction = [
        self::cssCoreName => ['cache' => 'FilesystemCache','options' => ['dir' => self::cssCorePath]],
        self::jsCoreName => ['cache' => 'FilesystemCache', 'options' => ['dir' => self::jsCorePath]],
    ];

    private static $staticLibs = [
        self::cssLibsName => ['cache' => 'FilesystemCache','options' => ['dir' => self::cssCorePath]],
        self::jsLibsName => ['cache' => 'FilesystemCache', 'options' => ['dir' => self::jsCorePath]],
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
    private $cssLibCollection = [];

    /**
     * @var array
     */
    private $jsCollection = [];

    /**
     * @var array
     */
    private $jsLibCollection = [];

    /**
     * @var array
     */
    private $cssMin = [];

    /**
     * @var array
     */
    private $jsMin = [];

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

        $this->alwaysMinCss = isset($assets['alwaysMinCss']) ? $assets['alwaysMinCss'] : false;
        $this->alwaysMinJs = isset($assets['alwaysMinJs']) ? $assets['alwaysMinJs'] : false;

        if (isset($assets['assetsCss']['libs'])) {
            foreach ($assets['assetsCss']['libs'] as $css) {
                $this->cssLibCollection[] = $css;
                if (strpos($css, "min.css") === false) {
                    $this->cssMin[$css] = self::cssMinFilter;
                }
            }
        }
        $this->cssCollection = isset($assets['assetsCss']['app']) ? $assets['assetsCss']['app'] : [];

        if (isset($assets['assetsJs']['libs'])) {
            foreach ($assets['assetsJs']['libs'] as $js) {
                $this->jsLibCollection[] = $js;
                if (strpos($js, "min.js") === false) {
                    $this->jsMin[$js] = self::jsMinFilter;
                }
            }
        }
        $this->jsCollection = isset($assets['assetsJs']['app']) ? $assets['assetsJs']['app'] : $assets['assetsJs']['app'];
    }

    public function getAssets()
    {
        $result = [];
        $result['asset_manager']['resolver_configs'] = self::$staticParams;
        $result['asset_manager']['resolver_configs']["collections"] = [
            self::cssLibsName => $this->cssLibCollection,
            self::jsLibsName => array_reverse($this->jsLibCollection),
        ];
        $result['asset_manager']['filters'] = array_merge($this->jsMin, $this->cssMin);
        $result['asset_manager']['caching'] = self::$staticLibs;

        if (!$this->development || $this->alwaysMinCss) {
            $cssAppMin = [];
            foreach ($this->cssCollection as $css) {
                if (strpos($css, "min.css") === false) {
                    $cssAppMin[$css] = self::cssMinFilter;
                }
            }
            $result['asset_manager']['resolver_configs']['collections'][self::cssCoreName] = array_merge(
                $this->cssLibCollection,
                $this->cssCollection
            );
            unset($result['asset_manager']['resolver_configs']["collections"][self::cssLibsName]);

            $result['asset_manager']['filters'] = array_merge(
                $result['asset_manager']['filters'],
                $cssAppMin
            );

            $result['asset_manager']['caching'][self::cssCoreName] = self::$staticProduction[self::cssCoreName];
            unset($result['asset_manager']['caching'][self::cssLibsName]);
        }
        if (!$this->development || $this->alwaysMinJs) {
            $jsAppMin = [];
            foreach ($this->jsCollection as $js) {
                if (strpos($js, "min.js") === false) {
                    $jsAppMin[$js] = self::jsMinFilter;
                }
            }
            $result['asset_manager']['resolver_configs']['collections'][self::jsCoreName] = array_merge(
                $result['asset_manager']['resolver_configs']["collections"][self::jsLibsName],
                array_reverse($this->jsCollection)
            );
            unset($result['asset_manager']['resolver_configs']["collections"][self::jsLibsName]);

            $result['asset_manager']['filters'] = array_merge(
                $result['asset_manager']['filters'],
                $jsAppMin
            );

            $result['asset_manager']['caching'][self::jsCoreName] = self::$staticProduction[self::jsCoreName];
            unset($result['asset_manager']['caching'][self::jsLibsName]);
        }
        return $result;
    }

    public function getCssCollection()
    {
        if ($this->alwaysMinCss || !$this->development) {
            return [self::cssCoreName];
        }
        else {
            return array_merge([self::cssLibsName], $this->cssCollection);
        }
    }

    public function getJsCollection()
    {
        if ($this->alwaysMinJs || !$this->development) {
            return [self::jsCoreName];
        }
        else {
            return array_merge($this->jsCollection, [self::jsLibsName]);
        }
    }
}