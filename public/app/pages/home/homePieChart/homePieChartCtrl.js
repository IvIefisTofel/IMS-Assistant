/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .controller('HomePieChartCtrl', HomePieChartCtrl);

  /** @ngInject */
  function HomePieChartCtrl($scope, $timeout, baConfig, baUtil, $http) {
    var pieColor = baUtil.hexToRGB(baConfig.colors.defaultText, 0.2);
    $scope.charts = [{
      color: pieColor,
      description: 'New Visits',
      stats: '57,820',
      icon: 'person',
    }, {
      color: pieColor,
      description: 'Purchases',
      stats: '$ 89,745',
      icon: 'money',
    }, {
      color: pieColor,
      description: 'Active Users',
      stats: '178,391',
      icon: 'face',
    }, {
      color: pieColor,
      description: 'Returned',
      stats: '32,592',
      icon: 'refresh',
    }
    ];

    function getRandomArbitrary(min, max) {
      return Math.random() * (max - min) + min;
    }

    function loadPieCharts() {
      $('.chart').each(function () {
        var chart = $(this);
        chart.easyPieChart({
          easing: 'easeOutBounce',
          onStep: function (from, to, percent) {
            $(this.el).find('.percent').text(Math.round(percent));
          },
          barColor: chart.attr('rel'),
          trackColor: 'rgba(0,0,0,0)',
          size: 84,
          scaleLength: 0,
          animation: 2000,
          lineWidth: 9,
          lineCap: 'round',
        });
      });

      $('.refresh-data').on('click', function () {
        updatePieCharts();
      });
    }

    function updatePieCharts() {
      $('.pie-charts .chart').each(function(index, chart) {
        $(chart).data('easyPieChart').update(getRandomArbitrary(55, 90));
      });
    }

    $timeout(function () {
      loadPieCharts();
      updatePieCharts();
    }, 1000);

    $.ajax({
      type: "POST",
      url: 'get-route',
      data: {data: null}
    }).success(function(response) {
      console.log(response);
    }).error(function(response) {
      console.log({error: response}.toString());
    });
    // $http({
    //   url: 'get-route',
    //   method: 'POST',
    //   headers: { 'X-Requested-With': 'XMLHttpRequest' },
    //   data: JSON.stringify({data:null})
    // }).then(function(response) {
    //   console.log(response.data);
    // });
    // $http.post('get-route', JSON.stringify({data:null}), headers: { 'Content-Type': 'application/json' })
  }
})();