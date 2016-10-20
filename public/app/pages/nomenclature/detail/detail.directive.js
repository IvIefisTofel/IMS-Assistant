/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .directive('detail', detail);

  /** @ngInject */
  function detail() {
    return {
      restrict: 'E',
      controller: 'DetailCtrl',
      templateUrl: 'app/pages/nomenclature/detail/detail.html'
    };
  }
})();