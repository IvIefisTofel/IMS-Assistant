(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .run(themeRun);

  /** @ngInject */
  function themeRun($timeout, $rootScope, layoutPaths, preloader, $q, baSidebarService, themeLayoutSettings, Idle,
                    $http, $uibModal, toastr, toastrConfig){
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

    $rootScope.roles = {};
    $rootScope.roles[ADMIN_ROLE] = 'Администратор';
    $rootScope.roles[INSPECTOR_ROLE] = 'Контролер';
    $rootScope.roles[SUPERVISOR_ROLE] = 'Начальник КТБ';
    $rootScope.roles[CONSTRUCTOR_ROLE] = 'Конструктор';
    $rootScope.roles[TECHNOLOGIST_ROLE] = 'Технолог';
    $rootScope.roles[USER_ROLE] = 'Пользователь';
    $rootScope.roles[GUEST_ROLE] = 'Гость';

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
              var data = response.data;
              if (data.error){
                if (!isNull(data.messages)){
                  console.log(data.messages);
                  if (!isNull(data.messages.password)){
                    delete data.messages.password;
                    scope.showErrors = true;
                    scope.editable.errPassword = true;
                  }
                }
              } else {
                callBack(response.data);
                modal.dismiss('close');
                if (data.error || data.status){ $rootScope.showMessage(data); }
              }
            });
          }
        }
      };
    };

    $rootScope.$getPermissions = function(){
      if (isNull($rootScope.user)){
        return $rootScope.$user().currentRole;
      } else {
        return $rootScope.user.currentRole;
      }
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
          if (response.data.permissions <= USER_ROLE){
            Idle.unwatch();
            angular.extend(toastrConfig, { positionClass: 'toast-top-full-width' });
            toastr.warning('Вы отсутствовали в течении <b>15-ти минут</b>.<br>В целях безопасности права пользователя ' +
                           'были сброшены до <b>"Только просмотр"</b>.<br>Для возврата прав редактирования, ' +
                           'пожалуйста, авторизуйтесь заново.', '<h5>Предупреждение</h5>', {
              timeOut: 0,
              extendedTimeOut: 0
            });
            angular.extend(toastrConfig, { positionClass:   'toast-top-right' });
          }
        } else {
          console.log(response.data);
        }
      }, function errorCallback(response){
        console.log(response);
      });
    });

    if ($rootScope.$getPermissions() > USER_ROLE){
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

    $rootScope.showMessage = function(data){
      var type = 'info', header = 'Информация', message = '';
      if (!isNull(data.error)){
        type = 'error';
        header = 'Ошибка!';
        message = 'Произошла ошибка во время выполнения операции.';
      }
      if (!isNull(data.status)){
        if (!data.status){
          type = 'warning';
          header = 'Предупреждение';
        } else {
          type = 'success';
          header = 'Успешно';
        }
      }
      if (isNull(data.messages)) {
        if (!isNull(data.msgHeader)){ header = data.msgHeader; }
        if (!isNull(data.message)){ message = data.message; }
        toastr[type](message, header);
      } else {
        angular.forEach(data.messages, function(msg){
          var thisHeader = header, thisType = type, thisMessage = message;
          if (!isNull(msg.type)){ thisType = msg.type; }
          if (!isNull(msg.header)){ thisHeader = msg.header; }
          if (!isNull(msg.message)){ thisMessage = msg.message; }
          toastr[thisType](thisMessage, thisHeader);
        });
      }
    }
  }
})();