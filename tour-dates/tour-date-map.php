<?php 
	$today = date('Y-m-d');
	$tour_args = array(
		'post_type' => 'tour_date',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'meta_query' => array(
		  'relation' => 'OR',
		  array(
		    'key' => 'tour-date-from',
		    'value' => $today,
		    'compare' => '>=',
		    'type' => 'DATE'
		  ),
		  array(
		    'key' => 'tour-date-to',
		    'value' => $today,
		    'compare' => '>=',
		    'type' => 'DATE'
		  ),
		),
	);
	
	if ( ! empty( $cat ) ) { $tour_args['category_name'] = $cat; }
	$tour_date_query = new WP_Query( $tour_args );
	$options = get_option('tdp_settings');
?>

<style type="text/css">
.infobox {color: #111;}
.infobox a {color: #343742;float: right;font-weight: bold;}
.infobox a:hover {color: #8188A1;}
.infobox p {margin-bottom: 10px;}
#map img {max-width: none;}
.tdp-date {display:<?php if ($options['tdp_showdate'] !== '1') : echo 'none'; else : echo $options['tdp_showdate']; endif; ?>;}
</style>

<script type="text/javascript">
var tourStylesheet = document.createElement("link");
tourStylesheet.rel = "stylesheet";
tourStylesheet.href = "/wp-content/plugins/tour-dates/style.css";
document.head.appendChild(tourStylesheet);

var fontAwesome = document.createElement("link");
fontAwesome.rel = "stylesheet";
fontAwesome.href = "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css";
document.head.appendChild(fontAwesome);
</script>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo $options['tdp_gmapi']; ?>&sensor=false"></script>
<!--<script type="text/javascript" src="<?php echo network_site_url('wp-content/plugins/tour-dates/map.js', __FILE__); ?>"></script>-->

<script type="text/javascript">
var tdpzoom = <?php if ($options['tdp_zoom'] == '') : echo '2'; else : echo $options['tdp_zoom']; endif; ?>;
var tdpcenter = new google.maps.LatLng(<?php if ($options['tdp_center'] == '') : echo '15.8667131,74.5084405'; else : echo $options['tdp_center']; endif; ?>);
var infowindow = new google.maps.InfoWindow();
var custmarker = new google.maps.MarkerImage('/wp-content/images/marker-v1.png', new google.maps.Size(50, 48) );
var shadow = new google.maps.MarkerImage('/wp-content/themes/mapdemo/shadow.png', new google.maps.Size(2, 3) );

function initialize() {
	var stylez = [
	    {
	        "featureType": "administrative",
	        "elementType": "labels",
	        "stylers": [
	            {
	                "visibility": "off"
	            }
	        ]
	    },
	    {
	        "featureType": "landscape",
	        "elementType": "geometry",
	        "stylers": [
	            {
	                "hue": "#fff100"
	            },
	            {
	                "saturation": "100"
	            },
	            {
	                "lightness": "-16"
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "elementType": "geometry",
	        "stylers": [
	            {
	                "color": "#1F2D4E"
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "elementType": "labels.text.fill",
	        "stylers": [
	            {
	                "color": "#ffffff"
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "elementType": "labels.text.stroke",
	        "stylers": [
	            {
	                "visibility": "off"
	            }
	        ]
	    }
	]

	map = new google.maps.Map(document.getElementById('map'), { 
		zoom: tdpzoom, 
		center: tdpcenter, 
		scrollwheel: false,
		tilt: 45,
		mapTypeControlOptions: {
			mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'Joseph the Musical'] }
	});
	
	var styledMapOptions = { name: "Joseph the Musical" }
	var tdpMapType = new google.maps.StyledMapType( stylez, styledMapOptions);
	
	map.mapTypes.set('Joseph the Musical', tdpMapType);
	map.setMapTypeId('Joseph the Musical');

	for (var i = 0; i < locations.length; i++) {  
		var marker = new google.maps.Marker({
	    	position: locations[i].latlng,
			//icon: custmarker,
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
</script>

		<?php if ( $tour_date_query->have_posts() ) : ?>
			<div style="display: none;">
				<?php $i = 1; ?>
				<?php while ( $tour_date_query->have_posts() ) : $tour_date_query->the_post(); ?>
				<?php $datetoquery = get_post_meta( get_the_ID(), 'tour-date-to', true ); ?>
					<?php if ( get_post_meta(get_the_ID(), 'tour-date-latlng', true) !== '' ) : ?>
						<div id="item<?php echo $i; ?>" class="infobox">
							<table class="tdpmap">
								<tr>
									<td class="tdpmaplabel"><strong><?php if ($options['tdp_citylabel'] == '') : echo 'CITY'; else : echo $options['tdp_citylabel']; endif; ?></strong></td>
									<td><strong><?php the_title(); ?></strong></td>
								</tr>
								<tr>
									<td class="tdpmaplabel"><?php if ($options['tdp_venuelabel'] == '') : echo 'VENUE'; else : echo $options['tdp_venuelabel']; endif; ?></td>
									<td><?php echo get_post_meta(get_the_ID(), 'tour-date-venue', true); ?></td>
								</tr>
								<tr class="tdp-date">
									<td class="tdpmaplabel"><strong>DATE</strong></td>
									<td style="line-height: 1.3em;">From <?php $datefrom = new DateTime( get_post_meta( get_the_ID(), 'tour-date-from', true ) );
										echo $datefrom->format('j M y');
											if ( ! empty ( $datetoquery ) ) { ?><br/>- <?php $dateto = new DateTime( get_post_meta( get_the_ID(), 'tour-date-to', true ) );
										echo $dateto->format('j M y'); } ?></td>
								</tr>
								<tr>
									<td colspan="2"><?php if (get_post_meta(get_the_ID(), 'tour-date-tickets', true) !== '' ) : ?><a href="<?php echo get_post_meta(get_the_ID(), 'tour-date-tickets', true); ?>" class="tdp-btn tourbtn"><i class="fa fa-ticket"></i> BOOK TICKETS &raquo;</a><?php else : ?>COMING SOON<?php endif; ?></td>
								</tr>
							</table>
						</div>
					<?php endif; ?>
					<?php $i++;	?>
				<?php endwhile; ?>
			</div>

			<script type="text/javascript">
				var locations = [
					<?php  $i = 1;
					while ( $tour_date_query->have_posts() ) : $tour_date_query->the_post(); ?>
						<?php if ( get_post_meta(get_the_ID(), 'tour-date-latlng', true) !== '' ) : ?>
							{
								latlng : new google.maps.LatLng(<?php echo get_post_meta(get_the_ID(), 'tour-date-latlng', true); ?>), 
								info : document.getElementById('item<?php echo $i; ?>')
							},
						<?php endif; ?>
					<?php $i++; endwhile; ?>
				];
			</script>
						
			<div id="map" class="hideonmobile" style="width: 100%; height: <?php if (!empty($height)) : echo $height; elseif ($options['tdp_mapheight'] == '') : echo '400px'; else : echo $options['tdp_mapheight']; endif; ?>;"></div>
						
		<?php else : ?>
				<!-- No matching posts, show an error -->
				Error 404 &mdash; Page not found.
		<?php endif; ?>

<script type="text/javascript">
initialize();
</script>