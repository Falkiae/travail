<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Consent Mode v2 — défaut refusé (DOIT être avant tout autre script) -->
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    (function(){
      var stored = null;
      try { stored = localStorage.getItem('kn_consent'); } catch(e){}
      var granted = stored === 'granted';
      gtag('consent', 'default', {
        'ad_storage':            granted ? 'granted' : 'denied',
        'analytics_storage':     granted ? 'granted' : 'denied',
        'ad_user_data':          granted ? 'granted' : 'denied',
        'ad_personalization':    granted ? 'granted' : 'denied',
        'functionality_storage': 'granted',
        'security_storage':      'granted',
        'wait_for_update':       500
      });
      gtag('set', 'ads_data_redaction', !granted);
      gtag('set', 'url_passthrough', true);
    })();
    </script>

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

    <?php $__cv = isset($cache_version) && $cache_version ? '?v=' . htmlspecialchars($cache_version) : ''; ?>
    <link rel="stylesheet" href="/public/assets/css/tokens.css<?php echo $__cv; ?>">
    <link rel="stylesheet" href="/public/assets/css/public.css<?php echo $__cv; ?>">
    <?php if (isset($block_styles) && $block_styles): ?>
    <style id="kn-block-styles"><?php echo $block_styles; ?></style>
    <?php endif; ?>

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
<?php
$__navClass = 'kn-nav';
if (!empty($blocks) && is_array($blocks)) {
    $__firstBlock = $blocks[0];
    if (isset($__firstBlock['type']) && $__firstBlock['type'] === 'hero') {
        $__navClass .= ' kn-nav--transparent';
        $__heroBg = isset($__firstBlock['bg']) ? $__firstBlock['bg'] : 'cream';
        $__heroVideoBg = (isset($__firstBlock['visual_type']) && $__firstBlock['visual_type'] === 'video_bg');
        if ($__heroBg === 'blue' || $__heroBg === 'night' || $__heroVideoBg) {
            $__navClass .= ' kn-nav--light';
        }
    }
}
?>
<nav class="<?php echo $__navClass; ?>" aria-label="Navigation principale">
  <div class="container">
    <div class="kn-nav__inner">
      <a class="kn-nav__logo" href="/">
        <?php if (!empty($logo_url)): ?>
          <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="<?php echo htmlspecialchars(isset($logo_alt) ? $logo_alt : (isset($site_name) ? $site_name : 'Keepnew')); ?>" class="kn-nav__logo-img">
        <?php else: ?>
          <?php echo htmlspecialchars(isset($site_name) ? $site_name : 'Keepnew'); ?>
        <?php endif; ?>
      </a>
      <ul class="kn-nav__menu" role="list">
        <?php if (!empty($nav_items)): ?>
          <?php foreach ($nav_items as $item): ?>
            <?php
              $has_children = !empty($item['children']);
              $has_cols = false;
              if ($has_children) {
                foreach ($item['children'] as $child) {
                  if (!empty($child['col'])) { $has_cols = true; break; }
                }
              }
              $is_cta = !empty($item['cta']);
            ?>
            <?php if ($is_cta): ?>
              <li><a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>" class="kn-btn" target="<?php echo htmlspecialchars(isset($item['target']) ? $item['target'] : '_self'); ?>"><?php echo htmlspecialchars($item['label']); ?></a></li>
            <?php elseif ($has_children && $has_cols): ?>
              <!-- MEGA MENU -->
              <li class="kn-nav__has-mega">
                <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>" class="kn-nav__parent"><?php echo htmlspecialchars($item['label']); ?> <span class="kn-nav__arrow">&#9662;</span></a>
                <div class="kn-mega">
                  <div class="container">
                    <div class="kn-mega__grid">
                      <?php
                        $cols = array();
                        foreach ($item['children'] as $child) {
                          $c = (int)(isset($child['col']) ? $child['col'] : 1);
                          $cols[$c][] = $child;
                        }
                        ksort($cols);
                        foreach ($cols as $col_items):
                      ?>
                      <div class="kn-mega__col">
                        <?php foreach ($col_items as $ci): ?>
                        <a href="<?php echo htmlspecialchars(isset($ci['url']) ? $ci['url'] : '#'); ?>" class="kn-mega__link" target="<?php echo htmlspecialchars(isset($ci['target']) ? $ci['target'] : '_self'); ?>">
                          <?php echo htmlspecialchars($ci['label']); ?>
                          <?php if (!empty($ci['badge'])): ?>
                          <span class="kn-mega__badge"><?php echo htmlspecialchars($ci['badge']); ?></span>
                          <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </li>
            <?php elseif ($has_children): ?>
              <!-- DROPDOWN -->
              <li class="kn-nav__has-drop">
                <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>" class="kn-nav__parent"><?php echo htmlspecialchars($item['label']); ?> <span class="kn-nav__arrow">&#9662;</span></a>
                <ul class="kn-dropdown">
                  <?php foreach ($item['children'] as $ci): ?>
                  <li>
                    <a href="<?php echo htmlspecialchars(isset($ci['url']) ? $ci['url'] : '#'); ?>" target="<?php echo htmlspecialchars(isset($ci['target']) ? $ci['target'] : '_self'); ?>">
                      <?php echo htmlspecialchars($ci['label']); ?>
                      <?php if (!empty($ci['badge'])): ?>
                      <span class="kn-mega__badge"><?php echo htmlspecialchars($ci['badge']); ?></span>
                      <?php endif; ?>
                    </a>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php else: ?>
              <li><a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>" target="<?php echo htmlspecialchars(isset($item['target']) ? $item['target'] : '_self'); ?>"><?php echo htmlspecialchars($item['label']); ?></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <li><a href="/services">Services</a></li>
          <li><a href="/zones">Zones</a></li>
          <li><a href="/atelier">Atelier</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/contact">Contact</a></li>
        <?php endif; ?>
      </ul>
      <a href="<?php echo htmlspecialchars(isset($booking_url) ? $booking_url : '#'); ?>" class="kn-btn kn-nav__cta" style="display:none;">Prendre RDV</a>
      <button class="kn-nav__toggle" aria-label="Menu" aria-expanded="false" aria-controls="mobile-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <!-- mega menu overlay -->
  <div class="kn-mega-overlay"></div>
</nav>
<!-- mobile nav: flat list of all links -->
<div id="mobile-nav" class="kn-nav__mobile" role="navigation">
  <?php if (!empty($nav_items)): ?>
    <?php foreach ($nav_items as $item): ?>
      <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>"><?php echo htmlspecialchars($item['label']); ?></a>
      <?php if (!empty($item['children'])): ?>
        <?php foreach ($item['children'] as $ci): ?>
          <a href="<?php echo htmlspecialchars(isset($ci['url']) ? $ci['url'] : '#'); ?>" style="padding-left:1.5rem; font-size:.875rem; opacity:.8;"><?php echo htmlspecialchars($ci['label']); ?></a>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <a href="/services">Services</a>
    <a href="/zones">Zones</a>
    <a href="/atelier">Atelier</a>
    <a href="/blog">Blog</a>
    <a href="/contact">Contact</a>
  <?php endif; ?>
  <a href="<?php echo htmlspecialchars(isset($booking_url) ? $booking_url : '#'); ?>" style="margin-top:.75rem; display:inline-block; font-weight:800; color:var(--kn-blue);">&#8594; Prendre RDV en 2 min</a>
</div>

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
<?php
$__footerMenus   = isset($footer_menus) && is_array($footer_menus) ? $footer_menus : [];
$__footerSvcs    = isset($__footerMenus['services']) ? $__footerMenus['services'] : [];
$__footerZones   = isset($__footerMenus['zones']) ? $__footerMenus['zones'] : [];
$__footerLegal   = isset($__footerMenus['legal']) ? $__footerMenus['legal'] : (isset($__footerMenus['legal-links']) ? $__footerMenus['legal-links'] : []);
$__footerTag     = isset($footer_tagline) && $footer_tagline ? $footer_tagline : 'Service de nettoyage professionnel à domicile ou en atelier. Liège · Namur · Bruxelles · Luxembourg.';
$__footerPhone   = isset($footer_phone) && $footer_phone ? $footer_phone : '+32 (0)4 55 13 84 19';
$__footerLegalTx = isset($footer_legal_text) && $footer_legal_text ? $footer_legal_text : 'Tous droits réservés.';
$__siteName      = isset($site_name) && $site_name ? $site_name : 'Keepnew';
?>
<footer class="kn-footer" aria-label="Pied de page">
    <div class="container">
        <div class="kn-footer__grid">
            <div>
                <a class="kn-footer__logo" href="/"><?php echo htmlspecialchars($__siteName); ?></a>
                <p class="kn-footer__tagline"><?php echo htmlspecialchars($__footerTag); ?></p>
            </div>
            <div>
                <p class="kn-footer__heading">Services</p>
                <nav class="kn-footer__links" aria-label="Services">
                    <?php if (!empty($__footerSvcs)): ?>
                        <?php foreach ($__footerSvcs as $item): ?>
                        <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>"><?php echo htmlspecialchars(isset($item['label']) ? $item['label'] : ''); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="/services/canape">Canapé</a>
                        <a href="/services/matelas">Matelas</a>
                        <a href="/services/voiture">Voiture</a>
                        <a href="/services/terrasse">Terrasse</a>
                        <a href="/services/polissage">Polissage &amp; Céramique</a>
                        <a href="/atelier">Atelier à Visé</a>
                    <?php endif; ?>
                </nav>
            </div>
            <div>
                <p class="kn-footer__heading">Contact</p>
                <a class="kn-footer__phone" href="tel:<?php echo htmlspecialchars(preg_replace('/[^+0-9]/', '', $__footerPhone)); ?>"><?php echo htmlspecialchars($__footerPhone); ?></a>
                <nav class="kn-footer__links" aria-label="Zones">
                    <?php if (!empty($__footerZones)): ?>
                        <?php foreach ($__footerZones as $item): ?>
                        <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>"><?php echo htmlspecialchars(isset($item['label']) ? $item['label'] : ''); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="/zones/liege">Liège</a>
                        <a href="/zones/namur">Namur</a>
                        <a href="/zones/bruxelles">Bruxelles</a>
                        <a href="/zones/luxembourg">Luxembourg</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
        <div class="kn-footer__bottom">
            <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($__siteName); ?> &mdash; <?php echo htmlspecialchars($__footerLegalTx); ?></span>
            <span>
                <?php if (!empty($__footerLegal)): ?>
                    <?php foreach ($__footerLegal as $i => $item): ?>
                        <?php if ($i > 0): ?>&nbsp;&middot;&nbsp;<?php endif; ?>
                        <a href="<?php echo htmlspecialchars(isset($item['url']) ? $item['url'] : '#'); ?>" style="color:rgba(255,255,255,.5);transition:color .15s;"><?php echo htmlspecialchars(isset($item['label']) ? $item['label'] : ''); ?></a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="/mentions-legales" style="color:rgba(255,255,255,.5);transition:color .15s;">Mentions légales</a>
                    &nbsp;&middot;&nbsp;
                    <a href="/politique-de-confidentialite" style="color:rgba(255,255,255,.5);transition:color .15s;">Confidentialité</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</footer>

<?php if (!empty($cookie_banner_enabled)): ?>
<div id="kn-cookie-banner" class="kn-cookie" role="dialog" aria-live="polite" aria-label="Consentement cookies" hidden>
    <div class="kn-cookie__inner">
        <p class="kn-cookie__text"><?php echo htmlspecialchars($cookie_banner_text ?? ''); ?>
            <?php if (!empty($cookie_policy_url)): ?>
            <a class="kn-cookie__link" href="<?php echo htmlspecialchars($cookie_policy_url); ?>">En savoir plus</a>
            <?php endif; ?>
        </p>
        <div class="kn-cookie__actions">
            <button type="button" class="kn-cookie__btn kn-cookie__btn--reject" data-kn-cookie="reject">Refuser</button>
            <button type="button" class="kn-cookie__btn kn-cookie__btn--accept" data-kn-cookie="accept">Accepter</button>
        </div>
    </div>
</div>
<script>
(function(){
  var banner = document.getElementById('kn-cookie-banner');
  if (!banner) return;
  var stored = null;
  try { stored = localStorage.getItem('kn_consent'); } catch(e){}
  if (!stored) banner.hidden = false;
  function setConsent(state) {
    try { localStorage.setItem('kn_consent', state); } catch(e){}
    if (typeof gtag === 'function') {
      gtag('consent', 'update', {
        'ad_storage':         state === 'granted' ? 'granted' : 'denied',
        'analytics_storage':  state === 'granted' ? 'granted' : 'denied',
        'ad_user_data':       state === 'granted' ? 'granted' : 'denied',
        'ad_personalization': state === 'granted' ? 'granted' : 'denied'
      });
    }
    banner.hidden = true;
  }
  banner.querySelector('[data-kn-cookie="accept"]').addEventListener('click', function(){ setConsent('granted'); });
  banner.querySelector('[data-kn-cookie="reject"]').addEventListener('click', function(){ setConsent('denied'); });
})();
</script>
<?php endif; ?>

<script src="/public/assets/js/public.js<?php echo isset($__cv) ? $__cv : ''; ?>"></script>
</body>
</html>
