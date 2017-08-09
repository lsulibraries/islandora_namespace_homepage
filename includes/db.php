<?php

function vg($key) {
  return variable_get(dplns() . '_' . $key, FALSE);
}

function vs($key, $val) {
  return variable_set(dplns() . '_' . $key, $val);
}

function get_record($prefix) {
  $tbl = dplns();
  $query = "SELECT id, prefix, title, description, logo from {$tbl} WHERE prefix = :p";
  $args = array(':p' => $prefix);
  $record = db_query($query, $args);
  return $record->fetchObject();
}

function inh_table($key, $fields = array()) {
  $keyfield = is_numeric($key) ? 'id' : 'prefix';
//  $field = $field ? $field : '*';
  $result = db_select('islandora_namespace_homepage', 'inh')
      ->fields('inh', $fields)
      ->condition($keyfield, $key)
      ->execute();
  return $result->fetchObject();
}

function inh_title($namespace) {
  $row = inh_table($namespace, array('title'));
  if (!$row) {
    return "No Title set for $namespace.";
  }
  return $row->title;
}
