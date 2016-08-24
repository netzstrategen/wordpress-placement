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
    static::register_post_type();
    static::register_acf();
    if (is_admin()) {
      return;
    }
    add_action('pre_get_posts', __CLASS__ . '::pre_get_posts');
  }

  /**
   * @implements pre_get_posts
   */
  public static function pre_get_posts(\WP_Query $wp_query) {
    if (!$wp_query->is_main_query() || !$wp_query->is_front_page()) {
      return;
    }
    if ($post_ids = array_merge([static::getCurrentBreakingNews()], static::getCurrentPlacements())) {
      $wp_query->query_vars['post__in'] = array_merge($post_ids);
      $wp_query->query_vars['orderby'] = 'post__in';
    }
  }

  /**
   * Gets the ID of the most recent breaking news.
   *
   * @return int
   */
  public static function getCurrentBreakingNews() {
    return (int) get_field('placement_breaking_news', static::getCurrentPlacementPost());
  }

  /**
   * Gets the IDs for the most recent available placements.
   *
   * @return array
   */
  public static function getCurrentPlacements() {
    $post_ids = [];
    if ($positions = get_field('placement_position', static::getCurrentPlacementPost())) {
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
    ];
    if (!$posts = get_posts($args)) {
      return;
    }
    return $posts[0]->ID;
  }

  /**
   * Registers site-specific post types.
   */
  public static function register_post_type() {
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
  public static function register_acf() {
    if (function_exists('register_field_group')) {
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
          'return_format' => 'id',
        ],
        [
          'key' => 'placement_position',
          'label' => __('Position', Plugin::L10N),
          'name' => 'placement_position',
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
