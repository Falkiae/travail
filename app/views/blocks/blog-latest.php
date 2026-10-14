<?php
require_once __DIR__ . '/_block_helpers.php';

$count = isset($block['count']) ? max(1, min(6, (int) $block['count'])) : 3;
$posts = isset($latest_posts) && is_array($latest_posts) ? array_slice($latest_posts, 0, $count) : array();
if (!$posts) return;
?>
<section class="<?php echo blockClasses($block, 'cream'); ?>">
  <div class="container">
    <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'LE JOURNAL'); ?></span>
    <h2 class="kn-section__title"><?php echo !empty($block['h2']) ? $block['h2'] : 'Conseils d\'entretien &amp; <span class="kn-tape">actualités</span>'; ?></h2>
    <?php if (!empty($block['intro'])): ?>
    <p class="kn-services__intro"><?php echo htmlspecialchars($block['intro']); ?></p>
    <?php endif; ?>

    <div class="kn-blog-grid kn-blog-latest__grid">
      <?php foreach ($posts as $p):
        $dateSrc = !empty($p['published_at']) ? $p['published_at'] : $p['created_at'];
      ?>
      <a href="/blog/<?php echo htmlspecialchars($p['slug']); ?>" class="kn-blog-card">
        <div class="kn-blog-card__media">
          <?php if (!empty($p['image_url'])): ?>
          <?php echo knImage($p['image_url'], htmlspecialchars($p['title']), array('imgSizes' => '(max-width:768px) 100vw, 380px', 'style' => 'width:100%;height:100%;object-fit:cover')); ?>
          <?php else: ?>
          <span class="kn-blog-card__media-placeholder" aria-hidden="true">&#10022;</span>
          <?php endif; ?>
        </div>
        <div class="kn-blog-card__body">
          <?php if (!empty($p['category_name'])): ?>
          <span class="kn-blog-card__cat"><?php echo htmlspecialchars($p['category_name']); ?></span>
          <?php endif; ?>
          <h3 class="kn-blog-card__title"><?php echo htmlspecialchars($p['title']); ?></h3>
          <?php if (!empty($p['excerpt'])): ?>
          <p class="kn-blog-card__excerpt"><?php echo htmlspecialchars($p['excerpt']); ?></p>
          <?php endif; ?>
          <time class="kn-blog-card__date" datetime="<?php echo htmlspecialchars(date('Y-m-d', strtotime($dateSrc))); ?>">
            <?php echo htmlspecialchars(date('d.m.Y', strtotime($dateSrc))); ?>
          </time>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <p class="kn-blog-latest__more">
      <a href="/blog" class="kn-blog-latest__link"><?php echo htmlspecialchars(!empty($block['cta_label']) ? $block['cta_label'] : 'Voir tous les articles'); ?> &rarr;</a>
    </p>
  </div>
</section>
