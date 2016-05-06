<?php 
while (have_posts()) : the_post(); 

$exhibition_term = wp_get_post_terms($post->ID,'exhibition')[0];
$exhibition_abbr = $exhibition_term->name;
$exhibition_id = $exhibition_term->term_id;

$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info($exhibition_id); 
$moreinfo = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_moreinfo',true));

$thumbs = Firebelly\PostTypes\Exhibit\get_exhibit_thumbnails(); 
?>

  <?= $exhibition_info ?>
  <article <?php post_class(); ?>>
    <?= $thumbs ?>
    <header>
      <div class="entry-exhibition"><?= $exhibition_abbr ?></h1>
      <h1 class="entry-title"><?php the_title(); ?></h1>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
    <div class="entry-moreinfo">
      <?= $moreinfo; ?>
    </div>
    <footer>
      <nav class="exhibit-nav">
        <?php previous_post_link('%link','Previous Designer'); ?>
        <?php next_post_link('%link','Next Designer'); ?>
      </nav>
    </footer>
  </article>
<?php endwhile; ?>
