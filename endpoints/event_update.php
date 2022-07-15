<?php
function api_event_put($request) {
  $user = wp_get_current_user();

  if ($user->data->ID === 0) {
    $response = new WP_Error("error", "User doesn't have permission", ['status' => 401]);
    return rest_ensure_response($response);
  }
  
  $event_id = $request['id'];
  // $event_type = sanitize_text_field($request['type']);
  $event_title = sanitize_text_field($request['title']);
  // $event_date = sanitize_text_field($request['date']);
  // $event_description = sanitize_text_field($request['description']);
  // $event_files = $request->get_file_params();

  $response = [
    'ID' => $event_id,
    // 'post_author' => $user->ID,
    // 'post_type' => 'post',
    // 'post_status' => 'publish',
    'post_title' => $event_title,
    // 'post_content' => $event_description,
    // 'post_category' => [$cat_id],
    // 'files' => $event_files,
    // 'meta_input' => [
    //   'date' => $event_date
    // ]
  ];

  $post_id = wp_update_post($response);

  return rest_ensure_response($request);
}

function register_api_event_put() {
  register_rest_route('api', '/event/update', [
    'methods' => 'POST',
    'callback' => 'api_event_put'
  ]);
}
add_action('rest_api_init', 'register_api_event_put');