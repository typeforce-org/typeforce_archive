<?php 
while (have_posts()) : the_post(); 

$exhibition_obj = Firebelly\PostTypes\Exhibition\get_exhibition_object($post->ID);
$exhibition_year = $exhibition_obj->name;


$title_entries = get_post_meta($post->ID,'_cmb2_titles',true);
$title_header = __('Title'.( (count($title_entries)>1) ? 's' : ''),'sage');
$titles = '<ul class="exhibit-titles">';
foreach ( (array) $title_entries as $title_entry ) {
  if ( isset ( $title_entry['title'] ) )
    $titles .= '<li class="exhibit-title">'.$title_entry['title'].'</li>';
}
$titles .= '</ul>';

$materials = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_materials',true));
$bio = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_bio',true));
$social = apply_filters('the_content',get_post_meta($post->ID,'_cmb2_social',true));
$thumbs = Firebelly\PostTypes\Exhibit\get_exhibit_thumbnails(); 

$args = array(
  'post_type'   => 'exhibit',
  'posts_per_page'  => -1,
  'numberposts'     => -1,
  'tax_query'   => array(
    array(
        'taxonomy'  => 'exhibition',
        'field'     =>  'id',
        'terms'     => $exhibition_obj->term_id,
      )
  ),
);
$more_exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args,false); 


?>

  <article <?php post_class(); ?>>
    
    <div class="header-wrap">
      <?= $thumbs ?>
      <header>
        <div class="entry-exhibition"><?= $exhibition_year ?></div>
        <h1 class="entry-artist"><?php the_title(); ?></h1>
      </header>
    </div>
    <div class="content-wrap">
      <div class="entry-main">
        <div class="entry-title"><h2><?= $title_header ?></h2><?= $titles ?></div>
        <div class="entry-description user-content"><h2><?= __('Description','sage') ?></h2><?php the_content(); ?></div>
        <div class="entry-bio user-content"><h2><?= __('Bio','sage') ?></h2><?= $bio ?></div>
      </div>
      <div class="entry-secondary">
        <div class="entry-materials user-content">
          <h2><?= __('Materials','sage') ?></h2>
          <?= $materials ?>
        </div>
        <div class="entry-social user-content">
          <h2><?= __('Social','sage') ?></h2>
          <?= $social; ?>
        </div>
      </div>
    </div>
    <footer class="footer-wrap">
      <nav class="exhibit-nav">
        <div class="next">
          <?php next_post_link('%link','<div class="anim-wrap">'.__('Next Designer','sage').' <svg class="icon-arrow-right" role="img"><use xlink:href="#icon-arrow-right"></use></svg></div>', TRUE, ' ', 'exhibition' ); ?>
        </div>
        <div class="prev">

          <?php previous_post_link('%link','<div class="anim-wrap"><svg class="icon-arrow-left" role="img"><use xlink:href="#icon-arrow-left"></use></svg> '.__('Previous Designer').'</div>', TRUE, ' ', 'exhibition' ); ?>
        </div>
      </nav>
    </footer>
  </article>
  <?= $more_exhibits ?>
<?php endwhile; ?>




