<?php

/**
 * 
 * @param type $user
 * @param type $object
 * @return TRUE|FALSE|NULL
 */
function decide_access($user, $object) {
  if (!url_is_protected()) {
    return NULL;
  }
  elseif (user_access(ISLANDORA_NAMESPACED_OBJECT_MANAGEMENT_EXEMPT)) {
    return TRUE;
  }
  else {
    return check_user_access_for_object($user, $object);
  }
}

/**
 * 
 * @param type $user
 * @param type $object
 * @return TRUE|FALSE
 */
function check_user_access_for_object($user, $object) {
  $whitelist   = get_user_whitelist($user->uid);
  $pid         = $object->id;
  module_load_include('module', 'islandora_namespace_homepage');
  $institution = parse_pid($pid, 'prefix');
  return in_array($institution, $whitelist);
}

/**
 * 
 * @param type $user
 * @return array
 */
function get_user_whitelist($uid) {
  module_load_include('.module', 'islandora_user_namespaces');
  return get_user_namespaces($uid);
}

/**
 * 
 * @return TRUE|FALSE
 */
function url_is_protected() {
  $url_segments = explode('/', request_path());
  if (in_array('manage', $url_segments)) {
    return TRUE;
  }
  return FALSE;
}

function homepage_administrator_access_callback($namespace) {
  $general_access = user_access(ISLANDORA_NAMESPACE_HOMEPAGE_ADMINISTER_HOMEPAGE);
  if (!$general_access) {
    return FALSE;
  }

  global $user;
  $whitelist = get_user_whitelist($user->uid);
  $associated = in_array($namespace, $whitelist);

  return $associated;
}
