<?php 
/**
 * Exhibit Post Type
 */

 namespace Firebelly\PostTypes\Exhibit;
 use Firebelly\Utils;

 /**
  * Register Custom Post Type
  */
function post_type() {

  $labels = array(
    'name'                => 'Exhibits',
    'singular_name'       => 'Exhibit',
    'menu_name'           => 'Exhibits',
    'parent_item_colon'   => '',
    'all_items'           => 'All Exhibits',
    'view_item'           => 'View Exhibit',
    'add_new_item'        => 'Add New Exhibit',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Exhibit',
    'update_item'         => 'Update Exhibit',
    'search_items'        => 'Search Exhibits',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => 'exhibits',
    'with_front'          => false,
    'pages'               => true,
    'feeds'               => true,
  );
  $args = array(
    'label'               => 'exhibit',
    'description'         => 'Exhibit',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', 'thumbnail', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'page',
  );
  register_post_type( 'exhibit', $args );
}  
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Custom admin cols for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox">',
    'title' => 'Artist',
    '_cmb2_titles' => 'Title(s)',
    'content' => 'Description',
    'featured_image' => 'Featured Image',
    'taxonomy-exhibition' => 'Exhibition',
  );
  return $columns;
}
add_filter('manage_exhibit_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'exhibit' ) {
    if ( $column == 'featured_image' ) 
      echo the_post_thumbnail('thumbnail');
    elseif ( $column == 'content') 
      echo Utils\get_excerpt($post);
    elseif ( $column == '_cmb2_titles') {
      get_exhibit_titles();
    } else {
      $custom = get_post_custom();
      if ( array_key_exists($column, $custom) ) 
        echo $custom[$column][0];
    }
  }
}
add_action('manage_posts_custom_column', __NAMESPACE__ . '\custom_columns');

/**
 * Custom CMB2 fields for Exhibits
 */
function register_exhibit_metaboxes() {
  $prefix = '_cmb2_'; //start with underscore to hide from custom fields list
  
  $type = new_cmb2_box( array(
    'id'            => 'exhibit_type_metabox',
    'title'         => __( 'Exhibit Type', 'cmb2' ),
    'object_types'  => array( 'exhibit' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    )
  );
  $type->add_field( 
    array(
      'name'             => 'Choose:',
      'id'               => $prefix . 'type',
      'default'          => 'exhibit',
      'type'             => 'radio',
      'options'          => array(
          'exhibit' => __( 'Normal Exhibit', 'cmb2' ),
          'window'   => __( 'Window Display', 'cmb2' ),
          'opening'     => __( 'Exhibition Opening', 'cmb2' ),
      ),
    ) 
  );

  // $all_exhibits = get_posts($args);
  // foreach ( $all_exhibits as $an_exhibit ) {
  //   update_post_meta($an_exhibit->ID, '_cmb2_type', 'exhibit');
  // }

  $cmb = new_cmb2_box( array(
    'id'            => 'exhibit_metabox',
    'title'         => __( 'More Information', 'cmb2' ),
    'object_types'  => array( 'exhibit' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    )
  );
  $group_field_id = $cmb->add_field(
      array(
        'name'  => 'Titles',
        'desc'  => 'Titles of exhibit(s)',
        'id'    => $prefix . 'titles',
        'type'  => 'group',
        'options'     => array(
          'group_title'   => __( 'Title {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
          'add_button'    => __( 'Add Another Title', 'cmb2' ),
          'remove_button' => __( 'Remove Title', 'cmb2' ),
          'sortable'      => true, // beta
        ),
      )
  );

  $cmb->add_group_field( $group_field_id, array(
    'name' => 'Title',
    'id'   => 'title',
    'type' => 'text_medium',
) );
  $cmb->add_field(     
      array(
        'name'  => 'Materials & Dimensions',
        'desc'  => '(optional)',
        'id'    => $prefix . 'materials',
        'type'  => 'wysiwyg',
      )
  );
  $cmb->add_field(    
      array(
        'name'  => 'Biography',
        'desc'  => 'Artist\'s Biography (optional)',
        'id'    => $prefix . 'bio',
        'type'  => 'wysiwyg',
      )
  );
  $cmb->add_field(         
      array(
        'name'  => 'Social',
        'desc'  => 'Website, Social Media, etc. (optional)',
        'id'    => $prefix . 'social',
        'type'  => 'wysiwyg',
      )
  );
  $cmb->add_field(         
      array(
        'name'  => 'Stats',
        'desc'  => 'Event information',
        'id'    => $prefix . 'stats',
        'type'  => 'wysiwyg',
      )
  );
  $cmb->add_field(         
      array(
        'name'  => 'Photographer',
        'desc'  => 'Links for who photographed the event',
        'id'    => $prefix . 'photographer',
        'type'  => 'wysiwyg',
      )
  );
  $cmb->add_field(
      array(
        'name'  => 'More Images',
        'desc'  => 'Any images for exhibit page in addition to featured image (optional)',
        'id'    => $prefix . 'more_images',
        'type'  => 'file_list',
        'preview_size' => array( 150, 150 ),
      )
  );

}
add_action( 'cmb2_admin_init', __NAMESPACE__ . '\register_exhibit_metaboxes' );

// function get_intro_slider() {

//   $args = array(
//     'numberposts' => 5,
//     'post_type'   => 'exhibit',
//     'orderby'     => 'rand',
//     );

//   $exhibit_posts = get_posts($args);
//   if (!$exhibit_posts) return false;

//   $output = '<div class="slider intro-slider">';

//   foreach ($exhibit_posts as $exhibit_post):
//     $output .= '<div class="slide-item">';
//     ob_start();
//     $thumb_size = 'slide';
//     include(locate_template('templates/exhibit-listing.php'));
//     $output .= ob_get_clean();
//     $output .=  '</div>';
//   endforeach;

//   $output .=  '</div>';
 
//   return $output;
// }

function get_exhibits($args, $loadmore = true, $li_only=false) {

  $output ='';

  $exhibit_posts = new \WP_Query( $args );

  if ( $exhibit_posts->have_posts() ) {

    if(!$li_only) { $output .= '<ul class="exhibit-list load-more-container">'; }
    while ( $exhibit_posts->have_posts() ) { 
      $exhibit_posts->the_post();
      global $post;
      $exhibit_post = $post;
      $output .= '<li class="exhibit">';
      ob_start();
      include(locate_template('templates/exhibit-listing.php'));
      $output .= ob_get_clean();
      $output .= '</li>';
    }
    if(!$li_only) { $output .= '</ul>'; }
    wp_reset_postdata();

    if($loadmore) { 

      $output .= \Firebelly\Ajax\load_more_button($exhibit_posts);
     }
    
  }else{
    return '';
  }

  return $output;
}

function get_exhibit_titles() {
  global $post;
  $title_entries = get_post_meta($post->ID,'_cmb2_titles',true);
  $titles = '<ul class="exhibit-titles">';
  foreach ( (array) $title_entries as $title_entry ) {
    if ( isset ( $title_entry['title'] ) )
      $titles .= '<li class="exhibit-title">'.$title_entry['title'].'</li>';
  }
  $titles .= '</ul>';
  echo $titles;
}

function get_exhibit_thumbnails() {
  // Do not proceed if no thumbnail
  if( !has_post_thumbnail() ){
    return '<div class="slider thumbnail-slider single-slide"><div class="slide-item"></div></div>';
  }
  // Lets get all the thumbnail ids
  $thumb_ids = array();
  // Grab the featured image ids as first id
  $thumb_ids[$i=0] = get_post_thumbnail_id( get_the_ID() );
  // Grab all the image from our cmb2 file_list
  $files = get_post_meta( get_the_ID(), '_cmb2_more_images', true );
  // well style differently if a single slide, lets assume there is only one until proven otherwise
  $single_slide = true;
  if($files) {
    foreach($files as  $file_id => $file_url) {
      $thumb_ids[++$i] = $file_id;
    }
    $single_slide = false; //proven otherwise
  }
  // Loop through each image gathered and make some html!
  $output = '';
  $output .= '<div class="slider thumbnail-slider'.($single_slide ? ' single-slide' : '').'">';
  foreach($thumb_ids as $thumb_id){

    $thumbs = \Firebelly\Media\get_color_and_duo_thumbs($thumb_id, 'slide' );
    $caption = get_post_field('post_excerpt', $thumb_id);

    if ($thumbs) {
      //let's do a hacky thing to make sure this guy has aspect ratio.
      $thumb_src = wp_get_attachment_image_src($thumb_id, 'slide' );
      $thumb_w = $thumb_src[1];
      $thumb_h = $thumb_src[2];
      $thumb_ratio = ( $thumb_w / $thumb_h );
      $output .= '<div class="slide-item" data-width-height-ratio="'.$thumb_ratio.'">';
      $output .= $thumbs;
    } else {
      $output .= '<div class="slide-item">';
    }

    if($caption) {
        $output .= '<div class="content"><div class="caption"><span class="highlight">'.$caption.'</span></div></div>';
    }

    $output .= '</div>';

  }
  $output .= '</div>';

  return $output;
}