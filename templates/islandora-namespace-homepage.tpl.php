<?php ?>
<div class="institution-title"><h2><?php echo $nsHome['title']; ?></h2></div>
<div class="institution-header">
    <?php if ($nsHome['namespace_admin']): ?>
    <a href="<?php echo "/" . $nsHome['namespace'] . "/settings"; ?>">Edit Settings</a>
    <?php endif; ?>
    <div class="institution-logo">
        <?php if (isset($nsHome['logo_href'])): ?>
        <img src="<?php echo $nsHome['logo_href']; ?>" style="max-width: 100px;">
        <?php endif; ?>
    </div>
    <div class="institution-about"><?php echo $nsHome['description']; ?></div>
</div>

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

<div class="child-institution-collections">
    <?php foreach ($nsHome['child_collections_for_display'] as $ns => $data): ?>
    <div class="child-institution-title"><?php echo $data['title']; ?></div>
    <ul class="child-institution-collection-list">
        <?php foreach ($data['collections'] as $coll): ?>
        <li class="child-institution-collection-list-item">
            <a href='/<?php echo "islandora/object/" . $coll; ?>'>
              <?php echo $coll; ?>
            </a>
            <div class='institution-collection-description'><?php //echo $object->description; ?></div>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>

