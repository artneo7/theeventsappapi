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