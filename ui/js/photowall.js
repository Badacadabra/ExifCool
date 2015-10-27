PhotoWall = {
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
		if(typeof(Worker) !== "undefined") {
			if(typeof(w) == "undefined") {
				w = new Worker("ui/js/web_worker.js");
			}
			w.onmessage = function(event) {
				res = event.data;
                if (res.code == 200) {
                    $( ".ui.active.page.dimmer" ).hide();
                    PhotoWall.config(res.data);
                }
                else
                    alert(res.message);
			};
		} else {
			console.log("Nous sommes désolés. Les web workers ne sont pas gérés par votre navigateur.");
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
