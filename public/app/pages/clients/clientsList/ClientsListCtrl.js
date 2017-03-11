(function () {
  'use strict';
  angular.module('BlurAdmin.pages.clients')
      .controller('ClientsListCtrl', ClientsListCtrl);

  /** @ngInject */
  function ClientsListCtrl($scope, $rootScope, $uibModal, $http, $filter) {
    $scope.clients = [];
    $scope.showErrors = false;
    $scope.loading = true;
    $scope.current = null;
    $scope.search = '';
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

    var modalInstance = null;
    $scope.modal = {
      add: null,
      client: null,
      save: function(){
        if (isNull($scope.modal.client.editName)) {
          $scope.showErrors = true;
        } else {
          var send = new FormData(), i;
          send.append('clientName', $scope.modal.client.editName);
          if (!isNull($scope.modal.client.editDescription)) {
            send.append('clientDescription', $scope.modal.client.editDescription);
          }
          if (!isNull($scope.modal.client.updAddons)) {
            angular.forEach($scope.modal.client.updAddons, function(file, key){
              send.append('clientAddons[' + key + ']', file);
            });
          }
          if (!isNull($scope.modal.client.newAddons)) {
            for (i = 0; i < $scope.modal.client.newAddons.length; i++) {
              send.append('clientNewAddons[]', $scope.modal.client.newAddons[i]);
            }
          }

          var $url = ($scope.modal.add ? "/api/clients/add" : "/api/clients/update/" + $scope.modal.client.id);
          $http.post($url, send, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
          }).then(function successCallback(response) {
            if (response.data.error) {
              console.log(response.data.error);
            } else {
              var data = response.data[0];
              data.editName = data.name;
              data.editDescription = data.description;
              if (!$scope.modal.add) {
                $scope.filtred[$scope.current] = $scope.modal.client = data;
              } else {
                var id = data.id;
                $scope.clients.push(data);
                $scope.filtred = $filter('orderBy')($filter('filter')($scope.clients, { name: $scope.search }), 'name');
                for (var i = 0; i < $scope.filtred.length; i++) {
                  if (id == $scope.filtred[i].id) {
                    $scope.current = i;
                    break;
                  }
                }
                $scope.newClient = {
                  name: '',
                  description: ''
                };
              }
              modalInstance.dismiss();
            }
          }, function errorCallback(response) {
            console.log(response.statusText);
          });
        }
      }
    };
    function modalClose(){
      modalInstance = null;
      $scope.modal.add = null;
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
      if ($scope.modal.client == $scope.newClient) {
        if (key > $scope.current) {
          $scope.current = 0;
        } else {
          $scope.current = $scope.filtred.length - 1;
        }
        $scope.modal.add = false;
        $scope.modal.client = $scope.filtred[$scope.current];
      } else {
        if (key < 0 || key >= $scope.filtred.length) {
          $scope.modal.client = $scope.newClient;
          $scope.modal.add = true;
        } else {
          $scope.current = key;
          $scope.modal.client = $scope.filtred[$scope.current];
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

    $scope.filtred = [];
    $scope.$watch("search", function(){
      $scope.filtred = $filter('orderBy')($filter('filter')($scope.clients, { name: $scope.search }), 'name');
      if (isNull($scope.filtred) || $scope.current == null) {
        $scope.actions = [$scope.actionVariants[0], $scope.actionVariants[2]];
      } else {
        $scope.actions = $scope.actionVariants;
      }
    });
    $scope.$watch('current', function(newVal, oldVal){
      if (newVal != oldVal) {
        if (newVal == null) {
          $scope.actions = [$scope.actionVariants[0], $scope.actionVariants[2]];
        } else {
          $scope.actions = $scope.actionVariants;
        }
      }
    });

    $scope.refreshData = function () {
      $scope.loading = true;

      $http.post('/api/clients', null).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.clients = data;
          $scope.filtred = $filter('orderBy')($filter('filter')($scope.clients, { name: $scope.search }), 'name');
          angular.forEach($scope.clients, function(client, key){
            client.editName = client.name;
            client.editDescription = client.description;
          });
          if ($scope.filtred.length > 0) {
            if (isNull($scope.current) || $scope.current >= $scope.filtred.length) {
              $scope.current = 0;
            }
            $scope.showInfo($scope.current);
          } else {
            $scope.current = null;
          }

          $scope.loading = false;
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