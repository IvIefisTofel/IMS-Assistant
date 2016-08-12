/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.theme')
      .directive('switch', switchDirective);

  /** @ngInject */
  function switchDirective($timeout) {
    return {
      restrict: 'EA',
      replace: true,
      scope: {
        ngModel: '='
      },
      template: function(el, attrs) {
        return '<div class="switch-container ' + (attrs.color || '') + '"><input type="checkbox" data-label-text="' + $(el).text() + '" ng-model="ngModel"></div>';
      },
      link: function (scope, elem, attr) {
        $timeout(function(){
          var input = $(elem).find('input');
          input.bootstrapSwitch({
            handleWidth: "60",
            onText: "Вкл.",
            offText: "Выкл.",
            size: 'small',
            onColor: attr.color
          });
          input.on('switchChange.bootstrapSwitch', function(event, state) {
            scope.ngModel = state;
            scope.$apply();
          });

        });
      }
    };
  }
})();
