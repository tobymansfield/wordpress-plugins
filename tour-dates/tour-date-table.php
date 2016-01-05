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
	'orderby' => 'meta_value', 
	'meta_key' => 'tour-date-from'
);


if ( ! empty( $cat ) ) { $tour_args['category_name'] = $cat; }
if ( ! empty( $number ) ) { $tour_args['posts_per_page'] = $number; }

$tour_date_query = new WP_Query( $tour_args );

$options = get_option('tdp_settings');
?>

<style type="text/css">
.tdptable thead th {background: <?php echo $options['tdp_dark_color']; ?>;
  background: -moz-linear-gradient(top, <?php echo $options['tdp_light_color']; ?> 0%, <?php echo $options['tdp_dark_color']; ?> 100%) !important;
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $options['tdp_light_color']; ?>), color-stop(100%,<?php echo $options['tdp_dark_color']; ?>)) !important;
  background: -webkit-linear-gradient(top, <?php echo $options['tdp_light_color']; ?> 0%,<?php echo $options['tdp_dark_color']; ?> 100%) !important;
  background: -o-linear-gradient(top, <?php echo $options['tdp_light_color']; ?> 0%,<?php echo $options['tdp_dark_color']; ?> 100%) !important;
  background: -ms-linear-gradient(top, <?php echo $options['tdp_light_color']; ?> 0%,<?php echo $options['tdp_dark_color']; ?> 100%) !important;
  background: linear-gradient(to bottom, <?php echo $options['tdp_light_color']; ?> 0%,<?php echo $options['tdp_dark_color']; ?> 100%) !important;
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $options['tdp_light_color']; ?>', endColorstr='<?php echo $options['tdp_dark_color']; ?>',GradientType=0 ) !important;}
.tdptable tr:nth-child(even) {background-color: <?php echo $options['tdp_dark_color']; ?> !important;}
.tdptable tr:nth-child(odd) {background-color: <?php echo $options['tdp_light_color']; ?> !important;}
.tdptable td, .tdptable th {color: <?php if ($options['tdp_font_color'] == '') : echo '#ffffff'; else : echo $options['tdp_font_color']; endif; ?>!important;}
.tdp-btn {background: <?php echo $options['tdp_button_color']; ?> !important;}
.tdp-date {display:<?php if ($options['tdp_showdate'] !== '1') : echo 'none'; else : echo $options['tdp_showdate']; endif; ?>;}
.tdp_flag {height: 1em; margin: 0px 0.5em; vertical-align: -10%;}
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
<div class="table-1">
	<table width="100%" class="tdptable">
		<thead>
			<tr>
				<th align="left"><?php if ($options['tdp_citylabel'] == '') : echo 'CITY'; else : echo $options['tdp_citylabel']; endif; ?></th>
				<th class="hideonmob" align="left"><?php if ($options['tdp_venuelabel'] == '') : echo 'VENUE'; else : echo $options['tdp_venuelabel']; endif; ?></th>
				<th align="left" class="tdp-date">DATE</th>
				<th align="center"><i class="fa fa-ticket"></i> TICKETS</th>
			</tr>
		</thead>
		<tbody>
			<?php while ( $tour_date_query->have_posts() ) :
				$tour_date_query->the_post();
				$datetoquery = get_post_meta( get_the_ID(), 'tour-date-to', true );
				?>
				<tr>
					<td>
						<img class="tdp_flag" src="<?php echo get_post_meta( get_the_ID(), 'tour-date-flag', true );?>"><?php the_title(); ?>
					</td>
					<td class="hideonmob">
						<?php $venueurl = get_post_meta( get_the_ID(), 'tour-date-venue-url', true );
							if ( ! empty ( $venueurl ) ) { ?><a href="<?php echo $venueurl; ?>"><?php echo get_post_meta( get_the_ID(), 'tour-date-venue', true );?> <sup><i class="fa fa-external-link"></i></sup></a>
							<?php } else { echo get_post_meta( get_the_ID(), 'tour-date-venue', true ); } ?>
					</td>
					<td class="tour-date-date tdp-date">
						<?php $datefrom = new DateTime( get_post_meta( get_the_ID(), 'tour-date-from', true ) );
								echo $datefrom->format('j M y');
								if ( ! empty ( $datetoquery ) ) { ?><br/>- <?php $dateto = new DateTime( get_post_meta( get_the_ID(), 'tour-date-to', true ) );
								echo $dateto->format('j M y'); } ?>
					</td>
					<td>
						<?php $ticketurl = get_post_meta( get_the_ID(), 'tour-date-tickets', true );
						if ( ! empty ( $ticketurl ) ) { ?>
							<a href="<?php echo $ticketurl; ?>" class="tourbtn tdp-btn" target="_blank">TICKETS &raquo;</a>
						<?php } else { ?>
							<span class="tourbtn tdp-btn nolink">COMING SOON</span>
						<?php } ?>
					</td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
</div>