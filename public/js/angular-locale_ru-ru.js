'use strict';
angular.module("ngLocale", [], ["$provide", function($provide) {
    var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};
    function getDecimals(n) {
        n = n + '';
        var i = n.indexOf('.');
        return (i == -1) ? 0 : n.length - i - 1;
    }

    function getVF(n, opt_precision) {
        var v = opt_precision;

        if (undefined === v) {
            v = Math.min(getDecimals(n), 3);
        }

        var base = Math.pow(10, v);
        var f = ((n * base) | 0) % base;
        return {v: v, f: f};
    }

    $provide.value("$locale", {
        "DATETIME_FORMATS": {
            "AMPMS": [
                "AM",
                "PM"
            ],
            "DAY": [
                "Воскресенье",
                "Понедельник",
                "Вторник",
                "Среда",
                "Четверг",
                "Пятница",
                "Суббота"
            ],
            "ERANAMES": [
                "До Рождества Христова",
                "От Рождества Христова"
            ],
            "ERAS": [
                "До н. э.",
                "Н. э."
            ],
            "FIRSTDAYOFWEEK": 0,
            "MONTH": [
                "Января",
                "Февраля",
                "Марта",
                "Апреля",
                "Мая",
                "Июня",
                "Июля",
                "Августа",
                "Сентября",
                "Октября",
                "Ноября",
                "Декабря"
            ],
            "SHORTDAY": [
                "вс",
                "пн",
                "вт",
                "ср",
                "чт",
                "пт",
                "сб"
            ],
            "SHORTMONTH": [
                "Янв.",
                "Февр.",
                "Мар.",
                "Апр.",
                "Мая",
                "Июн.",
                "Июл.",
                "Авг.",
                "Сент.",
                "Окт.",
                "Нояб.",
                "Дек."
            ],
            "STANDALONEMONTH": [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль",
                "Август",
                "сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
            "WEEKENDRANGE": [
                5,
                6
            ],
            "fullDate": "EEEE, d MMMM y 'г'.",
            "longDate": "d MMMM y 'г'.",
            "medium": "d MMM y 'г'. H:mm:ss",
            "mediumDate": "d MMM y 'г'.",
            "mediumTime": "H:mm:ss",
            "short": "dd.MM.yy H:mm",
            "shortDate": "dd.MM.yy",
            "shortTime": "H:mm"
        },
        "NUMBER_FORMATS": {
            "CURRENCY_SYM": "\u20bd",
            "DECIMAL_SEP": ",",
            "GROUP_SEP": "\u00a0",
            "PATTERNS": [
                {
                    "gSize": 3,
                    "lgSize": 3,
                    "maxFrac": 3,
                    "minFrac": 0,
                    "minInt": 1,
                    "negPre": "-",
                    "negSuf": "",
                    "posPre": "",
                    "posSuf": ""
                },
                {
                    "gSize": 3,
                    "lgSize": 3,
                    "maxFrac": 2,
                    "minFrac": 2,
                    "minInt": 1,
                    "negPre": "-",
                    "negSuf": "\u00a0\u00a4",
                    "posPre": "",
                    "posSuf": "\u00a0\u00a4"
                }
            ]
        },
        "id": "ru-ru",
        "localeID": "ru_RU",
        "pluralCat": function(n, opt_precision) {  var i = n | 0;  var vf = getVF(n, opt_precision);  if (vf.v == 0 && i % 10 == 1 && i % 100 != 11) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v == 0 && i % 10 >= 2 && i % 10 <= 4 && (i % 100 < 12 || i % 100 > 14)) {    return PLURAL_CATEGORY.FEW;  }  if (vf.v == 0 && i % 10 == 0 || vf.v == 0 && i % 10 >= 5 && i % 10 <= 9 || vf.v == 0 && i % 100 >= 11 && i % 100 <= 14) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;}
    });
}]);