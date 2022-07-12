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

function api_events_get($request) {
  $user_id = wp_get_current_user()->ID;
  if ($user_id === 0) {
    $response = new WP_Error('error', "User doesn't have permission", ['status' => 401]);
    return rest_ensure_response($response);
  }

  $_total = sanitize_text_field($request['_total']) ?: 3;
  $_page = sanitize_text_field($request['_page']) ?: 1;
  
  // If events list is public
  // $_user = sanitize_text_field($request['_user']);
  // if (!is_numeric($_user)) {
  //   $user = get_user_by('login', $_user);
  //   if (!$user) {
  //     $response = new WP_Error('error', 'User not found.', ['status' => 404]);
  //     return rest_ensure_response($response);
  //   }
  //   $_user = $user->ID;
  // }

  $args = [
    'post_type' => 'post',
    'author' => $user_id,
    'posts_per_page' => $_total,
    'paged' => $_page
  ];

  $query = new WP_Query($args);
  $posts = $query->posts;

  $events = [];
  if ($posts) {
    foreach ($posts as $event) {
      $events[] = event_data($event);
    }
  }

  return rest_ensure_response($events);
}

function register_api_events_get() {
  register_rest_route('api', '/events', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_events_get'
  ]);
}
add_action( 'rest_api_init', 'register_api_events_get');
?>