<?php
require_once __DIR__ . '/_block_helpers.php';
$cards = isset($block['cards']) && is_array($block['cards']) ? $block['cards'] : array(
  array('title' => 'Formule 1', 'sub' => 'Tarifs à partir de', 'rows' => "Citadine | ex. Peugeot 208 | 550 €\nBerline | ex. BMW Série 3 | 650 €\nBreak / SUV | ex. Peugeot 3008 | 720 €", 'cta_label' => 'Demander un devis', 'cta_url' => '#contact'),
);
?>
<section class="<?php echo blockClasses($block, 'alt', 'kn-ptable'); ?>">
  <div class="container">
    <header class="kn-plans__head">
      <?php if (!empty($block['eyebrow'])): ?><span class="kn-eyebrow"><?php echo htmlspecialchars($block['eyebrow']); ?></span><?php endif; ?>
      <?php if (!empty($block['h2'])): ?><h2 class="kn-section__title"><?php echo htmlspecialchars($block['h2']); ?></h2><?php endif; ?>
      <?php if (!empty($block['intro'])): ?><p class="kn-plans__intro"><?php echo htmlspecialchars($block['intro']); ?></p><?php endif; ?>
    </header>
    <div class="kn-ptable__grid kn-ptable__grid--<?php echo min(3, max(1, count($cards))); ?>">
      <?php foreach ($cards as $c): if (!is_array($c)) continue; ?>
      <article class="kn-plans__card kn-ptable__card">
        <?php if (!empty($c['title'])): ?><h3 class="kn-plans__name"><?php echo htmlspecialchars($c['title']); ?></h3><?php endif; ?>
        <?php if (!empty($c['sub'])): ?><p class="kn-plans__sub"><?php echo htmlspecialchars($c['sub']); ?></p><?php endif; ?>
        <ul class="kn-ptable__rows">
          <?php foreach (preg_split('/\r\n|\r|\n|;/', isset($c['rows']) ? (string) $c['rows'] : '') as $ln):
            $ln = trim($ln); if ($ln === '') continue;
            $p = array_map('trim', explode('|', $ln));
            $label = $p[0]; $ex = count($p) > 2 ? $p[1] : ''; $price = count($p) > 1 ? $p[count($p) - 1] : '';
          ?>
          <li><span class="kn-ptable__name"><strong><?php echo htmlspecialchars($label); ?></strong><?php if ($ex !== ''): ?><small><?php echo htmlspecialchars($ex); ?></small><?php endif; ?></span><span class="kn-ptable__price"><?php echo htmlspecialchars($price); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php if (!empty($c['cta_label'])): ?>
        <a class="kn-btn kn-plans__cta" href="<?php echo htmlspecialchars(!empty($c['cta_url']) ? $c['cta_url'] : '#contact'); ?>"><?php echo htmlspecialchars($c['cta_label']); ?></a>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($block['note'])): ?><p class="kn-tc__note kn-ptable__note"><?php echo nl2br(htmlspecialchars($block['note'])); ?></p><?php endif; ?>
  </div>
</section>
