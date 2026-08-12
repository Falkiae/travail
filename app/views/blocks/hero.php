<?php
require_once __DIR__ . '/_block_helpers.php';
$booking_url = isset($booking_url) ? $booking_url : '#';

$bg = isset($block['bg']) ? $block['bg'] : 'cream';
if (!in_array($bg, ['white','alt','blue','night','cream','rose'], true)) $bg = 'cream';
$visualType   = isset($block['visual_type']) ? $block['visual_type'] : 'photo';
$isVideoBg    = ($visualType === 'video_bg');
$isDark = ($bg === 'blue' || $bg === 'night' || $isVideoBg);
$isFirstBlock = (isset($__block_index) && $__block_index === 0);
$heroExtra    = 'kn-hero' . ($isVideoBg ? ' kn-hero--video-bg' : '') . ($isFirstBlock ? ' kn-hero--under-nav' : '');
$sectionClass = blockClasses($block, 'cream', $heroExtra);
$gridClass    = gridClasses($block, '1-1');

$isRose = ($bg === 'rose');
$eyebrowColor = $isDark ? 'var(--kn-rose-mid)' : ($isRose ? 'var(--kn-rose-600)' : 'var(--kn-blue)');
$h1Color      = $isDark ? 'var(--kn-white)'  : 'var(--kn-night)';
$proofColor   = $isDark ? 'rgba(255,255,255,.7)' : 'var(--kn-muted)';

$pillClassMap = [
    'white'  => $isDark ? 'kn-pill--white' : 'kn-pill--blue',
    'night'  => 'kn-pill--night',
    'blue'   => 'kn-pill--blue',
    'cream'  => 'kn-pill--cream',
    'rose'   => 'kn-pill--rose',
    // Alias hérité : du contenu déjà enregistré référence encore "yellow",
    // qui a toujours rendu du rose (jamais du jaune) — conservé pour ne
    // pas casser ces pastilles existantes.
    'yellow' => 'kn-pill--rose',
];
$defaultPills = [
    ['icon' => 'check',  'text' => 'À domicile',     'style' => 'white'],
    ['icon' => 'shield', 'text' => 'Garantie',       'style' => 'yellow'],
    ['icon' => 'zap',    'text' => 'Devis en 2 min', 'style' => 'night'],
];
$rawPills = isset($block['pills']) && is_array($block['pills']) ? $block['pills'] : $defaultPills;
?>
<section class="<?php echo $sectionClass; ?>">
  <?php if ($isVideoBg && !empty($block['video_bg_url'])): ?>
  <video
    class="kn-hero__bg-video"
    autoplay muted loop playsinline aria-hidden="true"
    preload="<?php echo !empty($block['video_bg_poster_url']) ? 'metadata' : 'auto'; ?>"
    <?php if (!empty($block['video_bg_poster_url'])): ?>poster="<?php echo htmlspecialchars($block['video_bg_poster_url']); ?>"<?php endif; ?>
  >
    <?php if (!empty($block['video_bg_webm_url'])): ?>
    <source src="<?php echo htmlspecialchars($block['video_bg_webm_url']); ?>" type="video/webm">
    <?php endif; ?>
    <source src="<?php echo htmlspecialchars($block['video_bg_url']); ?>" type="video/mp4">
  </video>
  <?php endif; ?>
  <div class="container">
    <div class="<?php echo $gridClass; ?>">
      <div class="kn-hero__content">
        <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;">
          <?php echo isset($block['eyebrow']) ? $block['eyebrow'] : 'CANAPÉ · VOITURE · MATELAS — À DOMICILE'; ?>
        </span>
        <h1 class="kn-hero__heading" style="color:<?php echo $h1Color; ?>;">
          <?php echo isset($block['h1']) ? $block['h1'] : 'Nettoyage à domicile de canapé, matelas et voitures.'; ?>
        </h1>
        <?php if (!empty($block['body'])): ?>
        <p class="kn-hero__body"><?php echo $block['body']; ?></p>
        <?php endif; ?>
        <div class="kn-hero__pills">
          <?php foreach ($rawPills as $pill):
            $pillText  = is_array($pill) ? (isset($pill['text'])  ? $pill['text']  : '') : $pill;
            $pillStyle = is_array($pill) ? (isset($pill['style']) ? $pill['style'] : 'white') : 'white';
            $pillCls   = isset($pillClassMap[$pillStyle]) ? $pillClassMap[$pillStyle] : 'kn-pill--blue';
            $pillIcon  = is_array($pill) && isset($pill['icon']) ? $pill['icon'] : '';
            // Contenu ancien : l'icône était collée au début du libellé
            if ($pillIcon === '' && preg_match('/^(\S+)\s+(.+)$/u', $pillText, $pm) && knIconName($pm[1]) !== '') {
                $pillIcon = $pm[1];
                $pillText = $pm[2];
            }
          ?>
          <span class="kn-pill <?php echo $pillCls; ?>"><?php echo knIcon($pillIcon, array('size' => 15, 'class' => 'kn-icon--pill')); ?><?php echo htmlspecialchars($pillText); ?></span>
          <?php endforeach; ?>
        </div>
        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-card <?php echo $isDark ? 'kn-cta-card--glass' : 'kn-cta-card--navy'; ?>">
          <div class="kn-cta-card__content">
            <span class="kn-eyebrow"><?php echo knIcon('calendar', array('size' => 14, 'class' => 'kn-icon--eyebrow')); ?><?php echo htmlspecialchars(isset($block['cta_eyebrow']) ? $block['cta_eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
            <p class="kn-cta-card__title"><?php echo htmlspecialchars(isset($block['cta_title']) ? $block['cta_title'] : 'Prendre RDV en 2 min'); ?></p>
          </div>
          <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
        </a>
        <?php if (!empty($block['social_proof'])): ?>
        <p class="kn-hero__proof" style="color:<?php echo $proofColor; ?>;">
          <?php echo htmlspecialchars($block['social_proof']); ?>
        </p>
        <?php endif; ?>
      </div>
      <?php if (!$isVideoBg): ?>
      <div class="kn-hero__visual">
        <?php if ($visualType === 'video' && !empty($block['video_url'])): ?>
          <video
            <?php echo !empty($block['video_autoplay']) ? 'autoplay' : ''; ?>
            <?php echo !empty($block['video_loop'])     ? 'loop'     : ''; ?>
            <?php echo !empty($block['video_muted'])    ? 'muted'    : ''; ?>
            <?php echo !empty($block['video_controls']) ? 'controls' : ''; ?>
            preload="<?php echo !empty($block['video_poster_url']) ? 'metadata' : 'auto'; ?>"
            <?php if (!empty($block['video_poster_url'])): ?>poster="<?php echo htmlspecialchars($block['video_poster_url']); ?>"<?php endif; ?>
            playsinline
          >
            <?php if (!empty($block['video_webm_url'])): ?>
            <source src="<?php echo htmlspecialchars($block['video_webm_url']); ?>" type="video/webm">
            <?php endif; ?>
            <source src="<?php echo htmlspecialchars($block['video_url']); ?>" type="video/mp4">
          </video>
        <?php elseif (!empty($block['image_url'])): ?>
          <?php echo knImage($block['image_url'], isset($block['image_alt']) ? $block['image_alt'] : '', ['loading' => 'eager', 'imgSizes' => '50vw']); ?>
        <?php else: ?>
          <div class="kn-placeholder kn-placeholder--hero">Photo / illustration héro</div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
