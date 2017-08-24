<?php


function admin_form($form, &$form_state, $ns) {
  $title_field = "{$ns}_title";
  $descr_field = "{$ns}_description";
  $logo_field = "{$ns}_logo";
  $dplns = dplns();
  $tbl_prefix = $dplns . "_";

  $data = get_record($ns);
  $defval = function($key) use ($data) {
    return isset($data->$key) ? $data->$key : '';
  };

  // Don't let unprivileged users alter harvest fields.
  $harvest_access = user_access(ISLANDORA_NAMESPACE_HOMEPAGE_OBJECT_MANAGEMENT_EXEMPT);

  $form = array();
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => "Full title for namespace '$ns'",
    '#default_value' => $defval('title'),
  );
  $form['description'] = array(
    '#type' => 'text_format',
    '#title' => "Description for namespace '$ns'",
    '#default_value' => $defval('description'),
  );
  $form['logo'] = array(
    '#type' => 'managed_file',
    '#title' => t('Logo'),
    '#description' => t('Upload a file, allowed extensions: jpg, jpeg, png, gif'),
    '#default_value' => $defval('logo'),
    '#upload_location' => 'public://namespace-thumbs/',
  );
  $form['harvested'] = array(
    '#type' => 'textfield',
    '#title' => "Base URL for harvested namespaces",
    '#default_value' => $defval('harvested'),
    '#access' => $harvest_access,
  );
  $form['harvested_regex'] = array(
    '#type' => 'textfield',
    '#title' => "Harvested-from url regex",
    '#description' => 'Regex to capture the harvested-from url from a MODS.abstract field.',
    '#default_value' => $defval('harvested_regex'),
    '#access' => $harvest_access,
  );
  $form["ns"] = array(
    '#type' => 'hidden',
    '#value' => $ns,
  );
  $form["submit"] = array(
    '#type' => 'submit',
    '#value' => 'Submit'
  );
  return $form;
}

function admin_form_submit($form, &$form_state) {

  $v = $form_state['values'];
  $mod = dplns();
  $p = $mod . '_';
  $ns = $v['ns'];

  $exists = get_record($ns);
  $record = $exists ? $exists : new stdClass();

  $title_key = "title";
  $descr_key = "description";
  $logo_key = "logo";
  $harvested_key = "harvested";
  $harvested_regex_key = "harvested_regex";

  if ($form_state['values'][$logo_key]) {
    // Load the file via file.fid.
    $file = file_load($form_state['values'][$logo_key]);

    // Change status to permanent.
    $file->status = FILE_STATUS_PERMANENT;

    // Save.
    $file = file_save($file);
  }

  $record->$title_key = $v[$title_key];
  $record->$descr_key = $v[$descr_key]['value'];
  $record->$logo_key = isset($file->fid) ? $file->fid : NULL;
  $record->$harvested_key = !empty($v[$harvested_key]) ? $v[$harvested_key] : NULL;
  $record->$harvested_regex_key = !empty($v[$harvested_regex_key]) ? $v[$harvested_regex_key] : NULL;

  if (!$exists) {
    $record->prefix = $ns;
    $record->id = db_insert($mod)
        ->fields((array) $record)
        ->execute();
  }
  else {
    $record->id = db_update($mod)
        ->fields((array) $record)
        ->condition('prefix', $record->prefix, '=')
        ->execute();
  }

  // Record that the module (in this example, user module) is using the file. 
  if (isset($file)) {
    file_usage_add($file, $mod, $mod, $record->id);
  }
}

function myform($form, &$form_state, $namespace) {
  $form = array();
  $form['term'] = array(
    '#type' => 'textfield',
    '#title' => "Search",
  );
  $form['namespace'] = array(
    '#title' => 'Search within institutions',
    '#type' => 'hidden',
    '#value' => $namespace,
    '#weight' => 5,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('myform_handle'),
  );
  return $form;
}

function myform_handle($form, &$form_state) {
  $namespace = $form_state['values']['namespace'];
  $child_ns = get_namespace_children($namespace);
  $namespaces = array($namespace . '*');
  foreach ($child_ns as $child) {
    $namespaces[] = $child . '*';
  }
  // See islandora_solr/includes/blocks.inc
  $search_string = islandora_solr_replace_slashes($form_state['values']['term']);
  $query = array('type' => 'dismax', 'ns' => $namespaces);

  $form_state['redirect'] = array(
    ISLANDORA_SOLR_SEARCH_PATH . "/$search_string",
    array(
      'query' => $query,
    ),
  );
}