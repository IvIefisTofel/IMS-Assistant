/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .directive('detailsList', detailsList);

  /** @ngInject */
  function detailsList() {
    return {
      restrict: 'E',
      controller: 'DetailsListCtrl',
      templateUrl: 'app/pages/nomenclature/detailsList/detailsList.html'
    };
  }
})();