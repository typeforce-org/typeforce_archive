<?php
# Get absolute path of thumbnail for duo processing
$thumb_size = 'slide';
$thumb_id = get_post_thumbnail_id($intro_post->ID);
$thumb_url = wp_get_attachment_image_src( $thumb_id, $thumb_size)[0];
$duo_url = \Firebelly\Media\get_duo_url( $thumb_id, [ 'size' => $thumb_size ] );

$link_text = get_post_meta( $intro_post->ID, '_cmb2_link_text', true );
$link_exhibit = get_permalink(get_post_meta( $intro_post->ID, '_cmb2_link_exhibit', true ));
$link_url = get_permalink(get_post_meta( $intro_post->ID, '_cmb2_link_url', true ));
$url = $link_url ? $link_url : $link_exhibit;

$update = apply_filters('the_content',$intro_post->post_content);
?>
<article class="intro-slide">
  <div class="color" style="background-image: url('<?= $thumb_url ?>')" ></div>
  <div class="duo" style="background-image: url('<?= $duo_url ?>')"></div>
  <div class="content">


    <div class="update"><?= $update ?></div>

    <?php if ( $link_text ) : ?>
      <h1 class="intro-link">
        <a href="<?= $url ?>"><span class="highlight"><?= $link_text ?></span></a>
      </h1>
    <?php endif; ?>


  </div>
</article>