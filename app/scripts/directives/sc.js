angular.module('soundmapApp')
	.directive('scWidget',  function factory() {
		return {
			priority: 0,
			template: '<iframe ng-transclude width="100%" height="166" scrolling="no" class="uninitialized" frameborder="no" src="//w.soundcloud.com/player/?url="></iframe>',
			replace: false,
			transclude: true,
			restrict: 'AC',
			scope: {
				src : '=url',
				options : '=options'
			},
			link: function postLink(scope, element, attrs) {
				var iframe = element.children()[0],
					widget;

				function initWidget(){
					widget = SC.Widget(element.children()[0]);
		//			iframe.classList.remove('uninitialized');
				}

				scope.$watch('src', function(newVal, oldVal){
					if(widget) {
						var originalCallback = scope.options.callback,
							options = angular.extend({}, scope.options, {
								callback : function(){
									console.log('callback', arguments);
									originalCallback && originalCallback.apply(this, arguments);
								}
							});
						console.log(newVal, options);

						widget.load(scope.src, options);
						
						console.log($(iframe).attr('class'),iframe);
						$(iframe).removeClass('uninitialized');
					}
				});

				initWidget();
			}
		};
	});