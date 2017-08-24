<?php

function get_namespace_prefixes($cache = TRUE) {
  $prefixes_cache_value = vg('prefixes');

  // Use a stored value when appropriate.
  if (!$cache || !$prefixes_cache_value) {
    return update_namespace_prefixes_cache();
  }
  return $prefixes_cache_value;
}

function update_namespace_prefixes_cache() {
  $namespace_prefixes = array();
  foreach (array_keys(islandora_basic_collection_get_collections()) as $collection) {
    $prefix = parse_pid($collection, 'prefix');
    if (!$prefix) {
      continue;
    }
    $namespace_prefixes[] = $prefix;
  }
  $prefixes = array_unique(array_values($namespace_prefixes));

  vs('prefixes', $prefixes);

  return $prefixes;
}

function parse_pid($pid, $filter = NULL) {
  $parts = explode(':', $pid);
  $suffix = $parts[1];
  $prefix_parts = explode('-', $parts[0]);
  $num_parts = count($prefix_parts);
  if ($num_parts <= 1) {
    return FALSE;
  }
  $alias = array_pop($prefix_parts);
  $prefix = implode('-', $prefix_parts);

  switch ($filter) {
    case('pid'):
      return $pid;

    case('prefix'):
      return $prefix;

    case('alias'):
      return $alias;

    case('suffix'):
      return $suffix;

    case('namespace'):
      return $prefix . '-' . $alias;

    default:
      return array($prefix, $alias, $suffix);
  }
}

function get_field_or_default($key, $record, $namespace) {
  switch ($key) {
    case 'title':
      return isset($record->$key) ? $record->$key : $namespace;

    case 'description':
      if (!isset($record->$key)) {
        $name = ucwords(get_field_or_default('title', $record, $namespace));
        return "$name is a contributing member of the Louisiana Digital Library.";
      }
      return $record->$key;
  }
}

/**
 * Get the pids for a given ns prefix and optionally, child prefixes.
 * 
 * @param type $target_prefix
 * 
 * @param type $recurse
 * @return type
 */
function get_namespace_collections($target_prefix, $recurse = FALSE) {
  $heirarchy = get_namespace_subtree($target_prefix);
  $collections = get_collections_from_heirarchy($heirarchy, $target_prefix, $recurse);
  return $collections;
}

function get_namespace_subtree($namespace) {
  $heirarchy = vg('heirarchy');
  $steps = explode('-', $namespace);
  $key = '';
  while (!empty($steps)) {
    $step = array_shift($steps);
    $key = $key ? $key . '-' . $step : $step;
    $heirarchy = $heirarchy[$key];
  }
  return $heirarchy;
}

function get_collections_from_heirarchy($heirarchy, $key, $recurse = FALSE) {
  $collections = array();
  foreach ($heirarchy as $name => $value) {
    if ($name == 'collections') {
      $collections += (array) $value;
    }
    elseif ($recurse) {
      $new_key = $key . '-' . $name;
      $collections[$new_key] = array_merge($collections, get_collections_from_heirarchy($heirarchy[$name], $new_key, $recurse));
    }
  }
  return $collections;
}

function get_child_collections_for_display($namespace) {
  $child_ns = get_namespace_children($namespace);
  $child_collections = array();
  foreach ($child_ns as $ns) {
    $child_collections[$ns] = array('collections' => get_namespace_collections($ns));
    $child_collections[$ns]['title'] = inh_title($ns);
  }

  return $child_collections;
}

function get_namespace_children($namespace) {
  $subtree = get_namespace_subtree($namespace);
  unset($subtree['collections']);
  return array_keys($subtree);
}

function update_namespace_heirarchy_cache($namespace = 'all') {
  $ns = function($pid) {
    return parse_pid($pid, 'pid');
  };
  $collections = array_filter(array_map($ns, array_keys(islandora_basic_collection_get_collections())));
  $heirarchy = array();
  foreach ($collections as $collection) {
    $branch_segments = explode('-', $collection);
    $branch = flat_to_nest($branch_segments);
    $heirarchy = array_merge_recursive($heirarchy, $branch);
  }
  vs('heirarchy', $heirarchy);
}

function collection_is_in_namespace($pid, $namespace, $consider_children = FALSE) {
  $pfx = parse_pid($pid, 'prefix');
  return $consider_children ? preg_match("/^$namespace/", $pfx) : $pfx == $namespace;
}

function flat_to_nest($flat, $prefix = '') {
  $first = array_shift($flat);
  $nest = array();
  if (empty($flat)) {
    $pid = $prefix . '-' . $first;
    return array('collections' => $pid);
  }
  else {
    $prefix = strlen($prefix) ? $prefix . '-' . $first : $first;
    $nest[$prefix] = flat_to_nest($flat, $prefix);
    return $nest;
  }
}
