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
    add_action('post_submitbox_misc_actions', __CLASS__ . '::outputPlacementDropdown');
    add_action('save_post', __CLASS__ . '::save_post');
  }

  /**
   * Renders a select dropdown to assign a placement position to a single post.
   */
  public static function outputPlacementDropdown($post) {
    if ($post->post_type !== 'post') {
      return;
    }
    wp_nonce_field('placement', 'placement_nonce');
    load_template(Plugin::getBasePath() . '/templates/dropdown.php');
  }

  /**
   * @implements save_post
   */
  public static function save_post($post_id) {
    if (empty($_POST['placement_nonce']) || !wp_verify_nonce($_POST['placement_nonce'], 'placement') || wp_is_post_revision($post_id)) {
      return $post_id;
    }
    $new_position = isset($_POST['placement']) ? (int) $_POST['placement'] : NULL;
    $placements = get_option('placements');
    $orig_placements = $placements;
    $found_position = array_search($post_id, $placements, TRUE);
    if ($found_position !== FALSE) {
      $placements[$found_position] = 0;
    }
    if ($new_position > -1) {
      $placements[$new_position] = $post_id;
    }
    if ($orig_placements !== $placements) {
      update_option('placements', $placements);
    }
  }

}
