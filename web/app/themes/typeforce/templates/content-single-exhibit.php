<?php 
while (have_posts()) : the_post(); 

$exhibition_term = wp_get_post_terms($post->ID,'exhibition')[0];
$exhibition_id = $exhibition_term->term_id;

$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info($exhibition_id); 
$exhibition = $exhibition_term->name;
$title = get_post_meta($post->ID,'_cmb2_title',true);
$materials = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_materials',true));
$bio = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_bio',true));
$social = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_social',true));
$thumbs = Firebelly\PostTypes\Exhibit\get_exhibit_thumbnails(); 

?>

  <?= $exhibition_info ?>
  <article <?php post_class(); ?>>
    <?= $thumbs ?>
    <header>
      <div class="entry-exhibition"><?= $exhibition ?></h1>
      <h1 class="entry-artist"><?php the_title(); ?></h1>
    </header>
    <div class="entry-content">
      <h2><?= $title ?></h2>
      <div class="entry-materials"><?= $materials ?></div>
      <div class="entry-statement"><?php the_content(); ?></div>
      <h2>Bio</h2>
      <div class="entry-bio"><?= $bio ?></div>
    </div>
    <div class="entry-social">
      <h2>Social</h2>
      <?= $social; ?>
    </div>
    <footer>
      <nav class="exhibit-nav">
        <?php previous_post_link('%link','Previous Designer'); ?>
        <?php next_post_link('%link','Next Designer'); ?>
      </nav>
    </footer>
  </article>
<?php endwhile; ?>




