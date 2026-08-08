<?php
require_once APP_PATH . '/views/blocks/_block_helpers.php';

$posts          = isset($posts) && is_array($posts) ? $posts : array();
$categories     = isset($categories) && is_array($categories) ? $categories : array();
$activeCategory = isset($active_category) ? $active_category : null;
$page           = isset($page) ? (int) $page : 1;
$totalPages     = isset($total_pages) ? (int) $total_pages : 1;

if (!function_exists('knBlogPageUrl')) {
    function knBlogPageUrl(int $p, ?array $cat): string
    {
        $params = array();
        if ($p > 1) $params['page'] = $p;
        if ($cat) $params['cat'] = $cat['slug'];
        return '/blog' . ($params ? '?' . http_build_query($params) : '');
    }
}
?>
<section class="kn-section kn-blog-hero">
  <div class="container">
    <span class="kn-eyebrow">LE JOURNAL KEEPNEW</span>
    <h1 class="kn-section__title">Conseils d'entretien &amp; actualités.</h1>
    <p class="kn-blog-hero__intro">Nos astuces d'artisans pour prendre soin de vos canapés, matelas et véhicules, et les nouvelles de l'atelier.</p>

    <?php if ($categories): ?>
    <nav class="kn-blog-filters" aria-label="Filtrer par catégorie">
      <a href="/blog" class="kn-blog-filters__pill<?php echo !$activeCategory ? ' is-active' : ''; ?>">Tous les articles</a>
      <?php foreach ($categories as $cat): ?>
      <a href="/blog?cat=<?php echo urlencode($cat['slug']); ?>" class="kn-blog-filters__pill<?php echo ($activeCategory && $activeCategory['id'] === $cat['id']) ? ' is-active' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

<section class="kn-section kn-blog-grid-section">
  <div class="container">
    <?php if (!$posts): ?>
    <p class="kn-blog-empty">
      <?php echo $activeCategory ? 'Aucun article dans cette catégorie pour le moment.' : 'Aucun article publié pour le moment, revenez bientôt.'; ?>
    </p>
    <?php else: ?>
    <div class="kn-blog-grid">
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
          <h2 class="kn-blog-card__title"><?php echo htmlspecialchars($p['title']); ?></h2>
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

    <?php if ($totalPages > 1): ?>
    <nav class="kn-blog-pagination" aria-label="Pagination du blog">
      <?php if ($page > 1): ?>
      <a href="<?php echo htmlspecialchars(knBlogPageUrl($page - 1, $activeCategory)); ?>" class="kn-blog-pagination__link">&larr; Précédent</a>
      <?php endif; ?>
      <span class="kn-blog-pagination__status">Page <?php echo $page; ?> / <?php echo $totalPages; ?></span>
      <?php if ($page < $totalPages): ?>
      <a href="<?php echo htmlspecialchars(knBlogPageUrl($page + 1, $activeCategory)); ?>" class="kn-blog-pagination__link">Suivant &rarr;</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
