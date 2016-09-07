<?php

/**
 * @file
 * Contains \Netzstrategen\Placement\Admin.
 */

namespace Netzstrategen\Placement;

/**
 * Administrative back-end functionality.
 */
class Admin {

  /**
   * @implements admin_init
   */
  public static function init() {
    add_filter('wp_insert_post_data', __CLASS__ . '::wp_insert_post_data', 10, 2);
    add_action('admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts');
  }

  /**
   * @implements wp_insert_post_data
   */
  public static function wp_insert_post_data($data, $postarr) {
    if ($data['post_type'] === 'placement') {
      $data['post_title'] = __(sprintf('Placement – %s', date_i18n(get_option('date_format') . ' | ' . get_option('time_format'), strtotime($data['post_date']))), Plugin::L10N);
    }
    return $data;
  }

  /**
   * @implements admin_enqueue_scripts
   */
  public static function admin_enqueue_scripts() {
    wp_enqueue_script('placement/admin', Plugin::getBaseUrl() . '/assets/js/admin.js', ['jquery'], FALSE, TRUE);
  }

}
