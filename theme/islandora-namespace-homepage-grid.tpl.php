<?php
/**
 * @file
 * islandora-namespace-homepage-grid.tpl.php
 *
 */
?>

<div class="homepageSection">
  <div class="homepageText">
    <?php foreach ($variables['results'] as $grid) : ?>
      <?php dpm($grid); ?>
    <?php endforeach; ?>
</div>
</div>

