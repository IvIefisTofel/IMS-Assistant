(function(){
  'use strict';
  angular.module('BlurAdmin.pages.orders')
      .controller('OrdersListCtrl', OrdersListCtrl);

  /** @ngInject */
  function OrdersListCtrl($scope, $rootScope, $stateParams, $http, $uibModal, $filter){
    $scope.search = {clientId: $stateParams.id};
    $scope.clientName = null;
    $scope.defList = [];
    $scope.list = [];
    $scope.loading = true;
    $scope.orderEdit = false;
    $scope.canEdit = false;

    $scope.filter = {1: true, 2: true, 3: false};
    $scope.propertyName = 'code';
    $scope.reverse = false;

    $scope.actions = [];
    $scope.actionVariants = [
      {
        text:                "Добавить",
        class:               "btn-success",
        iconClass:           "fa fa-plus",
        action:              'addOrder',
        permissionsRequired: SUPERVISOR_ROLE
      },
      {
        text:                "Обновить",
        class:               "btn-info",
        iconClass:           "fa fa-refresh",
        action:              'refreshData',
        permissionsRequired: USER_ROLE
      }
    ];

    function filterActions(){
      $scope.actions = $filter('filter')($scope.actionVariants, function(item){
        return item.permissionsRequired <= $rootScope.$getPermissions();
      });
      $scope.canEdit = SUPERVISOR_ROLE <= $rootScope.$getPermissions();
    }

    $scope.$watch(function(){ return $rootScope.$getPermissions(); }, function(newVal, oldVal){
      if (newVal != oldVal){
        filterActions();
      }
    });

    var statuses = {
      1: 'Заказ',
      2: 'В работе',
      3: 'Исполнено'
    };

    $scope.sortBy = function(propertyName){
      if (propertyName.indexOf('date') != -1){
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
      } else {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
      }
      $scope.propertyName = propertyName;
    };

    function noFilter(filterObj){
      return Object.keys(filterObj).every(function(key){
        return !filterObj[key];
      });
    }

    $scope.filterBy = function(item){
      return $scope.filter[item.statusCode] || noFilter($scope.filter);
    };

    $scope.selected = {
      client: null,
      change: {
        client: null,
        order:  null,
        detail: null
      },
      details: {
        new:     {},
        showNew: false,
        import:  []
      },
      new: {
        add: function(event){
          $scope.selected.details.showNew = false;
          for (var i = 0; i < event.files.length; i++){
            var file = event.files[i], indStart, indEnd,
                fName = (/[.]/.exec(file.name)) ? /[^.]+/.exec(file.name)[0] : undefined;
            var first = true;
            indStart = fName.indexOf('{');
            indEnd = fName.indexOf('}');
            if (indStart != -1 && indEnd != -1){
              fName = fName.substr(0, indStart) + fName.substr(indEnd + 1);
              first = false;
            }
            indStart = fName.indexOf('(');
            indEnd = fName.indexOf(')');
            var code = '', name = '';
            if (indStart != -1 && indEnd != -1){
              code = (fName.substr(0, indStart) + fName.substr(indEnd + 1)).replace(/(^\s*)|(\s*)$/g, '');
              name = (fName.substr(indStart + 1, indEnd - indStart - 1)).replace(/(^\s*)|(\s*)$/g, '');
            } else {
              code = fName.replace(/(^\s*)|(\s*)$/g, '');
              name = '';
            }
            if ($scope.selected.details.new[code] === undefined){
              $scope.selected.details.new[code] = {files: []};
            }
            $scope.selected.details.new[code].name = name;
            $scope.selected.details.new[code].code = code;
            if (first){
              $scope.selected.details.new[code].files.unshift({data: file});
            } else {
              $scope.selected.details.new[code].files.push({data: file});
            }
          }
          angular.forEach($scope.selected.details.new, function(detail, code){
            angular.forEach(detail.files, function(file, index){
              var url, ext = (/[.]/.exec(file.data.name)) ? /[^.]+$/.exec(file.data.name)[0].toLowerCase() : undefined;
              if (!isNull(file.data) && !isNull(ext)){
                url = URL.createObjectURL(file.data);
                if (ext.substr(0, 3) == 'tif'){
                  var xhr = new XMLHttpRequest();
                  xhr.open('GET', url);
                  xhr.responseType = 'arraybuffer';
                  xhr.onload = function(e){
                    $scope.$apply(function(){
                      var buffer = xhr.response;
                      var tiff = new Tiff({buffer: buffer});
                      var canvas = tiff.toCanvas();
                      if (canvas){
                        var base64 = canvas.toDataURL('image/jpeg'),
                            ind = $scope.selected.details.new[code].files.indexOf(file);
                        if (ind != -1 && isNull($scope.selected.details.new[code].files[ind].url)){
                          $scope.selected.details.new[code].files[ind].url = base64;
                        }
                      }
                    });
                  };
                  xhr.send();
                } else {
                  $scope.$apply(function(){
                    var ind = $scope.selected.details.new[code].files.indexOf(file);
                    if (ind != -1 && isNull($scope.selected.details.new[code].files[ind].url)){
                      $scope.selected.details.new[code].files[ind].url = url;
                    }
                  });
                }
              }
            });
          });
          $scope.$apply(function(){
            if (Object.keys($scope.selected.details.new).length){
              $scope.selected.details.showNew = true;
            }
          });
          $('#fromFile').val(null);
        },
        remove: function(code, key){
          $scope.selected.details.new[code].files.splice(key, 1);
          if ($scope.selected.details.new[code].files.length == 0){
            delete $scope.selected.details.new[code];
            if (isNull($scope.selected.details.new)){
              $scope.selected.details.showNew = false;
              $scope.selected.details.new = {};
            }
          }
        }
      },
      import: {
        add: function(){
          if (!isNull($scope.selected.change.detail)){
            $scope.selected.change.detail.viewCode = $scope.selected.change.detail.code;
            $scope.selected.change.detail.viewName = $scope.selected.change.detail.name;
            $scope.selected.details.import.push($scope.selected.change.detail);

            var key = $scope.details.indexOf($scope.selected.change.detail);
            $scope.selected.change.detail = null;
            $scope.details.splice(key, 1);
          }
        },
        remove: function(key){
          delete $scope.selected.details.import[key].viewCode;
          delete $scope.selected.details.import[key].viewName;
          $scope.details.push($scope.selected.details.import[key]);
          $scope.selected.details.import.splice(key, 1);
        }
      },
      dropDetail: function(key){
        if (isNull($scope.listEdit.editable[key].drop)){
          $scope.listEdit.editable[key].drop = false;
        }
        $scope.listEdit.editable[key].drop = !$scope.listEdit.editable[key].drop;
      },
      save: function(){
        var send = new FormData();
        if (isNull($scope.orderEdit)){
          if (isNull($scope.listEdit.editable.code) || isNull($scope.selected.client)){
            $scope.showErrors = true;
          } else {
            send.append('order[code]', $scope.listEdit.editable.code);
            if ($scope.selected.client != null){
              send.append('order[clientId]', $scope.selected.client.id);
            }
            send.append('order[dateCreation]', $filter('date')($scope.listEdit.editable.dateCreation, "dd.MM.yyyy"));
            if (!isNull($scope.listEdit.editable.dateStart)){
              send.append('order[dateStart]', $filter('date')($scope.listEdit.editable.dateStart, "dd.MM.yyyy"));
            }
            if (!isNull($scope.listEdit.editable.dateEnd)){
              send.append('order[dateEnd]', $filter('date')($scope.listEdit.editable.dateEnd, "dd.MM.yyyy"));
            }
            if (!isNull($scope.listEdit.editable.dateDeadline)){
              send.append('order[dateDeadline]', $filter('date')($scope.listEdit.editable.dateDeadline, "dd.MM.yyyy"));
            }

            angular.forEach($scope.selected.details.import, function(detail, key){
              send.append('details[import][' + detail.id + '][code]', detail.viewCode);
              send.append('details[import][' + detail.id + '][name]', detail.viewName);
            });
            angular.forEach($scope.selected.details.new, function(detail, code){
              send.append('details[new][' + code + '][name]', detail.name);
              if (detail.files[0].url.substr(0, 22) == 'data:image/jpeg;base64'){
                send.append('details[new][' + code + '][base64][first]', detail.files[0].url);
              } else {
                send.append('files[' + code + '][first]', detail.files[0].data);
              }
              for (var i = 1; i < detail.files.length; i++){
                if (detail.files[i].url.substr(0, 22) == 'data:image/jpeg;base64'){
                  send.append('details[new][' + code + '][base64][' + i + ']', detail.files[i].url);
                } else {
                  send.append('files[' + code + '][' + i + ']', detail.files[i].data);
                }
              }
            });

            $http.post("/api/orders/add", send, {
              transformRequest: angular.identity,
              headers:          {'Content-Type': undefined}
            }).then(function successCallback(response){
              var data = response.data;
              if (data.error || data.status){ $rootScope.showMessage(data); }
              if (isNull(data.error) || !data.error){
                data = data.data;
                data.dateCreation = !isNull(data.dateCreation) ? Date.parse(data.dateCreation) : null;
                data.dateStart = !isNull(data.dateStart) ? Date.parse(datadata.dateStart) : null;
                data.dateEnd = !isNull(data.dateEnd) ? Date.parse(data.dateEnd) : null;
                data.dateDeadline = !isNull(data.dateDeadline) ? Date.parse(data.dateDeadline) : null;
                data.clientName = $scope.clients[data.clientId].name;
                $scope.list.push(data);
                modalInstance.dismiss();
                modalInstance = null;
                $scope.refreshAddData();
              }
            });
          }
        } else {
          var edited = false, i;
          for (i = 0; i < $scope.listEdit.editable.length; i++){
            if (!isNull($scope.listEdit.editable[i].drop) && $scope.listEdit.editable[i].drop){
              edited = true;
              send.append('dropDetails[' + $scope.listEdit.editable[i].id + ']', true);
            }
          }
          angular.forEach($scope.selected.details.import, function(detail, key){
            edited = true;
            send.append('details[import][' + detail.id + '][code]', detail.viewCode);
            send.append('details[import][' + detail.id + '][name]', detail.viewName);
          });
          angular.forEach($scope.selected.details.new, function(detail, code){
            edited = true;
            send.append('details[new][' + code + '][name]', detail.name);
            if (detail.files[0].url.substr(0, 22) == 'data:image/jpeg;base64'){
              send.append('details[new][' + code + '][base64][first]', detail.files[0].url);
            } else {
              send.append('files[' + code + '][first]', detail.files[0].data);
            }
            for (i = 1; i < detail.files.length; i++){
              if (detail.files[i].url.substr(0, 22) == 'data:image/jpeg;base64'){
                send.append('details[new][' + code + '][base64][' + i + ']', detail.files[i].url);
              } else {
                send.append('files[' + code + '][' + i + ']', detail.files[i].data);
              }
            }
          });

          if (edited){
            $http.post("/api/orders/update/" + $scope.orderEdit.id, send, {
              transformRequest: angular.identity,
              headers:          {'Content-Type': undefined}
            }).then(function successCallback(response){
              var data = response.data;
              if (data.error || data.status){ $rootScope.showMessage(data); }
              if (data.status){
                modalInstance.dismiss();
                modalInstance = null;
                $scope.refreshAddData();
              }
            });
          }
        }
      }
    };
    $scope.clients = {};
    $scope.details = [];
    $scope.calendar = {
      dateCreation: {options: {startingDay: 1}},
      dateStart:    {options: {startingDay: 1}},
      dateEnd:      {options: {startingDay: 1}},
      dateDeadline: {options: {startingDay: 1}},
      init: function(){
        $scope.calendar.dateStart.options.minDate = $scope.listEdit.editable.dateCreation;
        $scope.calendar.dateDeadline.options.minDate = $scope.listEdit.editable.dateCreation;
        if ($scope.listEdit.editable.dateStart != null){
          $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
        } else {
          $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateStart.options.minDate;
        }
      },
      checkDate: function(state, minDate){
        if (!isNull(minDate)){
          minDate = getCropDate(minDate);
        } else {
          minDate = null;
        }
        switch (state) {
          case 'dateCreation':
          case 1:
            if (minDate !== null){
              if ($scope.listEdit.editable.dateStart !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateStart)){
                $scope.listEdit.editable.dateStart = null;
              }
              if ($scope.listEdit.editable.dateDeadline !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateDeadline)){
                $scope.listEdit.editable.dateDeadline = null;
              }
            }
            $scope.calendar.dateStart.options.minDate = $scope.calendar.dateDeadline.options.minDate = minDate;
          case 'dateStart':
          case 2:
            if (minDate !== null && $scope.listEdit.editable.dateEnd !== null
                && minDate > getCropDate($scope.listEdit.editable.dateEnd)){
              $scope.listEdit.editable.dateEnd = null;
            }
            if (isNull($scope.listEdit.editable.dateStart)){
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateCreation;
            } else {
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
            }
        }
      },
      today: function(model){
        var now = getCropDate(Date.now());
        if (model === $scope.calendar.dateCreation){
          $scope.listEdit.editable.dateCreation = now;
          $scope.calendar.checkDate('dateCreation', now);
        } else if (model === $scope.calendar.dateStart){
          $scope.listEdit.editable.dateStart = now;
          $scope.calendar.checkDate('dateStart', now);
        } else if (model === $scope.calendar.dateEnd){
          $scope.listEdit.editable.dateEnd = now;
          $scope.calendar.checkDate('dateEnd', now);
        } else if (model === $scope.calendar.dateDeadline){
          $scope.listEdit.editable.dateDeadline = now;
        }
      },
      clear: function(model){
        if (model === $scope.calendar.dateStart){
          $scope.listEdit.editable.dateStart = null;
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateStart');
        } else if (model === $scope.calendar.dateEnd){
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateEnd');
        } else if (model === $scope.calendar.dateDeadline){
          $scope.listEdit.editable.dateDeadline = null;
        }
      }
    };
    $scope.listEdit = {editable: {}};
    var lastEditId = null;
    $scope.editItem = function(item){
      if ($scope.canEdit){
        if (isNull(lastEditId)){
          lastEditId = item.id;
        } else {
          $scope.listEdit[lastEditId] = false;
          lastEditId = item.id;
        }
        $scope.listEdit.editable = angular.copy(item);
        $scope.calendar.init();
        $scope.selected.client = $scope.clients[item.clientId];
        $scope.listEdit[item.id] = true;
      }
    };
    $scope.saveItem = function(item){
      if ($scope.canEdit){
        var send = new FormData();

        send.append('order[code]', $scope.listEdit.editable.code);
        send.append('order[clientId]', $scope.selected.client.id);
        send.append('order[dateCreation]', $filter('date')($scope.listEdit.editable.dateCreation, "dd.MM.yyyy"));
        if (!isNull($scope.listEdit.editable.dateStart)){
          send.append('order[dateStart]', $filter('date')($scope.listEdit.editable.dateStart, "dd.MM.yyyy"));
        }
        if (!isNull($scope.listEdit.editable.dateEnd)){
          send.append('order[dateEnd]', $filter('date')($scope.listEdit.editable.dateEnd, "dd.MM.yyyy"));
        }
        if (!isNull($scope.listEdit.editable.dateDeadline)){
          send.append('order[dateDeadline]', $filter('date')($scope.listEdit.editable.dateDeadline, "dd.MM.yyyy"));
        }
        var status = 3;
        if (!isNull(item.dateEnd)){
          status = 3;
        } else if (!isNull(item.dateStart)){
          status = 2;
        } else {
          status = 1;
        }
        send.append('order[statusCode]', item.statusCode);

        $http.post("/api/orders/update/" + item.id, send, {
          transformRequest: angular.identity,
          headers:          {'Content-Type': undefined}
        }).then(function successCallback(response){
          var data = response.data;
          if (data.error || data.status){ $rootScope.showMessage(data); }
          if (isNull(data.error) || !data.error){
            item.code = $scope.listEdit.editable.code;
            item.clientId = $scope.selected.client.id;
            item.clientName = $scope.selected.client.name;
            item.dateCreation = $scope.listEdit.editable.dateCreation;
            item.dateStart = $scope.listEdit.editable.dateStart;
            item.dateEnd = $scope.listEdit.editable.dateEnd;
            item.dateDeadline = $scope.listEdit.editable.dateDeadline;
            item.statusCode = status;
            item.status = statuses[status];
          }
          $scope.listEdit[item.id] = false;
        });
      }
    };
    $scope.abortItem = function(item){
      $scope.listEdit[item.id] = false;
    };

    var modalInstance = null;
    $scope.modal = {
      open: function(){
        modalInstance = $uibModal.open({
          animation:   true,
          templateUrl: 'app/pages/orders/modal/editOrder.html',
          size:        'exlg',
          backdrop:    'static',
          scope:       $scope
        });
        modalInstance.closed.then(function(){
          modalInstance = null;
          $scope.details = angular.copy($scope.detailsOriginal);
          $scope.selected.details.import = [];
          $scope.selected.details.new = {};
          $scope.selected.details.showNew = false;
          $scope.showErrors = false;
        });
      }
    };
    $scope.addOrder = function(){
      if ($scope.canEdit){
        if (!isNull(lastEditId)){
          $scope.listEdit[lastEditId] = false;
        }
        $scope.listEdit.editable = {dateCreation: getCropDate(Date.now())};
        $scope.calendar.init();
        $scope.selected.client = isNull($stateParams.id) ? null : $scope.clients[$stateParams.id];
        $scope.orderEdit = null;
        $scope.modal.open();
      }
    };
    $scope.editOrder = function(item){
      if ($scope.canEdit){
        $scope.listEdit.editable = $filter('orderBy')($filter('filter')($scope.detailsOriginal, {orderId: item.id}),
            'code');
        $scope.orderEdit = item;
        $scope.modal.open();
      }
    };

    $scope.refreshData = function(){
      $scope.loading = true;
      $scope.search.clientId = $stateParams.id;
      if (isNull($stateParams.id)){
        $scope.clientName = null;
      }

      var $url = (isNull($stateParams.id)) ? "/api/orders" : "/api/orders/get-with-client/" + $stateParams.id;
      $http.post($url).then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.list = data.data;
          $scope.orders = [
            {
              id:       null,
              clientId: null,
              code:     "Архив"
            }
          ];
          angular.forEach($scope.list, function(order, key){
            $scope.orders.push({
              id:       order.id,
              clientId: order.clientId,
              code:     order.code
            });
            $scope.list[key]['dateCreation'] = (order.dateCreation) ? Date.parse(order.dateCreation) : null;
            $scope.list[key]['dateStart'] = (order.dateStart) ? Date.parse(order.dateStart) : null;
            $scope.list[key]['dateEnd'] = (order.dateEnd) ? Date.parse(order.dateEnd) : null;
            $scope.list[key]['dateDeadline'] = (order.dateDeadline) ? Date.parse(order.dateDeadline) : null;
          });
          $scope.listEdit = {
            editable: {}
          };
          if (!isNull(data.clientName)){
            $scope.clientName = data.clientName;
          }
        }
        $scope.loading = false;
      }, function errorCallback(response){
        console.log(response.statusText);
      });
      $http.post('api/clients/only-names').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.clients = data.data;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
      $scope.refreshAddData();
    };

    $scope.refreshAddData = function(){
      $http.post('/api/nomenclature/only-names-archive').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.details = data.data;
          $scope.detailsOriginal = angular.copy($scope.details);
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    };

    filterActions();
    $scope.refreshData();
  }
})();