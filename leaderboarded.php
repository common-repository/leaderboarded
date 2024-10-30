<?php
/**
 * @package Leaderboarded
 * @version 0.1readme.txt
 */
/*
Plugin Name: Leaderboarded
Plugin URI: http://help.leaderboarded.com/knowledgebase/articles/840126-embedding-the-leaderboard-as-a-wordpress-widget
Description: Allows you to embed a Rise.global Leaderboard in your Wordpress blog
Author: Toby Beresford
Version: 0.28
Author URI: http://www.tobyberesford.com
*/

// We need some CSS for colors and what not
// add_action( 'wp_head', 'leaderboarded_css' );
function leaderboarded_css() {
	echo '
<style>	
</style>
	';
}

// add_action('admin_menu', 'leaderboarded_admin_settings');
function leaderboarded_admin_settings() {
	add_menu_page(  
		'Leaderboarded Settings',				// The title to be displayed in the browser window for this page.  
		'Leaderboarded Settings',				// The text to be displayed for this menu item  
		'add_users',							// Which type of users can see this menu item  
		'leaderboarded_settings',				// The unique ID - that is, the slug - for this menu item  
		'leaderboarded_admin_settings_page',	// The name of the function to call when rendering this menu's page,
		NULL,									// Menu Icon URL
		NULL 									// Menu Position
    );
}

function leaderboarded_admin_settings_page() {
	echo 'hello';
}

add_shortcode('leaderboarded', 'leaderboarded_board');
function leaderboarded_board($atts) {
	// Merge Shortcode Parameters with some default ones
	extract(shortcode_atts(array(
		'slug' => 'gurus',
		'release' => 'latest',
		'theme' => 'default',
		'format' => 'embed',
		'cut' => 'cut',
	), $atts));
	
	//check Curl is installed
	if (!(leaderboarded_is_curl_installed())) {
	  return "Plugin cannot run since cURL, a php library that is used to retrieve data from other servers, has not been installed on this server. Please contact your server administrator for more details.";
	}

	//check if caching is enabled
	$bool_use_cache = get_option( 'leaderboarded_use_caching' );

	// Create the URL for the Leaderboard that we're going to get
	$url = 'https://www.rise.global/display/' . $slug . '/' . $release . '/' . $format . '/' . $cut . '/' . $theme;

	//if its enabled then try and get a cached version first
	if ($bool_use_cache) {
		// Create the Cache Key
		$cacheKey = 'leaderboarded:' . $slug . ':' . $release . ':' . $format . ':' . $cut . ':' . $theme;

		// Try getting the Cached Version (daily one)
		$theBody = wp_cache_get($cacheKey . ':' . 'd', 'leaderboarded');
	
		// Try getting the Cached Version (weekly one)
		if ($theBody === false) {
			$theBody = wp_cache_get($cacheKey . ':' . 'w', 'leaderboarded');
		}
	} else {
		$theBody = false;
	}
	
	// Check we got something, if we didn't then try fetching a live one
	if ($theBody === false) {
		// Try fetching the Leaderboard from Rise.global
		
		$use_unsigned =  get_option( 'leaderboarded_use_unsigned' );
		
		if ($use_unsigned) {
			$wp_get_params = array('unsigned'=>'unsigned');
		} else {
			$wp_get_params = array();
		}
		$theBody = wp_remote_get($url, $wp_get_params);
		
		// Show a message when we get an error
		if (is_wp_error($theBody)) {
			return 'The leaderboard is not currently available, please try back later or refresh the page.';
		} else {
			// Grab the Body and Cache the Leaderboard
			$theBody = wp_remote_retrieve_body($theBody);

			// Cache both a daily and weekly copy
			if ($bool_use_cache) {
				wp_cache_set($cacheKey . ':' . 'd', $theBody, 'leaderboarded', 3600);
				wp_cache_set($cacheKey . ':' . 'w', $theBody, 'leaderboarded', (86400 * 7));
			}
		}
	}

	// Return the Leaderboard
	return $theBody;
}
/**
 * Support shortcodes within the text widget
 *
 */
add_filter('widget_text', 'do_shortcode');


/* Helper functions */

// Check Curl is installed
function leaderboarded_is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}

//Provide an option to use unsigned HTTPS requests
add_filter( 'http_request_args', '_leaderboarded_overwrite_http_request_params', 10, 2 );

function _leaderboarded_overwrite_http_request_params($params, $url) {
	// find out if this is the request you are targeting and if not: abort
    if ((!(isset($params['unsigned']))) or ( 'unsigned' !== $params['unsigned'] )) {
         return $params;
    }

    add_filter( 'https_ssl_verify', '__return_false' );

    return $params;
}
/* Settings menu */
add_action( 'admin_menu', 'leaderboarded_plugin_menu' );

/** Step 1. */
function leaderboarded_plugin_menu() {
	add_options_page( 'Leaderboarded Options', 'Leaderboarded', 'manage_options', 'leaderboarded_plugin_options', 'leaderboarded_options' );
}

/** Step 3. */
function leaderboarded_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

   $hidden_field_name = 'leaderboarded_submit_hidden';

    

	    // CACHING SETTING 
	    $data_field_name = 'leaderboarded_use_caching_field';
	    $opt_name[$data_field_name] = 'leaderboarded_use_caching';
		// UNSIGNED SETTING
	    $unsigned_field_name = 'leaderboarded_use_unsigned_field';       
        $opt_name[$unsigned_field_name] = 'leaderboarded_use_unsigned';
        
	    // Read in existing option values from database
		$opt_val[$data_field_name] = get_option( $opt_name[$data_field_name] );        
        $opt_val[$unsigned_field_name] = get_option( $opt_name[$unsigned_field_name] );     
        
        // See if the user has posted us some information
	    // If they did, this hidden field will be set to 'Y'
	    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
	        // Read their posted values
	        if (isset($_POST[ $data_field_name ])) {
	        	$opt_val[$data_field_name] = $_POST[ $data_field_name ];
	        } else {
		        $opt_val[$data_field_name] = 0;
	        }
	        // Save the posted values in the database
	        update_option( $opt_name[$data_field_name], $opt_val[$data_field_name] );
			
		    $opt_val[$unsigned_field_name] = $_POST[ $unsigned_field_name ];
	        update_option( $opt_name[$unsigned_field_name], $opt_val[$unsigned_field_name] );
	        
	        
	        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Settings saved.', 'leaderboarded_options' ); ?></strong></p></div>
<?php

		// Did they ask to clear cache
		$bool_clear_cache = $_POST[ 'clear_cache_now' ];
		
		if ($bool_clear_cache) {
			
			//clear all the caches Rodney!
			wp_cache_flush();
			// Put an settings updated message on the screen

			?>
			<div class="updated"><p><strong><?php _e('Cache cleared.', 'leaderboarded_options' ); ?></strong></p></div>
			<?php

		}
		 

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Leaderboarded Plugin Settings', 'leaderboarded_options' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Cache leaderboards to improve performance:", 'leaderboarded_options' ); ?> 
<input type="checkbox" name="<?php echo $data_field_name; ?>" value="1" <?php if ($opt_val[$data_field_name]) {echo "checked";} ?>>
</p>
<p><?php _e("Use unsigned verification (no SSL) when retrieving latest leaderboard from Rise:", 'leaderboarded_options' ); ?> 
<input type="checkbox" name="<?php echo $unsigned_field_name; ?>" value="1" <?php if ($opt_val[$unsigned_field_name]) {echo "checked";} ?>>
</p>
<p><?php _e("Clear cache now (note this flushes all caches for your wordpress site)", 'leaderboarded_options' ); ?> 
<input type="checkbox" name="clear_cache_now" value="1">
</p><hr />
<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}

?>