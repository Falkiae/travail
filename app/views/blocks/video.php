<?php
$url = isset($block['url']) ? $block['url'] : '';
$embed = '';
if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m) || preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
    $embed = 'https://www.youtube.com/embed/' . $m[1];
} elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
    $embed = 'https://player.vimeo.com/video/' . $m[1];
}
$bg = isset($block['bg']) ? $block['bg'] : 'white';
$bgClass = 'kn-bg--' . (in_array($bg, array('white','alt','blue','night','cream','rose'), true) ? $bg : 'white');
?>
<?php if ($embed): ?>
<section class="kn-section <?php echo $bgClass; ?>">
  <div class="container">
    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:var(--r-card);">
      <iframe src="<?php echo htmlspecialchars($embed); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
    </div>
    <?php if (!empty($block['caption'])): ?>
    <p style="color:var(--kn-muted);font-size:.875rem;margin-top:.5rem;text-align:center;"><?php echo htmlspecialchars($block['caption']); ?></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
