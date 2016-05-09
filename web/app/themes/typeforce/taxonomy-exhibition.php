<?php
$exhibition_id = $wp_query->queried_object->term_id;
$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info($exhibition_id); 
?>

<?= $exhibition_info ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while (have_posts()) : the_post(); ?>
  <?php if( get_post_type() === 'exhibit' ) : ?>
      <?php $exhibit_post = $post; include(locate_template('templates/exhibit-listing.php')); ?>
  <?php else : ?>
    <?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format()); ?>
  <?php endif; ?>

<?php endwhile; ?>

<?php the_posts_navigation(); ?>

