'use strict';

angular.module('soundmapApp', ['ngResource', 'google-maps'])
    .config(function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: 'views/main.html',
                controller: 'MainCtrl',
                reloadOnSearch : false
            })
            .when('/add', {
                templateUrl: 'views/add.html',
                controller: 'AddCtrl'
            })
            .otherwise({
              redirectTo: '/'
            });
    })
    .run( function($rootScope, $location, $http) {

        // register listener to watch route changes
        $rootScope
            .$on( "$routeChangeStart", function(event, next, current) {

                // keep a reference to the current controller's name in the root scope
                $rootScope.currentController = (next.$route || next.$$route).controller;

                // restrict access to other pages to any unauthorized user
                if (!$rootScope.loggedUser && $rootScope.currentController != "MainCtrl" ) {
                    $location.path( "/" );
                }
            });
     });
