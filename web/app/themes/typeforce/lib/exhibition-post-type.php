<?php 
/**
 * Exhibition Post Type
 */

 namespace Firebelly\PostTypes\Exhibition;
 use Firebelly\Utils;

 /**
  * Register Custom Post Type
  */
function post_type() {

  $labels = array(
    'name'                => 'Exhibition Info',
    'singular_name'       => 'Exhibition Info',
    'menu_name'           => 'Exhibition Info',
    'parent_item_colon'   => '',
    'all_items'           => 'All Exhibition Info',
    'view_item'           => 'View Exhibition Info',
    'add_new_item'        => 'Add New Exhibition Info',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Exhibition Info',
    'update_item'         => 'Update Exhibition Info',
    'search_items'        => 'Search Exhibition Info',
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
    'label'               => 'exhibition_info',
    'description'         => 'Exhibition Info',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', ),
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
  register_post_type( 'exhibition_info', $args );
}  
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );


/**
 * Custom admin cols for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox">',
    'title' => 'Exhibition',
    'content' => 'Description',
    // '_cmb2_exhibition_link' => 'Link',
  );
  return $columns;
}
add_filter('manage_exhibition_info_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'exhibition_info' ) {
    if ( $column == 'featured_image' ) 
      echo the_post_thumbnail();
    elseif ( $column == 'content') 
      echo Utils\get_excerpt($post);
    else {
      $custom = get_post_custom();
      if ( array_key_exists($column, $custom) ) 
         { echo $custom[$column][0]; } else echo 'not set';
    }
  }
}
add_action('manage_posts_custom_column', __NAMESPACE__ . '\custom_columns');

/**
 * Custom CMB2 fields for Exhibitions
 */ 
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; //start with underscore to hide from custom fields list

  $meta_boxes['exhibition_info_metabox'] = array(
    'id'            => 'exhibition_info_metabox',
    'title'         => __( 'Additional Options', 'cmb2' ),
    'object_types'  => array( 'exhibition_info' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name'  => 'Catalogue Purchase Link',
        'id'    => $prefix . 'catalogue',
        'type'  => 'text_medium',
      ),
      array(
        'name'      => 'Link to Exhibition',
        'id'        => $prefix . 'exhibition_link',
        'type'    => 'select',
        'options' => cmb2_get_term_options('exhibition'),
      ),
    ),
  );
  
  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );


/**
 * Gets a number of terms and displays them as options
 * @param  string       $taxonomy Taxonomy terms to retrieve. Default is category.
 * @param  string|array $args     Optional. get_terms optional arguments
 * @return array                  An array of options that matches the CMB2 options array
 */
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




function get_exhibition_info($exhibition_id) {


  $args = array (
    'post_type' => 'exhibition_info',
    'meta_key' => '_cmb2_exhibition_link',
    'meta_value' => $exhibition_id,
  );

  $exhibition_info = get_posts($args)[0];

  $title = $exhibition_info->post_title;

  $description = apply_filters('the_content', $exhibition_info->post_content);

  $args = array(
    'post_type'   => 'exhibit',
    'tax_query'   => array(
      array(
          'taxonomy'  => 'exhibition',
          'field'     =>  'id',
          'terms'     => $exhibition_id,
        )
    ),
  );
  $exhibits = get_posts($args);
  $exhibited_list = ''; 
  foreach ($exhibits as $exhibit) {
    $exhibit_title = $exhibit->post_title;
    $exhibit_url = get_permalink($exhibit->ID);
    $exhibited_list .= '<li class="exhibit"><a href="'.$exhibit_url.'">'.$exhibit_title.'</a></li>';
  }

  $catalogue_link = get_post_meta($exhibition_info->ID,'_cmb2_catalogue',true);

  $output = <<< HTML
  <div class="page-header exhibition-info active">
    <h1 class="title">{$title}</h1>
    <div class="description user-content">
      {$description}
    </div>
    <div class="additional">
      <div class="exhibited">
        <h2>Exhibited</h2>
        <ul class="exhibition-exhibit-list">
          {$exhibited_list}
        </ul>
      </div>
      <div class="catalogue">
        <h2>Exhibition Catalogue</h2>
        <a href="{$catalogue_link}">Purchase through Firebelly</a>
      </div>
    </div>
  </div>
HTML;

  return $output;

}












































