<?php
/* Homepage template — Keepnew
 * Variables available: $booking_url, $reviews, $site_name, $blocks
 */
$booking_url = isset($booking_url) && $booking_url ? $booking_url : '#';
$reviews     = isset($reviews) && is_array($reviews) ? $reviews : array();
$blocks      = isset($blocks) && is_array($blocks) ? $blocks : array();

$default_services = array(
    array('name' => 'Canapé',                'desc' => 'Aspiration profonde, vapeur, anti-odeurs. Résultat garanti.',           'icon' => '🛋️'),
    array('name' => 'Matelas',               'desc' => 'Nettoyage en profondeur, anti-acariens et désinfection.',               'icon' => '🛏️'),
    array('name' => 'Voiture',               'desc' => 'Intérieur complet, sièges, moquettes et plastiques.',                   'icon' => '🚗'),
    array('name' => 'Polissage & Céramique', 'desc' => 'Protection longue durée pour votre carrosserie.',                      'icon' => '✨'),
    array('name' => 'Terrasse',              'desc' => 'Haute pression et traitement anti-mousse.',                             'icon' => '🏠'),
    array('name' => 'Atelier Visé',          'desc' => 'Traitements approfondis dans notre atelier à Visé.',                   'icon' => '🔧'),
);
?>

<?php if (empty($blocks)): ?>
  <p style="text-align:center;padding:4rem;">Aucun bloc configuré. <a href="/admin/pages">Ajouter des blocs →</a></p>
<?php else: ?>

<?php foreach ($blocks as $block):
    $btype = isset($block['type']) ? $block['type'] : '';
    switch ($btype):

    /* ── HERO ── */
    case 'hero': ?>
<section class="kn-hero" aria-labelledby="hero-heading">
    <div class="container">
        <div class="kn-hero__inner">

            <div class="kn-hero__content">
                <span class="kn-eyebrow kn-eyebrow--white">
                    <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'NETTOYAGE À DOMICILE · LIÈGE · NAMUR · BRUXELLES'); ?>
                </span>

                <h1 id="hero-heading" class="kn-hero__heading">
                    <?php echo htmlspecialchars(isset($block['h1']) ? $block['h1'] : 'Nettoyage de canapés, matelas & voitures'); ?>
                    <span class="kn-hero__sub"><?php echo htmlspecialchars(isset($block['h1_sub']) ? $block['h1_sub'] : 'à domicile ou en atelier.'); ?></span>
                </h1>

                <div class="kn-hero__pills kn-pills-row">
                    <span class="kn-pill kn-pill--white">&#10003; À domicile</span>
                    <span class="kn-pill kn-pill--white">&#9733; Garantie</span>
                    <span class="kn-pill kn-pill--white">&#9889; En 2 min</span>
                    <span class="kn-pill kn-pill--white">&#127463;&#127466; Belgique</span>
                </div>

                <div class="kn-cta-pave" style="padding:2.5rem 2rem; border-radius:var(--r-card); margin-bottom:1.5rem;">
                    <div class="kn-cta-pave__inner">
                        <span class="kn-eyebrow" style="color:var(--kn-yellow);">&#128197; RÉSERVATION EN LIGNE</span>
                        <p class="kn-cta-pave__title">Prendre RDV en 2 min</p>
                        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous">&#8594;</a>
                    </div>
                </div>

                <p class="kn-hero__social-proof"><?php echo htmlspecialchars(isset($block['social_proof']) ? $block['social_proof'] : '4,9/5 · +110 avis · +400 canapés · +250 voitures'); ?></p>
            </div>

            <div class="kn-hero__img-placeholder" aria-hidden="true">
                Photo hero
            </div>

        </div>
    </div>
</section>
<?php break;

    /* ── SERVICES ── */
    case 'services':
        $svc_items = array();
        if (isset($block['items']) && is_array($block['items']) && !empty($block['items'])) {
            $svc_items = $block['items'];
        } elseif (isset($block['items']) && is_string($block['items']) && $block['items'] !== '') {
            $decoded_svc = json_decode($block['items'], true);
            if (is_array($decoded_svc)) {
                $svc_items = $decoded_svc;
            }
        }
        if (empty($svc_items)) {
            $svc_items = $default_services;
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
                <?php if (isset($svc['icon']) && $svc['icon'] !== ''): ?>
                <div class="kn-service-card__icon" aria-hidden="true"><?php echo htmlspecialchars($svc['icon']); ?></div>
                <?php else: ?>
                <div class="kn-service-card__icon" aria-hidden="true"></div>
                <?php endif; ?>
                <div>
                    <h3 class="kn-service-card__title"><?php echo htmlspecialchars(isset($svc['name']) ? $svc['name'] : ''); ?></h3>
                    <p class="kn-service-card__desc"><?php echo htmlspecialchars(isset($svc['desc']) ? $svc['desc'] : ''); ?></p>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>

    </div>
</section>
<?php break;

    /* ── TWO-COL ── */
    case 'two-col': ?>
<section class="kn-two-col kn-section" aria-labelledby="deux-modes-heading">
    <div class="container">

        <header class="kn-section__header">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'DEUX FAÇONS DE TRAVAILLER'); ?></span>
            <h2 id="deux-modes-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'On vient chez vous — ou vous venez chez nous.'); ?></h2>
        </header>

        <div class="kn-two-col__grid">

            <div>
                <p class="kn-two-col__block-title"><?php echo htmlspecialchars(isset($block['left_title']) ? $block['left_title'] : 'À domicile'); ?></p>
                <p class="kn-two-col__block-body"><?php echo htmlspecialchars(isset($block['left_body']) ? $block['left_body'] : ''); ?></p>
            </div>

            <div>
                <p class="kn-two-col__block-title"><?php echo htmlspecialchars(isset($block['right_title']) ? $block['right_title'] : 'Atelier à Visé'); ?></p>
                <p class="kn-two-col__block-body"><?php echo htmlspecialchars(isset($block['right_body']) ? $block['right_body'] : ''); ?></p>
            </div>

        </div>

        <div class="kn-two-col__img-placeholder" aria-hidden="true">Photo atelier Visé</div>

    </div>
</section>
<?php break;

    /* ── HOW ── */
    case 'how':
        $steps = array();
        if (isset($block['steps']) && is_array($block['steps'])) {
            $steps = $block['steps'];
        } elseif (isset($block['steps']) && is_string($block['steps']) && $block['steps'] !== '') {
            $decoded_steps = json_decode($block['steps'], true);
            if (is_array($decoded_steps)) {
                $steps = $decoded_steps;
            }
        }
    ?>
<section class="kn-how kn-section" aria-labelledby="how-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'RÉSERVATION EN LIGNE'); ?></span>
            <h2 id="how-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'Réservez votre nettoyage à domicile en 2 minutes'); ?></h2>
        </header>

        <ol class="kn-how__steps" role="list">
        <?php foreach ($steps as $si => $step): ?>
            <li class="kn-step">
                <div class="kn-step__number" aria-hidden="true"><?php echo (int)$si + 1; ?></div>
                <div>
                    <p class="kn-step__title"><?php echo htmlspecialchars(isset($step['title']) ? $step['title'] : ''); ?></p>
                    <p class="kn-step__desc"><?php echo htmlspecialchars(isset($step['body']) ? $step['body'] : ''); ?></p>
                </div>
            </li>
        <?php endforeach; ?>
        </ol>

        <div style="text-align:center;">
            <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-btn kn-btn--lg">
                Prendre RDV en 2 min &#8594;
            </a>
        </div>

    </div>
</section>
<?php break;

    /* ── REVIEWS ── */
    case 'reviews': ?>
<section class="kn-reviews kn-section" aria-labelledby="reviews-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'ILS NOUS FONT CONFIANCE'); ?></span>
            <h2 id="reviews-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : '4,9/5 · +110 avis · +400 canapés nettoyés'); ?></h2>
        </header>

        <ul class="kn-reviews__grid" role="list">

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
            <li class="kn-review-card">
                <div class="kn-review-card__stars" aria-label="<?php echo isset($review['rating']) ? (int)$review['rating'] : 5; ?> étoiles sur 5">
                    <?php
                    $stars = isset($review['rating']) ? (int)$review['rating'] : 5;
                    for ($i = 0; $i < $stars; $i++) { echo '&#9733;'; }
                    ?>
                </div>
                <p class="kn-review-card__text"><?php echo htmlspecialchars(isset($review['text']) ? $review['text'] : ''); ?></p>
                <div class="kn-review-card__meta">
                    <span class="kn-review-card__author"><?php echo htmlspecialchars(isset($review['author_name']) ? $review['author_name'] : 'Client'); ?></span>
                    <?php if (isset($review['relative_time_description']) && $review['relative_time_description']): ?>
                    <span>&mdash; <?php echo htmlspecialchars($review['relative_time_description']); ?></span>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>

        <?php else: ?>

            <li class="kn-review-card">
                <div class="kn-review-card__stars" aria-label="5 étoiles sur 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Résultat absolument impeccable ! Mon canapé en tissu était tellement encrassé que j'avais perdu espoir. Keepnew l'a rendu comme neuf en moins de deux heures."</p>
                <div class="kn-review-card__meta">
                    <span class="kn-review-card__author">Marie-Claire V.</span>
                    <span>&mdash; il y a 2 semaines</span>
                </div>
            </li>

            <li class="kn-review-card">
                <div class="kn-review-card__stars" aria-label="5 étoiles sur 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Intervention rapide, technicien très professionnel et ponctuel. L'intérieur de ma voiture est méconnaissable. Je recommande sans hésiter."</p>
                <div class="kn-review-card__meta">
                    <span class="kn-review-card__author">Thomas D.</span>
                    <span>&mdash; il y a 1 mois</span>
                </div>
            </li>

            <li class="kn-review-card">
                <div class="kn-review-card__stars" aria-label="5 étoiles sur 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Deux matelas nettoyés à domicile à Namur. Service au top, tarifs raisonnables et l'odeur fraîche qui reste après le nettoyage est vraiment agréable."</p>
                <div class="kn-review-card__meta">
                    <span class="kn-review-card__author">Sophie M.</span>
                    <span>&mdash; il y a 3 semaines</span>
                </div>
            </li>

        <?php endif; ?>

        </ul>

        <div class="kn-reviews__link">
            <a class="kn-reviews__google-link" href="https://g.page/r/ChIJ3eozWDz5wEcR8MOdpMxrwsY/review" target="_blank" rel="noopener noreferrer">
                Voir tous les avis sur Google &#8594;
            </a>
        </div>

    </div>
</section>
<?php break;

    /* ── ZONE ── */
    case 'zone':
        $pills = array();
        if (isset($block['pills']) && is_array($block['pills'])) {
            $pills = $block['pills'];
        } elseif (isset($block['pills']) && is_string($block['pills']) && $block['pills'] !== '') {
            $decoded_pills = json_decode($block['pills'], true);
            if (is_array($decoded_pills)) {
                $pills = $decoded_pills;
            }
        }
    ?>
<section class="kn-zone kn-section" aria-labelledby="zone-heading">
    <div class="container">

        <header class="kn-section__header">
            <span class="kn-eyebrow"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'OÙ ON INTERVIENT'); ?></span>
            <h2 id="zone-heading"><?php echo htmlspecialchars(isset($block['h2']) ? $block['h2'] : 'Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg'); ?></h2>
        </header>

        <?php if (isset($block['body']) && $block['body'] !== ''): ?>
        <p class="kn-zone__desc"><?php echo htmlspecialchars($block['body']); ?></p>
        <?php endif; ?>

        <?php if (!empty($pills)): ?>
        <div class="kn-zone__pills">
            <?php foreach ($pills as $pill): ?>
            <span class="kn-pill"><?php echo htmlspecialchars(isset($pill['label']) ? $pill['label'] : ''); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php break;

    /* ── ACCORDION (FAQ) ── */
    case 'accordion':
        $acc_items = array();
        if (isset($block['items']) && is_array($block['items'])) {
            $acc_items = $block['items'];
        }
    ?>
<section class="kn-faq kn-section" aria-labelledby="faq-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow">VOS QUESTIONS</span>
            <h2 id="faq-heading">Questions fréquentes sur le nettoyage à domicile</h2>
        </header>

        <dl class="kn-faq__list">
        <?php foreach ($acc_items as $faq_item): ?>
            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        <?php echo htmlspecialchars(isset($faq_item['question']) ? $faq_item['question'] : ''); ?>
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text"><?php echo htmlspecialchars(isset($faq_item['answer']) ? $faq_item['answer'] : ''); ?></p>
                </dd>
            </div>
        <?php endforeach; ?>
        </dl>

    </div>
</section>
<?php break;

    /* ── CTA-FINAL ── */
    case 'cta-final': ?>
<section class="kn-cta-pave" aria-labelledby="cta-final-heading">
    <div class="kn-cta-pave__inner">
        <span class="kn-eyebrow" style="color:var(--kn-yellow);"><?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : '📅 RÉSERVATION EN LIGNE'); ?></span>
        <h2 id="cta-final-heading" class="kn-cta-pave__title"><?php echo htmlspecialchars(isset($block['title']) ? $block['title'] : 'Prendre RDV en 2 min'); ?></h2>
        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous en ligne">&#8594;</a>
        <a class="kn-cta-pave__phone" href="tel:+32455138419">+32 (0)4 55 13 84 19</a>
    </div>
</section>
<?php break;

    endswitch;
endforeach; ?>

<?php endif; ?>
