
<script>
document.body.className += ' ' + 'institutionPage';
</script>

<?php ?>

<div class="institution-details">
  <div class="institution-logo">
    <?php if (isset($nsHome['logo_href'])): ?>
      <img src="<?php echo $nsHome['logo_href']; ?>" style="max-width: 100px;">
    <?php endif; ?>
  </div>
  <div class="institution-about"><?php echo $nsHome['description']; ?></div>
</div>

<div class="institution-content">
  <div class="institution-grid">
    <div class="institution-header masonryItem">

      <div class="itemTitle" id="institution-title"><?php echo $nsHome['title']; ?></div>
      <div class="userMenu">
          <div class="infoToggle userSelect"><div class="iconSelect"></div><div class="textSelect">details</div></div>
          <?php if ($nsHome['namespace_admin']): ?>
            <a class="institution-about-edit userSelect" href="<?php echo "/" . $nsHome['namespace'] . "/settings"; ?>">
              <div class="iconSelect"></div>
              <div class="textSelect">Edit Settings</div>
            </a>
          <?php endif; ?>              
      </div>      
      <div class="institution-search"><?php echo render($nsHome['search']); ?></div>
    </div>
    <?php foreach ($nsHome['child_collections_for_display'] as $ns => $data): ?>
    <a class="institution-tile sub-group masonryItem" href="<?php echo "/$ns"; ?>">
      <div class="category"><i class="fa fa-folder-open" aria-hidden="true"></i> Sub-Group</div>
      <div class="label">
        <div class="title">
          <?php echo $data['title']; ?>
        </div>
        <div class="institution-count-collections">
          <?php echo $data['collectioncount']; ?> 
        </div>
        <div class="items">
          <?php echo $data['itemcount']; ?> items
        </div>
      </div>
    </a>
    <?php endforeach; ?>        
    <?php foreach ($nsHome['collections'] as $pid => $map): ?>
    <a class="institution-tile collection masonryItem" href="<?php echo $map['url']; ?>">
        <div class="category"><i class="fa fa-th" aria-hidden="true"></i> Collection</div>
        <div class="label">
            <div class="title"><?php echo $map['obj']->label; ?></div>
            <div class="items"><?php echo $map['count']; ?> items</div>
       </div>
        <?php if ($nsHome['proxyAdmin'] && isset($map['proxy_url'])): ?>
          <div class="institution-collection-list-item-manage-proxy">
              <a class="institution-collection-list-item-manage-proxy-link" href="<?php echo $map['proxy_url'] ?>">Manage proxy</a>
          </div>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>