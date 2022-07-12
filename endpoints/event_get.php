<?php
function photo_data($post) {
  $post_meta = get_post_meta($post->ID);
  $img = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
  $date = $post_meta['date'];
  $user = get_userdata($post->post_author);
  $total_comments = get_comments_number($post->ID);

  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'description' => $post->post_description,
    'date' => $post->post_date,
    'img' => $img,
  ];
}

function api_photo_get($request) {
  $post_id = $request['id'];
  $post = get_post($post_id);

  if (!isset($post) || empty($post_id) ) {
    $response = new WP_Error('error', 'Post não encontrado.', ['status' => 404]);
    return rest_ensure_response($response);
  }

  $photo = photo_data($post);

  $photo['acessos'] = (int) $photo['acessos'] + 1;
  update_post_meta($post_id, 'acessos', $photo['acessos']);

  $comments = get_comments([
    'post_id' => $post_id,
    'order' => 'ASC'
  ]);
  
  $response = [
    'photo' => $photo,
    'comments' => $comments
  ];

  return rest_ensure_response($response);
}

function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photo_get'
  ]);
}
add_action( 'rest_api_init', 'register_api_photo_get');
?>