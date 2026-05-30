<?php
$booking_url = isset($booking_url) ? $booking_url : '#';
?>
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
                <div class="kn-cta-pave" style="padding:2.5rem 2rem;border-radius:var(--r-card);margin-bottom:1.5rem;">
                    <div class="kn-cta-pave__inner">
                        <span class="kn-eyebrow" style="color:var(--kn-yellow);">&#128197; RÉSERVATION EN LIGNE</span>
                        <p class="kn-cta-pave__title">Prendre RDV en 2 min</p>
                        <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous">&#8594;</a>
                    </div>
                </div>
                <p class="kn-hero__social-proof"><?php echo htmlspecialchars(isset($block['social_proof']) ? $block['social_proof'] : '4,9/5 · +110 avis · +400 canapés · +250 voitures'); ?></p>
            </div>
            <div class="kn-hero__img-placeholder" aria-hidden="true">Photo hero</div>
        </div>
    </div>
</section>
