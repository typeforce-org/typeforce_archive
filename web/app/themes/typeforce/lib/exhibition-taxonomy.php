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
        'desc'  => '(Optional)',
        'id'    => $prefix . 'description',
        'type'  => 'wysiwyg',
      ) 
    );
    $cmb_term->add_field( 
      array(
        'name'  => 'Featured Image',
        'desc'  => '(Optional)',
        'id'    => $prefix . 'featured_image',
        'type'  => 'file',
      ) 
    );
    $cmb_term->add_field( 
      array(
        'name'  => 'Catalog Purchase',
        'desc'  => '(Optional) Put a link to purchase a catalog.  Or explain "Out of stock", "Coming soon", etc.',
        'id'    => $prefix . 'catalogue',
        'type'  => 'wysiwyg',
      ) 
    );    
    $cmb_term->add_field( 
      array(
        'name'  => 'Sponsors',
        'desc'  => '(Optional)',
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


function get_exhibit_link_li($exhibition_id,$type) {
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
    'meta_value'      => $type
  );
    // echo '<pre>'.print_r($args,true).'</pre>';
  $exhibits = get_posts($args);

  $list = ''; 
  foreach ($exhibits as $exhibit) {
    $exhibit_title = $exhibit->post_title;
    $exhibit_url = get_permalink($exhibit->ID);
    $list .= '<li class="exhibit"><a href="'.$exhibit_url.'">'.$exhibit_title.'</a></li>';
  }

  return $list;
}


function get_exhibition_info() {

  global $wp_query;
  $exhibition_id = $wp_query->queried_object->term_id;

  $title = $wp_query->queried_object->name; //'<a href="'.get_term_link($exhibition_id).'">'.get_term_meta($exhibition_id,'_cmb2_full_title',true).'</a>';

  $description = apply_filters('the_content', get_term_meta($exhibition_id,'_cmb2_description',true));
  $sponsors = apply_filters('the_content', get_term_meta($exhibition_id,'_cmb2_sponsors',true));

  $exhibited_list = get_exhibit_link_li($exhibition_id,'exhibit');
  $window_list = get_exhibit_link_li($exhibition_id,'window');
  $opening_list = get_exhibit_link_li($exhibition_id,'opening');

  $catalogue_link = apply_filters('the_content', get_term_meta($exhibition_id,'_cmb2_catalogue',true));

  $thumb = \Firebelly\Media\get_color_and_duo_thumbs(get_term_meta($exhibition_id,'_cmb2_featured_image_id',true), 'slide' );

  $output = <<< HTML
    <div class="exhibition-info" id="content">
      <h1>{$title}</h1>
      <div class="main">
HTML;
    if($thumb) {
      //let's do a hacky thing to make sure this guy has aspect ratio.
      $thumb_src = wp_get_attachment_image_src(get_term_meta($exhibition_id,'_cmb2_featured_image_id',true), 'slide' );
      $thumb_w = $thumb_src[1];
      $thumb_h = $thumb_src[2];
      $thumb_padding_bottom = ($thumb_h / $thumb_w)*100;
      $thumb_css = 'padding-bottom: '.$thumb_padding_bottom.'%';
      $output .= '<div class="featured-image" style="'.$thumb_css.'">'.$thumb.'</div>';
    }
    if($description) {
      $output .= '<div class="description user-content"><h2>Description</h2>'.$description.'</div>';
    }
  $output .= <<< HTML
      </div>
      <div class="additional">
        <div class="exhibited">
          <h2>Exhibited</h2>
          <ul class="exhibit-link-list">
            {$exhibited_list}
          </ul>
        </div>
HTML;
  if($window_list || $opening_list) {
    $output .= <<< HTML
        <div class="window-and-opening">
          <h2>Title Window &amp; Opening</h2>
          <ul class="exhibit-link-list">
            {$window_list}
            {$opening_list}
          </ul>
        </div>
HTML;
  }

  if($catalogue_link) {
    $output .= <<< HTML
        <div class="catalogue">
          <h2>Exhibition Catalog</h2>
          {$catalogue_link}
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













