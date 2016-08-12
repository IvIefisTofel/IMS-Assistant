/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.clients')
      .controller('ClientsListCtrl', ClientsListCtrl);

  /** @ngInject */
  function ClientsListCtrl($scope, $uibModal, $http) {
    var modalInstance = null;

    $scope.list = [];
    $scope.loading = true;
    $scope.current = null;
    $scope.client = {
      info: "Клиент не выбран.",
      files: null
    };

    angular.element($('content-actions')).scope().refreshData('clients-list', [
      {
        text: "Добавить",
        class: "btn-success",
        iconClass: "fa fa-user-plus",
        action: 'modal',
        params: {
          title: 'Добавить клиента',
          page: 'app/pages/clients/modals/content/add.html',
          size: 'lg'
        }
      },
      {
        text: "Удалить",
        class: "btn-danger",
        iconClass: "fa fa-user-times",
        action: 'modal',
        params: {
          title: 'Удалить клиента',
          page: 'app/pages/clients/modals/content/remove.html',
          size: 'lg'
        }
      },
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ]);

    $scope.modalActions = {
      close: function () {
        modalInstance.dismiss();
        modalInstance = null;
      }
    };

    $scope.modal = function (opt) {
      $scope.currModal = {
        title: opt.title,
        page: opt.page
      };
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'app/pages/clients/modals/modalContainer.html',
        size: opt.size,
        scope: $scope
      });
    };

    $scope.showInfo = function(key) {
      var item = $scope.list.filter(function(obj) {
        return obj.id == key;
      });
      if (item.length > 0) {
        item = item[0];

        $scope.current = item.id;
        $scope.client.files = item.additions;

        if (item.description != null) {
          $scope.client.info = item.description;
        } else {
          $scope.client.info = "Нет информации.";
        }
      }
    };

    $scope.refreshData = function () {
      // if ($scope.current === null) {
        $scope.loading = true;
      // }

      $http.post('/api/clients', null).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.list = data.data;
          $scope.loading = false;
          if ($scope.list.length > 0) {
            if ($scope.current === null) {
              $scope.current = $scope.list[0].id;
            }
            $scope.showInfo($scope.current);
          }
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();