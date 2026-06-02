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
    <div class="form-row">
      <div class="form-group">
        <label for="logo_url">Logo image</label>
        <div class="input-row">
          <input type="text" id="logo_url" name="logo_url" value="<?= s($settings, 'logo_url') ?>" placeholder="/uploads/logo.svg" style="flex:1">
          <button type="button" class="btn btn-secondary btn-sm" id="logo-media-btn">Médiathèque</button>
        </div>
        <small style="color:var(--color-muted)">Laissez vide pour afficher le nom du site en texte.</small>
      </div>
      <div class="form-group">
        <label for="logo_alt">Texte alternatif du logo</label>
        <input type="text" id="logo_alt" name="logo_alt" value="<?= s($settings, 'logo_alt') ?>" placeholder="Keepnew — Nettoyage à domicile">
      </div>
    </div>
    <?php if (!empty($settings['logo_url'])): ?>
    <div class="form-group">
      <label>Aperçu</label>
      <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:var(--radius);padding:1rem;display:inline-block">
        <img src="<?= htmlspecialchars($settings['logo_url']) ?>" alt="<?= htmlspecialchars($settings['logo_alt'] ?? '') ?>" style="max-height:60px;max-width:200px;object-fit:contain">
      </div>
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
    <div class="form-row">
      <div class="form-group">
        <label for="google_reviews_api_key">Clé API Google Places</label>
        <input type="text" id="google_reviews_api_key" name="google_reviews_api_key" value="<?= s($settings, 'google_reviews_api_key') ?>" placeholder="AIzaSy…">
      </div>
      <div class="form-group">
        <label for="google_place_id">Place ID (établissement)</label>
        <input type="text" id="google_place_id" name="google_place_id" value="<?= s($settings, 'google_place_id') ?>" placeholder="ChIJ…">
        <small style="color:var(--color-muted)">Trouvez votre Place ID sur <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener">developers.google.com</a></small>
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

  <div class="form-actions">
    <button type="submit" class="btn">Enregistrer les réglages</button>
  </div>
</form>
<script>
document.getElementById('logo-media-btn').addEventListener('click', function() {
  openMediaModal(function(media) {
    document.getElementById('logo_url').value = media.webp_path || media.path;
  });
});
</script>
