<?php if(is_search()) : ?>
  <div class="search-title">
    <h1><?= Roots\Sage\Titles\title() ?></h1>
  </div>
<?php endif; ?>
<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <!--<?php get_search_form(); ?>-->
<?php else : ?>

  <ul class="exhibit-list load-more-container">
  <?php while (have_posts()) : the_post(); ?>
    
    <?php if( get_post_type() === 'exhibit' ) : ?>
      <li class="exhibit">
        <?php $exhibit_post = $post; include(locate_template('templates/exhibit-listing.php')); ?>
      </li>
    <?php else : ?>
      <li class="<?= get_post_type() ?>">
      <?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format()); ?>
      </li>
    <?php endif; ?>

  <?php endwhile; ?>
  </ul>
  <?= \Firebelly\Ajax\load_more_button();
  ?>
<?php endif; ?>
