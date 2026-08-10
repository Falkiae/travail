<?php
require_once __DIR__ . '/_block_helpers.php';

// $reviews est maintenant ['items' => [...], 'rating' => 4.9, 'total' => 110]
// (avant : un simple tableau d'avis — la note globale et le nombre total
// étaient demandés à l'API puis jetés, jamais affichés).
$reviewsData = isset($reviews) && is_array($reviews) ? $reviews : array();
$items       = isset($reviewsData['items']) && is_array($reviewsData['items']) ? $reviewsData['items'] : (is_array($reviewsData) ? $reviewsData : array());
$avgRating   = isset($reviewsData['rating']) ? (float) $reviewsData['rating'] : null;
$totalCount  = isset($reviewsData['total']) ? (int) $reviewsData['total'] : null;
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
    <div class="kn-reviews__grid">
      <?php foreach ($items as $r): ?>
      <?php $authorName = isset($r['author_name']) ? $r['author_name'] : ''; ?>
      <div class="kn-card kn-review-card">
        <div class="kn-review-card__header">
          <div class="kn-review-card__avatar" aria-hidden="true"><?php echo mb_strtoupper(mb_substr($authorName !== '' ? $authorName : '?', 0, 1)); ?></div>
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
    <?php else: ?>
    <p class="text-muted" style="text-align:center;margin-top:2rem;">Les avis Google apparaîtront ici (configurez la clé API et le Place ID dans les réglages).</p>
    <?php endif; ?>
  </div>
</section>
