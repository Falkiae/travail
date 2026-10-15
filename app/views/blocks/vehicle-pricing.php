<?php
require_once __DIR__ . '/_block_helpers.php';

$booking_url = isset($booking_url) ? $booking_url : '#';

$bg     = isset($block['bg']) ? $block['bg'] : 'cream';
$isDark = ($bg === 'blue' || $bg === 'night');
$isRose = ($bg === 'rose');

$eyebrowColor = $isDark ? 'var(--kn-rose-mid)' : ($isRose ? 'var(--kn-rose-600)' : 'var(--kn-blue)');
$headingColor = $isDark ? 'var(--kn-white)' : 'var(--kn-night)';

$items = isset($block['items']) && is_array($block['items']) ? $block['items'] : array();
if (!$items) return;

$sizes = array(
    'citadine'    => 'Citadine',
    'berline'     => 'Berline / Break',
    'suv'         => 'SUV / 4x4',
    'utilitaire'  => 'Camionnette / Utilitaire',
);
$places = array(
    'domicile' => 'À domicile',
    'atelier'  => 'En atelier',
);

$zones = array(
    'sieges'    => array('Sièges & textiles', 'armchair'),
    'habitacle' => array('Habitacle', 'vitres'),
    'ext'       => array('Extérieur', 'voiture'),
    'finitions' => array('Finitions', 'sparkles'),
);
$detailLabel  = !empty($block['detail_label']) ? $block['detail_label'] : 'Voir le détail';
$openDesktop  = isset($block['details_default']) && $block['details_default'] === 'open_desktop';

$style       = (isset($block['selector_style']) && $block['selector_style'] === 'select') ? 'select' : 'pills';
$labelCar    = !empty($block['label_vehicle']) ? $block['label_vehicle'] : 'Mon véhicule';
$labelPlace  = !empty($block['label_place'])   ? $block['label_place']   : 'Lieu du nettoyage';
$pricePrefix = isset($block['price_prefix']) && $block['price_prefix'] !== '' ? $block['price_prefix'] : 'à partir de';

$defSize  = 'citadine';
$defPlace = 'domicile';

$uid = 'vp-' . (isset($__block_index) ? (int) $__block_index : 0);

if (!function_exists('knVpPrice')) {
    function knVpPrice(array $item, string $size, string $place): string
    {
        $k = 'price_' . $size . '_' . $place;
        return isset($item[$k]) ? trim((string) $item[$k]) : '';
    }
}
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-vp'); ?>" id="<?php echo $uid; ?>" data-vp>
  <div class="container">
    <?php if (!empty($block['eyebrow'])): ?>
    <span class="kn-eyebrow" style="color:<?php echo $eyebrowColor; ?>;"><?php echo $block['eyebrow']; ?></span>
    <?php endif; ?>
    <?php if (!empty($block['h2'])): ?>
    <h2 class="kn-section__title" style="color:<?php echo $headingColor; ?>;"><?php echo $block['h2']; ?></h2>
    <?php endif; ?>
    <?php if (!empty($block['intro'])): ?>
    <p class="kn-vp__intro"><?php echo nl2br(htmlspecialchars($block['intro'])); ?></p>
    <?php endif; ?>

    <div class="kn-vp__controls kn-vp__controls--<?php echo $style; ?>">
      <?php if ($style === 'select'): ?>
      <label class="kn-vp__field">
        <span class="kn-vp__label"><?php echo htmlspecialchars($labelCar); ?></span>
        <select class="kn-vp__select" data-vp-size>
          <?php foreach ($sizes as $k => $label): ?>
          <option value="<?php echo $k; ?>"<?php echo $k === $defSize ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="kn-vp__field">
        <span class="kn-vp__label"><?php echo htmlspecialchars($labelPlace); ?></span>
        <select class="kn-vp__select" data-vp-place>
          <?php foreach ($places as $k => $label): ?>
          <option value="<?php echo $k; ?>"<?php echo $k === $defPlace ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <?php else: ?>
      <div class="kn-vp__group">
        <span class="kn-vp__label" id="<?php echo $uid; ?>-l1"><?php echo htmlspecialchars($labelCar); ?></span>
        <div class="kn-vp__pills" role="radiogroup" aria-labelledby="<?php echo $uid; ?>-l1" data-vp-size>
          <?php foreach ($sizes as $k => $label): ?>
          <button type="button" class="kn-vp__pill<?php echo $k === $defSize ? ' is-active' : ''; ?>" role="radio" aria-checked="<?php echo $k === $defSize ? 'true' : 'false'; ?>" data-value="<?php echo $k; ?>"><?php echo htmlspecialchars($label); ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="kn-vp__group">
        <span class="kn-vp__label" id="<?php echo $uid; ?>-l2"><?php echo htmlspecialchars($labelPlace); ?></span>
        <div class="kn-vp__pills" role="radiogroup" aria-labelledby="<?php echo $uid; ?>-l2" data-vp-place>
          <?php foreach ($places as $k => $label): ?>
          <button type="button" class="kn-vp__pill<?php echo $k === $defPlace ? ' is-active' : ''; ?>" role="radio" aria-checked="<?php echo $k === $defPlace ? 'true' : 'false'; ?>" data-value="<?php echo $k; ?>"><?php echo htmlspecialchars($label); ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="kn-vp__grid">
      <?php foreach ($items as $item):
        $featured = !empty($item['featured']) && $item['featured'] === 'yes';
        $prices = array();
        foreach ($sizes as $sk => $_) {
            foreach ($places as $pk => $__) {
                $prices[$sk][$pk] = knVpPrice($item, $sk, $pk);
            }
        }
        $initial   = $prices[$defSize][$defPlace];
        $features  = isset($item['features']) ? preg_split('/\r\n|\r|\n/', (string) $item['features']) : array();
        $features  = array_values(array_filter(array_map('trim', $features), 'strlen'));
        $zoneSum   = array();
        $zoneDet   = array();
        $hasZones  = false;
        foreach ($zones as $zk => $_z) {
            $zoneSum[$zk] = isset($item['sum_' . $zk]) ? trim((string) $item['sum_' . $zk]) : '';
            $d = isset($item['det_' . $zk]) ? preg_split('/\r\n|\r|\n/', (string) $item['det_' . $zk]) : array();
            $zoneDet[$zk] = array_values(array_filter(array_map('trim', $d), 'strlen'));
            if ($zoneSum[$zk] !== '' || $zoneDet[$zk]) $hasZones = true;
        }
        $ctaUrl    = !empty($item['cta_url']) ? $item['cta_url'] : $booking_url;
        $ctaText   = !empty($item['cta_text']) ? $item['cta_text'] : 'Prenez RDV';
      ?>
      <article class="kn-vp-card<?php echo $featured ? ' kn-vp-card--featured' : ''; ?>" data-prices="<?php echo htmlspecialchars(json_encode($prices, JSON_UNESCAPED_UNICODE)); ?>">
        <div class="kn-vp-card__band" data-vp-band><?php echo htmlspecialchars($places[$defPlace]); ?></div>
        <div class="kn-vp-card__body">
          <h3 class="kn-vp-card__title"><?php echo htmlspecialchars(isset($item['title']) ? $item['title'] : ''); ?></h3>
          <span class="kn-vp-card__badge"><?php echo !empty($item['badge']) ? htmlspecialchars($item['badge']) : '&nbsp;'; ?></span>
          <p class="kn-vp-card__price" aria-live="polite">
            <span class="kn-vp-card__prefix"<?php echo $initial === '' ? ' hidden' : ''; ?>><?php echo htmlspecialchars($pricePrefix); ?></span>
            <span class="kn-vp-card__amount<?php echo $initial === '' ? ' kn-vp-card__amount--quote' : ''; ?>" data-vp-amount><?php echo $initial === '' ? 'Sur devis' : htmlspecialchars($initial) . '<small>€</small>'; ?></span>
          </p>
          <?php if (!empty($item['desc'])): ?>
          <p class="kn-vp-card__desc"><?php echo htmlspecialchars($item['desc']); ?></p>
          <?php endif; ?>
          <?php if (!empty($item['note'])): ?>
          <p class="kn-vp-card__note"><?php echo htmlspecialchars($item['note']); ?></p>
          <?php endif; ?>
          <?php if ($hasZones): ?>
          <ul class="kn-vp-sum">
            <?php foreach ($zones as $zk => $z): ?>
            <li<?php echo $zoneSum[$zk] === '' && !$zoneDet[$zk] ? ' class="is-off"' : ''; ?>>
              <span class="kn-vp-sum__ico"><?php echo knIcon($z[1], array('size' => 18)); ?></span>
              <span class="kn-vp-sum__txt"><strong><?php echo htmlspecialchars($z[0]); ?></strong><?php echo $zoneSum[$zk] === '' && !$zoneDet[$zk] ? 'Non inclus' : htmlspecialchars($zoneSum[$zk]); ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php if (array_filter($zoneDet)): ?>
          <details class="kn-vp-more">
            <summary><?php echo htmlspecialchars($detailLabel); ?></summary>
            <?php foreach ($zones as $zk => $z): if (!$zoneDet[$zk]) continue; ?>
            <div class="kn-vp-more__zone">
              <h4><?php echo htmlspecialchars($z[0]); ?></h4>
              <ul>
                <?php foreach ($zoneDet[$zk] as $f): ?>
                <li><span class="kn-vp-card__check" aria-hidden="true"><?php echo knIcon('check', array('size' => 12)); ?></span><span><?php echo htmlspecialchars($f); ?></span></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endforeach; ?>
          </details>
          <?php endif; ?>
          <?php elseif ($features): ?>
          <details class="kn-vp-more">
            <summary><?php echo htmlspecialchars($detailLabel); ?> (<?php echo count($features); ?>)</summary>
            <div class="kn-vp-more__zone">
              <ul>
                <?php foreach ($features as $f): ?>
                <li><span class="kn-vp-card__check" aria-hidden="true"><?php echo knIcon('check', array('size' => 12)); ?></span><span><?php echo htmlspecialchars($f); ?></span></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </details>
          <?php endif; ?>
          <a href="<?php echo htmlspecialchars($ctaUrl); ?>" class="kn-btn kn-vp-card__cta"><?php echo knIcon('calendar', array('size' => 16)); ?><?php echo htmlspecialchars($ctaText); ?></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($block['ozone_text'])): ?>
    <p class="kn-vp__ozone"><strong><?php echo htmlspecialchars(!empty($block['ozone_title']) ? $block['ozone_title'] : 'Option ozone'); ?></strong> — <?php echo htmlspecialchars($block['ozone_text']); ?></p>
    <?php endif; ?>
    <?php if (!empty($block['note'])): ?>
    <p class="kn-vp__note"><?php echo nl2br(htmlspecialchars($block['note'])); ?></p>
    <?php endif; ?>
  </div>
</section>
<script>
(function () {
  var root = document.getElementById('<?php echo $uid; ?>');
  if (!root || root.dataset.vpInit) return;
  root.dataset.vpInit = '1';

  var PLACES = <?php echo json_encode($places, JSON_UNESCAPED_UNICODE); ?>;
  var state  = { size: '<?php echo $defSize; ?>', place: '<?php echo $defPlace; ?>' };
  var cards  = root.querySelectorAll('.kn-vp-card');

  function render() {
    cards.forEach(function (card) {
      var prices = {};
      try { prices = JSON.parse(card.dataset.prices); } catch (e) {}
      var p = (prices[state.size] || {})[state.place] || '';
      var amount = card.querySelector('[data-vp-amount]');
      var prefix = card.querySelector('.kn-vp-card__prefix');
      if (p === '') {
        amount.textContent = 'Sur devis';
        amount.classList.add('kn-vp-card__amount--quote');
        if (prefix) prefix.hidden = true;
      } else {
        amount.textContent = p;
        var cur = document.createElement('small');
        cur.textContent = '€';
        amount.appendChild(cur);
        amount.classList.remove('kn-vp-card__amount--quote');
        if (prefix) prefix.hidden = false;
      }
      amount.classList.remove('is-swapped'); void amount.offsetWidth; amount.classList.add('is-swapped');
      var band = card.querySelector('[data-vp-band]');
      if (band) band.textContent = PLACES[state.place] || '';
    });
  }

  function bindPills(group, key) {
    if (!group) return;
    group.addEventListener('click', function (e) {
      var btn = e.target.closest('.kn-vp__pill');
      if (!btn) return;
      state[key] = btn.dataset.value;
      group.querySelectorAll('.kn-vp__pill').forEach(function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-checked', on ? 'true' : 'false');
      });
      render();
    });
  }

  bindPills(root.querySelector('div[data-vp-size]'), 'size');
  bindPills(root.querySelector('div[data-vp-place]'), 'place');

<?php if ($openDesktop): ?>
  if (window.matchMedia('(min-width: 1180px)').matches) {
    root.querySelectorAll('.kn-vp-more').forEach(function (d) { d.open = true; });
  }
<?php endif; ?>

  var selSize  = root.querySelector('select[data-vp-size]');
  var selPlace = root.querySelector('select[data-vp-place]');
  if (selSize)  selSize.addEventListener('change',  function () { state.size  = selSize.value;  render(); });
  if (selPlace) selPlace.addEventListener('change', function () { state.place = selPlace.value; render(); });
})();
</script>
