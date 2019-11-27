<?php
$args = array(
  'post_type'       => 'exhibit',
  'numberposts'     => -1,
  'orderby'         => 'rand',
  'posts_per_page'  => get_option( 'posts_per_page', 25 ),
);
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args);
$home = get_page_by_path('home');
$headline = apply_filters('the_content', $home->post_content);
$update = apply_filters('the_content', get_post_meta($home->ID , '_cmb2_update')[0] );
$thumb_id = get_post_thumbnail_id($post->ID);
$thumb_url = wp_get_attachment_image_src($thumb_id, 'slide' )[0];
?>

<!-- cowboy wildstyle -->
<div class="intro-content" style="height:auto;padding:0">
  <div class="video">
    <iframe src="https://player.vimeo.com/video/375947890?background=1;autoplay=1&autopause=0&loop=1&title=0&byline=0&portrait=0" width="960" height="540" frameborder="0"></iframe>
  </div>
  <div class="headline" style="top:auto;bottom:15px;text-align:right;padding:0;">
    <div class="wrap" style="max-width:none;width:calc(100vw - 25px);">
      <?= $headline ?>
    </div>
  </div>
</div>
<!-- no repo man -->

<?= $exhibits ?>