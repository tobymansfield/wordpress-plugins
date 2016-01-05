<?php
/**
 * Plugin Name: Simple Tour Dates Plugin.
 * Description: Displays tour dates on a customisable table and/or map.
 * Version: 1.0.0
 * Author: Toby Mansfield
 */
 
  
// CUSTOM POST TYPE
// TOUR DATES
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'tour_date',
    array(
      'labels' => array(
        'name' => __( 'Tour Dates' ),
        'singular_name' => __( 'Tour Date' ),
        'add_new_item' => __( 'City' ),
        'edit_item' => __( 'Edit Tour Date' ),
        'update_item' => __( 'Update Tour Date' ),
      ),
      'taxonomies' => array('category'),
      'public' => true,
      'hierarchical' => false,
      'show_ui' => true,
      'has_archive' => true,
      'query_var' => true,
      'menu_icon' => 'dashicons-list-view',
      'supports' => array(
       	'title',
         'excerpt',
         'revisions',
         'thumbnail',
         'threewp-broadcast')
    )
  );
}


// CREATE META BOX
function add_custom_meta_box()
{
    add_meta_box("demo-meta-box", "Details", "tour_date_markup", "tour_date", "normal", "high", null);
}
 
add_action("add_meta_boxes", "add_custom_meta_box");


// ADD META DATA FIELDS
function tour_date_markup($object)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
 
    ?>
        <div>
            <div style="float: left;width: 50%;"><label for="tour-date-from" style="font-size: 17px;line-height:normal;">From</label>
            <br><span style="color: #aaa;">Required</span>
            <br><input id="from" name="tour-date-from" type="date" value="<?php echo get_post_meta($object->ID, "tour-date-from", true); ?>">
            </div>
            
            <div style="float: left;width: 50%;"><label for="tour-date-to" style="font-size: 17px;line-height:normal;">To</label>
            <br><span style="color: #aaa;">Optional (tour date is hidden <em>after</em> this date)</span>
            <br><input id="to" name="tour-date-to" type="date" value="<?php echo get_post_meta($object->ID, "tour-date-to", true); ?>">
            </div>
            
            <div style="clear: both;"></div>
                        
            <p><label for="tour-date-venue" style="font-size: 17px;line-height:normal;">Venue Name</label>
            <br><span style="color: #aaa;">Required</span>
            <br><input name="tour-date-venue" type="text" value="<?php echo get_post_meta($object->ID, "tour-date-venue", true); ?>" style="width: 97%;">
            </p>
            
            <p>
            <label for="tour-date-venue-url" style="font-size: 17px;line-height:normal;">Venue Website</label>
            <br><span style="color: #aaa;">Optional</span>
            <br><input name="tour-date-venue-url" type="text" value="<?php echo get_post_meta($object->ID, "tour-date-venue-url", true); ?>" style="width: 97%;" placeholder="http://">
            </p>
            
            <p><label for="tour-date-tickets" style="font-size: 17px;line-height:normal;">Ticket URL</label>
            <br><span style="color: #aaa;">Ticket button will read "Coming Soon" if no URL found</span>
            <br><input name="tour-date-tickets" type="text" value="<?php echo get_post_meta($object->ID, "tour-date-tickets", true); ?>" style="width: 97%;" placeholder="http://">
    				</p>
    				
						<p><label for="tour-date-flag" style="font-size: 17px;line-height:normal;">Flag URL</label>
						<br><span style="color: #aaa;">Required - must be 300 x 100px</span>
						<br><input name="tour-date-flag" type="text" value="<?php echo get_post_meta($object->ID, "tour-date-flag", true); ?>" style="width: 97%;" placeholder="http://">
						</p>
    				
    				<p><label for="tour-date-latlng" style="font-size: 17px;line-height:normal;">Lat/Long</label>
    				<br><span style="color: #aaa;">Required for map - without parentheses eg 38.898748, -77.037684</span>
    				<br><input id="latlng" name="tour-date-latlng" type="text" value="<?php echo get_post_meta($object->ID, "tour-date-latlng", true); ?>">
    				</p>
    				
        </div>
    <?php  
}



// SAVE META DATA
function save_tour_date_meta($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
 
    if(!current_user_can("edit_post", $post_id))
        return $post_id;
 
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
 
    $slug = "tour_date";
    if($slug != $post->post_type)
        return $post_id;
 
    $tour_date_city_value = "";
    $tour_date_venue_value = "";
    $tour_date_venue_url_value = "";
    $tour_date_from_value = "";
    $tour_date_to_value = "";
    $tour_date_tickets_value = "";
    $tour_date_flag_value = "";
    $tour_date_latlng_value = "";
 
    if(isset($_POST["tour-date-tickets"]))
    {
        $tour_date_tickets_value = $_POST["tour-date-tickets"];
    }   
    update_post_meta($post_id, "tour-date-tickets", $tour_date_tickets_value);

    if(isset($_POST["tour-date-flag"]))
    {
        $tour_date_flag_value = $_POST["tour-date-flag"];
    }   
    update_post_meta($post_id, "tour-date-flag", $tour_date_flag_value);
    
    if(isset($_POST["tour-date-venue"]))
    {
        $tour_date_venue_value = $_POST["tour-date-venue"];
    }   
    update_post_meta($post_id, "tour-date-venue", $tour_date_venue_value);
    
    if(isset($_POST["tour-date-venue-url"]))
    {
        $tour_date_venue_url_value = $_POST["tour-date-venue-url"];
    }   
    update_post_meta($post_id, "tour-date-venue-url", $tour_date_venue_url_value);
    
    if(isset($_POST["tour-date-from"]))
    {
        $tour_date_from_value = $_POST["tour-date-from"];
    }   
    update_post_meta($post_id, "tour-date-from", $tour_date_from_value);
        
    if(isset($_POST["tour-date-to"]))
    {
        $tour_date_to_value = $_POST["tour-date-to"];
    }   
    update_post_meta($post_id, "tour-date-to", $tour_date_to_value);
    
    if(isset($_POST["tour-date-latlng"]))
    {
        $tour_date_latlng_value = $_POST["tour-date-latlng"];
    }   
    update_post_meta($post_id, "tour-date-latlng", $tour_date_latlng_value);
        
}
 
add_action("save_post", "save_tour_date_meta", 10, 3);


// ADD CUSTOM COLUMNS
add_filter('manage_edit-tour_date_columns', 'add_new_tour_date_columns');
function add_new_tour_date_columns($tour_date_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';
    $new_columns['title'] = _x('City', 'column name');
    $new_columns['venue'] = __('Venue'); 
    $new_columns['from'] = __('Start Date'); 
    return $new_columns;
}

add_action('manage_tour_date_posts_custom_column', 'manage_tour_date_columns', 10, 2);
function manage_tour_date_columns($column_name, $post_id) {
    switch ($column_name) {
    case 'venue':
        echo get_post_meta( $post_id, 'tour-date-venue', true );
            break;
		case 'from':
		    echo get_post_meta( $post_id, 'tour-date-from', true );
		        break;
		default:
        break;
    } // end switch
}


// SORT COLUMNS BY DATE
add_filter( 'manage_edit-tour_date_sortable_columns', 'my_sortable_tour_date_column' );
function my_sortable_tour_date_column( $columns ) {
    $columns['from'] = 'from';
    return $columns;
}

add_action( 'pre_get_posts', 'my_date_orderby' );
function my_date_orderby( $query ) {
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby');
 
    if( 'from' == $orderby ) {
        $query->set('meta_key','tour-date-from');
        $query->set('orderby','meta_value');
    }
}


// BUFFERED SHORTCODES
function tourdates_shortcode( $atts, $content = null ) {
	ob_start();
		extract(shortcode_atts(array(
		    "cat" => '',
		    "number" => '',
		  ), $atts));
		include 'tour-date-table.php';
	return ob_get_clean();
}
add_shortcode( 'tourdates', 'tourdates_shortcode' );


function tourdatesmap_shortcode( $atts, $content = null ) {
	ob_start();
		extract(shortcode_atts(array(
		    "cat" => '',
		    "height" => ''
		  ), $atts));
		include 'tour-date-map.php';
	return ob_get_clean();
}
add_shortcode( 'tourdatesmap', 'tourdatesmap_shortcode' );

	
?>
<?php

// ADD OPTIONS PAGE
add_action( 'admin_menu', 'tdp_add_admin_menu' );
add_action( 'admin_init', 'tdp_settings_init' );


function tdp_add_admin_menu(  ) { 

	add_options_page( 'Tour Date Options', 'Tour Date Options', 'manage_options', 'tour_dates', 'tdp_options_page' );

}


function tdp_settings_init(  ) { 

	register_setting( 'pluginPage', 'tdp_settings' );

	add_settings_section(
		'tdp_pluginPage_section', 
		__( 'Settings', 'wordpress' ), 
		'tdp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'tdp_light_color', 
		__( 'Light Color (hex)', 'wordpress' ), 
		'tdp_light_color_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);

	add_settings_field( 
		'tdp_dark_color', 
		__( 'Dark Color (hex)', 'wordpress' ), 
		'tdp_dark_color_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);

	add_settings_field( 
		'tdp_font_color', 
		__( 'Font Color (hex)', 'wordpress' ), 
		'tdp_font_color_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);

	add_settings_field( 
		'tdp_button_color', 
		__( 'Button Color (hex)', 'wordpress' ), 
		'tdp_button_color_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
	
	add_settings_field( 
		'tdp_citylabel', 
		__( 'CITY label override', 'wordpress' ), 
		'tdp_citylabel_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
	
	add_settings_field( 
		'tdp_venuelabel', 
		__( 'VENUE label override', 'wordpress' ), 
		'tdp_venuelabel_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
		
	add_settings_field( 
		'tdp_center', 
		__( 'Map Center (lat/long)', 'wordpress' ), 
		'tdp_center_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
		
	add_settings_field( 
		'tdp_zoom', 
		__( 'Map Zoom (0-19)', 'wordpress' ), 
		'tdp_zoom_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
	
	add_settings_field( 
		'tdp_mapheight', 
		__( 'Map Height', 'wordpress' ), 
		'tdp_mapheight_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
	
	add_settings_field( 
		'tdp_gmapi', 
		__( 'Google Maps API Key', 'wordpress' ), 
		'tdp_gmapi_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);
	
	add_settings_field( 
		'tdp_showdate', 
		__( 'Display date?', 'wordpress' ), 
		'tdp_showdate_render', 
		'pluginPage', 
		'tdp_pluginPage_section' 
	);

}


function tdp_light_color_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_light_color]' value='<?php echo $options['tdp_light_color']; ?>' placeholder="#">
	<?php

}


function tdp_dark_color_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_dark_color]' value='<?php echo $options['tdp_dark_color']; ?>' placeholder="#">
	<?php

}


function tdp_font_color_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_font_color]' value='<?php echo $options['tdp_font_color']; ?>' placeholder="#">
	<?php

}


function tdp_button_color_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_button_color]' value='<?php echo $options['tdp_button_color']; ?>' placeholder="#">
	<?php

}


function tdp_citylabel_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_citylabel]' value='<?php echo $options['tdp_citylabel']; ?>' placeholder="CITY">
	<?php

}


function tdp_venuelabel_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_venuelabel]' value='<?php echo $options['tdp_venuelabel']; ?>' placeholder="VENUE">
	<?php

}


function tdp_center_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_center]' value='<?php echo $options['tdp_center']; ?>'>
	<?php

}


function tdp_zoom_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_zoom]' value='<?php echo $options['tdp_zoom']; ?>' placeholder="2">
	<?php

}


function tdp_gmapi_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_gmapi]' value='<?php echo $options['tdp_gmapi']; ?>'>
	<?php

}


function tdp_mapheight_render(  ) { 

	$options = get_option( 'tdp_settings' );
	?>
	<input type='text' name='tdp_settings[tdp_mapheight]' value='<?php echo $options['tdp_mapheight']; ?>' placeholder="400px">
	<?php

}


function tdp_showdate_render(  ) { 

	$options = get_option( 'tdp_settings' );
	$datehtml = '<input type="checkbox" id="tdp_showdate" name="tdp_settings[tdp_showdate]" value="1"' . checked( 1, $options['tdp_showdate'], false ) . '/>'; 
	echo $datehtml;

}


function tdp_settings_section_callback(  ) { 

	echo __( 'Customise your tour date list and map.', 'wordpress' );

}


function tdp_options_page(  ) { 

	?><form action='options.php' method='post'>
		
		<h2>Tour Date Options</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form><?php

}

?>