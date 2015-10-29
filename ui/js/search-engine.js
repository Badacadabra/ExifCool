// Gestion du module de recherche
$('.ui.search')
	.search({
		maxResults: 10,
		type : 'category',
		apiSettings : {
		  url: '?a=search&q={query}',
		  onResponse : function(serverResponse) {
			var response = { results : {} };
			if(!serverResponse || !serverResponse.data) {
			  return;
			}
			// Transformation de la réponse du serveur
			// afin qu'elle fontionne avec le search
			$.each(serverResponse.data, function(index, item) {
			  var title   = item.title || 'Unknown',
				  maxResults = 7;
			  if(index >= maxResults) {
				return false;
			  }
			  // création d'une nouvelle catégorie de nom
			  if(response.results[title] === undefined) {
				response.results[title] = {
				  results : []
				};
			  }
			  // ajout du résultat à la catégorie
			  response.results[title].results.push({
				title : item.title,
				url : item.url
			  });
			});
			return response;
		  }
		},
		onSelect: function(result, response) {
			location.href = result.url;
		}
	});
