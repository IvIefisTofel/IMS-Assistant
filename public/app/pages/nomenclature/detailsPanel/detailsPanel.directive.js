/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .directive('detailsPanel', detailsPanel);

  /** @ngInject */
  function detailsPanel() {
    return {
      restrict: 'E',
      controller: 'DetailsPanelCtrl',
      templateUrl: 'app/pages/nomenclature/detailsPanel/detailsPanel.html'
    };
  }
})();