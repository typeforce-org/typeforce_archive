<?php
/**
 * Headline post type
 */

namespace Firebelly\PostTypes\Intro;


// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Intro Slides',
    'singular_name'       => 'Intro Slide',
    'menu_name'           => 'Intro Slides',
    'parent_item_colon'   => '',
    'all_items'           => 'All Intro Slides',
    'view_item'           => 'View Intro Slide',
    'add_new_item'        => 'Add New Intro Slide',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Intro Slide',
    'update_item'         => 'Update Intro Slide',
    'search_items'        => 'Search Intro Slides',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => '',
    'with_front'          => false,
    'pages'               => false,
    'feeds'               => false,
  );
  $args = array(
    'label'               => 'intro',
    'description'         => 'Intro Slides',
    'labels'              => $labels,
    'supports'            => array( 'title','thumbnail','editor'), //took out editor
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'page',
  );
  register_post_type( 'intro', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'content' => 'Content',
    'featured_image' => 'Image',
    // '_cmb2_link_text' => 'Link Text',
    // '_cmb2_links_to' => 'Links To',
  );
  return $columns;
}
add_filter('manage_intro_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'intro' ) {
    $custom = get_post_custom();
    if ( $column == 'featured_image' )
      echo the_post_thumbnail( 'thumbnail' );
    elseif ( $column == '_cmb2_links_to' ) {
      if ($pages = get_pages(array('include' => $custom[$column][0]))) {
        foreach($pages as $page) {
          $pages_on[] = $page->post_title;
        }
        echo implode(',', $pages_on);
      }
    }
    elseif ( $column == 'content' )
      echo the_content();
    elseif ( $column == '_cmb2_link_text' || $column == '_cmb2_order_num' )
      echo $custom[$column][0];
  }
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['intro_metabox'] = array(
    'id'            => 'intro_metabox',
    'title'         => __( 'Optional Link', 'cmb2' ),
    'object_types'  => array( 'intro', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
        'name'    => 'Link text',
        'id'      => $prefix . 'link_text',
        'type'    => 'text',
      ),
      array(
        'name'    => 'Links To',
        'id'      => $prefix . 'links_to',
        'type'    => 'radio_inline',
        'options' => array(
          'exhibit'       => __( 'Exhibit', 'sage' ),
          'exhibition'    => __( 'Exhibition', 'sage' ),
          'url'           => __( 'URL', 'sage' ),
        ),
      ),
      array(
        'name'    => 'Exhibit',
        'id'      => $prefix . 'link_exhibit',
        'type'    => 'select',
        'options' => cmb2_get_post_options( 
          array( 
            'post_type'   => array('exhibit'), 
            'numberposts' => -1, 
            'post_parent' => 0  
          ) 
        ),
      ),
      array(
        'name'     => 'Exhibition',
        'id'       => $prefix . 'link_exhibition',
        'options'  => cmb2_get_term_options('exhibition'),
        'type'     => 'select',
      ),
      array(
        'name'    => 'URL',
        'id'      => $prefix . 'link_url',
        'type'    => 'text_medium',
      )
    )
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );


function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => -1,
        'post_parent' => 0,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }

    return $post_options;
}


function cmb2_get_term_options( $taxonomy = 'category', $args = array() ) {

    $args['taxonomy'] = $taxonomy;
    // $defaults = array( 'taxonomy' => 'category' );
    $args = wp_parse_args( $args, array( 'taxonomy' => 'category' ) );

    $taxonomy = $args['taxonomy'];

    $terms = (array) get_terms( $taxonomy, $args );

    // Initate an empty array
    $term_options = array();
    if ( ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            $term_options[ $term->term_id ] = $term->name;
        }
    }

    return $term_options;
}


function get_intro_slider() {

  $args = array(
    'numberposts' => -1,
    'post_type'   => 'intro',
    'orderby'     => 'rand',
    );

  $intro_posts = get_posts($args);
  if (!$intro_posts) return false;

  $output = '<div class="slider intro-slider">';

  foreach ($intro_posts as $intro_post):
    ob_start();
    include(locate_template('templates/intro-slide.php'));
    $output .= ob_get_clean();
  endforeach;

  $output .=  '</div>';
 
  return $output;
}




















