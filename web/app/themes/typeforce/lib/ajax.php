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


function load_more_button($orig_query=false) {

  //if a query obj is not provided, grab the global wp_query
  if (!$orig_query) {
    global $wp_query;
    $orig_query = $wp_query;
  }

  //stop if no posts
  if(!isset($orig_query->posts) && empty($orig_query->posts)){
    return '';
  }

  // have a look at this object
  // echo "<pre>".print_r($orig_query->query,true)."</pre>";

  //extract query vars
  $exhibition_id = isset($orig_query->queried_object->term_id) ? $orig_query->queried_object->term_id : '';
  $search_query = isset($orig_query->query_vars['s']) ? $orig_query->query_vars['s'] : '';
  $per_page = isset($orig_query->query['posts_per_page']) ? $orig_query->query['posts_per_page'] : get_option( 'posts_per_page', 24 );
  $orderby = isset($orig_query->query['orderby']) ? $orig_query->query['orderby'] : '';


  //get total post count for all posts in all pages of query
  if($exhibition_id) {
    $term = get_term_by('id',$exhibition_id,'exhibition');
    $total_posts = $term->count;
  } elseif ($search_query) {
    $total_posts = $wp_query->found_posts;
  } else {
    $total_posts = wp_count_posts('exhibit')->publish;
  }
  $total_pages = ceil( $total_posts / $per_page);

  //return the markup
  $output = '<div class="load-more" data-page-at="1" data-exhibition-id="'.$exhibition_id.'" data-search-query="'.$search_query.'" data-per-page="'.$per_page.'" data-total-pages="'.$total_pages.'" data-orderby="'.$orderby.'"><a class="no-ajaxy" href="#">Load More</a></div>';
  return $output;

}

/**
 * AJAX load more posts (news or events)
 */
function load_more_posts() {
  
  // get potential args:
  $post_type = !empty($_REQUEST['post_type']) ? $_REQUEST['post_type'] : 'post';
  $exhibition_id = !empty($_REQUEST['exhibition_id']) ? $_REQUEST['exhibition_id'] : '';
  $search_query = !empty($_REQUEST['search_query']) ? $_REQUEST['search_query'] : '';
  $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
  $post__not_in = !empty($_REQUEST['post__not_in']) ? $_REQUEST['post__not_in'] : '';

  // get page offsets
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : get_option('posts_per_page');
  $offset = ($page-1) * $per_page;

  //base args
  $args = [
    'posts_per_page' => $per_page,
    'post_type' => $post_type,
    'suppress_filters' => true, 
  ];

  //check for any additional args
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
  if($post__not_in) {
    $args['post__not_in'] = $post__not_in; 
  }
  if($orderby) {
    $args['orderby'] = $orderby;
  }
  //offset breaks orderby rand
  if($orderby != 'rand') {
    $args['offset'] = $offset;
  }

  //ICPO is MESSING STUFF UP!!!!  We gotta disable it for here.
  //https://wordpress.org/support/topic/over-ride-cpo-in-shortcode-wp_query
  global $hicpo; // Call the class variable for the ICO plugin, so we can disable its overriding of the orderby parameter
  remove_filter( 'pre_get_posts', array( $hicpo, 'hicpo_pre_get_posts' ) );


  $posts = get_posts($args);

 // echo "<pre>".print_r($posts,true)."</pre>";

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
