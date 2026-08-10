<?php
require_once __DIR__ . '/_block_helpers.php';

// $reviews est ['items' => [...], 'rating' => 4.9, 'total' => 110]
// (avant : un simple tableau d'avis — la note globale et le nombre total
// étaient demandés à l'API puis jetés, jamais affichés).
$reviewsData = isset($reviews) && is_array($reviews) ? $reviews : array();
$items       = isset($reviewsData['items']) && is_array($reviewsData['items']) ? $reviewsData['items'] : (is_array($reviewsData) ? $reviewsData : array());
$avgRating   = isset($reviewsData['rating']) ? (float) $reviewsData['rating'] : null;
$totalCount  = isset($reviewsData['total']) ? (int) $reviewsData['total'] : null;
$blockId     = 'reviews-' . (isset($__block_index) ? (int) $__block_index : 0);
?>
<section class="<?php echo blockClasses($block, 'white'); ?>">
  <div class="container">
    <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'AVIS CLIENTS'); ?></span>
    <?php $revH2 = !empty($block['h2']) ? $block['h2'] : (!empty($block['title']) ? $block['title'] : ''); ?>
    <?php if ($revH2): ?>
    <h2 class="kn-section__title"><?php echo htmlspecialchars($revH2); ?></h2>
    <?php else: ?>
    <h2 class="kn-section__title">Ils ont testé. <span class="kn-tape">Ils l'ont vu.</span></h2>
    <?php endif; ?>

    <?php if ($avgRating !== null || $totalCount !== null): ?>
    <div class="kn-reviews__summary">
      <?php if ($avgRating !== null): ?>
      <span class="kn-reviews__summary-stars" aria-hidden="true">
        <?php for ($i = 0; $i < 5; $i++): ?>
        <?php echo knIcon('star', array('size' => 18, 'fill' => true, 'class' => $i < round($avgRating) ? 'kn-reviews__star-on' : 'kn-reviews__star-off')); ?>
        <?php endfor; ?>
      </span>
      <?php endif; ?>
      <p class="kn-reviews__summary-text">
        <?php if ($avgRating !== null): ?><strong><?php echo htmlspecialchars(number_format($avgRating, 1, ',', '')); ?>/5</strong><?php endif; ?>
        <?php if ($avgRating !== null && $totalCount !== null): ?> · <?php endif; ?>
        <?php if ($totalCount !== null): ?>basé sur <strong><?php echo htmlspecialchars((string) $totalCount); ?> avis</strong> Google<?php endif; ?>
      </p>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="kn-reviews__slider" id="<?php echo $blockId; ?>">
      <div class="kn-reviews__track" tabindex="0" aria-label="Avis clients, défiler pour en voir plus">
        <?php foreach ($items as $r): ?>
        <?php
          $authorName = isset($r['author_name']) ? $r['author_name'] : '';
          $photoUrl   = !empty($r['profile_photo_url']) ? $r['profile_photo_url'] : '';
        ?>
        <div class="kn-card kn-review-card">
          <div class="kn-review-card__header">
            <?php if ($photoUrl !== ''): ?>
            <img class="kn-review-card__avatar kn-review-card__avatar--photo" src="<?php echo htmlspecialchars($photoUrl); ?>" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'kn-review-card__avatar',textContent:this.dataset.fallback}))" data-fallback="<?php echo htmlspecialchars(mb_strtoupper(mb_substr($authorName !== '' ? $authorName : '?', 0, 1))); ?>">
            <?php else: ?>
            <div class="kn-review-card__avatar" aria-hidden="true"><?php echo mb_strtoupper(mb_substr($authorName !== '' ? $authorName : '?', 0, 1)); ?></div>
            <?php endif; ?>
            <div class="kn-review-card__identity">
              <strong class="kn-review-card__name"><?php echo htmlspecialchars($authorName); ?></strong>
              <div class="kn-stars" aria-label="<?php echo (int)(isset($r['rating']) ? $r['rating'] : 5); ?>/5">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <span class="kn-stars__star<?php echo $i < (int)(isset($r['rating']) ? $r['rating'] : 5) ? ' is-on' : ''; ?>"><?php echo knIcon('star', array('size' => 14, 'fill' => true)); ?></span>
                <?php endfor; ?>
              </div>
            </div>
          </div>
          <p class="kn-review-card__text"><?php echo htmlspecialchars(isset($r['text']) ? $r['text'] : ''); ?></p>
          <?php if (!empty($r['relative_time_description'])): ?>
          <span class="kn-review-card__date text-muted"><?php echo htmlspecialchars($r['relative_time_description']); ?></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($items) > 1): ?>
      <div class="kn-reviews__nav">
        <button type="button" class="kn-reviews__arrow kn-reviews__arrow--prev" aria-label="Avis précédents">&#8592;</button>
        <div class="kn-reviews__dots" role="tablist" aria-label="Aller à un avis"></div>
        <button type="button" class="kn-reviews__arrow kn-reviews__arrow--next" aria-label="Avis suivants">&#8594;</button>
      </div>
      <?php endif; ?>
    </div>
    <script>
    (function () {
      var root = document.getElementById('<?php echo $blockId; ?>');
      if (!root) return;
      var track = root.querySelector('.kn-reviews__track');
      var cards = Array.prototype.slice.call(track.children);
      var prevBtn = root.querySelector('.kn-reviews__arrow--prev');
      var nextBtn = root.querySelector('.kn-reviews__arrow--next');
      var dotsWrap = root.querySelector('.kn-reviews__dots');
      if (!cards.length) return;

      // Un point tous les ~1 carte visible à la fois (approximation simple,
      // recalculée au resize) plutôt qu'un point par carte individuelle.
      function cardsPerView() {
        var w = root.clientWidth;
        if (w >= 980) return 3;
        if (w >= 640) return 2;
        return 1;
      }

      function buildDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        var per = cardsPerView();
        var pages = Math.max(1, Math.ceil(cards.length / per));
        for (var i = 0; i < pages; i++) {
          var dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'kn-reviews__dot';
          dot.setAttribute('aria-label', 'Page ' + (i + 1));
          dot.addEventListener('click', function (idx, per) {
            return function () { scrollToCard(idx * per); };
          }(i, per));
          dotsWrap.appendChild(dot);
        }
        updateActiveDot();
      }

      function scrollToCard(index) {
        var card = cards[Math.max(0, Math.min(cards.length - 1, index))];
        if (!card) return;
        track.scrollTo({ left: card.offsetLeft - track.offsetLeft, behavior: 'smooth' });
      }

      function currentIndex() {
        var pos = track.scrollLeft + track.offsetWidth * 0.1;
        var idx = 0;
        cards.forEach(function (c, i) { if (c.offsetLeft - track.offsetLeft <= pos) idx = i; });
        return idx;
      }

      function updateActiveDot() {
        if (!dotsWrap) return;
        var per = cardsPerView();
        var page = Math.floor(currentIndex() / per);
        Array.prototype.forEach.call(dotsWrap.children, function (d, i) {
          d.classList.toggle('is-active', i === page);
        });
        if (prevBtn) prevBtn.disabled = track.scrollLeft <= 4;
        if (nextBtn) nextBtn.disabled = track.scrollLeft >= track.scrollWidth - track.offsetWidth - 4;
      }

      if (prevBtn) prevBtn.addEventListener('click', function () { scrollToCard(currentIndex() - cardsPerView()); });
      if (nextBtn) nextBtn.addEventListener('click', function () { scrollToCard(currentIndex() + cardsPerView()); });
      track.addEventListener('scroll', function () {
        window.requestAnimationFrame(updateActiveDot);
      }, { passive: true });
      window.addEventListener('resize', buildDots);

      buildDots();
    })();
    </script>
    <?php else: ?>
    <p class="text-muted" style="text-align:center;margin-top:2rem;">Les avis Google apparaîtront ici (configurez la clé API et le Place ID dans les réglages).</p>
    <?php endif; ?>
  </div>
</section>
