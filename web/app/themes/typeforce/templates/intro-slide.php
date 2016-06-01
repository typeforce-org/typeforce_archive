<?php
# Get absolute path of thumbnail for duo processing
$thumb_size = 'slide';
$thumb_id = get_post_thumbnail_id($intro_post->ID);
$thumbs = \Firebelly\Media\get_color_and_duo_thumbs($thumb_id, $thumb_size );
$link_text = get_post_meta( $intro_post->ID, '_cmb2_link_text', true );

$links_to = get_post_meta( $intro_post->ID, '_cmb2_links_to', true );

if ($links_to === 'exhibit') {
  $url = get_permalink(get_post_meta( $intro_post->ID, '_cmb2_link_exhibit', true ));
} elseif ($links_to === 'exhibition') {
  $exhibition = intval(get_post_meta( $intro_post->ID, '_cmb2_link_exhibition', true ));
  $url = get_term_link($exhibition,'exhibition'); 
} else {
  $url = get_post_meta( $intro_post->ID, '_cmb2_link_url', true );
}

$update = apply_filters('the_content',$intro_post->post_content);
?>
<article class="intro-slide">
  <?php if ($thumbs) {
    echo $thumbs;
  } ?>
  <div class="content">


    <div class="update user-content"><?= $update ?></div>

    <?php if ( $link_text ) : ?>
      <h1 class="intro-link">
        <a href="<?= $url ?>"><span class="highlight"><?= $link_text ?></span></a>
      </h1>
    <?php endif; ?>


  </div>
</article>