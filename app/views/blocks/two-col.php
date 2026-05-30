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
