(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .directive('baPanelSelf', baPanelSelf);

  /** @ngInject */
  function baPanelSelf(baPanel){
    return angular.extend({}, baPanel, {
      link: function(scope, el, attrs){
        el.addClass('panel panel-white');
        if (attrs.baPanelClass){
          el.addClass(attrs.baPanelClass);
        }
      }
    });
  }
})();