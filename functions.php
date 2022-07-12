<?php
// Remove access to /json/wp/v2/users
add_filter('rest_endpoints', function ($endpoints) {
  unset($endpoints['/wp/v2/users']);
  unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);

  return $endpoints;
});

$dirbase = get_template_directory();
require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

require_once $dirbase . '/endpoints/event_post.php';
require_once $dirbase . '/endpoints/events_get.php';

function change_api() {
  return 'json';
}
add_filter('rest_url_prefix', 'change_api');

function expire_token() {
  return time() + (60 * 60 * 24 * 30);
}
add_action('jwt_auth_expire', 'expire_token');

// Disable Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false' );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

// Custom image sizes
function artneo_image_sizes() {
	add_image_size( 'main', 280, 280, ['center', 'center']);
  update_option('medium_large_size_w', '0');
  update_option('medium_large_size_h', '0');
	remove_image_size( '1536x1536' );
	remove_image_size( '2048x2048' );
}
add_action('after_setup_theme', 'artneo_image_sizes');

// Resize original uploaded image
function replace_uploaded_image($image_data)
{
	// if there is no large image : return
	if ( !isset($image_data['sizes']['main']) ) 
	return $image_data;
	
	// paths to the uploaded image and the large image
	$upload_dir = wp_upload_dir();
	$uploaded_image_location = $upload_dir['basedir'] . '/' . $image_data['file'];
	$large_image_location    = $upload_dir['path'] . '/' . $image_data['sizes']['main']['file'];
	
	// delete the uploaded image
	unlink($uploaded_image_location);
	
	// rename the large image
	rename($large_image_location, $uploaded_image_location);
	
	// update image metadata and return them
	$image_data['width']  = $image_data['sizes']['main']['width'];
	$image_data['height'] = $image_data['sizes']['main']['height'];
	unset($image_data['sizes']['main']);
	
	return $image_data;
}
add_filter('wp_generate_attachment_metadata','replace_uploaded_image');