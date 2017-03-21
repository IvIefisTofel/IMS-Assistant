(function(){
  'use strict';
  angular.module('BlurAdmin.pages.orders')
      .directive('ordersList', ordersList);

  /** @ngInject */
  function ordersList(){
    return {
      restrict:    'E',
      controller:  'OrdersListCtrl',
      templateUrl: 'app/pages/orders/ordersList/ordersList.html'
    };
  }
})();