<?php ?>
<div class="institution-header">
    <div class="institution-logo">logo goes here</div>
    <div class="institution-title">Institution Title here</div>
</div>

<div class="institution-about">About this institution...</div>

<div class="institution-search">Search box goes here</div>

<div class="institution-browse-bys">browse-bys here</div>

<div class="institution-collections">
    <ul class="institution-collection-list">
        <div class="institution-collection-list-header">Collections</div>
          <?php foreach ($nsHome['collections'] as $pid => $object): ?>
          <li class="institution-collection-list-item">
              <a href='/<?php echo "islandora/object/" . $object->id; ?>'>
                <?php echo $object->label; ?>
              </a>
              <div class='institution-collection-description'><?php echo $object->description; ?></div>
          </li>
          <?php endforeach; ?>
    </ul>
</div>
