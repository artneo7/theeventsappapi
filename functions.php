<?php
// Remove access to /json/wp/v2/users
add_filter('rest_endpoints', function ($endpoints) {
  unset($endpoints['/wp/v2/users']);
  unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);

  return $endpoints;
});

$dirbase = get_template_directory();
require_once $dirbase . '/endpoints/user_post.php';

function change_api() {
  return 'json';
}
add_filter('rest_url_prefix', 'change_api');

function expire_token() {
  return time() + (60 * 60 * 24);
}
add_action('jwt_auth_expire', 'expire_token');
