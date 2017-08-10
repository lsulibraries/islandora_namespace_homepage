<?php

/**
 * @file
 * Drush commands.
 */

/**
 * Implements hook_drush_command().
 */
function islandora_user_namespaces_drush_command() {
  $items = array();

  $items['islandora-user-namespaces-set-ns'] = array(
    'description' => dt('Set namespaces field for the given user."'),
    'examples' => array('drush islandora-user-namespaces-set-ns --username=myuser --ns=lsu,fsu,upei'),
    'bootstrap' => DRUSH_BOOTSTRAP_LOGIN,
    'drupal dependencies' => array(
      'islandora',
    ),
    'options' => array(
      'username' => array(
        'description' => dt('Username to set ns for.'),
        'required' => TRUE,
      ),
      'ns' => array(
        'description' => dt('comma-separated list of ns'),
        'required' => TRUE,
      ),
    ),
  );
  return $items;
}

function drush_islandora_user_namespaces_set_ns() {
  $username = drush_get_option('username');
  $ns       = drush_get_option('ns');
  $user     = user_load_by_name($username);

  $field_name  = ISLANDORA_USER_NAMESPACES_FIELD_NAME;
  $field_items = field_get_items('user', $user, $field_name);
  $current_ns  = array();
  foreach($field_items as $item) {
    $current_ns[] = $item['value'];
  }

  foreach(explode(',', $ns) as $namespace) {
    if(in_array($namespace, $current_ns)) {
      continue;
    }
    $user->field_namespace[LANGUAGE_NONE][] = array('value' => $namespace);
  }
  user_save($user);
}