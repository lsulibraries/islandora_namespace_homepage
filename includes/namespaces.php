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
    // heirarchical prefix
    if (FALSE !== strpos($prefix, '-')) {
      $prefix_chunks = explode('-', $prefix);
      array_pop($prefix_chunks);
      while (!empty($prefix_chunks)) {
        $chunk = array_pop($prefix_chunks);
        if (!in_array($chunk, $namespace_prefixes)) {
          $namespace_prefixes[] = $chunk;
        }
      }
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

function get_child_collections_for_display($namespace, $reset = FALSE) {
  $cache_key = 'islandora_namespace_homepage_child_collections_' . $namespace;
  if (!$reset && ($cache = cache_get($cache_key)) && !empty($cache->data)) {
    $child_collections = unserialize($cache->data);
  }
  else {
    $child_ns = get_namespace_children($namespace);
    $child_collections = array();
    foreach ($child_ns as $ns) {
      $pids = get_namespace_collections($ns);
      $child_collections[$ns]['collectioncount'] = count($pids);
      $ns_itemcount = 0;
      foreach ($pids as $pid) {
        $obj = islandora_object_load($pid);
        if (!$obj) {
          continue;
        }

        $ns_itemcount += islandora_namespace_homepage_collection_item_count($pid);
      }
      $child_collections[$ns]['itemcount'] = $ns_itemcount;
      $child_collections[$ns]['title'] = inh_title($ns);
      $child_collections[$ns]['description'] = inh_field($ns, 'description');
    }
    cache_set($cache_key, serialize($child_collections), 'cache');
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

// taken from islandora_namespace_homepage_preprocess_islandora_solr
// which needs to be refactored
function get_link_back_for_proxy_object($object, $namespace = NULL) {
  if (!$object) {
    return FALSE;
  }
  if (NULL === $namespace) {
    $namespace = parse_pid($object->id, 'prefix');
  }
  $mods = $object['MODS'];
  $modsxml = simplexml_load_string($mods->content);
  $row = inh_table($namespace, array('harvested_regex'));
  if (!isset($row->harvested_regex) && !is_string($row->harvested_regex)) {
    return FALSE;
  }
  $regex = $row->harvested_regex;
  $pattern = sprintf('/%s/', $regex);

  foreach ($modsxml->abstract as $abstract) {
    $matches = array();
    preg_match($pattern, $abstract, $matches);
    if (isset($matches[1])) {
      return $matches[1];
    }
  }
  return FALSE;
}
