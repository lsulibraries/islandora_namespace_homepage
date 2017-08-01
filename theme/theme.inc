<?php

function islandora_namespace_homepage_preprocess(&$variables, $hook) {
  if($hook == 'page' && $variables['node']->type == 'islandora_namespace_homepage') {
    $variables['islandora_ns_home']['hello'] = "yo bruh";
  }
}

function template_preprocess_islandora_namespace_homepage(&$variables) {
  $variables['islandora_ns_home']['yo'] = "say bruh";
}
