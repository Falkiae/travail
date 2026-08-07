<?php
require_once __DIR__ . '/_block_helpers.php';
$reviews = isset($reviews) && is_array($reviews) ? $reviews : array();
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
    <?php if (!empty($reviews)): ?>
    <div class="kn-reviews__grid">
      <?php foreach ($reviews as $r): ?>
      <div class="kn-card kn-review-card">
        <div class="kn-review-card__header">
          <div class="kn-review-card__avatar"><?php echo mb_strtoupper(mb_substr(isset($r['author_name']) ? $r['author_name'] : '?', 0, 1)); ?></div>
          <div>
            <strong><?php echo htmlspecialchars(isset($r['author_name']) ? $r['author_name'] : ''); ?></strong>
            <div class="kn-stars" aria-label="<?php echo (int)(isset($r['rating']) ? $r['rating'] : 5); ?>/5">
              <?php for ($i = 0; $i < 5; $i++): ?>
              <span class="kn-stars__star" style="color:<?php echo $i < (int)(isset($r['rating']) ? $r['rating'] : 5) ? 'var(--kn-gold)' : 'var(--kn-border)'; ?>"><?php echo knIcon('star', array('size' => 15)); ?></span>
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
    <p class="text-muted" style="text-align:center;margin-top:2rem;">Les avis Google apparaîtront ici (configurez la clé API dans les réglages).</p>
    <?php endif; ?>
    <div class="kn-reviews__proof" style="text-align:center;margin-top:2.5rem;">
      <span class="kn-pill kn-pill--yellow"><?php echo knIcon('star', array('size' => 15, 'class' => 'kn-icon--pill')); ?>4,9/5 · +110 avis Google</span>
    </div>
  </div>
</section>
