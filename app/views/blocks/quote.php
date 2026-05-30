<section class="kn-section">
    <div class="container">
        <blockquote style="border-left:4px solid var(--kn-blue);padding:1.5rem 2rem;background:var(--kn-blue-050);border-radius:0 var(--r-card) var(--r-card) 0;max-width:720px;margin:0 auto;">
            <p style="font-size:1.125rem;font-style:italic;margin:0 0 1rem;">"<?php echo htmlspecialchars(isset($block['text']) ? $block['text'] : ''); ?>"</p>
            <?php if (!empty($block['author'])): ?>
            <footer style="font-weight:600;color:var(--kn-blue);">
                — <?php echo htmlspecialchars($block['author']); ?>
                <?php if (!empty($block['role'])): ?><span style="font-weight:400;color:var(--kn-muted);">, <?php echo htmlspecialchars($block['role']); ?></span><?php endif; ?>
            </footer>
            <?php endif; ?>
        </blockquote>
    </div>
</section>
