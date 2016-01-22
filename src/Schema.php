<?php

/**
 * @file
 * Contains \Netzstrategen\Placement\Schema.
 */

namespace Netzstrategen\Placement;

/**
 * Generic plugin lifetime and maintenance functionality.
 */
class Schema {

  /**
   * register_activation_hook() callback.
   */
  public static function activate() {
    add_option('placements', array_fill(0, Plugin::POSITIONS, 0), NULL, FALSE);
  }

  /**
   * register_deactivation_hook() callback.
   */
  public static function deactivate() {
  }

  /**
   * register_uninstall_hook() callback.
   */
  public static function uninstall() {
    delete_option('placements');
  }

}
