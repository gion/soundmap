'use strict';

angular.module('soundmapApp')
	.factory('songManager', function($resource){

		var url = 'http://bogdang.users.projects-directory.com/soundmap/app/service/service.php/songs/:songId',
			props = {
				songId: '@id'
			},
			actions = {
				get: {
					method: 'GET',
          isArray: false
				},
				save: {
					method: 'PUT'
				},
				query: {
					method: 'GET',
					isArray: true
				},
				remove: {
					method: 'DELETE'
				},
				delete: {
					method: 'DELETE'
				}
			};

		return $resource(url, props, actions);
	});
