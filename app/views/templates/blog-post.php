<?php
require_once APP_PATH . '/views/blocks/_block_helpers.php';

$post           = isset($post) && is_array($post) ? $post : array();
$blocks         = isset($blocks) && is_array($blocks) ? $blocks : array();
$related        = isset($related_posts) && is_array($related_posts) ? $related_posts : array();
$readingMinutes = isset($reading_minutes) ? (int) $reading_minutes : 1;
$booking_url    = isset($booking_url) ? $booking_url : '#';

$dateSrc = !empty($post['published_at']) ? $post['published_at'] : (isset($post['created_at']) ? $post['created_at'] : 'now');
?>
<article class="kn-blog-article">

  <!-- ── En-tête récurrent : identique pour chaque article ──────── -->
  <header class="kn-blog-article__head">
    <div class="container">
      <nav class="kn-blog-breadcrumb" aria-label="Fil d'Ariane">
        <a href="/blog">Blog</a>
        <span aria-hidden="true">/</span>
        <?php if (!empty($post['category_name'])): ?>
        <a href="/blog?cat=<?php echo urlencode($post['category_slug']); ?>"><?php echo htmlspecialchars($post['category_name']); ?></a>
        <span aria-hidden="true">/</span>
        <?php endif; ?>
        <span class="kn-blog-breadcrumb__current"><?php echo htmlspecialchars($post['title']); ?></span>
      </nav>

      <?php if (!empty($post['category_name'])): ?>
      <span class="kn-eyebrow"><?php echo htmlspecialchars($post['category_name']); ?></span>
      <?php endif; ?>

      <h1 class="kn-blog-article__title"><?php echo htmlspecialchars($post['title']); ?></h1>

      <div class="kn-blog-article__meta">
        <time datetime="<?php echo htmlspecialchars(date('Y-m-d', strtotime($dateSrc))); ?>"><?php echo htmlspecialchars(date('d.m.Y', strtotime($dateSrc))); ?></time>
        <span class="kn-blog-article__meta-sep" aria-hidden="true">&middot;</span>
        <span><?php echo $readingMinutes; ?> min de lecture</span>
      </div>
    </div>

    <?php if (!empty($post['image_url'])): ?>
    <div class="kn-blog-article__cover">
      <?php echo knImage($post['image_url'], htmlspecialchars($post['title']), array('loading' => 'eager', 'imgSizes' => '(max-width:900px) 100vw, 1160px', 'style' => 'width:100%;height:100%;object-fit:cover')); ?>
    </div>
    <?php endif; ?>
  </header>

  <!-- ── Corps : librement composé via l'éditeur de blocs ────────── -->
  <div class="kn-blog-article__body">
    <?php if (!empty($post['excerpt'])): ?>
    <div class="container">
      <p class="kn-blog-article__excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
    </div>
    <?php endif; ?>

    <?php if (empty($blocks)): ?>
    <div class="container">
      <p style="text-align:center;padding:3rem 0;color:var(--kn-muted)">Cet article n'a pas encore de contenu.</p>
    </div>
    <?php else: ?>
      <?php foreach ($blocks as $__block_index => $block):
        $btype    = isset($block['type']) ? $block['type'] : '';
        $safeType = preg_replace('/[^a-z0-9\-]/', '', $btype);
        $blockFile = APP_PATH . '/views/blocks/' . $safeType . '.php';
        if ($safeType && file_exists($blockFile)) {
            include $blockFile;
        }
      endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ── Pied récurrent : CTA + articles liés ────────────────────── -->
  <div class="container">
    <div class="kn-blog-article__cta">
      <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-card kn-cta-card--blue">
        <div class="kn-cta-card__content">
          <span class="kn-eyebrow">RÉSERVATION EN LIGNE</span>
          <p class="kn-cta-card__title">Prendre RDV en 2 min</p>
        </div>
        <span class="kn-cta-card__arrow" aria-hidden="true">&#8594;</span>
      </a>
    </div>

    <?php if ($related): ?>
    <section class="kn-blog-related" aria-label="Articles liés">
      <p class="kn-blog-related__title">À lire aussi</p>
      <div class="kn-blog-grid kn-blog-grid--related">
        <?php foreach ($related as $p):
          $rDate = !empty($p['published_at']) ? $p['published_at'] : $p['created_at'];
        ?>
        <a href="/blog/<?php echo htmlspecialchars($p['slug']); ?>" class="kn-blog-card">
          <div class="kn-blog-card__media">
            <?php if (!empty($p['image_url'])): ?>
            <?php echo knImage($p['image_url'], htmlspecialchars($p['title']), array('imgSizes' => '340px', 'style' => 'width:100%;height:100%;object-fit:cover')); ?>
            <?php else: ?>
            <span class="kn-blog-card__media-placeholder" aria-hidden="true">&#10022;</span>
            <?php endif; ?>
          </div>
          <div class="kn-blog-card__body">
            <?php if (!empty($p['category_name'])): ?>
            <span class="kn-blog-card__cat"><?php echo htmlspecialchars($p['category_name']); ?></span>
            <?php endif; ?>
            <h3 class="kn-blog-card__title"><?php echo htmlspecialchars($p['title']); ?></h3>
            <time class="kn-blog-card__date" datetime="<?php echo htmlspecialchars(date('Y-m-d', strtotime($rDate))); ?>"><?php echo htmlspecialchars(date('d.m.Y', strtotime($rDate))); ?></time>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
  </div>
</article>
