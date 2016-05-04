<?php
/**
 * Extra fields for Pages
 */

namespace Firebelly\PostTypes\Pages;

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['header_metabox'] = array(
    'id'            => 'header_metabox',
    'title'         => __( 'Additional Content', 'cmb2' ),
    'object_types'  => array( 'page', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_on'       => array( 'key' => 'page-template', 'value' => 'templates/header.php'),
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      // General page fields
      array(
        'name' => 'Update',
        'desc' => 'What\'s the latest Typeforce news?',
        'id'   => $prefix . 'update',
        'type' => 'wysiwyg',
      ),
    ),
  );

  $meta_boxes['footer_metabox'] = array(
    'id'            => 'footer_metabox',
    'title'         => __( 'Additional Content', 'cmb2' ),
    'object_types'  => array( 'page', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_on'       => array( 'key' => 'page-template', 'value' => 'templates/footer.php'),
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      // General page fields
      array(
        'name' => 'Links',
        'id'   => $prefix . 'links',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Sponsors',
        'id'   => $prefix . 'sponsors',
        'type' => 'wysiwyg',
      ),
    ),
  );


  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );


