<?php if (!empty($block['path'])): ?>
<section class="kn-section">
    <div class="container">
        <a href="<?php echo htmlspecialchars($block['path']); ?>" class="kn-btn kn-btn--outline" download>
            &#8595; <?php echo htmlspecialchars(isset($block['label']) ? $block['label'] : 'Télécharger'); ?>
        </a>
    </div>
</section>
<?php endif; ?>
