<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo isset($meta_title) && $meta_title ? htmlspecialchars($meta_title) . ' — Keepnew' : (isset($title) && $title ? htmlspecialchars($title) . ' — Keepnew' : 'Keepnew — Nettoyage à domicile'); ?></title>

    <?php if (isset($meta_description) && $meta_description): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php else: ?>
    <meta name="description" content="Keepnew — Service de nettoyage professionnel à domicile : canapés, matelas, voitures, terrasses. Zone Liège, Namur, Bruxelles, Luxembourg. Atelier à Visé.">
    <?php endif; ?>

    <?php if (isset($noindex_all) && $noindex_all): ?>
    <meta name="robots" content="noindex,nofollow">
    <?php elseif (isset($robots_global) && $robots_global && $robots_global !== 'index,follow'): ?>
    <meta name="robots" content="<?php echo htmlspecialchars($robots_global); ?>">
    <?php endif; ?>

    <?php if (isset($canonical) && $canonical): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
    <?php endif; ?>

    <?php if (isset($og_image) && $og_image): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo isset($meta_title) && $meta_title ? htmlspecialchars($meta_title) : 'Keepnew — Nettoyage à domicile'; ?>">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;0,800;0,900;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/public/assets/css/tokens.css">
    <link rel="stylesheet" href="/public/assets/css/public.css">

    <?php if (isset($gtm_id) && $gtm_id): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo htmlspecialchars($gtm_id); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php endif; ?>
</head>
<body>

<?php if (isset($gtm_id) && $gtm_id): ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo htmlspecialchars($gtm_id); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>

<!-- ── Navigation ──────────────────────────────────────────── -->
<nav class="kn-nav" aria-label="Navigation principale">
    <div class="container">
        <div class="kn-nav__inner">
            <a class="kn-nav__logo" href="/">Keepnew</a>

            <ul class="kn-nav__menu" role="list">
                <?php if (isset($nav_items) && is_array($nav_items) && count($nav_items) > 0): ?>
                    <?php foreach ($nav_items as $nav_item): ?>
                    <li><a href="<?php echo htmlspecialchars($nav_item['url']); ?>"><?php echo htmlspecialchars($nav_item['label']); ?></a></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><a href="/services">Services</a></li>
                    <li><a href="/zones">Zones</a></li>
                    <li><a href="/atelier">Atelier</a></li>
                    <li><a href="/blog">Blog</a></li>
                    <li><a href="/contact">Contact</a></li>
                <?php endif; ?>
            </ul>

            <a href="<?php echo isset($booking_url) && $booking_url ? htmlspecialchars($booking_url) : '#'; ?>" class="kn-btn kn-nav__cta" style="display:none;">
                Prendre RDV
            </a>

            <button class="kn-nav__toggle" aria-label="Menu" aria-expanded="false" aria-controls="mobile-nav">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
    <div id="mobile-nav" class="kn-nav__mobile" role="navigation">
        <?php if (isset($nav_items) && is_array($nav_items) && count($nav_items) > 0): ?>
            <?php foreach ($nav_items as $nav_item): ?>
            <a href="<?php echo htmlspecialchars($nav_item['url']); ?>"><?php echo htmlspecialchars($nav_item['label']); ?></a>
            <?php endforeach; ?>
        <?php else: ?>
            <a href="/services">Services</a>
            <a href="/zones">Zones</a>
            <a href="/atelier">Atelier</a>
            <a href="/blog">Blog</a>
            <a href="/contact">Contact</a>
        <?php endif; ?>
        <a href="<?php echo isset($booking_url) && $booking_url ? htmlspecialchars($booking_url) : '#'; ?>" style="margin-top:.75rem; display:inline-block; font-weight:800; color:var(--kn-blue);">&#8594; Prendre RDV en 2 min</a>
    </div>
</nav>

<!-- ── Flash Messages ───────────────────────────────────────── -->
<?php if (isset($_SESSION['flash']) && $_SESSION['flash']): ?>
    <?php foreach ($_SESSION['flash'] as $flash_type => $flash_msg): ?>
    <div class="kn-flash kn-flash--<?php echo htmlspecialchars($flash_type); ?>" role="alert">
        <?php echo htmlspecialchars($flash_msg); ?>
    </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ── Page Content ─────────────────────────────────────────── -->
<?php $this->content(); ?>

<!-- ── Footer ───────────────────────────────────────────────── -->
<footer class="kn-footer" aria-label="Pied de page">
    <div class="container">
        <div class="kn-footer__grid">
            <div>
                <a class="kn-footer__logo" href="/">Keepnew</a>
                <p class="kn-footer__tagline">Service de nettoyage professionnel à domicile ou en atelier. Liège · Namur · Bruxelles · Luxembourg.</p>
            </div>
            <div>
                <p class="kn-footer__heading">Services</p>
                <nav class="kn-footer__links" aria-label="Services">
                    <a href="/services/canape">Canapé</a>
                    <a href="/services/matelas">Matelas</a>
                    <a href="/services/voiture">Voiture</a>
                    <a href="/services/terrasse">Terrasse</a>
                    <a href="/services/polissage">Polissage &amp; Céramique</a>
                    <a href="/atelier">Atelier à Visé</a>
                </nav>
            </div>
            <div>
                <p class="kn-footer__heading">Contact</p>
                <a class="kn-footer__phone" href="tel:+3245513841 9">+32 (0)4 55 13 84 19</a>
                <nav class="kn-footer__links" aria-label="Zones">
                    <a href="/zones/liege">Liège</a>
                    <a href="/zones/namur">Namur</a>
                    <a href="/zones/bruxelles">Bruxelles</a>
                    <a href="/zones/luxembourg">Luxembourg</a>
                </nav>
            </div>
        </div>
        <div class="kn-footer__bottom">
            <span>&copy; <?php echo date('Y'); ?> <?php echo isset($site_name) && $site_name ? htmlspecialchars($site_name) : 'Keepnew'; ?> &mdash; Tous droits réservés.</span>
            <span>
                <a href="/mentions-legales" style="color:rgba(255,255,255,.5);transition:color .15s;">Mentions légales</a>
                &nbsp;&middot;&nbsp;
                <a href="/politique-de-confidentialite" style="color:rgba(255,255,255,.5);transition:color .15s;">Confidentialité</a>
            </span>
        </div>
    </div>
</footer>

<script src="/public/assets/js/public.js"></script>
</body>
</html>
