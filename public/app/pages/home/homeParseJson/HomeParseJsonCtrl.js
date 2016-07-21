/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .controller('HomeParseJsonCtrl', HomeParseJsonCtrl);

  /** @ngInject */
  function HomeParseJsonCtrl($scope) {
    function replacer(match, pIndent, pKey, pVal, pEnd) {
      var key = '<span class=json-key>';
      var val = '<span class=json-value>';
      var str = '<span class=json-string>';
      var r = pIndent || '';
      if (pKey)
        r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
      if (pVal)
        r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
      return r + (pEnd || '');
    }
    function prettyPrint(obj) {
      var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
      return JSON.stringify(obj, null, 3)
          .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
          .replace(/</g, '&lt;').replace(/>/g, '&gt;')
          .replace(jsonLine, replacer);
    }

    function reloadData(data) {
      $("#jsonData").html(data);
    }

    $scope.load = function () {
      var url = $('.home-parse-json #search').val();

      $.ajax({
        type: "POST",
        url: url,
        data: {data: null}
      }).success(function(response) {
        reloadData(prettyPrint(response));
      }).error(function(response) {
        console.log(response);
      });
    };

    reloadData({});
  }
})();