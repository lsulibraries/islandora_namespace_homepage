<?php ?>
<div class="institution-title"><h2><?php echo $nsHome['title']; ?></h2></div>
<div class="institution-header">
    <?php if ($nsHome['namespace_admin']): ?>
      <a class="institution-about-edit" href="<?php echo "/" . $nsHome['namespace'] . "/settings"; ?>">Edit Settings</a>
    <?php endif; ?>
    <div class="institution-logo">
        <?php if (isset($nsHome['logo_href'])): ?>
          <img src="<?php echo $nsHome['logo_href']; ?>" style="max-width: 100px;">
        <?php endif; ?>
    </div>
    <div class="institution-about"><?php echo $nsHome['description']; ?>
      <div class='institution_total_wrapper'>Total number of items by type:
      <?php foreach ($nsHome['global_totals'] as $model_machine => $model_and_count) : ?>
        <div class="inst_total <?php print $model_machine ?>">
        <?php foreach ($model_and_count as $model => $count) : ?>
            <div class='model'><?php print $model?></div>
            <div class='count'><?php print $count?></div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
    </div>
</div>

<div class="institution-search"><?php echo render($nsHome['search']); ?></div>

<div class="institution-browse-bys"></div>

<div class="institution-collections">
    <ul class="institution-collection-list">
        <div class="institution-collection-list-header">Collections</div>
        <?php foreach ($nsHome['collections'] as $pid => $map): ?>
          <div class="institution-collection-list-a" data-target='<?php echo $map['url']; ?>'>
              <li class="institution-collection-list-li">
                  <div class="institution-collection-list-item-count"><?php echo $map['count']; ?></div>
                  <div class="institution-collection-list-item-label"><?php echo $map['obj']->label; ?></div>
                  <div class='institution-collection-description'><?php echo $map['obj']->description; ?></div>
              </li>
              <?php if ($nsHome['proxyAdmin'] && isset($map['proxy_url'])): ?>
                <div class="institution-collection-list-item-manage-proxy">
                    <a class="institution-collection-list-item-manage-proxy-link" href="<?php echo $map['proxy_url'] ?>">Manage proxy</a>
                </div>
              <?php endif; ?>
          </div>

        <?php endforeach; ?>
    </ul>
</div>

<div class="child-institution-collections">
    <?php foreach ($nsHome['child_collections_for_display'] as $ns => $data): ?>
    <a class="child-institution-link" href="<?php echo "/$ns"; ?>">
      <div class="child-institution-container">
        <div class="child-institution-title">
          <?php echo $data['title']; ?>
        </div>
        <div class="child-institution-description">
          <?php echo $data['description']; ?>
        </div>
        <div class="child-institution-count-collections">
          <?php echo $data['collectioncount']; ?>
        </div>
        <div class="child-institution-count-items">
          <?php echo $data['itemcount']; ?>
        </div>
        <!-- institution totals for institution/sub-institution-->
        <div class='sub_institution_totals_wrapper'>Total items in this sub-institution:
        <?php foreach ($nsHome['child_collections_for_display'][$ns]['child_totals'] as $model_machine => $model_and_count) : ?>
          <?php foreach ($model_and_count as $model => $count) : ?>
            <div class="inst_total <?php print $model_machine ?>">
              <div class='model'><?php print $model ?></div>
              <div class='count'><?php print $count ?></div>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
</div>
