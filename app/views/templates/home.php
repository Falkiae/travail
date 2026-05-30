<?php
/* Homepage template — Keepnew
 * Variables available: $booking_url, $reviews, $site_name
 */
$booking_url = isset($booking_url) && $booking_url ? $booking_url : '#';
$reviews     = isset($reviews) && is_array($reviews) ? $reviews : array();
?>

<!-- ════════════════════════════════════════════════════════════
     Section 1 — Hero
     ════════════════════════════════════════════════════════════ -->
<section class="kn-hero" aria-labelledby="hero-heading">
    <div class="container">
        <div class="kn-hero__inner">

            <div class="kn-hero__content">
                <span class="kn-eyebrow kn-eyebrow--white">
                    NETTOYAGE À DOMICILE &middot; LIÈGE &middot; NAMUR &middot; BRUXELLES
                </span>

                <h1 id="hero-heading" class="kn-hero__heading">
                    Nettoyage de canapés,<br>matelas &amp; voitures
                    <span class="kn-hero__sub">à domicile ou en atelier.</span>
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

                <p class="kn-hero__social-proof">4,9/5 &middot; +110 avis &middot; +400 canapés &middot; +250 voitures</p>
            </div>

            <div class="kn-hero__img-placeholder" aria-hidden="true">
                Photo hero
            </div>

        </div>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 2 — Services
     ════════════════════════════════════════════════════════════ -->
<section class="kn-services kn-section" aria-labelledby="services-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow">NOS SERVICES</span>
            <h2 id="services-heading">Nettoyage de canapés, matelas &amp; voitures à domicile</h2>
        </header>

        <ul class="kn-services__grid" role="list">

            <?php
            $services = array(
                array('name' => 'Canapé',                'desc' => 'Nettoyage en profondeur de tous types de canapés, à votre domicile.'),
                array('name' => 'Matelas',               'desc' => 'Désinfection et nettoyage de matelas pour un sommeil sain.'),
                array('name' => 'Voiture',               'desc' => 'Nettoyage intérieur complet : sièges, moquette, plastiques.'),
                array('name' => 'Polissage &amp; Céramique', 'desc' => 'Protection et brillance longue durée pour votre carrosserie.'),
                array('name' => 'Terrasse',              'desc' => 'Nettoyage haute pression de terrasses, dalles et allées.'),
                array('name' => 'Atelier Visé',          'desc' => 'Apportez votre pièce dans notre atelier pour un traitement en profondeur.'),
            );
            foreach ($services as $service):
            ?>
            <li class="kn-card kn-service-card">
                <div class="kn-service-card__icon" aria-hidden="true"></div>
                <div>
                    <h3 class="kn-service-card__title"><?php echo $service['name']; ?></h3>
                    <p class="kn-service-card__desc"><?php echo $service['desc']; ?></p>
                </div>
            </li>
            <?php endforeach; ?>

        </ul>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 3 — Domicile & Atelier
     ════════════════════════════════════════════════════════════ -->
<section class="kn-two-col kn-section" aria-labelledby="deux-modes-heading">
    <div class="container">

        <header class="kn-section__header">
            <span class="kn-eyebrow">DEUX FAÇONS DE TRAVAILLER</span>
            <h2 id="deux-modes-heading">On vient chez vous — ou vous venez chez nous.</h2>
        </header>

        <div class="kn-two-col__grid">

            <div>
                <p class="kn-two-col__block-title">&#127968; À domicile</p>
                <p class="kn-two-col__block-body">
                    Notre équipe se déplace directement chez vous dans tout le territoire desservi :
                    Liège, Seraing, Visé, Namur et sa région, le Grand-Duché de Luxembourg ainsi que
                    Bruxelles et sa périphérie. Vous restez chez vous, nous apportons le matériel professionnel,
                    et vous retrouvez vos meubles et votre voiture comme neufs — sans aucun déplacement de votre part.
                </p>
            </div>

            <div>
                <p class="kn-two-col__block-title">&#127981; Atelier à Visé</p>
                <p class="kn-two-col__block-body">
                    Pour les traitements les plus poussés — polissage céramique, nettoyage en profondeur de canapés
                    très encrassés — notre atelier physique à Visé vous accueille sur rendez-vous.
                    Un environnement contrôlé pour un résultat optimal, avec des équipements professionnels
                    que nous ne pouvons pas transporter à domicile.
                </p>
            </div>

        </div>

        <div class="kn-two-col__img-placeholder" aria-hidden="true">Photo atelier Visé</div>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 4 — Comment ça marche
     ════════════════════════════════════════════════════════════ -->
<section class="kn-how kn-section" aria-labelledby="how-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow">RÉSERVATION EN LIGNE</span>
            <h2 id="how-heading">Réservez votre nettoyage à domicile en 2 minutes</h2>
        </header>

        <ol class="kn-how__steps" role="list">

            <li class="kn-step">
                <div class="kn-step__number" aria-hidden="true">1</div>
                <div>
                    <p class="kn-step__title">Choisissez votre service en ligne</p>
                    <p class="kn-step__desc">Sélectionnez le service qui vous convient, indiquez votre adresse et choisissez un créneau. C'est rapide et sans engagement.</p>
                </div>
            </li>

            <li class="kn-step">
                <div class="kn-step__number" aria-hidden="true">2</div>
                <div>
                    <p class="kn-step__title">On passe chez vous</p>
                    <p class="kn-step__desc">Notre technicien arrive à l'heure convenue avec tout le matériel professionnel. Vous n'avez rien à préparer.</p>
                </div>
            </li>

            <li class="kn-step">
                <div class="kn-step__number" aria-hidden="true">3</div>
                <div>
                    <p class="kn-step__title">Résultat garanti</p>
                    <p class="kn-step__desc">Vos meubles, matelas ou voiture ressortent propres et frais. Si vous n'êtes pas satisfait, nous repassons — sans frais supplémentaires.</p>
                </div>
            </li>

        </ol>

        <div style="text-align:center;">
            <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-btn kn-btn--lg">
                Prendre RDV en 2 min &#8594;
            </a>
        </div>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 5 — Avis clients
     ════════════════════════════════════════════════════════════ -->
<section class="kn-reviews kn-section" aria-labelledby="reviews-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow">ILS NOUS FONT CONFIANCE</span>
            <h2 id="reviews-heading">4,9/5 &middot; +110 avis &middot; +400 canapés nettoyés</h2>
        </header>

        <ul class="kn-reviews__grid" role="list">

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
            <li class="kn-review-card">
                <div class="kn-review-card__stars" aria-label="<?php echo isset($review['rating']) ? (int)$review['rating'] : 5; ?> étoiles sur 5">
                    <?php
                    $stars = isset($review['rating']) ? (int)$review['rating'] : 5;
                    for ($i = 0; $i < $stars; $i++) {
                        echo '&#9733;';
                    }
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


<!-- ════════════════════════════════════════════════════════════
     Section 6 — Zone
     ════════════════════════════════════════════════════════════ -->
<section class="kn-zone kn-section" aria-labelledby="zone-heading">
    <div class="container">

        <header class="kn-section__header">
            <span class="kn-eyebrow">OÙ ON INTERVIENT</span>
            <h2 id="zone-heading">Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg</h2>
        </header>

        <p class="kn-zone__desc">
            Nous intervenons dans toute la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Verviers…),
            la province de Namur et ses communes, la Région de Bruxelles-Capitale ainsi que le Grand-Duché
            de Luxembourg. Vous n'êtes pas certain que nous passons dans votre commune ?
            Appelez-nous ou réservez en ligne — nous vous confirmons la disponibilité immédiatement.
        </p>

        <div class="kn-zone__pills">
            <span class="kn-pill">Liège</span>
            <span class="kn-pill">Visé</span>
            <span class="kn-pill">Seraing</span>
            <span class="kn-pill">Herstal</span>
            <span class="kn-pill">Huy</span>
            <span class="kn-pill">Verviers</span>
            <span class="kn-pill">Namur</span>
            <span class="kn-pill">Dinant</span>
            <span class="kn-pill">Bruxelles</span>
            <span class="kn-pill">Uccle</span>
            <span class="kn-pill">Ixelles</span>
            <span class="kn-pill">Luxembourg</span>
        </div>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 7 — FAQ
     ════════════════════════════════════════════════════════════ -->
<section class="kn-faq kn-section" aria-labelledby="faq-heading">
    <div class="container">

        <header class="kn-section__header kn-section__header--center">
            <span class="kn-eyebrow">VOS QUESTIONS</span>
            <h2 id="faq-heading">Questions fréquentes sur le nettoyage à domicile</h2>
        </header>

        <dl class="kn-faq__list">

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Combien coûte un nettoyage de canapé à domicile ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Le tarif dépend du type de canapé, du tissu et du niveau d'encrassement. Nous vous proposons un devis personnalisé et gratuit avant toute intervention. La plupart de nos nettoyages de canapé sont compris entre 80 et 200 €.</p>
                </dd>
            </div>

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Quels produits utilisez-vous ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Nous utilisons exclusivement des produits professionnels certifiés, sans solvants agressifs, adaptés à chaque type de surface et de tissu. Nos formulations sont respectueuses des personnes sensibles et des animaux de compagnie.</p>
                </dd>
            </div>

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Vous intervenez vraiment à domicile ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Oui, absolument. Notre technicien se déplace directement chez vous avec tout l'équipement nécessaire. Vous n'avez pas à démonter ni à transporter quoi que ce soit — nous gérons tout sur place, dans votre salon, votre chambre ou votre garage.</p>
                </dd>
            </div>

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Combien de temps dure une intervention ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Une intervention standard dure entre 1h et 2h30 selon le service choisi et la superficie à traiter. Nous vous communiquons une estimation précise lors de la confirmation de votre rendez-vous.</p>
                </dd>
            </div>

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Proposez-vous un devis gratuit ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Oui, le devis est entièrement gratuit et sans engagement. Réservez en ligne ou appelez-nous — nous vous rappelons sous 24h pour établir un devis précis selon votre situation.</p>
                </dd>
            </div>

            <div class="kn-faq__item" itemscope itemtype="https://schema.org/Question">
                <dt>
                    <button class="kn-faq__question" aria-expanded="false" itemprop="name">
                        Quelle zone géographique couvrez-vous ?
                        <span class="kn-faq__icon" aria-hidden="true">+</span>
                    </button>
                </dt>
                <dd class="kn-faq__answer" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                    <p itemprop="text">Nous couvrons la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Verviers…), la province de Namur, la Région de Bruxelles-Capitale et le Grand-Duché de Luxembourg. Notre atelier physique est situé à Visé.</p>
                </dd>
            </div>

        </dl>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     Section 8 — CTA Final
     ════════════════════════════════════════════════════════════ -->
<section class="kn-cta-pave" aria-labelledby="cta-final-heading">
    <div class="kn-cta-pave__inner">
        <span class="kn-eyebrow" style="color:var(--kn-yellow);">&#128197; RÉSERVATION EN LIGNE</span>
        <h2 id="cta-final-heading" class="kn-cta-pave__title">Prendre RDV en 2 min</h2>
        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous en ligne">&#8594;</a>
        <a class="kn-cta-pave__phone" href="tel:+32455138419">+32 (0)4 55 13 84 19</a>
    </div>
</section>

<!-- FAQ Schema.org JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Combien coûte un nettoyage de canapé à domicile ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Le tarif dépend du type de canapé, du tissu et du niveau d'encrassement. La plupart de nos nettoyages de canapé sont compris entre 80 et 200 €."
      }
    },
    {
      "@type": "Question",
      "name": "Quels produits utilisez-vous ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Nous utilisons exclusivement des produits professionnels certifiés, sans solvants agressifs, adaptés à chaque type de surface et de tissu."
      }
    },
    {
      "@type": "Question",
      "name": "Vous intervenez vraiment à domicile ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, notre technicien se déplace directement chez vous avec tout l'équipement nécessaire."
      }
    },
    {
      "@type": "Question",
      "name": "Combien de temps dure une intervention ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Une intervention standard dure entre 1h et 2h30 selon le service choisi et la superficie à traiter."
      }
    },
    {
      "@type": "Question",
      "name": "Proposez-vous un devis gratuit ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, le devis est entièrement gratuit et sans engagement."
      }
    },
    {
      "@type": "Question",
      "name": "Quelle zone géographique couvrez-vous ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Nous couvrons la province de Liège, la province de Namur, la Région de Bruxelles-Capitale et le Grand-Duché de Luxembourg. Notre atelier est situé à Visé."
      }
    }
  ]
}
</script>
