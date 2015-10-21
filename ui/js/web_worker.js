var dico;
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
   if (xhttp.readyState == 4 && xhttp.status == 200) {
		data = JSON.parse(xhttp.responseText);
		postMessage(data);
	}
}
xhttp.open("GET","../../?a=ajaxHome", true);
xhttp.send();
