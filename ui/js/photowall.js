PhotoWall = {
	
	init : function () {
		var data = [
					  { 'title' : 'Chicago Lighthouse' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Chicago_lighthouse.jpg' },
					  { 'title' : 'Cupertino Apple' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Cupertino_Apple.jpg' },
					  { 'title' : 'Dallas' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Dallas.jpg' },
					  { 'title' : 'Detroit' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Detroit.jpg' },
					  { 'title' : 'Grand Canyon National Park' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Grand_Canyon_National_Park.jpg' },
					  { 'title' : 'Joshua tree National_Park' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Joshua_tree_National_Park.jpg' },
					  { 'title' : 'Lincoln Memorial' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Lincoln_Memorial.jpg' },
					  { 'title' : 'MIT Stata center' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/MIT_Stata_center.jpg' },
					  { 'title' : 'Montreal' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Montreal.jpg' },
					  { 'title' : 'Mountain View Googleplex' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Mountain_View_Googleplex.jpg' },
					  { 'title' : 'White Sands' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/White_Sands.jpg' },
					  { 'title' : 'Yosemite National park' , 'subtitle' : 'auteur...' , 'url' : '?a=detail' , 'img' :  'ui/images/photos/Yosemite_National_park.jpg' }
					];
		// Configuration du photowall
		$( "#photowall" ).photocols({
			"data": data,
			height: 1080,
			colswidth: 400,
			titleSize: 18,
			subtitleSize: 16,
			opacity : 0.4
		});
	}
}

$( document ).ready(function() {
	PhotoWall.init();
});
