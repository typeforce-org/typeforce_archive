<?php
$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info(); 


//sorry wordpress.  I have to hijack the main loop to order these the way I want without doing a bunch of crazy stuff to the main query.

$exhibition_id = $wp_query->queried_object->term_id;

$args = array(
  'post_type'   => 'exhibit',
  'tax_query'   => array(
    array(
        'taxonomy'        => 'exhibition',
        'field'           =>  'id',
        'terms'           => $exhibition_id,
    ),
  ),  
  'posts_per_page'  => -1,
  'numberposts'     => -1,
  'orderby'         => 'title', 
  'order'           => 'ASC',
  'meta_key'        => '_cmb2_type',
);
$args['meta_value'] = 'window';
$windows = Firebelly\PostTypes\Exhibit\get_exhibits($args,false,true); 

$args['meta_value'] = 'opening';
$openings = Firebelly\PostTypes\Exhibit\get_exhibits($args,false,true); 

$args['meta_value'] = 'exhibit';
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args,false,true); 

?>

<?= $exhibition_info ?>


<?php if (!$windows && !$openings && !$exhibits ) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php else : ?>
  <ul class="exhibit-list load-more-container">
    <?= $windows ?>
    <?= $exhibits ?>
    <?= $openings ?>
  </ul>
<?php endif; ?>
