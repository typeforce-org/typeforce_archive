<?php

$title = $exhibit_post->post_title;
$exhibition = get_the_terms( $exhibit_post->ID, 'exhibition')[0]->name;
$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id($exhibit_post->ID), 'large')[0];
$duo_url = \Firebelly\Media\get_duo_url($thumb_url);
$url = get_permalink($exhibit_post->ID);
?>
<article class="exhibit-listing-info">
  <div class="color" style="background-image: url('<?= $thumb_url ?>')" ></div>
  <div class="duo" style="background-image: url('<?= $duo_url ?>')"></div>
  <h1 class="info"><a href="<?= $url ?>"><?= $title ?> &mdash;  <?= $exhibition ?></a></h1>
</article>