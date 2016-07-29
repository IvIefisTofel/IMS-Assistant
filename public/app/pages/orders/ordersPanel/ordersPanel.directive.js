/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.orders')
      .directive('ordersPanel', ordersPanel);

  /** @ngInject */
  function ordersPanel() {
    return {
      restrict: 'E',
      controller: 'OrdersPanelCtrl',
      templateUrl: 'app/pages/orders/ordersPanel/ordersPanel.html'
    };
  }
})();