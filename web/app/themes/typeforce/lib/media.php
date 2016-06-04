<?php
/**
 * Various media functions
 */

namespace Firebelly\Media;

// Custom image sizes
add_image_size( 'tiny-slide', 1000, 0, true );
add_image_size( 'tiny-listing', 600, 0, true );
add_image_size( 'slide', 1800, 0, true );
add_image_size( 'listing', 768, 0, true );
add_image_size( 'fb-share', 600, 0, true );

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
 * Get the file path (not URL) to a thumbnail of a particular size.  
 * (get_attached_file() only returns paths to full-sized thumbnails.)  
 * @param  int            $thumb_id - attachment id of thumbnail
 * @param  string|array   $size - thumbnail size string (e.g. 'full') or array [w,h]
 * @return path           file path to properly sized thumbnail
 */
function get_thumbnail_size_path($thumb_id,$size) {
  // Find the path to the root image. We can get this from get_attached_file.
  $old_path = get_attached_file($thumb_id, true);

  // Find the url of the image with the proper size
  $attr = wp_get_attachment_image_src( $thumb_id , $size);
  $url = $attr[0];

  // Grab the filename of the sized image from the url
  $exploded_url =  explode ( '/' , $url );
  $filename = $exploded_url[ count($exploded_url)-1 ];

  // Replace the filename in our path with the filename of the properly sized image
  $exploded_path = explode ( '/' , $old_path );
  $exploded_path[count($exploded_path)-1] = $filename; 
  $new_path = implode ( '/' , $exploded_path );

  return $new_path;
}



/**
 * Get url for duo image, make duo image if non-existent
 * @param  int|object   $post_or_id (WP post object or image attachment id)
 * @return URL            background image code
 */
function get_duo_url($post_or_id, $options=[]) {

  // Handle options
  $defaults = ['color1' => '241f21','color2' => '8a8788', 'size'=>'full' ];
  $options = wp_parse_args($options,$defaults);
  $color1 = $options['color1'];
  $color2 = $options['color2'];
  $size = $options['size'];


  // If WP post object, get the featured image
  if (is_object($post_or_id)) {
    if (has_post_thumbnail($post_or_id->ID)) {
      $thumb_id = get_post_thumbnail_id($post_or_id->ID);
    } else { 
      return false;  //thumbnail not found
    }
  } else {
    // Otherwise, id was sent directly
    $thumb_id = $post_or_id;
  }
  $full_image = get_attached_file($thumb_id, true); //this only returns images of size 'full'

  // Do not proceed if full image not found
  if (!file_exists($full_image)) { 
    return false; 
  } 

  // Get the image of proper size
  $image_to_convert = get_thumbnail_size_path($thumb_id,$size);

  // Do not proceed if sized image not found
  if (!file_exists($image_to_convert)) { 
    return false; 
  }

  $upload_dir = wp_upload_dir();
  $base_dir = $upload_dir['basedir'] . '/duos/';

  // Build treated filename with thumb_id in case there are filename conflicts
  $treated_filename = preg_replace("/.+\/(.+)\.(\w{2,5})$/", $thumb_id."-$1-".$color1."-".$color2.".$2", $image_to_convert);
  $treated_image = $base_dir . $treated_filename;

  // If treated file doesn't exist, create it
  if (!file_exists($treated_image)) {
    // If the duo directory doesn't exist, create it first
    if(!file_exists($base_dir)) {
      mkdir($base_dir);
    }

    // Build the ImageMagick convert command and execute
    $convert_command = (WP_ENV==='development') ? '/usr/local/bin/convert' : '/usr/bin/convert';
    $full_command = $convert_command.' '.$image_to_convert.' +profile "*" -quality 90 -colorspace gray +level-colors "#'.$color1.'","#'.$color2.'" '.$treated_image;
    exec($full_command);

  echo '<script>console.log(\'MESSAGE FROM PHP:'.$full_command.'\');</script>';
  }

  // Finally, get the URL
  $duo_url = $upload_dir['baseurl'] . '/duos/' . $treated_filename;
  return $duo_url;
}


/**
 * Output duo and color thumbnails with all the lazy load, multiple size schmigamaroo
 */
function get_color_and_duo_thumbs($thumb_id,$size,$div=true){

  $dummy = \Roots\Sage\Assets\asset_path('images/gray.gif');

  $thumb_src = wp_get_attachment_image_src( $thumb_id, $size );
  if(!$thumb_src){ return ''; } //stop and return empty if no image

  $thumb_url = $thumb_src[0]; //get normal color image url
  $duo_url = \Firebelly\Media\get_duo_url($thumb_id, ['size' => $size] );  //get duotone image url

  //tiny images for mobile
  $tiny_size = $size==='slide' ? 'tiny-slide' : 'tiny-listing';

  $thumb_url_tiny = wp_get_attachment_image_src( $thumb_id, $tiny_size )[0];
  $duo_url_tiny = \Firebelly\Media\get_duo_url($thumb_id, ['size' => $tiny_size] );  

  if($div) { //output as divs with backgrounds that cover
    $output = <<< HTML
    <div class="color lazy" style="background-image: url('{$dummy}');" data-original="{$thumb_url_tiny}" data-big-img-url="{$thumb_url}" data-current-img-size="tiny"></div>
    <div class="duo lazy" style="background-image: url('{$dummy}');" data-original="{$duo_url_tiny}" data-big-img-url="{$duo_url}" data-current-img-size="tiny"></div>
HTML;
  } 
  else { //output as images 
    $output = <<< HTML
    <img  src="{$dummy}" class="color lazy" data-original="{$thumb_url_tiny}" data-big-img-url="{$thumb_url}" data-current-img-size="tiny">
    <img  src="{$dummy}" class="duo lazy" data-original="{$duo_url_tiny}" data-big-img-url="{$duo_url}" data-current-img-size="tiny">
HTML;
  }
  return $output;

}
