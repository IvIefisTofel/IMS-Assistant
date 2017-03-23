(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .run(themeRun);

  /** @ngInject */
  function themeRun($timeout, $rootScope, layoutPaths, preloader, $q, baSidebarService, themeLayoutSettings, Idle,
                    $http, $uibModal){
    var whatToWait = [
      preloader.loadAmCharts(),
      $timeout(3000)
    ];

    var theme = themeLayoutSettings;
    if (theme.blur){
      whatToWait.unshift(preloader.loadImg(layoutPaths.images.root + 'blur-bg.jpg'));
      whatToWait.unshift(preloader.loadImg(layoutPaths.images.root + 'blur-bg-blurred.jpg'));
    }

    $q.all(whatToWait).then(function(){
      $rootScope.$pageFinishedLoading = true;
    });

    $timeout(function(){
      if (!$rootScope.$pageFinishedLoading){
        $rootScope.$pageFinishedLoading = true;
      }
    }, 7000);

    $rootScope.$baSidebarService = baSidebarService;

    $rootScope.roles = {
      1: 'Администратор',
      2: 'Модератор',
      3: 'Пользователь'
    };

    $rootScope.user = null;
    $rootScope.$user = function(async){
      if (async == undefined){
        async = false;
      }
      var result = null;
      $.ajax({
        type:    "POST",
        url:     '/api/users/get-identity',
        async:   async,
        data:    {data: null},
        success: function(response){
          result = response.data;
        },
        error:   function(response){
          console.log(response);
        }
      });

      $rootScope.user = result;
      return result;
    };
    $rootScope.userEdit = function(modal, scope, callBack) {
      function hidePassword(){ $('input.passwd').attr('type', 'password'); }
      function showPassword(){ $(this).parents('.input-group').children('input.passwd').attr('type', 'text'); }
      return {
        open: function(){
          modal = $uibModal.open({
            animation:   true,
            templateUrl: 'app/pages/users/modal/editUser.html',
            size:        'md',
            backdrop:    'static',
            scope:       scope
          });
          modal.rendered.then(function(){
            if ($('#user-edit').length == 1) {
              $("body").on('mouseup', hidePassword);
              $("button.view-password").on('mousedown', showPassword);
            }
          });
          modal.closed.then(function(){
            $("body").off('mouseup', hidePassword);
            $("button.view-password").off('mousedown', showPassword);
            modal = null;
            if (!isNull(scope.editable)) {
              scope.editable = {};
            }
            if (!isNull(scope.showErrors)) {
              scope.showErrors = false;
            }
          });
        },
        save: function(){
          if (!isNull(scope.showErrors) && (
              isNull(scope.editable.name) ||
              isNull(scope.editable.fullName) ||
              isNull(scope.editable.email) ||
              isNull(scope.editable.active) ||
              isNull(scope.editable.password) ||
              ($rootScope.user.id == scope.editable.id && isNull(scope.editable.password)) ||
              (!isNull(scope.editable.newPassword) && isNull(scope.editable.confirmPassword)) ||
              (isNull(scope.editable.newPassword) && !isNull(scope.editable.confirmPassword)) ||
              (!isNull(scope.editable.newPassword) && !isNull(scope.editable.confirmPassword) &&
               scope.editable.newPassword != scope.editable.confirmPassword)
              )) {
            scope.showErrors = true;
          } else {
            var send = new FormData();
            send.append('name', scope.editable.name);
            send.append('fullName', scope.editable.fullName);
            send.append('email', scope.editable.email);
            if (scope.currentUserId != scope.editable.id && !isNull(scope.editable.roleId)) {
              send.append('roleId', scope.editable.roleId.key);
            }
            send.append('active', scope.editable.active ? 1 : 0);

            send.append('password', scope.editable.password);
            if (!isNull(scope.editable.newPassword)) {
              send.append('newPassword', scope.editable.newPassword);
              send.append('confirmPassword', scope.editable.confirmPassword);
            }

            $http.post(isNull(scope.editable.id) ? '/api/users/add' : '/api/users/edit/' + scope.editable.id, send, {
              transformRequest: angular.identity,
              headers:          {'Content-Type': undefined}
            }).then(function successCallback(response){
              if (response.data.error){
                if (!isNull(response.data.messages)) {
                  console.log(response.data.messages);
                  if (!isNull(response.data.messages.password)) {
                    scope.showErrors = true;
                    scope.editable.errPassword = true;
                  }
                } else {
                  console.log(response.data.error);
                }
              } else {
                callBack(response.data);
                modal.dismiss('close');
              }
            });
          }
        }
      };
    };

    $rootScope.$getPermissions = function(r){
      var matches = document.cookie.match(new RegExp(
          "(?:^|; )rights=([^;]*)"
      ));
      return matches ? decodeURIComponent(matches[1]) === 'true' : false;
    };

    $rootScope.previousState = null;
    $rootScope.$on('$stateChangeSuccess', function(event, to, toParams, from, fromParams){
      if (from.abstract == undefined || from.abstract == false){
        $rootScope.previousState = {
          state:  from,
          params: fromParams
        };
      }
    });

    $rootScope.$on('IdleStart', function(){
      var form = new FormData();
      form.append('assistant_name', $rootScope.$user()['name']);
      form.append('assistant_password', '');

      $http.post('/login', form, {
        transformRequest: angular.identity,
        headers:          {'Content-Type': undefined}
      }).then(function successCallback(response){
        if (response.data.auth){
          if (!response.data.permissions){
            Idle.unwatch();
          }
        } else {
          console.log(response.data);
        }
      }, function errorCallback(response){
        console.log(response);
      });
    });

    if ($rootScope.$getPermissions()){
      Idle.watch();
    }

    $rootScope.gallery = {
      images:   null,
      methods:  {},
      isOpened: false,
      opened:   function(){
        $rootScope.gallery.isOpened = true;
      },
      closed:   function(){
        var modal = $('.modal');
        if (modal.length == 1 && $rootScope.gallery.isOpened){
          modal.focus();
        }
        $rootScope.gallery.isOpened = false;
      }
    };

    $rootScope.isNull = function(v){
      return isNull(v);
    };

    $rootScope.focusedTextEl = function(){
      var $focusEl = $(document.activeElement),
          res = false;

      switch ($focusEl.prop("tagName")) {
        case "INPUT":
          switch ($focusEl.attr('type').toLowerCase()) {
            case "text": case "password":
              res = true;
              break;
          }
          break;
        case "TEXTAREA":
          res = true;
          break;
      }

      return res;
    };
  }
})();