<?php
$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info();
$exhibition_id = $wp_query->queried_object->term_id;
$args = array(
  'post_type'      => 'exhibit',
  'tax_query'      => array(
    array(
      'taxonomy'   => 'exhibition',
      'field'      =>  'id',
      'terms'      => $exhibition_id,
    ),
  ),
  'numberposts'    => -1,
  'posts_per_page' => -1,
  'meta_query'     => array(
    '_cmb2_type'   => array(
      'key'        => '_cmb2_type',
      'compare'    => 'EXISTS',
    ),
  ),
  'meta__in'       => ['window','exhibit','opening'],
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
