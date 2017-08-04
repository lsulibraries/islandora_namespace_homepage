<?php ?>
<div class="institution-header">
    <a href="<?php echo "/" . $nsHome['namespace'] . "/admin"; ?>">edit</a>
    <div class="institution-logo">
        <?php if (isset($nsHome['logo_href'])): ?>
        <img src="<?php echo $nsHome['logo_href']; ?>" style="max-width: 100px;">
        <?php endif; ?>
    </div>
    <div class="institution-title"><h2><?php echo $nsHome['title']; ?></h2></div>
</div>

<div class="institution-about"><?php echo $nsHome['description']; ?></div>

<div class="institution-search"><?php echo render($nsHome['search']); ?></div>

<div class="institution-browse-bys"></div>

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
