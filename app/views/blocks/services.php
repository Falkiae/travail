<?php
$svc_items = array();
if (isset($block['items']) && is_array($block['items']) && !empty($block['items'])) {
    $svc_items = $block['items'];
} elseif (isset($block['items']) && is_string($block['items'])) {
    $d = json_decode($block['items'], true);
    if (is_array($d)) $svc_items = $d;
}
if (empty($svc_items)) {
    $svc_items = array(
        array('name' => 'Canapé',                'desc' => 'Aspiration profonde, vapeur, anti-odeurs.', 'icon' => '🛋️'),
        array('name' => 'Matelas',               'desc' => 'Nettoyage en profondeur, anti-acariens.',   'icon' => '🛏️'),
        array('name' => 'Voiture',               'desc' => 'Intérieur complet, sièges, moquettes.',     'icon' => '🚗'),
        array('name' => 'Polissage & Céramique', 'desc' => 'Protection longue durée.',                  'icon' => '✨'),
        array('name' => 'Terrasse',              'desc' => 'Haute pression et anti-mousse.',             'icon' => '🏠'),
        array('name' => 'Atelier Visé',          'desc' => 'Traitements approfondis en atelier.',       'icon' => '🔧'),
    );
}
?>
<section class="kn-services kn-section" aria-labelledby="services-heading">
    <div class="container">
        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'NOS SERVICES'); ?></span>
            <h2 id="services-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'Nettoyage de canapés, matelas & voitures à domicile'); ?></h2>
        </header>
        <ul class="kn-services__grid" role="list">
        <?php foreach ($svc_items as $svc): ?>
            <li class="kn-card kn-service-card">
                <div class="kn-service-card__icon" aria-hidden="true"><?php echo htmlspecialchars(isset($svc['icon']) ? $svc['icon'] : ''); ?></div>
                <div>
                    <h3 class="kn-service-card__title"><?php echo htmlspecialchars(isset($svc['name']) ? $svc['name'] : ''); ?></h3>
                    <p class="kn-service-card__desc"><?php echo htmlspecialchars(isset($svc['desc']) ? $svc['desc'] : ''); ?></p>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</section>
