<?php
$exhibition_id = $wp_query->queried_object->term_id;
$args = array(
  'post_type'   => 'exhibit',
  'tax_query'   => array(
    array(
        'taxonomy'  => 'exhibition',
        'field'     =>  'id',
        'terms'     => $exhibition_id,
      )
  ),
);
$exhibition_info = Firebelly\PostTypes\Exhibition\get_exhibition_info($exhibition_id); 
$exhibits = Firebelly\PostTypes\Exhibit\get_exhibits($args); 
?>

<?= $exhibition_info ?>
<?= $exhibits ?>

