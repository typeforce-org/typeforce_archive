<?php

namespace Firebelly\Utils;

/**
 * Bump up # search results
 */
// function search_queries( $query ) {
//   if ( !is_admin() && is_search() ) {
//     $query->set( 'posts_per_page', 40 );
//   }
//   return $query;
// }
// add_filter( 'pre_get_posts', __NAMESPACE__ . '\\search_queries' );

/**
 * Remove pages/posts from search
 */
function remove_pages_and_posts_from_search() {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
    $wp_post_types['post']->exclude_from_search = true;
}
add_action('init', __NAMESPACE__ . '\\remove_pages_and_posts_from_search');

/**
 * Custom li'l excerpt function
 */
function get_excerpt( $post, $length=15, $force_content=false ) {
  $excerpt = trim($post->post_excerpt);
  if (!$excerpt || $force_content) {
    $excerpt = $post->post_content;
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = apply_filters( 'the_content', $excerpt );
    $excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
    $excerpt_length = apply_filters( 'excerpt_length', $length );
    $excerpt = wp_trim_words( $excerpt, $excerpt_length );
  }
  return $excerpt;
}

/**
 * Get top ancestor for post
 */
function get_top_ancestor($post){
  if (!$post) return;
  $ancestors = $post->ancestors;
  if ($ancestors) {
    return end($ancestors);
  } else {
    return $post->ID;
  }
}

/**
 * Get first term for post
 */
function get_first_term($post, $taxonomy='category') {
  $return = false;
  if ($terms = get_the_terms($post->ID, $taxonomy))
    $return = array_pop($terms);
  return $return;
}

/**
 * Get page content from slug
 */
function get_page_content($slug) {
  $return = false;
  if ($page = get_page_by_path($slug))
    $return = apply_filters('the_content', $page->post_content);
  return $return;
}

/**
 * Get category for post
 */
function get_category($post) {
  if ($category = get_the_category($post)) {
    return $category[0];
  } else return false;
}

/**
 * Get num_pages for category given slug + per_page
 */
function get_total_pages($post_type,$per_page) {
  $count_posts = wp_count_posts($post_type);
  $total_published = $count_posts->publish;
  $num_pages = ceil($total_published/ $per_page);
  return $num_pages;
}

/**
 * Get Page Blocks
 */
function get_page_blocks($post) {
  $output = '';
  $page_blocks = get_post_meta($post->ID, '_cmb2_page_blocks', true);
  if ($page_blocks) {
    foreach ($page_blocks as $page_block) {
      if (empty($page_block['hide_block'])) {
        $block_title = $block_body = '';
        if (!empty($page_block['title']))
          $block_title = $page_block['title'];
        if (!empty($page_block['body'])) {
          $block_body = apply_filters('the_content', $page_block['body']);
          $output .= '<div class="page-block">';
          if ($block_title) {
            $output .= '<h2 class="flag">' . $block_title . '</h2>';
          }
          $output .= '<div class="user-content">' . $block_body . '</div>';
          $output .= '</div>';
        }
      }
    }
  }
  return $output;
}

/**
 * This function creates a custom order for posts, stored in a meta data field.  The order goes windows->exhibits->openings each sorted alphabetically.

 To sort in this order use in args:

  'orderby'         => 'meta_value_num', 
  'order'           => 'ASC',
  'meta_key'        => '_exhibition_order',

  after calling this function
 */
function set_exhibition_order($exhibition_id) {

  $args = array(
    'post_type'   => 'exhibit',
    'tax_query'   => array(
      array(
          'taxonomy'        => 'exhibition',
          'field'           =>  'id',
          'terms'           => $exhibition_id,
      ),
    ),  
    'posts_per_page'  => -1,
    'numberposts'     => -1,
    'orderby'         => 'title', 
    'order'           => 'ASC',
    'meta_key'        => '_cmb2_type',
  );
  $args['meta_value'] = 'window';
  $windows = get_posts($args); 

  $args['meta_value'] = 'exhibit';
  $exhibits = get_posts($args); 

  $args['meta_value'] = 'opening';
  $openings = get_posts($args); 

  $i=0;

  if(!empty($windows)){
    foreach( $windows as $window ) {
      if (empty(get_post_meta($window->ID,'_exhibition_order',true))) {
        add_post_meta($window->ID,'_exhibition_order',$i);
      } else {
        update_post_meta($window->ID,'_exhibition_order',$i);
      }
      $i++;
    }
  }

  if(!empty($exhibits)){
    foreach( $exhibits as $exhibit ) {
      if (empty(get_post_meta($exhibit->ID,'_exhibition_order',true))) {
        add_post_meta($exhibit->ID,'_exhibition_order',$i);
      } else {
        update_post_meta($exhibit->ID,'_exhibition_order',$i);
      }
      $i++;
    }
  }

  if(!empty($openings)){
    foreach( $openings as $opening ) {
      if (empty(get_post_meta($opening->ID,'_exhibition_order',true))) {
        add_post_meta($opening->ID,'_exhibition_order',$i);
      } else {
        update_post_meta($opening->ID,'_exhibition_order',$i);
      }
      $i++;
    }
  }

}
