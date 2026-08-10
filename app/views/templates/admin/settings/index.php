<?php
function s(array $settings, string $key, string $default = ''): string {
    return htmlspecialchars($settings[$key] ?? $default);
}
?>

<div class="page-header">
  <h1>Réglages</h1>
</div>

<form method="POST" action="/admin/settings">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <!-- Mode développement -->
  <div class="card" style="border: 2px solid #f59e0b; background: #fffbeb;">
    <div class="card-title">⚠️ Mode développement</div>
    <div class="form-group">
      <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer; font-weight:600;">
        <input type="checkbox" name="noindex_all" value="1" <?= !empty($settings['noindex_all']) && $settings['noindex_all'] === '1' ? 'checked' : '' ?> style="width:1.25rem;height:1.25rem;">
        Bloquer l'indexation (noindex,nofollow sur toutes les pages)
      </label>
      <p style="margin-top:.5rem; color:#92400e; font-size:.85rem;">Active <code>noindex,nofollow</code> sur l'ensemble du site — à désactiver avant le lancement en production.</p>
    </div>
  </div>

  <!-- Général -->
  <div class="card">
    <div class="card-title">Général</div>
    <div class="form-row">
      <div class="form-group">
        <label for="site_name">Nom du site</label>
        <input type="text" id="site_name" name="site_name" value="<?= s($settings, 'site_name') ?>">
      </div>
      <div class="form-group">
        <label for="site_baseline">Baseline / Slogan</label>
        <input type="text" id="site_baseline" name="site_baseline" value="<?= s($settings, 'site_baseline') ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="booking_url">URL de réservation <small style="font-weight:400;opacity:.6">(utilisée par tous les boutons « Réserver » du site, dont le CTA sticky)</small></label>
      <input type="text" id="booking_url" name="booking_url" value="<?= s($settings, 'booking_url') ?>" placeholder="/reservation">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="favicon">Favicon (URL)</label>
        <input type="text" id="favicon" name="favicon" value="<?= s($settings, 'favicon') ?>" placeholder="/uploads/favicon.ico">
      </div>
      <div class="form-group">
        <label for="og_image_default">Image OG par défaut (URL)</label>
        <input type="text" id="og_image_default" name="og_image_default" value="<?= s($settings, 'og_image_default') ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="robots_global">Robots global</label>
      <select id="robots_global" name="robots_global">
        <?php foreach (['index,follow' => 'index, follow', 'noindex,follow' => 'noindex, follow', 'noindex,nofollow' => 'noindex, nofollow'] as $v => $l): ?>
        <option value="<?= htmlspecialchars($v) ?>" <?= ($settings['robots_global'] ?? 'index,follow') === $v ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Logo -->
  <div class="card">
    <div class="card-title">Logo</div>
    <p class="help-text">Trois versions du logo, choisies automatiquement selon le fond du hero. Une fois la navigation défilée (fond crème fixe), c'est toujours la version sombre qui s'affiche — quel que soit le hero en dessous.</p>
    <div class="form-row">
      <div class="form-group">
        <label for="logo_url">Logo version sombre</label>
        <div class="input-row">
          <input type="text" id="logo_url" name="logo_url" value="<?= s($settings, 'logo_url') ?>" placeholder="/uploads/logo.svg" style="flex:1">
          <button type="button" class="btn btn-secondary btn-sm" id="logo-media-btn">Médiathèque</button>
        </div>
        <small style="color:var(--color-muted)">Affiché sur fond clair (blanc, ivoire, crème) et une fois la navigation défilée. Laissez vide pour afficher le nom du site en texte.</small>
      </div>
      <div class="form-group">
        <label for="logo_light_url">Logo version claire</label>
        <div class="input-row">
          <input type="text" id="logo_light_url" name="logo_light_url" value="<?= s($settings, 'logo_light_url') ?>" placeholder="/uploads/logo-light.svg" style="flex:1">
          <button type="button" class="btn btn-secondary btn-sm" id="logo-light-media-btn">Médiathèque</button>
        </div>
        <small style="color:var(--color-muted)">Affiché sur hero navy / navy foncé / vidéo. Si vide, le logo sombre est utilisé partout.</small>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="logo_rose_url">Logo version rose <small style="font-weight:400;opacity:.6">(optionnel)</small></label>
        <div class="input-row">
          <input type="text" id="logo_rose_url" name="logo_rose_url" value="<?= s($settings, 'logo_rose_url') ?>" placeholder="/uploads/logo-rose.svg" style="flex:1">
          <button type="button" class="btn btn-secondary btn-sm" id="logo-rose-media-btn">Médiathèque</button>
        </div>
        <small style="color:var(--color-muted)">Affiché uniquement sur hero à fond rose poudré. Si vide, le logo sombre est utilisé (comportement actuel).</small>
      </div>
      <div class="form-group">
        <label for="logo_alt">Texte alternatif du logo</label>
        <input type="text" id="logo_alt" name="logo_alt" value="<?= s($settings, 'logo_alt') ?>" placeholder="Keepnew — Nettoyage à domicile">
      </div>
    </div>
    <?php if (!empty($settings['logo_url']) || !empty($settings['logo_light_url']) || !empty($settings['logo_rose_url'])): ?>
    <div class="form-row">
      <?php if (!empty($settings['logo_url'])): ?>
      <div class="form-group">
        <label>Aperçu — fond clair / défilé</label>
        <div style="background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);padding:1rem;display:inline-flex;align-items:center">
          <img src="<?= htmlspecialchars($settings['logo_url']) ?>" alt="" style="max-height:50px;max-width:180px;object-fit:contain">
        </div>
      </div>
      <?php endif; ?>
      <?php if (!empty($settings['logo_light_url'])): ?>
      <div class="form-group">
        <label>Aperçu — fond navy</label>
        <div style="background:#24355C;border:1px solid #24355C;border-radius:var(--radius);padding:1rem;display:inline-flex;align-items:center">
          <img src="<?= htmlspecialchars($settings['logo_light_url']) ?>" alt="" style="max-height:50px;max-width:180px;object-fit:contain">
        </div>
      </div>
      <?php endif; ?>
      <?php if (!empty($settings['logo_rose_url'])): ?>
      <div class="form-group">
        <label>Aperçu — fond rose</label>
        <div style="background:#F8E7E9;border:1px solid #EFC6CB;border-radius:var(--radius);padding:1rem;display:inline-flex;align-items:center">
          <img src="<?= htmlspecialchars($settings['logo_rose_url']) ?>" alt="" style="max-height:50px;max-width:180px;object-fit:contain">
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Tracking -->
  <div class="card">
    <div class="card-title">Tracking</div>
    <div class="form-row">
      <div class="form-group">
        <label for="gtm_id">Google Tag Manager ID</label>
        <input type="text" id="gtm_id" name="gtm_id" value="<?= s($settings, 'gtm_id') ?>" placeholder="GTM-XXXXXXX">
      </div>
      <div class="form-group">
        <label for="ga4_id">Google Analytics 4 ID</label>
        <input type="text" id="ga4_id" name="ga4_id" value="<?= s($settings, 'ga4_id') ?>" placeholder="G-XXXXXXXXXX">
      </div>
    </div>
    <div class="form-group">
      <label for="head_custom_code">Code personnalisé &lt;head&gt;</label>
      <textarea id="head_custom_code" name="head_custom_code" rows="4" placeholder="<!-- scripts, meta, etc -->"><?= s($settings, 'head_custom_code') ?></textarea>
    </div>
    <div class="form-group">
      <label for="body_custom_code">Code personnalisé &lt;body&gt; (début)</label>
      <textarea id="body_custom_code" name="body_custom_code" rows="4" placeholder="<!-- noscript, scripts, etc -->"><?= s($settings, 'body_custom_code') ?></textarea>
    </div>
  </div>

  <!-- Cookies / Consent Mode v2 -->
  <div class="card">
    <div class="card-title">Cookies / Consent Mode v2</div>
    <div class="form-group">
      <label>
        <input type="hidden" name="cookie_banner_enabled" value="0">
        <input type="checkbox" name="cookie_banner_enabled" value="1" <?= ($settings['cookie_banner_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
        Afficher le bandeau de cookies
      </label>
    </div>
    <div class="form-group">
      <label for="cookie_banner_text">Texte du bandeau</label>
      <textarea id="cookie_banner_text" name="cookie_banner_text" rows="3"><?= s($settings, 'cookie_banner_text') ?></textarea>
    </div>
    <div class="form-group">
      <label for="cookie_policy_url">URL de la politique de cookies</label>
      <input type="text" id="cookie_policy_url" name="cookie_policy_url" value="<?= s($settings, 'cookie_policy_url') ?>" placeholder="/politique-cookies">
    </div>
    <div class="help-text" style="background:#f0f9ff;border-left:4px solid var(--color-primary);padding:.75rem;border-radius:var(--radius)">
      Le <strong>Consent Mode v2</strong> de Google est géré automatiquement. Les catégories
      <code>analytics_storage</code> et <code>ad_storage</code> sont refusées par défaut jusqu'à l'acceptation de l'utilisateur.
    </div>
  </div>

  <!-- Réseaux sociaux -->
  <div class="card">
    <div class="card-title">Réseaux sociaux</div>
    <div class="form-row">
      <div class="form-group">
        <label for="social_linkedin">LinkedIn</label>
        <input type="url" id="social_linkedin" name="social_linkedin" value="<?= s($settings, 'social_linkedin') ?>" placeholder="https://linkedin.com/company/…">
      </div>
      <div class="form-group">
        <label for="social_twitter">Twitter / X</label>
        <input type="url" id="social_twitter" name="social_twitter" value="<?= s($settings, 'social_twitter') ?>" placeholder="https://twitter.com/…">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="social_facebook">Facebook</label>
        <input type="url" id="social_facebook" name="social_facebook" value="<?= s($settings, 'social_facebook') ?>" placeholder="https://facebook.com/…">
      </div>
      <div class="form-group">
        <label for="social_instagram">Instagram</label>
        <input type="url" id="social_instagram" name="social_instagram" value="<?= s($settings, 'social_instagram') ?>" placeholder="https://instagram.com/…">
      </div>
    </div>
  </div>

  <!-- Avis Google -->
  <div class="card">
    <div class="card-title">Avis Google (My Business)</div>
    <p class="help-text">Récupère automatiquement vos 5 avis Google les plus récents (note ≥ 4/5, les 3 meilleurs affichés), mis en cache 24h. Nécessite les deux champs ci-dessous.</p>
    <div class="form-row">
      <div class="form-group">
        <label for="google_reviews_api_key">Clé API Google Places</label>
        <input type="text" id="google_reviews_api_key" name="google_reviews_api_key" value="<?= s($settings, 'google_reviews_api_key') ?>" placeholder="AIzaSy…">
      </div>
      <div class="form-group">
        <label for="google_place_id">Place ID (établissement)</label>
        <input type="text" id="google_place_id" name="google_place_id" value="<?= s($settings, 'google_place_id') ?>" placeholder="ChIJ…">
        <small style="color:var(--color-muted)">Trouvez votre Place ID sur <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener">developers.google.com</a>. Si votre compte Google gère plusieurs fiches établissement, veillez à choisir celle de la bonne adresse : cherchez son nom exact dans l'outil, vérifiez l'adresse affichée sur la carte, puis copiez le Place ID correspondant.</small>
      </div>
    </div>
    <div class="form-group">
      <button type="button" id="test-google-reviews-btn" class="btn btn-secondary btn-sm">Tester la connexion</button>
      <div id="test-google-reviews-result" style="margin-top:.75rem;font-size:.875rem;"></div>
    </div>
  </div>

  <!-- Footer -->
  <div class="card">
    <div class="card-title">Footer</div>
    <div class="form-group">
      <label for="footer_tagline">Tagline (sous le logo)</label>
      <textarea id="footer_tagline" name="footer_tagline" rows="2"><?= s($settings, 'footer_tagline') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="footer_phone">Téléphone affiché</label>
        <input type="text" id="footer_phone" name="footer_phone" value="<?= s($settings, 'footer_phone') ?>" placeholder="+32 (0)4 55 13 84 19">
      </div>
      <div class="form-group">
        <label for="footer_legal_text">Texte légal (bas de footer)</label>
        <input type="text" id="footer_legal_text" name="footer_legal_text" value="<?= s($settings, 'footer_legal_text') ?>" placeholder="Tous droits réservés.">
      </div>
    </div>
    <p class="help-text">Les listes de liens (Services, Zones, etc.) se gèrent dans <a href="/admin/menus">Menus</a> en créant des menus avec l'emplacement « Footer ». Nommez-les <code>Services</code>, <code>Zones</code>, ou <code>Légal</code>.</p>
  </div>

  <!-- CTA sticky -->
  <div class="card">
    <div class="card-title">CTA sticky (barre flottante)</div>
    <p class="help-text">Barre qui apparaît en bas de page après un certain pourcentage de défilement, avec un bouton d'appel et un bouton de réservation. Les champs laissés vides reprennent le téléphone et l'URL de réservation définis ailleurs dans les réglages.</p>

    <div class="form-group">
      <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer; font-weight:600;">
        <input type="hidden" name="sticky_cta_enabled" value="0">
        <input type="checkbox" name="sticky_cta_enabled" value="1" <?= ($settings['sticky_cta_enabled'] ?? '1') === '1' ? 'checked' : '' ?> style="width:1.25rem;height:1.25rem;">
        Afficher le CTA sticky
      </label>
    </div>

    <div class="form-group">
      <label for="sticky_cta_threshold">Apparition après (% de la page défilée)</label>
      <input type="number" id="sticky_cta_threshold" name="sticky_cta_threshold" min="0" max="100" step="5" value="<?= s($settings, 'sticky_cta_threshold', '40') ?>" style="max-width:140px">
    </div>

    <hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">
    <h4 style="margin:.5rem 0 1rem">Bouton téléphone</h4>
    <div class="form-row">
      <div class="form-group">
        <label for="sticky_cta_phone_label">Libellé <small style="font-weight:400;opacity:.6">(optionnel — sinon le numéro s'affiche seul)</small></label>
        <input type="text" id="sticky_cta_phone_label" name="sticky_cta_phone_label" value="<?= s($settings, 'sticky_cta_phone_label') ?>" placeholder="Nous appeler">
      </div>
      <div class="form-group">
        <label for="sticky_cta_phone_number">Numéro <small style="font-weight:400;opacity:.6">(optionnel — sinon reprend le téléphone du Footer)</small></label>
        <input type="text" id="sticky_cta_phone_number" name="sticky_cta_phone_number" value="<?= s($settings, 'sticky_cta_phone_number') ?>" placeholder="<?= s($settings, 'footer_phone', '+32 (0)4 55 13 84 19') ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="sticky_cta_phone_icon">Icône</label>
      <div class="input-row icon-field">
        <input type="text" list="kn-icon-list" id="sticky_cta_phone_icon" name="sticky_cta_phone_icon" value="<?= s($settings, 'sticky_cta_phone_icon') ?>" placeholder="phone" style="flex:1">
        <button type="button" class="btn btn-secondary btn-sm icon-pick-btn">Choisir</button>
        <span class="icon-field__preview"></span>
      </div>
    </div>

    <hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb">
    <h4 style="margin:.5rem 0 1rem">Bouton réservation</h4>
    <div class="form-row">
      <div class="form-group">
        <label for="sticky_cta_book_label">Libellé</label>
        <input type="text" id="sticky_cta_book_label" name="sticky_cta_book_label" value="<?= s($settings, 'sticky_cta_book_label') ?>" placeholder="Réserver un créneau">
      </div>
      <div class="form-group">
        <label for="sticky_cta_book_url">URL <small style="font-weight:400;opacity:.6">(optionnel — sinon reprend l'URL de réservation du bloc Général)</small></label>
        <input type="text" id="sticky_cta_book_url" name="sticky_cta_book_url" value="<?= s($settings, 'sticky_cta_book_url') ?>" placeholder="<?= s($settings, 'booking_url', '#') ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="sticky_cta_book_icon">Icône</label>
      <div class="input-row icon-field">
        <input type="text" list="kn-icon-list" id="sticky_cta_book_icon" name="sticky_cta_book_icon" value="<?= s($settings, 'sticky_cta_book_icon') ?>" placeholder="calendar" style="flex:1">
        <button type="button" class="btn btn-secondary btn-sm icon-pick-btn">Choisir</button>
        <span class="icon-field__preview"></span>
      </div>
    </div>
  </div>

  <!-- Maintenance -->
  <div class="card">
    <div class="card-title">Maintenance</div>
    <div class="form-group">
      <label>
        <input type="hidden" name="maintenance_mode" value="0">
        <input type="checkbox" name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
        Activer le mode maintenance
      </label>
    </div>
    <div class="form-group">
      <label for="maintenance_message">Message de maintenance</label>
      <textarea id="maintenance_message" name="maintenance_message" rows="3"><?= s($settings, 'maintenance_message', 'Site en maintenance, revenez bientôt.') ?></textarea>
    </div>
  </div>

  <!-- Performance / Cache -->
  <div class="card">
    <div class="card-title">Performance / Cache</div>
    <div class="form-group">
      <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer;">
        <input type="hidden" name="cache_enabled" value="0">
        <input type="checkbox" name="cache_enabled" value="1" <?= ($settings['cache_enabled'] ?? '0') === '1' ? 'checked' : '' ?> style="width:1.25rem;height:1.25rem;">
        Activer le cache navigateur (versionning des assets CSS/JS)
      </label>
      <p class="help-text">Ajoute un <code>?v=xxx</code> aux fichiers CSS et JS pour forcer le rechargement après une mise à jour.</p>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Enregistrer les réglages</button>
  </div>
</form>

<form method="POST" action="/admin/settings/clear-cache" style="margin-top:1rem;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <button type="submit" class="btn btn-secondary">Vider le cache navigateur</button>
  <span class="help-text" style="margin-left:.75rem;">Force les navigateurs à re-télécharger les fichiers CSS et JS.</span>
</form>
<script>
document.getElementById('logo-media-btn').addEventListener('click', function() {
  openMediaModal(function(media) {
    document.getElementById('logo_url').value = media.webp_path || media.path;
  });
});
document.getElementById('logo-light-media-btn').addEventListener('click', function() {
  openMediaModal(function(media) {
    document.getElementById('logo_light_url').value = media.webp_path || media.path;
  });
});
document.getElementById('logo-rose-media-btn').addEventListener('click', function() {
  openMediaModal(function(media) {
    document.getElementById('logo_rose_url').value = media.webp_path || media.path;
  });
});

document.getElementById('test-google-reviews-btn').addEventListener('click', function() {
  var btn    = this;
  var result = document.getElementById('test-google-reviews-result');
  var apiKey  = document.getElementById('google_reviews_api_key').value.trim();
  var placeId = document.getElementById('google_place_id').value.trim();

  result.style.color = '';
  result.textContent = 'Test en cours…';
  btn.disabled = true;

  var body = new URLSearchParams();
  body.set('csrf_token', <?= json_encode($csrf_token) ?>);
  body.set('api_key', apiKey);
  body.set('place_id', placeId);

  fetch('/admin/settings/test-google-reviews', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString(),
  })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      btn.disabled = false;
      if (data.ok) {
        result.style.color = 'var(--color-success, #16a34a)';
        result.textContent = '✓ Connecté à « ' + data.name + ' » — note ' + data.rating + '/5 (' + data.total + ' avis au total), ' + data.review_count + ' avis récupérés par cet appel.';
      } else {
        result.style.color = 'var(--color-danger, #dc2626)';
        result.textContent = '✗ ' + data.message;
      }
    })
    .catch(function() {
      btn.disabled = false;
      result.style.color = 'var(--color-danger, #dc2626)';
      result.textContent = '✗ Erreur réseau pendant le test.';
    });
});
</script>
