<?php

/**
 * 
 * @param type $user
 * @param type $object
 * @return TRUE|FALSE|NULL
 */
function decide_access($user, $object, $op) {
  if (!url_is_protected()) {
    return NULL;
  }
  elseif (user_access(ISLANDORA_NAMESPACE_HOMEPAGE_OBJECT_MANAGEMENT_EXEMPT)) {
    return TRUE;
  }
  else {
    return check_user_access_for_object($user, $object, $op);
  }
}

/**
 * 
 * @param type $user
 * @param type $object
 * @return TRUE|FALSE
 */
function check_user_access_for_object($user, $object, $op) {
  $pid = $object->id;
  $op_view = 'view fedora repository objects';
  $op_ingest = 'ingest fedora objects';
  if ($pid == 'islandora:root') {
    $safe_root_actions = array(
      $op_view,
      $op_ingest,
      'create child collection',
    );
    if ($op == 'delete fedora objects and datastreams') {
      return FALSE;
    }
    else {
      return in_array($op, $safe_root_actions);
    }
  }
  $whitelist = get_user_whitelist($user->uid);
  module_load_include('module', 'islandora_namespace_homepage');
  $institution = parse_pid($pid, 'prefix');
  if (in_array($institution, $whitelist)) {
    return TRUE;
  }
  else {
    return in_array($op, array($op_view));
  }
}

/**
 * 
 * @param type $user
 * @return array
 */
function get_user_whitelist($uid) {
  return get_user_namespaces($uid);
}

/**
 * @TODO add some more possibilities here, perhaps even admin config.
 * @return TRUE|FALSE
 */
function url_is_protected() {
  return TRUE;
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
  
  if (user_access(ISLANDORA_NAMESPACE_HOMEPAGE_OBJECT_MANAGEMENT_EXEMPT)) {
    return TRUE;
  }

  global $user;
  $whitelist = get_user_whitelist($user->uid);
  $associated = in_array($namespace, $whitelist);

  return $associated;
}
