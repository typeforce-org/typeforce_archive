<?php
/**
 * Various media functions
 */

namespace Firebelly\Media;

// image size for popout thumbs
add_image_size( 'popout-thumb', 250, 300, ['center', 'top'] );

/**
 * Get thumbnail image for post
 * @param  integer $post_id
 * @return string image URL
 */
function get_post_thumbnail($post_id, $size='medium') {
  $return = false;
  if (has_post_thumbnail($post_id)) {
    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
    $return = $thumb[0];
  }
  return $return;
}



/**
 * Get url for duo image, make duo image if non-existent
 */
function get_duo_url($duo_image, $thumb_id='', $options=['color1' => '2f2d28','color2' => 'dddcd6']) {
    $color1 = $options['color1'];
    $color2 = $options['color2'];
  $header_duo='';

  if ($duo_image) {
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'] . '/duos/';

    // Build treated filename with thumb_id in case there are filename conflicts
    $treated_filename = preg_replace("/.+\/(.+)\.(\w{2,5})$/", $thumb_id."-$1-".$color1."-".$color2.".$2", $duo_image);
    $treated_image = $base_dir . $treated_filename;
  
    // If treated file doesn't exist, create it
    if (!file_exists($treated_image)) {
      // If the duo directory doesn't exist, create it first
      if(!file_exists($base_dir)) {
        mkdir($base_dir);
      }
      $convert_command = (WP_ENV==='development') ? '/usr/local/bin/convert' : '/usr/bin/convert';
      exec($convert_command.' '.$duo_image.' +profile "*" -quality 65 -colorspace gray -level +10% +level-colors "#'.$color1.'","#'.$color2.'" '.$treated_image);
    }    
    $header_duo = $upload_dir['baseurl'] . '/duos/' . $treated_filename;
  }
  return $header_duo;
}
