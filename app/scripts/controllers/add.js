'use strict';

angular.module('soundmapApp')
	.controller('AddCtrl', function ($scope, $timeout, $log, user) {
	    // Enable the new Google Maps visuals until it gets enabled by default.
	    // See http://googlegeodevelopers.blogspot.ca/2013/05/a-fresh-new-look-for-maps-api-for-all.html
	    google.maps.visualRefresh = true;
		user.getTracks();

		var sound = {
			title : '',
			coords : {},
			url : '',
			description : '',
			id : ''
		};



		
		var map = {
		    position: {
				coords: {
					latitude : 47.15143535829049,
					longitude : 27.59490966796875
				}
		    },
			
			/** the initial center of the map */
			centerProperty: {
				latitude : 47.15143535829049,
				longitude : 27.59490966796875
			},
			
			/** the initial zoom level of the map */
			zoomProperty: 8,
			
			/** list of markers to put in the map */
			markersProperty: [],
			
			// These 2 properties will be set when clicking on the map
			clickedLatitudeProperty: null,	
			clickedLongitudeProperty: null,
			
			eventsProperty: {
				click: function (mapModel, eventName, originalEventArgs) {	
					window.AA = arguments;
					angular.extend($scope.sound.coords,{
						latitude : originalEventArgs[0].latLng.lat(),
						longitude : originalEventArgs[0].latLng.lng()
					});
					$scope.$apply();
				}
			},

			onMarkerClick : function(marker, url){
				console.log('markcer click', arguments);
				location.url = url;
				window.M = marker;
			/*	widget.load(url, {
					auto_play : true
				});*/
				$scope.$apply();
			}
		};

		var searchTypeTimeout,
			geocoder;

		$scope.$watch('searchedLocation', function(){
			$timeout.cancel(searchTypeTimeout);
			searchTypeTimeout = $timeout(function(){
				if(!$scope.searchedLocation)
					return;
 				geocoder = geocoder || new google.maps.Geocoder();
 				geocoder.geocode({'address': $scope.searchedLocation}, function(results, status){
				    if (status == google.maps.GeocoderStatus.OK) {
				    	$scope.map.position.coords = {
				    		latitude : results[0].geometry.location.lat(),
				    		longitude : results[0].geometry.location.lng()
				    	};
				    	$scope.$apply();
				    } else {
				      alert('Geocode was not successful for the following reason: ' + status);
				    }
				});
				
			}, 500);
		});


/*
		$scope.$watch(function(){

		});*/


		angular.extend($scope, {
			map : map,
			location : location,
			login : function(){
				user.login(function(){
					console.log('logged in');
					$scope.$apply();
				});
			},
			logout : function(){
				user.logout.apply(user, arguments);
			},
			logManage : function(){
				return user.loggedIn?$scope.logout():$scope.login();
			},
			user : user,

			selectTrack : function($event, track){
				$event.preventDefault();

				angular.extend($scope.sound, {
					url : track.permalink_url,
					id : track.id,
					description : track.description,
					title : track.title
				});

			//	$scope.$apply();
			},

			saveSound : function(){
				$log.log('sould save', $scope.sound);
			},

			sound : sound
		});

		window.scope = $scope;
});