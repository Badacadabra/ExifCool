var dico;
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
   if (xhttp.readyState == 4 && xhttp.status == 200) {
		dico = JSON.parse(xhttp.responseText);
		postMessage(dico.length);
	}
}
xhttp.open("GET","../data/dictionnaire.json", true);
xhttp.send();

//postMessage("Bonjour");

/*onmessage = function(ev) {
    var word = ev.data;
    //postMessage('mot reçu');
	if(dico.indexOf(word) > -1) {
		postMessage("Le mot <b>"+word+"</b>"+" "+"existe bien dans le dico");
	} else {
		postMessage("None");
	}
  }*/
