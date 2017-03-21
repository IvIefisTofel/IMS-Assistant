(function(){
  'use strict';
  angular.module('BlurAdmin.theme')
      .run(themeRun);

  /** @ngInject */
  function themeRun($timeout, $rootScope, layoutPaths, preloader, $q, baSidebarService, themeLayoutSettings, Idle,
                    $http){
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