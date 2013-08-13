angular.module('soundmapApp')
	.service('user', function(){
		api = {
			login : function(callback){
				SC.initialize({
					client_id: '9b9d346419c1d9931de96a21d481a033',
					redirect_uri: 'http://192.168.1.148:9000/'
				});

				// initiate auth popup
				SC.connect(function() {
					SC.get('/me', function(me) {
						api.info = me;
						api.loggedIn = true;
						callback && callback.apply(this, arguments);
					});
				});
			},
			logout : function(){

			},
			getTracks : function(callback){
				if(api.loggedIn)
					SC.get('/users/'+ api.info.id +'/tracks', function(tracks){
						api.trackList = tracks;
						callback && callback.apply(this, arguments);
					});
				else
					callback && callback(null);
			},
			info : null,
			loggedIn : false
		};
		return api;
	});
