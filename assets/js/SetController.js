(function(){
	'use strict';

	angular
	.module("hallofbeorn")
	.controller("SetController", SetController);

	SetController.$inject = ["$scope", "$http"];

	function SetController($scope, $http){
		$scope.ajaxLoading = false;
		$scope.images = [];
		$scope.set = '';

		const setForm = document.getElementById("setForm");
		const action = document.getElementById("action");
		const set = document.getElementById("set");

		$scope.sanitize = function(name){
			try{
				var temp = name.split(".");
				temp = temp[0].split("_");
				return temp.join(" ");
			}catch(e){
				return name;
			}
		};
		$scope.handleAction = function (act){
			action.value = act;
			switch(act){
				case "show":
					showImages();
					break;
				default:
					setForm.submit();
					break;
			}
		};
		$scope.clearImages = function(){
			$scope.set = '';
			$scope.images = [];
		};
		function showImages(){
			if(!set.value){
				return;
			}
			$scope.set = '';
			$scope.images = [];
			$scope.ajaxLoading = true;
			var toSend = {
				url : "handler.php",
				method : "POST",
				data : {
					"action" : "show",
					"set"    : set.value
				}
			};
			$http(toSend)
			.success(function(data){
				if(data.success){
					$scope.set = set.value;
					$scope.images = data.images;
					$(".fancybox").fancybox({
						'transitionIn'	:	'elastic',
						'transitionOut'	:	'elastic',
						'speedIn'		:	600,
						'speedOut'		:	200,
						'hideOnContentClick': true
					});
				}else{
					alert(data.reason);
				}
				$scope.ajaxLoading = false;
			}).catch(function(err){
				console.warn(err);
				$scope.ajaxLoading = false;
			})
		}
	}
})();