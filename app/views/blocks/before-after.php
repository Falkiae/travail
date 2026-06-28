<?php
require_once __DIR__ . '/_block_helpers.php';

$bg     = isset($block['bg']) ? $block['bg'] : 'alt';
$isDark = ($bg === 'blue' || $bg === 'night');

$sectionClass = blockClasses($block, 'alt', 'kn-before-after');
$eyebrowColor = $isDark ? 'var(--kn-yellow)' : 'var(--kn-blue)';
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
        <div class="kn-ba-slider" data-ba-slider>
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
    var handle = slider.querySelector('.kn-ba-slider__handle');
    var wrap   = slider.querySelector('.kn-ba-slider__before-wrap');
    var pct = 50;

    function setPos(x) {
      var rect = slider.getBoundingClientRect();
      pct = Math.min(100, Math.max(0, ((x - rect.left) / rect.width) * 100));
      wrap.style.width = pct + '%';
      handle.style.left = pct + '%';
    }

    handle.addEventListener('mousedown', function(e) {
      e.preventDefault();
      function onMove(e) { setPos(e.clientX); }
      function onUp()   { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); }
      document.addEventListener('mousemove', onMove);
      document.addEventListener('mouseup', onUp);
    });

    handle.addEventListener('touchstart', function(e) {
      function onMove(e) { setPos(e.touches[0].clientX); }
      function onEnd()   { document.removeEventListener('touchmove', onMove); document.removeEventListener('touchend', onEnd); }
      document.addEventListener('touchmove', onMove, { passive: true });
      document.addEventListener('touchend', onEnd);
    });

    // Init
    wrap.style.width = pct + '%';
    handle.style.left = pct + '%';
  });
})();
</script>
