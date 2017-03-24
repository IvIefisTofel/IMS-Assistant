'use strict';
const ADMIN_ROLE        = 7,
      INSPECTOR_ROLE    = 6,
      SUPERVISOR_ROLE   = 5,
      CONSTRUCTOR_ROLE  = 4,
      TECHNOLOGIST_ROLE = 3,
      USER_ROLE         = 2,
      GUEST_ROLE        = 1;

angular.module('BlurAdmin', [
  'ngAnimate',
  'ui.bootstrap',
  'ui.bootstrap.datepicker',
  'ui.bootstrap.datepickerPopup',
  'ui.router',
  'ntt.TreeDnD',
  'ui.select',
  'ngSanitize',
  'bootstrap3-typeahead',
  'thatisuday.ng-image-gallery',
  'ngIdle',
  'angular-inview',
  // 'ui.sortable',
  // 'ngTouch',
  // 'toastr',
  // 'smart-table',
  // "xeditable",
  // 'ui.slimscroll',
  // 'ngJsTree',
  // 'angular-progress-button-styles',

  'BlurAdmin.theme',
  'BlurAdmin.pages'
]);