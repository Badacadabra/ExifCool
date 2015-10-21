PhotoWall = {
	
	/**
	 * Initialisation des variables et fonctions
	 * **/
	
	/**
	 * Configuration du photowall
	 * */
	config : function (data) {
		$("#photowall" ).photocols({
			"data": data,
			height: 1080,
			colswidth: 400,
			titleSize: 18,
			subtitleSize: 16,
			opacity : 0.4
		});
	},
	startWorker : function () {
		var data;
		if(typeof(Worker) !== "undefined") {
			if(typeof(w) == "undefined") {
				w = new Worker("ui/js/web_worker.js");
			}
			w.onmessage = function(event) {
			  PhotoWall.config(event.data);
			};
		} else {
			console.log("Sorry! No Web Worker support.");
		}
		return true;
	},
	stopWorker : function () {
		w.terminate();
		w = undefined;
	}
	
}
$( document ).ready(function() {
	PhotoWall.startWorker();
});
