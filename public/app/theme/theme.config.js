(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .config(config);

  /** @ngInject */
  function config(baConfigProvider, $locationProvider, $httpProvider, uiSelectConfig, IdleProvider){
    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('');

    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    uiSelectConfig.theme = 'bootstrap';

    IdleProvider.idle(60 * 15); // 15 minutes
    IdleProvider.timeout(0);

    //baConfigProvider.changeTheme({blur: true});
    //
    //baConfigProvider.changeColors({
    //  default: 'rgba(#000000, 0.2)',
    //  defaultText: '#ffffff',
    //  dashboard: {
    //    white: '#ffffff',
    //  },
    //});
  }
})();