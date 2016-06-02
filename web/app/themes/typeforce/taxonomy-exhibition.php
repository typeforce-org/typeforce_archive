<?php
$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info(); 


//sorry wordpress.  I have to hijack the main loop to order these the way I want without doing a bunch of crazy stuff to the main query.

$exhibition_id = $wp_query->queried_object->term_id;
Firebelly\Utils\set_exhibition_order($exhibition_id);
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
  'orderby'         => 'meta_value_num', 
  'order'           => 'ASC',
  'meta_key'        => '_exhibition_order',
);
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args, false); 

?>

<?= $exhibition_info ?>


<?php if (!$exhibits ) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php else : ?>
  <?= $exhibits ?>
<?php endif; ?>
