<?php

/**
 * @file
 * Contains \Netzstrategen\Placement\Plugin.
 */

namespace Netzstrategen\Placement;

/**
 * Main front-end functionality.
 */
class Plugin {

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = 'placement';

  /**
   * Amount of fillable positions.
   *
   * @var int
   */
  const POSITIONS = 10;

  /**
   * @var string
   */
  private static $baseUrl;

  /**
   * Loads the plugin textdomain.
   */
  public static function loadTextdomain() {
    load_plugin_textdomain(static::L10N, FALSE, static::L10N . '/languages/');
  }

  /**
   * @implements init
   */
  public static function init() {
    if (is_admin()) {
      return;
    }
    add_action('pre_get_posts', __CLASS__ . '::pre_get_posts');
  }

  /**
   * @implements pre_get_posts
   */
  public static function pre_get_posts(\WP_Query $query) {
    if ($query->is_main_query() && $query->is_front_page()) {
      $placements = array_values(get_option('placements'));
      $query->query_vars['post__in'] = $placements;
      $query->query_vars['orderby'] = 'post__in';
      $query->query_vars['posts_per_page'] = static::POSITIONS;
      $query->query_vars['ignore_sticky_posts'] = TRUE;
    }
  }

  /**
   * The base URL path to this plugin's folder.
   *
   * Uses plugins_url() instead of plugin_dir_url() to avoid a trailing slash.
   */
  public static function getBaseUrl() {
    if (!isset(static::$baseUrl)) {
      static::$baseUrl = plugins_url('', static::getBasePath() . '/placement.php');
    }
    return static::$baseUrl;
  }

  /**
   * The absolute filesystem base path of this plugin.
   *
   * @return string
   */
  public static function getBasePath() {
    return dirname(__DIR__);
  }

}
