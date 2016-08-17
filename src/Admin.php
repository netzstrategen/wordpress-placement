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
    add_action('admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts');
    add_filter('wp_insert_post_data', __CLASS__ . '::wp_insert_post_data', 10, 2);
  }

  /**
   * @implements admin_enqueue_scripts
   */
  public static function admin_enqueue_scripts($page) {
    wp_enqueue_style('placement/admin', Plugin::getBaseUrl() . '/css/placement.admin.css');
  }

  /**
   * @implements wp_insert_post_data
   */
  public static function wp_insert_post_data($data, $postarr) {
    if ($data['post_type'] === 'placement') {
      $data['post_title'] = date_i18n(get_option('date_format') . ' - ' . get_option('time_format'), strtotime($data['post_date'])) . ' Uhr';
    }
    return $data;
  }

}
