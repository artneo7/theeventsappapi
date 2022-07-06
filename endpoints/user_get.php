<?php
function api_user_get($request) {
  $user = wp_get_current_user();

  if ($user->ID === 0) {
    $response = new WP_Error('error', "User doesn't have access", ['status' => 401]);
    return rest_ensure_response($response);
  }

  $response = [
    "id" => $user->ID,
    "username" => $user->user_login,
    "name" => $user->display_name,
    "email" => $user->user_email
  ];
  
  return rest_ensure_response($response);
}

function register_api_user_get() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_user_get',
  ]);
}
add_action( 'rest_api_init', 'register_api_user_get');
?>