<?php
/**
 * Template to display the placement select dropdown.
 */

namespace Netzstrategen\Placement;

$placements = get_option('placements');
$found_position = array_search((int) $post->ID, $placements, TRUE);
?>
<div class="misc-pub-section category-add">
  <label for="placement"><?= __('Placement position', Plugin::L10N) ?></label>
  <select name="placement" id="placement">
    <option value="-1"><?= __('– No position –', Plugin::L10N) ?></option>
    <?php for ($i = 0; $i < 10; $i++): ?>
      <option value="<?= $i ?>" <?= $found_position === $i ? 'selected' : '' ?>>
        <?= sprintf(__('%d: %s', Plugin::L10N), $i + 1, $placements[$i] ? wp_trim_words(get_the_title($placements[$i]), 4) : '–') ?>
      </option>
    <?php endfor ?>
  </select>
</div>

