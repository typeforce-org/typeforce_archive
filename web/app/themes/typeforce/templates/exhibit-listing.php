<?php

$title = $exhibit_post->post_title;
$exhibition = get_the_terms( $exhibit_post->ID, 'exhibition')[0]->name;

if (!isset($thumb_size)) { $thumb_size = 'listing'; }

# Get absolute path of thumbnail for duo processing
$thumb_id = get_post_thumbnail_id($exhibit_post->ID);
$thumb_url = wp_get_attachment_image_src( $thumb_id, $thumb_size)[0];
$duo_url = \Firebelly\Media\get_duo_url( $thumb_id, [ 'size' => $thumb_size ] );

$url = get_permalink($exhibit_post->ID);
?>
<article class="exhibit-listing-info">
  <div class="color" style="background-image: url('<?= $thumb_url ?>')" ></div>
  <div class="duo" style="background-image: url('<?= $duo_url ?>')"></div>
  <a href="<?= $url ?>" class="info-link"><h1 class="info"><span class="highlight"><?= $title ?><span class="info-link-exhibition">&nbsp;&mdash;&nbsp;<?= $exhibition ?></span> </span></h1></a>
</article>