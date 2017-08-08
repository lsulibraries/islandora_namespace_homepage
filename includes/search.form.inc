<?php

/**
 * Form for searching within a given namespace.
 *
 * @param array $form
 *   Associative array with Drupal Form API elements.
 *
 * @param array $form_state
 *   Associative array with form state information.
 */
function islandora_namespaced_search_form($form, &$form_state) {
  form_load_include($form_state, 'inc', 'islandora_namespaced_search', 'includes/search.form');
  $form['namespaced_search'] = array(
    '#type' => 'container',
    '#attributes' => array(
      'class' => array(
        'container-inline',
      ),
    ),
  );
  $form['namespaced_search']["islandora_namespaced_search_query"] = array(
    '#size' => '15',
    '#type' => 'textfield',
    '#title' => t('Search box'),
    '#title_display' => 'invisible',
    '#default_value' => '',
  );
  $form['namespaced_search']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('search'),
  );
  return $form;
}
