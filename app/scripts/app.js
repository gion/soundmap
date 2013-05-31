'use strict';

angular.module('soundmapApp', ['google-maps'])
    .config(function ($routeProvider) {
        $routeProvider
        .when('/', {
            templateUrl: 'views/main.html',
            controller: 'MainCtrl'
            })
        .when('/add', {
            templateUrl: 'views/add.html',
            controller: 'MainCtrl'
            })
        .otherwise({
          redirectTo: '/'
        });
    });
