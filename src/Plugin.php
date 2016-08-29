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
    static::registerPostTypes();
    static::registerFields();
    if (is_admin()) {
      return;
    }
    add_action('pre_get_posts', __CLASS__ . '::pre_get_posts');
  }

  /**
   * Registers site-specific post types.
   */
  public static function registerPostTypes() {
    register_post_type('placement', [
      'labels' => [
        'name' => __('Placements', Plugin::L10N),
        'singular_name' => __('Placement', Plugin::L10N),
      ],
      'description' => '',
      'show_ui' => TRUE,
      'show_in_menu' => TRUE,
      'capability_type' => 'post',
      'map_meta_cap' => TRUE,
      'supports' => ['author'],
      'taxonomies' => [],
    ]);
  }

  /**
   * Registers site-specific fields.
   */
  public static function registerFields() {
    if (!function_exists('register_field_group')) {
      return;
    }
    register_field_group([
      'key' => 'placement',
      'title' => __('Placement', Plugin::L10N),
      'fields' => [[
        'key' =>  'placement_breaking_news',
        'label' => __('Breaking News', Plugin::L10N),
        'name' => 'placement_breaking_news',
        'type' => 'post_object',
        'post_type' => ['post'],
        'allow_null' => 1,
        'return_format' => 'object',
      ],
      [
        'key' => 'placement_positions',
        'label' => __('Positions', Plugin::L10N),
        'name' => 'placement_positions',
        'type' => 'repeater',
        'layout' => 'table',
        'button_label' => __('Add entry', Plugin::L10N),
        'sub_fields' => [[
          'key' => 'post',
          'label' => __('Post', Plugin::L10N),
          'name' => 'post',
          'type' => 'post_object',
          'post_type' => ['post'],
          'allow_null' => 1,
          'return_format' => 'id',
        ]],
      ]],
      'location' => [[[
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'placement',
        'order_no' => 0,
        'group_no' => 0,
      ]]],
      'options' => [
        'position' => 'normal',
        'layout' => 'no_box',
      ],
      'menu_order' => 0,
    ]);
  }

  /**
   * @implements pre_get_posts
   */
  public static function pre_get_posts(\WP_Query $wp_query) {
    if (!$wp_query->is_main_query() || !$wp_query->is_front_page()) {
      return;
    }
    if ($post_ids = static::getCurrentPlacements()) {
      $wp_query->query_vars['post__in'] = $post_ids;
      $wp_query->query_vars['orderby'] = 'post__in';
    }
  }

  /**
   * Gets the ID of the most recent breaking news.
   *
   * @return int
   */
  public static function getCurrentBreakingNews() {
    if (!$post_id = static::getCurrentPlacementPost()) {
      return;
    }
    $post = get_field('placement_breaking_news', $post_id);
    if ($post && strtotime($post->post_date) >= current_time('timestamp')) {
      return;
    }
    return $post;
  }

  /**
   * Gets the IDs for the most recent available placements.
   *
   * @return array
   */
  public static function getCurrentPlacements() {
    if (!$post_id = static::getCurrentPlacementPost()) {
      return;
    }
    $post_ids = [];
    if ($positions = get_field('placement_positions', $post_id)) {
      foreach ($positions as $position) {
        if (!empty($position['post'])) {
          $post_ids[] = (int) $position['post'];
        }
      }
    }
    return $post_ids;
  }

  /**
   * Returns the ID of the most recent placement post type post.
   *
   * @return int
   */
  public static function getCurrentPlacementPost() {
    $args = [
      'post_type' => 'placement',
      'post_status' => 'publish',
      'date_query' => [
        'before' => 'now',
        'include' => TRUE,
      ],
      'orderby' => 'date',
      'order' => 'DESC',
      'posts_per_page' => 1,
      'fields' => 'ids',
    ];
    return current(get_posts($args));
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
