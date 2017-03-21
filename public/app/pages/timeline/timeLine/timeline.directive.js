(function(){
  'use strict';
  angular.module('BlurAdmin.pages.timeline')
      .directive('timeLine', timeLine);

  /** @ngInject */
  function timeLine(){
    return {
      restrict:    'E',
      controller:  'TimelineCtrl',
      templateUrl: 'app/pages/timeline/timeLine/timeline.html'
    };
  }
})();