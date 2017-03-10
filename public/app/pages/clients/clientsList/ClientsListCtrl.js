(function () {
  'use strict';
  angular.module('BlurAdmin.pages.clients')
      .controller('ClientsListCtrl', ClientsListCtrl);

  /** @ngInject */
  function ClientsListCtrl($scope, $rootScope, $uibModal, $http, $filter) {
    $scope.clients = [];
    $scope.loading = true;
    $scope.current = null;
    $scope.newClient = {
      name: '',
      description: ''
    };

    $scope.actions = [];
    $scope.actionVariants =  [
      {
        text: "Добавить",
        class: "btn-success",
        iconClass: "fa fa-user-plus",
        action: 'addClient'
      },
      {
        text: "Редактировать",
        class: "btn-primary",
        iconClass: "fa fa-pencil",
        action: 'updateClient'
      },
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ];

    $scope.$watch('current', function(newVal, oldVal){
      if (newVal != oldVal) {
        if (newVal == null) {
          $scope.actions = [$scope.actionVariants[0], $scope.actionVariants[2]];
        } else {
          $scope.actions = $scope.actionVariants;
        }
      }
    });

    var modalInstance = null;
    $scope.modal = {
      add: null,
      client: null
    };
    function modalClose(){
      modalInstance = null;
      $scope.modal.add = null;
      $scope.modal.client = $filter('filter')($scope.clients, $scope.search)[$scope.current];
    }
    function modalRendered(){
      $('#client-add').focus();
    }
    $scope.addClient = function () {
      $scope.modal.add = true;
      $scope.modal.client = $scope.newClient;
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'app/pages/clients/modals/updateClient.html',
        size: 'lg',
        backdrop: 'static',
        scope: $scope
      });
      modalInstance.rendered.then(function(){modalRendered()});
      modalInstance.closed.then(function(){modalClose()});
    };
    $scope.updateClient = function(){
      $scope.modal.add = false;
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'app/pages/clients/modals/updateClient.html',
        size: 'lg',
        backdrop: 'static',
        scope: $scope
      });
      modalInstance.rendered.then(function(){modalRendered()});
      modalInstance.closed.then(function(){modalClose()});
    };
    $scope.files = {
      add: function(event){
        if ($scope.modal.client.newAddons == undefined) {
          $scope.modal.client.newAddons = [];
        }
        angular.forEach(event.files, function (file, key) {
          $scope.modal.client.newAddons.push(file);
        });
        $(event).val(null);
        $scope.$apply();
      },
      remove: function(id){
        delete $scope.modal.client.updAddons[id];
      },
      update: function(event){
        var id = $(event).data('id');
        if ($scope.modal.client.updAddons == undefined) {
          $scope.modal.client.updAddons = {};
        }
        if (isNull(event.files[0])) {
          delete $scope.modal.client.updAddons[id];
        } else {
          $scope.modal.client.updAddons[id] = event.files[0];
        }
        $scope.$apply();
      }
    };

    $scope.showInfo = function(key) {
      var filtred = $filter('filter')($scope.clients, $scope.search);
      if ($scope.modal.client == $scope.newClient) {
        if (key > $scope.current) {
          $scope.current = 0;
        } else {
          $scope.current = filtred.length - 1;
        }
        $scope.modal.add = false;
        $scope.modal.client = $filter('filter')($scope.clients, $scope.search)[$scope.current];
      } else {
        if (key < 0 || key >= filtred.length) {
          $scope.modal.client = $scope.newClient;
          $scope.modal.add = true;
        } else {
          $scope.current = key;
          $scope.modal.client = $filter('filter')($scope.clients, $scope.search)[$scope.current];
        }
      }
    };
    $scope.keyUp = function(event){
      if (!$rootScope.focusedTextEl()) {
        if (event.keyCode == 37) { // left
          $scope.showInfo($scope.current - 1);
        } else if(event.keyCode == 39) { // right
          $scope.showInfo($scope.current + 1);
        }
      }
    };

    $scope.refreshData = function () {
      $scope.loading = true;

      $http.post('/api/clients', null).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.clients = data;
          angular.forEach($scope.clients, function(client, key){
            client.editName = client.name;
            client.editDescription = client.description;
          });
          $scope.loading = false;
          if ($scope.clients.length > 0) {
            if (isNull($scope.current)) {
              $scope.current = 0;
            }
            $scope.showInfo($scope.current);
          }
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
    $scope.test = function(k){
      $scope.current = k;
    }
  }
})();