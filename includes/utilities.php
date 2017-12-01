<?php

/**
 * 
 * @return string
 * The namespace of this module.
 */
function dplns() {
  return 'islandora_namespace_homepage';
}

function get_all_collections_inst() {
  $connection = islandora_get_tuque_connection();
  if ($connection) {
    $query = <<<EOQ
  SELECT ?pid FROM <#ri>
  WHERE { 
    ?pid <fedora-rels-ext:isMemberOfCollection> <info:fedora/islandora:root>
  }
EOQ;
    $results = $connection->repository->ri->sparqlQuery($query);
  }
  $objects = array();
  foreach ($results as $key => $value) {
    $plode = explode('-', $results[$key]['pid']['value']);
    $objects[$plode[0]] = $plode[0] . '-*';
  }
  //$objects['all'] = '*';
  return array_unique($objects);
}
