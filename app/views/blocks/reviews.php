<?php $reviews = isset($reviews) && is_array($reviews) ? $reviews : array(); ?>
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
                <div class="kn-review-card__stars">
                    <?php $stars = isset($review['rating']) ? (int)$review['rating'] : 5;
                    for ($i = 0; $i < $stars; $i++) echo '&#9733;'; ?>
                </div>
                <p class="kn-review-card__text"><?php echo htmlspecialchars(isset($review['text']) ? $review['text'] : ''); ?></p>
                <div class="kn-review-card__meta">
                    <span class="kn-review-card__author"><?php echo htmlspecialchars(isset($review['author_name']) ? $review['author_name'] : 'Client'); ?></span>
                    <?php if (!empty($review['relative_time_description'])): ?>
                    <span>&mdash; <?php echo htmlspecialchars($review['relative_time_description']); ?></span>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="kn-review-card">
                <div class="kn-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Résultat absolument impeccable ! Mon canapé en tissu était tellement encrassé que j'avais perdu espoir. Keepnew l'a rendu comme neuf en moins de deux heures."</p>
                <div class="kn-review-card__meta"><span class="kn-review-card__author">Marie-Claire V.</span><span>&mdash; il y a 2 semaines</span></div>
            </li>
            <li class="kn-review-card">
                <div class="kn-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Intervention rapide, technicien très professionnel et ponctuel. L'intérieur de ma voiture est méconnaissable. Je recommande sans hésiter."</p>
                <div class="kn-review-card__meta"><span class="kn-review-card__author">Thomas D.</span><span>&mdash; il y a 1 mois</span></div>
            </li>
            <li class="kn-review-card">
                <div class="kn-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="kn-review-card__text">"Deux matelas nettoyés à domicile à Namur. Service au top, tarifs raisonnables et l'odeur fraîche après le nettoyage est vraiment agréable."</p>
                <div class="kn-review-card__meta"><span class="kn-review-card__author">Sophie M.</span><span>&mdash; il y a 3 semaines</span></div>
            </li>
        <?php endif; ?>
        </ul>
        <div class="kn-reviews__link">
            <a class="kn-reviews__google-link" href="https://g.page/r/ChIJ3eozWDz5wEcR8MOdpMxrwsY/review" target="_blank" rel="noopener noreferrer">Voir tous les avis sur Google &#8594;</a>
        </div>
    </div>
</section>
