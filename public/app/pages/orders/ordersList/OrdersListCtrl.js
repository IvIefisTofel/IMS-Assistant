(function () {
  'use strict';
  angular.module('BlurAdmin.pages.orders')
      .controller('OrdersListCtrl', OrdersListCtrl);

  /** @ngInject */
  function OrdersListCtrl($scope, $stateParams, $http, $uibModal) {
    $scope.search = {clientId: $stateParams.id};
    $scope.clientName = null;
    $scope.defList = [];
    $scope.list = [];
    $scope.loading = true;

    $scope.filter = {1:true, 2:true, 3:false};
    $scope.propertyName = 'dateCreation';
    $scope.reverse = true;

    $scope.actions = [
      {
        text: "Добавить",
        class: "btn-success",
        iconClass: "fa fa-user-plus",
        action: 'addOrder'
      },
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ];
    var statuses = {
      1: 'Заказ',
      2: 'В работе',
      3: 'Исполнено'
    };

    $scope.sortBy = function(propertyName) {
      if (propertyName.indexOf('date') != -1) {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
      }
      else {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
      }
      $scope.propertyName = propertyName;
    };

    function noFilter(filterObj) {
      return Object.keys(filterObj).every(function (key) {
        return !filterObj[key];
      });
    }

    $scope.filterBy = function(item) {
      return $scope.filter[item.statusCode] || noFilter($scope.filter);
    };

    $scope.selected = {
      client: null,
      change: {
        client: null,
        order: null,
        detail: null
      },
      details: {
        new: {},
        showNew: false,
        import: []
      },
      new: {
        add: function(event) {
          $scope.selected.details.showNew = false;
          for (var i = 0; i < event.files.length; i++) {
            var file = event.files[i], indStart, indEnd,
                fName = (/[.]/.exec(file.name)) ? /[^.]+/.exec(file.name)[0] : undefined;
            var first = true;
            indStart = fName.indexOf('{');
            indEnd = fName.indexOf('}');
            if (indStart != -1 && indEnd != -1) {
              fName = fName.substr(0, indStart) + fName.substr(indEnd + 1);
              first = false;
            }
            indStart = fName.indexOf('(');
            indEnd = fName.indexOf(')');
            var code = '', name = '';
            if (indStart != -1 && indEnd != -1) {
              code = (fName.substr(0, indStart) + fName.substr(indEnd + 1)).replace(/(^\s*)|(\s*)$/g, '');
              name = (fName.substr(indStart + 1, indEnd - indStart - 1)).replace(/(^\s*)|(\s*)$/g, '');
            } else {
              code = fName.replace(/(^\s*)|(\s*)$/g, '');
              name = '';
            }
            if ($scope.selected.details.new[code] === undefined) {
              $scope.selected.details.new[code] = {files: [], base64: []};
            }
            $scope.selected.details.new[code].name = name;
            $scope.selected.details.new[code].code = code;
            $scope.selected.details.new[code].files.push({first: first, file: file})
          }
          angular.forEach($scope.selected.details.new, function (detail, code) {
            $scope.selected.details.showNew = true;
            angular.forEach(detail.files, function(file, index){
              var url, ext = (/[.]/.exec(file.file.name)) ? /[^.]+$/.exec(file.file.name)[0].toLowerCase() : undefined;
              if (!isNull(file.file) && !isNull(ext)) {
                url = URL.createObjectURL(file.file);
                if (ext.substr(0, 3) == 'tif') {
                  var xhr = new XMLHttpRequest();
                  xhr.open('GET', url);
                  xhr.responseType = 'arraybuffer';
                  xhr.onload = function (e) {
                    $scope.$apply(function() {
                      var buffer = xhr.response;
                      var tiff = new Tiff({buffer: buffer});
                      var canvas = tiff.toCanvas();
                      if (canvas) {
                        var base64 = canvas.toDataURL('image/jpeg'),
                            ind = $scope.selected.details.new[code].files.indexOf(file);
                        if ($scope.selected.details.new[code].base64.indexOf(base64) == -1) {
                          if ($scope.selected.details.new[code].files[ind].first) {
                            $scope.selected.details.new[code].base64.unshift(base64);
                          } else {
                            $scope.selected.details.new[code].base64.push(base64);
                          }
                        }
                        $scope.selected.details.new[code].files.splice(ind, 1);
                      }
                    });
                  };
                  xhr.send();
                } else {
                  toDataUrl(url, function (base64Img) {
                    $scope.$apply(function(){
                      var ind = $scope.selected.details.new[code].files.indexOf(file);
                      if ($scope.selected.details.new[code].base64.indexOf(base64Img) == -1) {
                        if ($scope.selected.details.new[code].files[ind].first) {
                          $scope.selected.details.new[code].base64.unshift(base64Img);
                        } else {
                          $scope.selected.details.new[code].base64.push(base64Img);
                        }
                      }
                      $scope.selected.details.new[code].files.splice(ind, 1);
                    });
                  }, 'image/jpeg');
                }
              }
            });
          });
          $('#fromFile').val(null);
        },
        remove: function(code, key){
          $scope.selected.details.new[code].base64.splice(key, 1);
          if ($scope.selected.details.new[code].base64.length == 0) {
            delete $scope.selected.details.new[code];
            if (isNull($scope.selected.details.new)) {
              $scope.selected.details.showNew = false;
            }
          }
        }
      },
      import: {
        add: function(){
          if (!isNull($scope.selected.change.detail)) {
            $scope.selected.details.import.push($scope.selected.change.detail);

            var key = $scope.details.indexOf($scope.selected.change.detail);
            $scope.selected.change.detail = null;
            $scope.details.splice(key, 1);
          }
        },
        remove: function(key){
          $scope.details.push($scope.selected.details.import[key]);
          $scope.selected.details.import.splice(key, 1);
        }
      }
    };
    $scope.clients = {};
    $scope.details = [];
    $scope.calendar = {
      dateCreation: {options: {startingDay: 1}},
      dateStart: {options: {startingDay: 1}},
      dateEnd: {options: {startingDay: 1}},
      dateDeadline: {options: {startingDay: 1}},
      init: function () {
        $scope.calendar.dateStart.options.minDate = $scope.listEdit.editable.dateCreation;
        $scope.calendar.dateDeadline.options.minDate = $scope.listEdit.editable.dateCreation;
        if ($scope.listEdit.editable.dateStart != null) {
          $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
        } else {
          $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateStart.options.minDate;
        }
      },
      checkDate: function (state, minDate) {
        if (!isNull(minDate)) {
          minDate = getCropDate(minDate);
        } else {
          minDate = null;
        }
        switch (state) {
          case 'dateCreation':
          case 1:
            if (minDate !== null) {
              if ($scope.listEdit.editable.dateStart !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateStart)) {
                $scope.listEdit.editable.dateStart = null;
              }
              if ($scope.listEdit.editable.dateDeadline !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateDeadline)) {
                $scope.listEdit.editable.dateDeadline = null;
              }
            }
            $scope.calendar.dateStart.options.minDate = $scope.calendar.dateDeadline.options.minDate = minDate;
          case 'dateStart':
          case 2:
            if (minDate !== null && $scope.listEdit.editable.dateEnd !== null
                && minDate > getCropDate($scope.listEdit.editable.dateEnd)) {
              $scope.listEdit.editable.dateEnd = null;
            }
            if (isNull($scope.listEdit.editable.dateStart)) {
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateCreation;
            } else {
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
            }
        }
      },
      today: function (model) {
        var now = getCropDate(Date.now());
        if (model === $scope.calendar.dateCreation) {
          $scope.listEdit.editable.dateCreation = now;
          $scope.calendar.checkDate('dateCreation', now);
        } else if (model === $scope.calendar.dateStart) {
          $scope.listEdit.editable.dateStart = now;
          $scope.calendar.checkDate('dateStart', now);
        } else if (model === $scope.calendar.dateEnd) {
          $scope.listEdit.editable.dateEnd = now;
          $scope.calendar.checkDate('dateEnd', now);
        } else if (model === $scope.calendar.dateDeadline) {
          $scope.listEdit.editable.dateDeadline = now;
        }
      },
      clear: function (model) {
        if (model === $scope.calendar.dateStart) {
          $scope.listEdit.editable.dateStart = null;
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateStart');
        } else if (model === $scope.calendar.dateEnd) {
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateEnd');
        } else if (model === $scope.calendar.dateDeadline) {
          $scope.listEdit.editable.dateDeadline = null;
        }
      }
    };
    $scope.listEdit = {editable: {}};
    var lastEditId = null;
    $scope.editItem = function (item) {
      if (isNull(lastEditId)) {
        lastEditId = item.id;
      } else {
        $scope.listEdit[lastEditId] = false;
        lastEditId = item.id;
      }
      $scope.listEdit.editable = angular.copy(item);
      $scope.calendar.init();
      $scope.selected.client = {
        key: item.clientId,
        value: $scope.clients[item.clientId]
      };
      $scope.listEdit[item.id] = true;
    };
    $scope.saveItem = function (item) {
      item.code = $scope.listEdit.editable.code;
      item.clientId = $scope.selected.client.id;
      item.clientName = $scope.selected.client.name;
      item.dateCreation = $scope.listEdit.editable.dateCreation;
      item.dateStart = $scope.listEdit.editable.dateStart;
      item.dateEnd = $scope.listEdit.editable.dateEnd;
      item.dateDeadline = $scope.listEdit.editable.dateDeadline;
      if (!isNull(item.dateEnd)) {
        item.statusCode = 3;
        item.status = statuses[item.statusCode];
      } else if (!isNull(item.dateStart)) {
        item.statusCode = 2;
        item.status = statuses[item.statusCode];
      } else {
        item.statusCode = 1;
        item.status = statuses[item.statusCode];
      }
      $scope.listEdit[item.id] = false;
    };
    $scope.abortItem = function (item) {
      $scope.listEdit[item.id] = false;
    };


    var modalInstance = null;
    $scope.modal = {
      open: function () {
        if (!isNull(lastEditId)) {
          $scope.listEdit[lastEditId] = false;
        }
        $scope.listEdit.editable = {dateCreation: getCropDate(Date.now())};
        $scope.calendar.init();
        $scope.selected.client = null;
        modalInstance = $uibModal.open({
          animation: true,
          templateUrl: 'app/pages/orders/modal/addOrder.html',
          size: 'exlg',
          backdrop: 'static',
          scope: $scope
        });
      },
      close: function () {
        modalInstance.dismiss();
        modalInstance = null;
        $scope.details = angular.copy($scope.detailsOriginal);
        $scope.selected.details.import = [];
        $scope.selected.details.new = [];
      },
      clear: {
        client: function(){
          $scope.selected.new.client = null;
        },
        order: function(){
          $scope.selected.new.order = null;
        }
      }
    };
    $scope.addOrder = function(){
      $scope.modal.open();
    };
    $scope.getSrc = function(src){
      return (typeof src == 'string') ? src : '';
    };

    $scope.refreshData = function () {
      $scope.loading = true;
      $scope.search.clientId = $stateParams.id;
      if (isNull($stateParams.id)) {
        $scope.clientName = null;
      }

      var $url = (isNull($stateParams.id)) ? "/api/orders" : "/api/orders/get-with-client/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.list = data.data;
          angular.forEach($scope.list, function (order, key) {
            $scope.list[key]['dateCreation'] = (order.dateCreation) ? new Date(order.dateCreation) : null;
            $scope.list[key]['dateStart'] = (order.dateStart) ? new Date(order.dateStart) : null;
            $scope.list[key]['dateEnd'] = (order.dateEnd) ? new Date(order.dateEnd) : null;
            $scope.list[key]['dateDeadline'] = (order.dateDeadline) ? new Date(order.dateDeadline) : null;
          });
          $scope.listEdit = {
            editable: {}
          };
          if (!isNull(data.clientName)) {
            $scope.clientName = data.clientName;
          }
          $scope.loading = false;
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $http.post('api/clients/only-names').then(function successCallback(response) {
      var data = response.data;
      if (data.error) {
        console.log(data);
      } else {
        $scope.clients = data;
      }
    }, function errorCallback(response) {
      console.log(response.statusText);
    });
    $http.post('/api/nomenclature/only-names').then(function successCallback(response) {
      var data = response.data;
      if (data.error) {
        console.log(data);
      } else {
        $scope.details = data.data;
        $scope.detailsOriginal = angular.copy($scope.details);
      }
    }, function errorCallback(response) {
      console.log(response.statusText);
    });
    $scope.refreshData();
  }
})();