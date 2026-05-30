<?php
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
