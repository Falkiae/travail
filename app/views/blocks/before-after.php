<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'alt';
$isDark = ($bg === 'blue' || $bg === 'night');
$isRose = ($bg === 'rose');

$sectionClass = blockClasses($block, 'alt', 'kn-before-after');
$eyebrowColor = $isDark ? 'var(--kn-rose-mid)' : ($isRose ? 'var(--kn-rose-600)' : 'var(--kn-blue)');
$headingColor = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';

$defaultItems = [
    ['image_before_url' => '', 'image_after_url' => '', 'caption' => 'Avant / Après'],
];
$items = isset($block['items']) && is_array($block['items']) && count($block['items']) ? $block['items'] : $defaultItems;
?>
<section class="<?php echo $sectionClass; ?>">
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo $block['eyebrow']; ?></span>
    <?php endif; ?>
    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>
    <div class="kn-ba-grid">
      <?php foreach ($items as $item):
        $before = isset($item['image_before_url']) ? $item['image_before_url'] : '';
        $after  = isset($item['image_after_url'])  ? $item['image_after_url']  : '';
        $cap    = isset($item['caption'])           ? $item['caption']           : '';
      ?>
      <div class="kn-ba-item">
        <div class="kn-ba-slider" data-ba-slider tabindex="0" role="slider" aria-label="Comparer avant et après" aria-valuemin="0" aria-valuemax="100">
          <?php if ($after): ?>
          <?php echo knImage($after, 'Après', ['class' => 'kn-ba-slider__after', 'imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
          <?php else: ?>
          <div class="kn-ba-slider__after kn-placeholder">Après</div>
          <?php endif; ?>
          <div class="kn-ba-slider__before-wrap">
            <?php if ($before): ?>
            <?php echo knImage($before, 'Avant', ['class' => 'kn-ba-slider__before', 'imgSizes' => '(max-width:768px) 100vw, 50vw']); ?>
            <?php else: ?>
            <div class="kn-ba-slider__before kn-placeholder">Avant</div>
            <?php endif; ?>
          </div>
          <div class="kn-ba-slider__handle" aria-label="Glisser pour comparer">
            <div class="kn-ba-slider__line"></div>
            <div class="kn-ba-slider__btn">
              <span>&#8249;</span><span>&#8250;</span>
            </div>
            <div class="kn-ba-slider__line"></div>
          </div>
          <span class="kn-ba-slider__badge kn-ba-slider__badge--before">Avant</span>
          <span class="kn-ba-slider__badge kn-ba-slider__badge--after">Après</span>
        </div>
        <?php if ($cap): ?>
        <p class="kn-ba-item__caption"><?php echo htmlspecialchars($cap); ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<script>
(function() {
  document.querySelectorAll('[data-ba-slider]').forEach(function(slider) {
    if (slider.dataset.baInit) return;
    slider.dataset.baInit = '1';

    function setPos(clientX) {
      var rect = slider.getBoundingClientRect();
      var pct = Math.min(100, Math.max(0, ((clientX - rect.left) / rect.width) * 100));
      slider.style.setProperty('--ba-pos', pct + '%');
    }

    // Toute la zone est saisissable (pas seulement la poignée) ; le geste
    // vertical reste un défilement de page grâce à touch-action: pan-y.
    slider.addEventListener('pointerdown', function(e) {
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      try { slider.setPointerCapture(e.pointerId); } catch (err) {}
      slider.classList.add('is-dragging');
      setPos(e.clientX);
    });
    slider.addEventListener('pointermove', function(e) {
      if (slider.classList.contains('is-dragging')) setPos(e.clientX);
    });
    function end(e) {
      slider.classList.remove('is-dragging');
      try { slider.releasePointerCapture(e.pointerId); } catch (err) {}
    }
    slider.addEventListener('pointerup', end);
    slider.addEventListener('pointercancel', end);

    slider.addEventListener('keydown', function(e) {
      var cur = parseFloat(getComputedStyle(slider).getPropertyValue('--ba-pos')) || 50;
      if (e.key === 'ArrowLeft')  { slider.style.setProperty('--ba-pos', Math.max(0, cur - 5) + '%'); e.preventDefault(); }
      if (e.key === 'ArrowRight') { slider.style.setProperty('--ba-pos', Math.min(100, cur + 5) + '%'); e.preventDefault(); }
    });
  });
})();
</script>
