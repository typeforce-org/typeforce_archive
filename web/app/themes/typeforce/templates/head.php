<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
  <link href='http://fonts.googleapis.com/css?family=Karla:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
  <link rel="icon" type="image/png" href="<?= Roots\Sage\Assets\asset_path('images/favicon.png'); ?>">
  <script src="<?= Roots\Sage\Assets\asset_path('scripts/modernizr.custom.js'); ?>"></script>
  <svg class="filter-defs">
    <filter id="duotone" color-interpolation-filters="sRGB"
            x="0" y="0" height="100%" width="100%">
      <feColorMatrix type="matrix"
        values="0.46 0 0 0  .14 
                0.47 0 0 0  .12  
                0.47 0 0 0  .13 
                  0  0 0 1  0" />
    </filter>
  </svg>
</head>
