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
          'name'    => 'Link to Exhibit',
          'id'      => $prefix . 'link_exhibit',
          'type'    => 'select',
          'options' => cmb2_get_post_options( array( 'post_type' => array('exhibit'), 'numberposts' => -1, 'post_parent' => 0  ) ),
      ),
      array(
          'name'    => 'Link to URL',
          'id'      => $prefix . 'link_url',
          'type'    => 'text_medium',
          'desc'  => 'If specified, will override "Link to Exhibit" above',
      ),
    ),
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
    $output .= '<div class="slide-item">';
    ob_start();
    include(locate_template('templates/intro-slide.php'));
    $output .= ob_get_clean();
    $output .=  '</div>';
  endforeach;

  $output .=  '</div>';
 
  return $output;
}

























// Get Headlines
function get_intros() {
  $output = '';
  $output .= <<<HTML
    <section class="intro-banner">
      <div class="slider intro-slider">
        
HTML;

  $args = array(
    'numberposts' => -1,
    'post_type' => 'intro',
    // 'orderby' => 'meta_value_num',
    // 'meta_key' => '_cmb2_order_num',
    // 'order'     => 'ASC',
    );

  $intro_posts = get_posts($args);
  if (!$intro_posts) return false;

  //bg divs
  $i = 0;
  foreach ($intro_posts as $post):
    $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'intro-thumb')[0];
    $duo_url = \Firebelly\Media\get_duo_url($post, [ 'size' => 'intro-thumb' ]);
    $output .= <<<HTML
    <div class="slide-item slide-bg" style="background-image: url('{$thumb_url}')" data-slick-index="{$i}" >
      <div class="intro-duo" style="background-image: url('{$duo_url}')"> </div>
    </div>
HTML;
    $i++;
  endforeach;

  //add the goddamn dots :)
  $output .= '<div class="bg-dots"></div>';

  $output .= '<div class="overflow-wrapper">';

  //article divs
  $i = 0;
  foreach ($intro_posts as $post):
    $link_text = get_post_meta( $post->ID, '_cmb2_link_text', true );
    $links_to = get_permalink(get_post_meta( $post->ID, '_cmb2_links_to', true ));
    $output .= <<<HTML
      <article class="slide-fg intro-article" data-slick-index="{$i}" data-links-to="{$links_to}">
        <h1 class="intro-title"><span class="gradient-highlight">{$post->post_title}</span></h1>
        <a class="learn-more">{$link_text}</a>
      </article>
HTML;
    $i++;
  endforeach;

  $output .= <<<HTML
        </div>
      </div>
</section>
HTML;
 
  return $output;
}



