<?php
function api_event_post($request) {
  $user = wp_get_current_user();

  if ($user->ID === 0) {
    $response = new WP_Error("error", "User doesn't have permission", ['status' => 401]);
    return rest_ensure_response($response);
  }

  $event_type = sanitize_text_field($request['type']);
  $event_title = sanitize_text_field($request['title']);
  $event_date = sanitize_text_field($request['date']);
  $event_description = sanitize_text_field($request['description']);

  if (empty($event_title)) {
    $response = new WP_Error('error', 'Missing data', ['status' => 422]);
    return rest_ensure_response($response);
  }

  $response = [
    'post_author' => $user->ID,
    'post_type' => 'post',
    'post_status' => 'publish',
    'post_title' => $event_title,
    'post_content' => $event_description,
    // 'files' => $files,
    'meta_input' => [
      'date' => $event_date,
      'category' => $event_type
    ]
  ];

  $post_id = wp_insert_post($response);

  return rest_ensure_response($response);
}

function register_api_event_post() {
  register_rest_route('api', '/event', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_event_post'
  ]);
}
add_action('rest_api_init', 'register_api_event_post');