<section class="kn-section">
    <div class="container" style="text-align:center;">
        <?php if (!empty($block['path'])): ?>
        <img src="<?php echo htmlspecialchars($block['path']); ?>" alt="<?php echo htmlspecialchars(isset($block['alt']) ? $block['alt'] : ''); ?>" loading="lazy" style="max-width:100%;border-radius:var(--r-card);">
        <?php if (!empty($block['caption'])): ?>
        <p style="color:var(--kn-muted);font-size:.875rem;margin-top:.5rem;"><?php echo htmlspecialchars($block['caption']); ?></p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
