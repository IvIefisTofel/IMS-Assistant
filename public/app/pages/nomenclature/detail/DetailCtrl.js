(function(){
  'use strict';
  angular.module('BlurAdmin.pages.nomenclature')
      .controller('DetailCtrl', DetailCtrl);

  /** @ngInject */
  function DetailCtrl($scope, $rootScope, $state, $stateParams, $http, $window, $uibModal, $filter){
    var cropper,
        currImgId = null,
        imgDef = {
          width:  undefined,
          height: undefined
        };

    function getAspect(img){
      if (img.width > img.height){
        return 29.7 / 21.0;
      } else {
        return 21.0 / 29.7;
      }
    }

    function clearValues(){
      $scope.new.patterns = [];
      $scope.galleries.new = [];
      $scope.new.add = {
        patterns: [],
        models:   [],
        projects: []
      };
      $scope.new.update = {
        patterns: {},
        models:   {},
        projects: {}
      };
      $('input[type="file"][name^="file_id_"],#add-model,#add-project').val(null);
    }

    $scope.caption = $stateParams.id == 'add' ? 'Новая деталь' : 'Деталь';

    $scope.doAction = function(id){
      if ($scope.actions[id].action !== undefined && $scope.actions[id].action !== null){
        if ($scope.actions[id].params !== undefined && $scope.actions[id].params !== null){
          $scope[$scope.actions[id].action]($scope.actions[id].params);
        } else {
          $scope[$scope.actions[id].action]();
        }
      }
    };
    $scope.actionVariants = {
      view: [
        {
          text:      "Редактировать",
          class:     "btn-primary",
          iconClass: "fa fa-pencil",
          action:    'switchEdit'
        },
        {
          text:      "Обновить",
          class:     "btn-info",
          iconClass: "fa fa-refresh",
          action:    'refreshData'
        }
      ],
      edit: [
        {
          text:      "Сохранить",
          class:     "btn-success",
          iconClass: "fa fa-floppy-o",
          action:    'saveData'
        },
        {
          text:      "Отменить",
          class:     "btn-danger",
          iconClass: "fa fa-ban",
          action:    'switchEdit'
        },
        {
          text:      "Обновить",
          class:     "btn-info",
          iconClass: "fa fa-refresh",
          action:    'refreshData'
        }
      ]
    };
    $scope.actions = $scope.actionVariants.view;

    $scope.edit = false;
    $scope.edited = false;
    $scope.preview = {
      detail:   null,
      selected: {
        client: null,
        order:  null
      }
    };
    $scope.showErrors = false;

    $scope.detail = {};
    $scope.orders = [];
    $scope.groups = [];
    $scope.loading = true;
    $scope.showGallery = false;
    $scope.galleries = {
      main: [],
      new:  []
    };
    $scope.openGallery = function(id, index){
      if (isNull(index)){
        index = 0;
      }
      if (!isNull($scope.galleries[id])){
        $rootScope.gallery.images = $scope.galleries[id];
        $rootScope.gallery.methods.open(index);
      }
    };

    $scope.new = {
      patterns:    [],
      updPatterns: function(){
        $scope.new.patterns = [];
        $scope.galleries.new = [];
        angular.forEach($scope.new.update.patterns, function(val, key){
          $scope.new.patterns.push(val);
          $scope.galleries.new.push({url: val.img});
        });
        angular.forEach($scope.new.add.patterns, function(val, key){
          $scope.new.patterns.push({id: null, arrId: key, img: val.img});
          $scope.galleries.new.push({url: val.img});
        });
      },
      models:      function(){
        var updated = [];
        angular.forEach($scope.new.update.models, function(val, key){
          updated.push(val);
        });
        return $scope.new.add.models.concat(updated);
      },
      projects:    function(){
        var updated = [];
        angular.forEach($scope.new.update.projects, function(val, key){
          updated.push(val);
        });
        return $scope.new.add.projects.concat(updated);
      },
      add:         {
        patterns: [],
        models:   [],
        projects: []
      },
      update:      {
        patterns: {},
        models:   {},
        projects: {}
      }
    };
    $scope.change = {
      add:    {
        pattern: function(event){
          var file = event.files[0], url, img,
              ext = (/[.]/.exec(file.name)) ? /[^.]+$/.exec(file.name)[0].toLowerCase() : undefined;
          if (!isNull(file) && !isNull(ext)){
            url = URL.createObjectURL(file);
            if (ext.substr(0, 3) == 'tif'){
              $('.pattern-wrapper')
                  .html('<div id="pattern-loading"><i class="fa fa-spinner fa-spin fa-5x" ></i></div>');
              var xhr = new XMLHttpRequest();
              xhr.open('GET', url);
              xhr.responseType = 'arraybuffer';
              xhr.onload = function(e){
                var buffer = xhr.response;
                var tiff = new Tiff({buffer: buffer});
                var canvas = tiff.toCanvas();
                var img = {
                  width:  tiff.width(),
                  height: tiff.height()
                };
                if (canvas){
                  canvas.style = 'display: none;';
                  canvas.id = 'image-original-data';
                  $('.pattern-wrapper').html('').append(canvas);

                  if (!isNull(cropper)){
                    cropper.cropper('destroy');
                  }
                  cropper = $('.pattern-wrapper canvas').cropper({
                    aspectRatio: getAspect(img),
                    viewMode:    2,
                    data:        {x: 0, y: 0, width: img.width, height: img.height}
                  });
                  imgDef = {
                    width:  img.width,
                    height: img.height
                  };
                }
              };
              xhr.send();
            } else {
              img = new Image;

              img.onload = function(){
                $('#pattern-loading').remove();
                if (isImageFile(file)){
                  if (!isNull(cropper)){
                    cropper.cropper('destroy');
                  }
                  cropper = $('.pattern-wrapper img').cropper({
                    aspectRatio: getAspect(img),
                    viewMode:    2,
                    data:        {x: 0, y: 0, width: img.width, height: img.height}
                  });
                  imgDef = {
                    width:  img.width,
                    height: img.height
                  };
                }
              };

              img.src = url;
              img.style = 'display: none;';
              img.id = 'image-original-data';
              $('.pattern-wrapper').html('<div id="pattern-loading"><i class="fa fa-spinner fa-spin fa-5x" ></i></div>')
                  .append(img);
            }
          }
        },
        model:   function(event){
          $scope.new.add.models = [];
          angular.forEach(event.files, function(file, key){
            $scope.new.add.models.push(file);
          });
          $scope.$apply();
        },
        project: function(event){
          $scope.new.add.projects = [];
          angular.forEach(event.files, function(file, key){
            $scope.new.add.projects.push(file);
          });
          $scope.$apply();
        }
      },
      update: {
        pattern: function(event){
        },
        model:   function(event){
          var id = $(event).data('id');
          if (event.files.length > 0){
            $scope.new.update.models[id] = event.files[0];
          } else {
            delete $scope.new.update.models[id];
          }
          $scope.$apply();
        },
        project: function(event){
          var id = $(event).data('id');
          if (event.files.length > 0){
            $scope.new.update.projects[id] = event.files[0];
          } else {
            delete $scope.new.update.projects[id];
          }
          $scope.$apply();
        }
      }
    };
    $scope.delNewImage = function(key){
      var img = $scope.new.patterns[key];
      if (isNull(img.id)){
        $scope.new.add.patterns.splice(img.arrId, 1);
      } else {
        delete $scope.new.update.patterns[img.id];
      }
      $scope.new.updPatterns();
    };

    $scope.selected = {
      client: null,
      order:  null
    };
    $scope.ordersObj = {
      client: {
        change: function(){
          if (!isEmpty($scope.selected.client)){
            $scope.selected.order = undefined;
          }
        }
      },
      order:  {
        change: function(){
          if (!isEmpty($scope.selected.order) && isEmpty($scope.selected.client)){
            var next = true;
            angular.forEach($scope.clients, function(client, key){
              if (next){
                if ($scope.selected.order.clientId == client.id){
                  $scope.selected.client = $scope.clients[$scope.selected.order.clientId];
                  next = false;
                }
              }
            });
          }
        }
      },
      clear:  function(){
        $scope.selected.client = undefined;
        $scope.selected.order = undefined;
      }
    };

    var now = Date.now();
    $scope.calendar = {
      dateCreation: {
        options:  {
          startingDay: 1
        },
        date:     now,
        change:   function(){
          $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateCreation.date;
          if ($scope.calendar.dateEnd.date !== null
              && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)){
            $scope.calendar.dateEnd.date = null;
          }
        },
        open:     function(){
          $scope.calendar.dateCreation.opened = true;
        },
        opened:   false,
        tooltips: {
          today:    false,
          clear:    false,
          calendar: false
        }
      },
      dateEnd:      {
        options:  {
          startingDay: 1,
          minDate:     now
        },
        date:     null,
        open:     function(){
          $scope.calendar.dateEnd.opened = true;
        },
        opened:   false,
        tooltips: {
          today:    false,
          clear:    false,
          calendar: false
        }
      },
      today:        function(model){
        if (model === $scope.calendar.dateCreation){
          $scope.calendar.dateCreation.date = getCropDate(Date.now());
          if ($scope.calendar.dateEnd.date !== null
              && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)){
            $scope.calendar.dateEnd.date = getCropDate(Date.now());
          }
        } else if (model === $scope.calendar.dateEnd){
          if ($scope.calendar.dateCreation.date !== null){
            if (getCropDate($scope.calendar.dateCreation.date) <= getCropDate(Date.now())){
              $scope.calendar.dateEnd.date = getCropDate(Date.now());
            } else {
              $scope.calendar.dateEnd.date = new Date($scope.calendar.dateCreation.date.valueOf());
              $scope.calendar.dateEnd.date.setDate($scope.calendar.dateCreation.date.getDate() + 1);
            }
          }
        }
      },
      clear:        function(model){
        if (model === $scope.calendar.dateCreation){
          $scope.calendar.dateCreation.date = null;
          $scope.calendar.dateEnd.date = null;
        } else {
          model.date = null;
        }
      }
    };

    $scope.filterBy = function(item){
      if (!isEmpty($scope.selected.client)){
        return item.clientId == $scope.selected.client.id;
      } else {
        return true;
      }
    };

    $scope.switchEdit = function(){
      if ($scope.edit){
        $scope.edit = false;
        $scope.edited = false;
        $scope.actions = $scope.actionVariants.view;
        if (angular.toJson($scope.detail) != angular.toJson($scope.preview.detail)){
          angular.forEach($scope.preview.detail, function(val, key){
            if ($scope.detail[key] != val){
              if (typeof(val) === 'object'){
                if (angular.toJson($scope.detail[key]) != angular.toJson(val)){
                  $scope.detail[key] = angular.copy(val);
                }
              } else {
                $scope.detail[key] = val;
              }
            }
          });
        }
        if (angular.toJson($scope.selected.client) != angular.toJson($scope.preview.selected.client)){
          $scope.selected.client = angular.copy($scope.preview.selected.client);
        }
        if (angular.toJson($scope.selected.order) != angular.toJson($scope.preview.selected.order)){
          $scope.selected.order = angular.copy($scope.preview.selected.order);
        }
        $scope.calendar.dateCreation.date =
            $scope.calendar.dateEnd.options.minDate = new Date($scope.preview.detail.dateCreation);
        $scope.calendar.dateEnd.date = $scope.preview.detail.dateEnd ? new Date($scope.preview.detail.dateEnd) : null;
        clearValues();
      } else {
        $scope.edit = true;
        $scope.edited = false;
        $scope.showErrors = false;
        $scope.actions = $scope.actionVariants.edit;
      }
    };

    var modalInstance = null;
    $scope.modalActions = {
      close:  function(){
        $('.pattern-wrapper').html('');
        $('#patternInput').val(null);
        if (!isNull(cropper)){
          cropper.cropper('destroy');
        }
        currImgId = null;
        imgDef = {
          width:  undefined,
          height: undefined
        };

        modalInstance.dismiss();
        modalInstance = null;
      },
      aspect: {
        hA4:  function(){
          var data = {};
          if (!isNull(imgDef.width) && !isNull(imgDef.height)){
            data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
          }
          if (!isNull(cropper)){
            cropper.cropper('destroy');
          }
          cropper = $('.pattern-wrapper #image-original-data').cropper({
            aspectRatio: 29.7 / 21.0,
            viewMode:    2,
            data:        data
          });
        },
        vA4:  function(){
          var data = {};
          if (!isNull(imgDef.width) && !isNull(imgDef.height)){
            data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
          }
          if (!isNull(cropper)){
            cropper.cropper('destroy');
          }
          cropper = $('.pattern-wrapper #image-original-data').cropper({
            aspectRatio: 21.0 / 29.7,
            viewMode:    2,
            data:        data
          });
        },
        free: function(){
          var data = {};
          if (!isNull(imgDef.width) && !isNull(imgDef.height)){
            data = {x: 0, y: 0, width: imgDef.width, height: imgDef.height};
          }
          if (!isNull(cropper)){
            cropper.cropper('destroy');
          }
          cropper = $('.pattern-wrapper #image-original-data').cropper({
            aspectRatio: NaN,
            viewMode:    2,
            data:        data
          });
        }
      },
      rotate: {
        p90:    function(){
          if (!isNull(cropper)) cropper.cropper('rotate', 90);
        },
        m90:    function(){
          if (!isNull(cropper)) cropper.cropper('rotate', -90);
        },
        mirror: function(){
          if (!isNull(cropper)) cropper.cropper('scaleX', cropper.cropper('getData')['scaleX'] * -1);
        }
      },
      update: function(imgId){
        if ($scope.new.update.patterns[imgId]){
          delete $scope.new.update.patterns[imgId];
          $scope.new.updPatterns();
        } else {
          currImgId = (!isNull(imgId)) ? imgId : null;
          $scope.modal();
        }
      },
      save:   function(){
        if (!isNull(cropper)){
          var dataURL = cropper.cropper('getCroppedCanvas').toDataURL('image/jpeg');
          if (currImgId !== null){
            $scope.new.update.patterns[currImgId] = {
              id:  currImgId,
              img: dataURL
            };
          } else {
            $scope.new.add.patterns.push({
              id:  null,
              img: dataURL
            });
          }
          $scope.new.updPatterns();
          $scope.modalActions.close();
        }
      }
    };
    $scope.modal = function(){
      modalInstance = $uibModal.open({
        animation:   true,
        templateUrl: 'app/pages/nomenclature/modal/newPattern.html',
        size:        'lg',
        backdrop:    'static',
        scope:       $scope
      });
    };

    $scope.saveData = function(){
      if (!$scope.edited){
        if (isNull($scope.detail.name) ||
            isNull($scope.detail.code) ||
            isNull($scope.selected.order) || isNull($scope.selected.order.id) ||
            isNull($scope.calendar.dateCreation.date)){
          $scope.showErrors = true;
        } else {
          $scope.edit = false;
          $scope.edited = true;
          $scope.actions = [$scope.actionVariants.edit[0], $scope.actionVariants.view[0], $scope.actionVariants.view[1]];
        }
      } else {
        $scope.loading = true;
        var send = new FormData();
        // Detail
        send.append('orderId', $scope.selected.order.id);
        if ($scope.detail.group){
          send.append('group', $scope.detail.group);
        }
        send.append('code', $scope.detail.code);
        send.append('name', $scope.detail.name);
        send.append('dateCreation', $filter('date')($scope.calendar.dateCreation.date, "dd.MM.yyyy"));
        if (!isNull($scope.calendar.dateEnd.date)){
          send.append('dateEnd', $filter('date')($scope.calendar.dateEnd.date, "dd.MM.yyyy"));
        }
        // New
        angular.forEach($scope.new.add.patterns, function(file, key){
          send.append('patterns[new][]', file.img);
        });
        angular.forEach($scope.new.add.models, function(file, key){
          send.append('new[models][]', file);
        });
        angular.forEach($scope.new.add.projects, function(file, key){
          send.append('new[projects][]', file);
        });
        // Update
        angular.forEach($scope.new.update.patterns, function(file, key){
          send.append('patterns[' + key + ']', file.img);
        });
        angular.forEach($scope.new.update.models, function(file, key){
          send.append('models[' + key + ']', file);
        });
        angular.forEach($scope.new.update.projects, function(file, key){
          send.append('projects[' + key + ']', file);
        });
        var $url = (($stateParams.id == 'add') ? "/api/nomenclature/add" : "/api/nomenclature/update/" +
                                                                           $stateParams.id);
        $http.post($url, send, {
          transformRequest: angular.identity,
          headers:          {'Content-Type': undefined}
        }).then(function successCallback(response){
          if (response.data.error){
            console.log(response.data.message);
          } else {
            if (!isNull(response.data.id) && response.data.id == $stateParams.id){
              $scope.actions = $scope.actionVariants.view;
              $scope.refreshData();
            } else {
              $state.go('nomenclature-detail-edit', {id: response.data.id}, {location: 'replace'});
            }
          }
        });
      }
    };

    $scope.refreshData = function(){
      if (isNull($stateParams.id)){
        $window.history.back();
      }
      $scope.loading = true;
      if ($scope.edit){
        $scope.edit = false;
        $scope.edited = false;
        $scope.showErrors = false;
        $scope.actions = $scope.actionVariants.view;
      }

      var $url = "/api/nomenclature/get-with-parents/" + $stateParams.id;
      $http.post($url, null, {headers: {'All-Versions': true}}).then(function successCallback(response){
        var data = response.data;
        if (data.error){
          console.log(data);
        } else {
          $scope.detail = data.data[0];
          $scope.clients = data.clients;
          $scope.orders = [];
          $scope.groups = data.groups;
          $scope.calendar.dateCreation.date =
              $scope.calendar.dateEnd.options.minDate = new Date($scope.detail.dateCreation);
          $scope.calendar.dateEnd.date = $scope.detail.dateEnd ? new Date($scope.detail.dateEnd) : null;
          angular.forEach($scope.clients, function(client, key){
            angular.forEach(client.orders, function(order, key){
              if ($scope.detail.orderId == order.id){
                $scope.selected.client = client;
                $scope.selected.order = order;
              }
              $scope.orders = $scope.orders.concat(order);
            });
          });
          $scope.preview = {
            detail:   angular.copy($scope.detail),
            selected: {
              client: angular.copy($scope.selected.client),
              order:  angular.copy($scope.selected.order)
            }
          };
          clearValues();
          $scope.galleries.main = [];
          angular.forEach($scope.detail.pattern, function(image, key){
            $scope.galleries.main.push({
              url:    '/files/h/950/' + image.versions[0].id + '/' + image.name,
              extUrl: '/files/' + image.versions[0].id + '/' + image.name
            });
            $scope.galleries[image.id] = [];
            angular.forEach(image.versions, function(version, key){
              $scope.galleries[image.id].push({
                url:     '/files/h/950/' + version.id + '/' + image.name,
                extUrl:  '/files/' + version.id + '/' + image.name,
                desText: version.date
              });
            });
          });
          $scope.loading = false;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    };

    if ($stateParams.id !== 'add'){
      $scope.refreshData();
    } else {
      $scope.loading = false;
      $scope.edit = true;
      $scope.actions = [$scope.actionVariants.edit[0]];

      var $url = "/api/nomenclature/get-only-parents";
      $http.post($url, null, {headers: {'All-Versions': true}}).then(function successCallback(response){
        var data = response.data;
        if (data.error){
          console.log(data);
        } else {
          $scope.clients = data.clients;
          $scope.groups = data.groups;

          angular.forEach($scope.clients, function(client, key){
            angular.forEach(client.orders, function(order, key){
              if ($scope.detail.orderId == order.id){
                $scope.selected.client = client;
                $scope.selected.order = order;
              }
              $scope.orders = $scope.orders.concat(order);
            });
          });
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    }
  }
})();