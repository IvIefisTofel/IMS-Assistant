(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .filter('plainText', plainText);

  /** @ngInject */
  function plainText(){
    return function(text){
      return text ? String(text).replace(/<[^>]+>/gm, '') : '';
    };
  }
})();