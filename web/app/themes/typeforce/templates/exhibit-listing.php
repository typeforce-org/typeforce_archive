<?php

$id = $exhibit_post->ID;
$title = $exhibit_post->post_title;
$exhibition = get_the_terms( $exhibit_post->ID, 'exhibition')[0]->name;

if (!isset($thumb_size)) { $thumb_size = 'listing'; }

# Get absolute path of thumbnail for duo processing
$thumb_id = get_post_thumbnail_id($exhibit_post->ID);
$thumbs = \Firebelly\Media\get_color_and_duo_thumbs($thumb_id, $thumb_size );
$url = get_permalink($exhibit_post->ID);

$show_year = !(is_tax('exhibition'));

?>
<article class="exhibit-listing-info" data-id="<?= $id ?>">
  <?php if ($thumbs) {
      echo $thumbs;
    } ?>
  <a href="<?= $url ?>" class="info-link">
    <h1 class="info"><span class="highlight"><?= $title ?><?php if ($show_year) : ?><span class="info-link-exhibition">&nbsp;&mdash;&nbsp;<?= $exhibition ?></span><?php endif; ?></span></h1></a>
</article>