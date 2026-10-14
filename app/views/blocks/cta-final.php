<?php
require_once __DIR__ . '/_block_helpers.php';
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'blue';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) {
    $bg = 'blue';
}
$bgClass = 'kn-bg--' . $bg;
$isDark  = ($bg === 'blue' || $bg === 'night');
// Sur un fond déjà sombre, une carte navy s'y fondait presque
// entièrement (même couleur que la section) : verre dépoli à la place,
// comme sur les blocs hero et domicile-vs-atelier.
$cardStyle = $isDark ? 'kn-cta-card--glass' : 'kn-cta-card--navy';

// Visibility classes
$visClasses = '';
$visible = isset($block['visible']) && is_array($block['visible']) ? $block['visible'] : ['desktop','tablet','mobile'];
if (!in_array('mobile',  $visible, true)) $visClasses .= ' kn-hide-mobile';
if (!in_array('tablet',  $visible, true)) $visClasses .= ' kn-hide-tablet';
if (!in_array('desktop', $visible, true)) $visClasses .= ' kn-hide-desktop';

$eyebrow = isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE';
$title   = !empty($block['title']) ? $block['title'] : 'Qu\'attendez-vous pour <span class="hl-inverse">prendre RDV</span> ?';
$intro   = isset($block['intro'])   ? $block['intro']   : 'Rejoignez nos nombreux clients satisfaits : intervention à domicile, résultat garanti.';
$ctaText = !empty($block['cta_title']) ? $block['cta_title'] : 'Prendre RDV en 2 min';
$phone   = isset($block['phone'])   ? $block['phone']   : '+32 (0)4 55 13 84 19';
$phoneHref = 'tel:' . preg_replace('/[^+0-9]/', '', $phone);

// Avis : note globale + deux témoignages récents (si l'API Google est configurée)
$showReviews = !isset($block['show_reviews']) || !empty($block['show_reviews']);
$rv          = isset($reviews) && is_array($reviews) ? $reviews : array();
$rvRating    = isset($rv['rating']) ? (float) $rv['rating'] : null;
$rvTotal     = isset($rv['total'])  ? (int) $rv['total']    : null;
$rvPicked    = array();
if ($showReviews && !empty($rv['items']) && is_array($rv['items'])) {
    foreach ($rv['items'] as $r) {
        if (empty($r['text']) || (int) (isset($r['rating']) ? $r['rating'] : 0) < 5) continue;
        $rvPicked[] = $r;
        if (count($rvPicked) === 2) break;
    }
}
$hasReviews = $showReviews && ($rvRating !== null || $rvPicked);
?>
<section class="kn-section kn-cta-pave kn-final <?php echo $bgClass . $visClasses; ?><?php echo $hasReviews ? ' kn-final--split' : ''; ?>">
  <div class="container">
    <div class="kn-final__grid">
      <div class="kn-final__main">
        <span class="kn-eyebrow"><?php echo knIcon('calendar', array('size' => 14, 'class' => 'kn-icon--eyebrow')); ?><?php echo htmlspecialchars($eyebrow); ?></span>
        <h2 class="kn-final__title"><?php echo $title; ?></h2>
        <?php if ($intro !== ''): ?>
        <p class="kn-final__intro"><?php echo htmlspecialchars($intro); ?></p>
        <?php endif; ?>
        <?php if ($showReviews && $rvRating !== null): ?>
        <div class="kn-final__rating">
          <span class="kn-final__stars" aria-hidden="true">
            <?php for ($i = 0; $i < 5; $i++): ?><?php echo knIcon('star', array('size' => 18, 'fill' => true, 'class' => $i < round($rvRating) ? 'kn-reviews__star-on' : 'kn-reviews__star-off')); ?><?php endfor; ?>
          </span>
          <span class="kn-final__rating-text"><strong><?php echo htmlspecialchars(number_format($rvRating, 1, ',', '')); ?>/5</strong><?php if ($rvTotal !== null): ?> · <?php echo htmlspecialchars((string) $rvTotal); ?> avis Google<?php endif; ?></span>
        </div>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-card <?php echo $cardStyle; ?>">
          <div class="kn-cta-card__content">
            <span class="kn-eyebrow"><?php echo knIcon('calendar', array('size' => 14, 'class' => 'kn-icon--eyebrow')); ?>Réservation en ligne</span>
            <p class="kn-cta-card__title"><?php echo htmlspecialchars($ctaText); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>
        <p class="kn-cta-pave__phone">
          Ou appelez-nous : <a href="<?php echo htmlspecialchars($phoneHref); ?>"><?php echo htmlspecialchars($phone); ?></a>
        </p>
      </div>
      <?php if ($rvPicked): ?>
      <div class="kn-final__reviews">
        <p class="kn-final__reviews-label">Les avis les plus récents</p>
        <?php foreach ($rvPicked as $r):
          $name  = isset($r['author_name']) ? $r['author_name'] : '';
          $photo = !empty($r['profile_photo_url']) ? $r['profile_photo_url'] : '';
          $init  = mb_strtoupper(mb_substr($name !== '' ? $name : '?', 0, 1));
          $text  = mb_strimwidth(trim($r['text']), 0, 220, '…', 'UTF-8');
        ?>
        <figure class="kn-final__quote">
          <span class="kn-final__quote-stars" aria-hidden="true">
            <?php for ($i = 0; $i < 5; $i++): ?><?php echo knIcon('star', array('size' => 14, 'fill' => true, 'class' => 'kn-reviews__star-on')); ?><?php endfor; ?>
          </span>
          <blockquote>« <?php echo htmlspecialchars($text); ?> »</blockquote>
          <figcaption>
            <?php if ($photo !== ''): ?>
            <img class="kn-review-card__avatar kn-review-card__avatar--photo" src="<?php echo htmlspecialchars($photo); ?>" alt="" loading="lazy" referrerpolicy="no-referrer" data-fallback="<?php echo htmlspecialchars($init); ?>" onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'kn-review-card__avatar',textContent:this.dataset.fallback}))">
            <?php else: ?>
            <div class="kn-review-card__avatar" aria-hidden="true"><?php echo htmlspecialchars($init); ?></div>
            <?php endif; ?>
            <span><strong><?php echo htmlspecialchars($name); ?></strong><small>Avis Google</small></span>
          </figcaption>
        </figure>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
