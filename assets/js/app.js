/**
 * Created by Ignatis on 17/1/2017.
 */
(function(){
	'use strict';

	angular
	.module("hallofbeorn", [])
	.constant('paramEncode', paramEncode)
	.config(config)
	.directive('errSrc', errSrc);

	function errSrc() {
		return {
			link: function(scope, element, attrs) {
				element.bind('error', function() {
					if (attrs.src !== attrs.errSrc) {
						attrs.$set('src', attrs.errSrc);
					}
				});

				attrs.$observe('ngSrc', function(value) {
					if (!value && attrs.errSrc) {
						attrs.$set('src', attrs.errSrc);
					}
				});
			}
		}
	}

	function paramEncode(obj) {
		var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

		for(name in obj) {
			value = obj[name];

			if(value instanceof Array) {
				for(i=0; i<value.length; ++i) {
					subValue = value[i];
					fullSubName = name + '[' + i + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += paramEncode(innerObj) + '&';
				}
			}
			else if(value instanceof Object) {
				for(subName in value) {
					subValue = value[subName];
					fullSubName = name + '[' + subName + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += paramEncode(innerObj) + '&';
				}
			}
			else if(value !== undefined && value !== null)
				query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
		}

		return query.length ? query.substr(0, query.length - 1) : query;
	}

	config.$inject = ['$httpProvider','paramEncode'];

	function config($httpProvider, paramEncode){
		$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

		$httpProvider.defaults.transformRequest = [function(data) {
			return angular.isObject(data) && String(data) !== '[object File]' ? paramEncode(data) : data;
		}];
	}

})();