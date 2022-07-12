<?php
function event_data($post) {
  $post_meta = get_post_meta($post->ID);
  $img = wp_get_attachment_image_src($post_meta['img'][0], 'main')[0];
  $date = $post_meta['date'];
  $user = get_userdata($post->post_author);

  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'description' => $post->post_content,
    'date' => $date[0],
    'img' => $img,
  ];
}

function api_event_get($request) {
  $post_id = $request['id'];
  $post = get_post($post_id);

  if (!isset($post) || empty($post_id) ) {
    $response = new WP_Error('error', 'Event not found.', ['status' => 404]);
    return rest_ensure_response($response);
  }

  $event = event_data($post);

  return rest_ensure_response($event);
}

function register_api_event_get() {
  register_rest_route('api', '/event/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_event_get'
  ]);
}
add_action( 'rest_api_init', 'register_api_event_get');
?>