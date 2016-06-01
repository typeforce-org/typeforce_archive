<?php
 namespace Firebelly\PostTypes\Exhibition;
 use Firebelly\Utils;

/**
 * Register Exhibition Taxonomy
 */
$labels = array(
  'name'              => 'Exhibitions',
  'singular_name'     => 'Exhibition',
  'search_items'      => 'Search Exhibitions',
  'all_items'         => 'All Exhibitions',
  'parent_item'       => 'Parent Exhibition',
  'parent_item_colon' => 'Parent Exhibition:',
  'edit_item'         => 'Edit Exhibition',
  'update_item'       => 'Update Exhibition',
  'add_new_item'      => 'Add New Exhibition',
  'new_item_name'     => 'New Exhibition',
);
$rewrite = array(
  'slug'                => 'exhibitions',
  'with_front'          => false,
  'pages'               => true,
  'feeds'               => true,
);
$args = array( 
  'hierarchical'      => true,
  'labels'            => $labels,
  'show_admin_column' => true,
  'show_ui'           => true,
  'query_var'         => true,
  'show_in_nav_menus' => true,
  'rewrite'           => $rewrite,
);
register_taxonomy( 'exhibition', array('exhibit'), $args);





/**
 * Extra fields for Taxonomies
 */
function register_taxonomy_metabox() {
    $prefix = '_cmb2_';
    /**
    * Metabox to add fields to categories and tags
    */
    $cmb_term = new_cmb2_box( 
      array(
       'id'               => $prefix . 'edit',
       'title'            => __( 'Exhibition Information', 'cmb2' ),
       'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
       'taxonomies'       => array( 'exhibition' ), // Tells CMB2 which taxonomies should have these fields
      ) 
    );
   // $cmb_term->add_field( 
   //    array(
   //      'name'  => 'Full Title',
   //      'id'    => $prefix . 'full_title',
   //      'type'  => 'text_medium',
   //    ) 
   //  );
    $cmb_term->add_field( 
      array(
        'name'  => 'Description',
        'id'    => $prefix . 'description',
        'type'  => 'wysiwyg',
      ) 
    );
    $cmb_term->add_field( 
      array(
        'name'  => 'Catalog Purchase Link',
        'id'    => $prefix . 'catalogue',
        'type'  => 'text_medium',
      ) 
    );    
    $cmb_term->add_field( 
      array(
        'name'  => 'Sponsors',
        'id'    => $prefix . 'sponsors',
        'type'  => 'wysiwyg',
      ) 
    );

}
add_action( 'cmb2_admin_init', __NAMESPACE__ . '\register_taxonomy_metabox' );


/**
 * Custom admin cols for taxonomy
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox">',
    'name' => 'Year',
    // '_cmb2_year' => 'Year',
    // '_cmb2_description' => 'Description',
  );
  return $columns;
}
add_filter('manage_edit-exhibition_columns', __NAMESPACE__ . '\edit_columns');




function hack_away_the_unnecessary_fields() {
  echo <<<HTML
  <style>
    .taxonomy-exhibition .term-description-wrap, .taxonomy-exhibition .term-parent-wrap {
      display: none !important; 
    }
  </style>
HTML;
}
add_action('admin_head', __NAMESPACE__ . '\hack_away_the_unnecessary_fields');

//  function custom_columns($column){ //BROKEN
//   global $post;
//   global $wp_query;
//   $term_id = $wp_query->queried_object->term_id;
//   print_r(get_term($term_id));
//   // if ( $post->post_type == 'exhibit' ) {
//     if ( $column == 'featured_image' ) 
//       echo the_post_thumbnail('thumbnail');
//     elseif ( $column == 'content') 
//       echo Utils\get_excerpt($post);
//     else {
//       $custom = get_post_custom();
//       if ( array_key_exists($column, $custom) ) 
//         echo $custom[$column][0];
//     }
//   // }
// }
// add_action('manage_exhibition_custom_column', __NAMESPACE__ . '\custom_columns');


/**
 * Enqueue admin_scripts.js //BROKEN
 */

// function admin_scripts() {
//    wp_enqueue_script( 'adminscripts', get_template_directory_uri(). '/assets/js/admin.min.js', array('jquery'), NULL, true );
// }
// add_action( 'admin_enqueue_scripts', __NAMESPACE__ . 'admin_scripts' );



/**
 * Utilities & Content
 */
function get_exhibition_object($exhibit_id) {
  return wp_get_post_terms($exhibit_id,'exhibition')[0];
}

function get_exhibition_info() {

  global $wp_query;
  $exhibition_id = $wp_query->queried_object->term_id;

  $title = $wp_query->queried_object->name; //'<a href="'.get_term_link($exhibition_id).'">'.get_term_meta($exhibition_id,'_cmb2_full_title',true).'</a>';

  $description = apply_filters('the_content', get_term_meta($exhibition_id,'_cmb2_description',true));
  $sponsors = apply_filters('the_content', get_term_meta($exhibition_id,'_cmb2_sponsors',true));

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
  );
  $exhibits = get_posts($args);
  $exhibited_list = ''; 
  foreach ($exhibits as $exhibit) {
    $exhibit_title = $exhibit->post_title;
    $exhibit_url = get_permalink($exhibit->ID);
    $exhibited_list .= '<li class="exhibit"><a href="'.$exhibit_url.'">'.$exhibit_title.'</a></li>';
  }

  $catalogue_link = get_term_meta($exhibition_id,'_cmb2_catalogue',true);

  $output = <<< HTML
    <div class="exhibition-info" id="content">
      <h1>{$title}</h1>
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
HTML;
  if($catalogue_link) {
    $output .= <<< HTML
        <div class="catalogue">
          <h2>Exhibition Catalog</h2>
          <a href="{$catalogue_link}">Purchase through Firebelly</a>
        </div>
HTML;
  }
  if($sponsors) {
    $output .= <<< HTML
        <div class="sponsors user-content">
          <h2>Sponsors</h2>
          {$sponsors}
        </div>
HTML;
  }
  $output .= <<< HTML
      </div>
    </div>
HTML;

  return $output;
}



/**
 * Add current menu class to menu items that are exhibitions containing the exhibit for the current post
 */
function highlight_exhibitions( $classes, $item ) {
  if(is_singular('exhibit') && $item->object === 'exhibition') {

    $item_exhibition_id = $item->object_id;
    global $post;
    $post_exhibition_id = \Firebelly\PostTypes\Exhibition\get_exhibition_object($post->ID)->term_id;
    
    if( $item_exhibition_id == $post_exhibition_id ){
      $classes[] = "current-menu-item";
    }
  }
    return $classes;
}
add_filter( 'nav_menu_css_class' , __NAMESPACE__ . '\highlight_exhibitions', 10, 2 );













