<?php

$title = $exhibit_post->post_title;
$exhibition = get_the_terms( $exhibit_post->ID, 'exhibition')[0]->name;

if (!isset($thumb_size)) { $thumb_size = 'listing'; }

# Get absolute path of thumbnail for duo processing
$thumb_id = get_post_thumbnail_id($exhibit_post->ID);
$thumb = wp_get_attachment_image_src( $thumb_id, $thumb_size);
$thumb_width = $thumb[1];
$thumb_height = $thumb[2];
$thumb_url = $thumb[0];
$duo_url = \Firebelly\Media\get_duo_url( $thumb_id, [ 'size' => $thumb_size ] );
$dummy = \Roots\Sage\Assets\asset_path('images/gray.gif');

$url = get_permalink($exhibit_post->ID);

$show_year = !(is_singular('exhibit') || is_tax('exhibition'))

?>
<article class="exhibit-listing-info">
  <div class="color lazy" style="background-image: url('<?= $dummy ?>');" data-original="<?= $thumb_url ?>"></div>
  <div class="duo lazy" style="background-image: url('<?= $dummy ?>');" data-original="<?= $duo_url ?>"></div>
  <a href="<?= $url ?>" class="info-link">
    <h1 class="info"><span class="highlight"><?= $title ?><?php if ($show_year) : ?><span class="info-link-exhibition">&nbsp;&mdash;&nbsp;<?= $exhibition ?></span><?php endif; ?></span></h1></a>
</article>