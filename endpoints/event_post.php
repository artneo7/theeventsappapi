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
  $event_files = $request->get_file_params();

  if (empty($event_title) || empty($event_date)) {
    $response = new WP_Error('error', 'Missing data', ['status' => 422]);
    return rest_ensure_response($response);
  }

  // Check if category already exists
  $cat_id = get_cat_ID($event_type);

  // if doesn't exist, create a new one
  if ($cat_id === 0) {
    $new_cat = wp_insert_term($event_type, "category");
    $cat_id = $new_cat['term_id'];
  }

  $response = [
    'post_author' => $user->ID,
    'post_type' => 'post',
    'post_status' => 'publish',
    'post_title' => $event_title,
    'post_content' => $event_description,
    'post_category' => [$cat_id],
    'files' => $event_files,
    'meta_input' => [
      'date' => $event_date
    ]
  ];

  $post_id = wp_insert_post($response);

  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';

  $photo_id = media_handle_upload('img', $post_id);
  update_post_meta($post_id, 'img', $photo_id);

  return rest_ensure_response($response);
}

function register_api_event_post() {
  register_rest_route('api', '/event', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_event_post'
  ]);
}
add_action('rest_api_init', 'register_api_event_post');