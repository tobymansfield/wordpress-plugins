var infowindow = new google.maps.InfoWindow();
var custmarker = new google.maps.MarkerImage('http://www.catsthemusical.com/australia/wp-content/blogs.dir/5/files/sites/5/2015/05/cats-marker.png', new google.maps.Size(30, 50) );
var shadow = new google.maps.MarkerImage('/wp-content/themes/mapdemo/shadow.png', new google.maps.Size(37, 34) );

function initialize() {
	map = new google.maps.Map(document.getElementById('map'), { 
		zoom: 12, 
		center: tdpcenter, 
		mapTypeId: google.maps.MapTypeId.ROADMAP 
	});

	for (var i = 0; i < locations.length; i++) {  
		var marker = new google.maps.Marker({
	    	position: locations[i].latlng,
			icon: custmarker,
			shadow: shadow,
			map: map
		});
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
		  return function() {
		    infowindow.setContent(locations[i].info);
		    infowindow.open(map, marker);
		  }
		})(marker, i));
	}

}