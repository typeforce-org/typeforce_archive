<?php
namespace Firebelly\Ajax;

/**
 * Add wp_ajax_url variable to global js scope
 */
function wp_ajax_url() {
  wp_localize_script('sage/js', 'wp_ajax_url', admin_url( 'admin-ajax.php'));
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\wp_ajax_url', 100);

/**
 * Silly ajax helper, returns true if xmlhttprequest
 */
function is_ajax() {
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * AJAX load more posts (news or events)
 */
function load_more_posts() {
  
  // news or projects?
  $post_type = !empty($_REQUEST['post_type']) ? $_REQUEST['post_type'] : 'post';
  $exhibition_id = !empty($_REQUEST['exhibition_id']) ? $_REQUEST['exhibition_id'] : '';
  $search_query = !empty($_REQUEST['search_query']) ? $_REQUEST['search_query'] : '';
  // get page offsets
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : get_option('posts_per_page');
  $offset = ($page-1) * $per_page;
  $args = [
    'offset' => $offset,
    'posts_per_page' => $per_page,
    'post_type' => $post_type,
  ];
  if($exhibition_id) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'exhibition',
        'field' => 'id',
        'terms' => $exhibition_id
      ]
    ];
  }
  if($search_query) {
    $args['s'] = $search_query;
  }

  $posts = get_posts($args);

  if ($posts): 
    foreach ($posts as $post) {
      // set local var for post type — avoiding using $post in global namespace
      if ($post_type == 'exhibit') {
        $exhibit_post = $post;
        echo '<li class="exhibit">';
        include(locate_template('templates/exhibit-listing.php'));
        echo '</li>';
      }
    }
  endif;

  // we use this call outside AJAX calls; WP likes die() after an AJAX call
  if (is_ajax()) die();
}
add_action( 'wp_ajax_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );
