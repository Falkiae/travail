<?php $booking_url = isset($booking_url) ? $booking_url : '#'; ?>
<section class="kn-section kn-bg--blue kn-hero">
  <div class="container">
    <div class="kn-hero__inner">
      <div class="kn-hero__content">
        <span class="kn-eyebrow" style="color:var(--kn-yellow);">
          <?php echo htmlspecialchars(isset($block['eyebrow']) ? $block['eyebrow'] : 'NETTOYAGE À DOMICILE · LIÈGE · NAMUR · BRUXELLES'); ?>
        </span>
        <h1 class="kn-hero__heading">
          <?php
            $h1 = isset($block['h1']) ? htmlspecialchars($block['h1']) : 'Nettoyage de canapés, matelas &amp; voitures';
            $h1sub = isset($block['h1_sub']) ? $block['h1_sub'] : 'à domicile ou en atelier.';
            echo $h1;
          ?>
          <br><em class="kn-hero__sub"><?php echo htmlspecialchars($h1sub); ?></em>
        </h1>
        <div class="kn-hero__pills">
          <span class="kn-pill kn-pill--white">&#10003; À domicile</span>
          <span class="kn-pill kn-pill--white">&#9733; Garantie</span>
          <span class="kn-pill kn-pill--white">&#9889; En 2&nbsp;min</span>
          <span class="kn-pill kn-pill--white">&#127463;&#127466; Belgique</span>
        </div>
        <div class="kn-hero__cta-block">
          <span class="kn-eyebrow" style="color:var(--kn-yellow);">&#128197; RÉSERVATION EN LIGNE</span>
          <p class="kn-cta-pave__title">Prendre RDV en 2&nbsp;min</p>
          <a href="<?php echo htmlspecialchars($booking_url); ?>" class="kn-cta-pave__arrow" aria-label="Prendre rendez-vous">&#8594;</a>
        </div>
        <p class="kn-hero__proof">
          <?php echo htmlspecialchars(isset($block['social_proof']) ? $block['social_proof'] : '4,9/5 · +110 avis · +400 canapés · +250 voitures'); ?>
        </p>
      </div>
      <div class="kn-hero__visual" aria-hidden="true">
        <?php if (!empty($block['image_url'])): ?>
          <img src="<?php echo htmlspecialchars($block['image_url']); ?>" alt="<?php echo htmlspecialchars(isset($block['image_alt']) ? $block['image_alt'] : ''); ?>" loading="eager">
        <?php else: ?>
          <div class="kn-placeholder kn-placeholder--hero">Photo héro</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
