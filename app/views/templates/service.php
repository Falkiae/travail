<?php
$booking_url = isset($booking_url) ? $booking_url : '#';
$reviews     = isset($reviews) && is_array($reviews) ? $reviews : array();
$blocks      = isset($blocks) && is_array($blocks) ? $blocks : array();
?>
<?php if (empty($blocks)): ?>
  <p style="text-align:center;padding:4rem;">Aucun bloc configuré. <a href="/admin/pages">Ajouter des blocs</a></p>
<?php else: ?>
<?php foreach ($blocks as $block):
    $btype    = isset($block['type']) ? $block['type'] : '';
    $safeType = preg_replace('/[^a-z0-9\-]/', '', $btype);
    $blockFile = APP_PATH . '/views/blocks/' . $safeType . '.php';
    if ($safeType && file_exists($blockFile)) {
        include $blockFile;
    }
endforeach; ?>
<?php endif; ?>
