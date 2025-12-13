<?php
$current_step = $step ?? 1;
$labels = [
  1 => 'Overview',
  2 => 'Personnel',
  3 => 'Students',
  4 => 'Travel',
  5 => 'Subawards',
  6 => 'Review'
];
?>
<div class="d-flex flex-wrap gap-1">
  <?php foreach ($labels as $num => $label): ?>
    <span class="bb-step-pill <?= $num == $current_step ? 'active' : 'inactive' ?>">
      <?= $num ?> · <?= h($label) ?>
    </span>
  <?php endforeach; ?>
</div>
