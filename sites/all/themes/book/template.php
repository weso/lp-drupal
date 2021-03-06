<?php

require_once('language.php');

/**
 * Implements hook_theme()
 */
function book_theme() {
  $items = array();
  $items['book'] = array(
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'custom',
    'variables' => array('mustache_data' => NULL, 'mustache_template' => NULL, 'mustache_navigation' => NULL),
  );
  $items['user_login'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'custom',
    'variables' => array('mustache_data' => NULL, 'mustache_template' => 'login', 'mustache_navigation' => 'login'),
  );
  $items['user_register_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book') .'/templates',
    'template' => 'user/user-register-form',
  );
  $items['user_profile_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book') .'/templates',
    'template' => 'user/user-profile-edit',
  );
  $items['user_pass'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book') .'/templates',
    'template' => 'user/user-password',
  );
  $items['event_node_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'templates/forms/form--event',
    'arguments' => array('form' => NULL,),
  );
  $items['blog_post_node_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'templates/forms/form--blog_post',
    'arguments' => array('form' => NULL,),
  );
  $items['debate_node_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'templates/forms/form--debate',
    'arguments' => array('form' => NULL,),
  );
  $items['news_node_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'templates/forms/form--news',
    'arguments' => array('form' => NULL,),
  );
  $items['organization_node_form'] = array(
    'render element' => 'form',
    'path' => drupal_get_path('theme', 'book'),
    'template' => 'templates/forms/form--organization',
    'arguments' => array('form' => NULL,),
  );
  return $items;
}


/**
 * Implements hook_preprocess.
 * Sets variables required by all the templates. Those variables are:
 *  - $application_data: contains common application data required by all the
 *    templates including the list of available languages and the current user name
 *  - $theme_path: the full path to the theme folder. Used to load some static resources
 */
function book_preprocess(&$variables) {
  $variables['application_data'] = get_application_data();
  $variables['theme_path'] = get_path();
  $variables["messages"] = theme("status_messages");

}


/**
 * Implements hook_preprocess_html.
 * In this case preprocess the HTML to add a link to the <head> corresponding
 * to the Google fonts.
 */
function book_preprocess_html(&$vars) {
  $googlefonts = array(
    '#tag' => 'link', // The #tag is the html tag - <link />
    '#attributes' => array( // Set up an array of attributes inside the tag
      'href' => 'http://fonts.googleapis.com/css?family=News+Cycle|Source+Sans+Pro:300,400|Josefin+Sans:300',
      'rel' => 'stylesheet',
      'type' => 'text/css',
    ),
  );
  drupal_add_html_head($googlefonts, 'googlefonts');

  $meta_viewport = array(
    '#tag' => 'meta',
    '#attributes' => array(
      'content' => 'width=device-width',
      'name' => 'viewport',
    )
  );
  drupal_add_html_head($meta_viewport, 'viewport');

}


/**
 * Implements template_preprocess_user_profile.  This function makes available
 * the UID of the viewed user in the user profile view.
 */
function book_preprocess_user_profile(&$variables) {
    $account = $variables['elements']['#account'];
    // Add the UID into the user profile as a variable
    $variables['user_id'] = $account->uid;
    // Helpful $user_profile variable for templates.
    foreach (element_children($variables['elements']) as $key) {
        $variables['user_profile'][$key] = $variables['elements'][$key];
    }
    // Preprocess fields.
    field_attach_preprocess('user', $account, $variables['elements'], $variables);
}


/**
 * Get the application data required by all the templates.
 *
 * @return a list of the available and selected langauges and the current user name (if
 *  it exists.
 */
function get_application_data() {
  $lang = new Language();
  $languages = $lang->get_languages('en');
  $server_name = $_SERVER['HTTP_HOST'];
  $ajax_files_path = drupal_get_path("module", "landportal_uris") . "/ajax";
  $book_theme_path = drupal_get_path("theme", "book");

  return array(
    "languages" => $languages,
    "language" => $lang->get_selected_language($languages),
    "user" => _get_current_user(),
    "server_name" => $server_name,
    "api" => "http://{$server_name}/{$ajax_files_path}",
    "sparql" => "http://{$server_name}/sparql",
    "api-widgets" => "http://{$server_name}/api",
    "theme_path" => "http://{$server_name}/{$book_theme_path}",
  );
}


function book_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"messages $type\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}


/**
 * Get the current user name.
 *
 * @return an associative array containing the current user name or NULL
 *  if there is not current user.
 */
function _get_current_user() {
  global $user;
  if (!isset($user->name)) {
    return NULL;
  } else {
    return array('name' => $user->name);
  }
}


/**
 * Get the full theme path.
 *
 * @return The full theme path.
 */
function get_path() {
  $server_name = $_SERVER['HTTP_HOST'];
  $theme_path = drupal_get_path('theme', 'book');
  return 'http://' . $server_name . '/' . $theme_path;
}
