<?php
$isEdit = !empty($page);
$action = $isEdit ? '/admin/pages/' . (int)$page['id'] . '/edit' : '/admin/pages/new';
$existingBlocks = $isEdit && !empty($page['content']) ? json_decode($page['content'], true) : [];
?>

<?php if ($isEdit): ?>
<script>window.existingBlocks = <?= json_encode($existingBlocks) ?>;</script>
<?php endif; ?>

<div class="page-header">
  <h1><?= htmlspecialchars($title) ?></h1>
  <a href="/admin/pages" class="btn btn-secondary">← Retour</a>
</div>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="form-row">
    <div>
      <div class="card">
        <div class="form-group">
          <label for="title">Titre <span style="color:var(--color-danger)">*</span></label>
          <input type="text" id="title" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="slug">Slug</label>
          <div class="input-row">
            <input type="text" id="slug" name="slug"
              value="<?= htmlspecialchars($page['slug'] ?? '') ?>"
              <?= $isEdit ? 'readonly data-existing="1"' : '' ?>
              placeholder="genere-automatiquement">
            <?php if ($isEdit): ?>
            <button type="button" id="slug-unlock" class="btn btn-secondary btn-sm">Modifier</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="template">Template</label>
            <select id="template" name="template">
              <?php foreach (['home' => 'Accueil', 'page' => 'Page standard', 'service' => 'Page service', 'blog-list' => 'Liste blog', 'portfolio' => 'Portfolio'] as $v => $l): ?>
              <option value="<?= htmlspecialchars($v) ?>" <?= ($page['template'] ?? 'page') === $v ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="lang">Langue</label>
            <select id="lang" name="lang">
              <option value="fr" <?= ($page['lang'] ?? 'fr') === 'fr' ? 'selected' : '' ?>>Français</option>
              <option value="nl" <?= ($page['lang'] ?? 'fr') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="status">Statut</label>
          <select id="status" name="status">
            <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
            <option value="published" <?= ($page['status'] ?? 'draft') === 'published' ? 'selected' : '' ?>>Publié</option>
          </select>
        </div>
      </div>

      <!-- Éditeur de blocs -->
      <div class="card">
        <div class="card-title">Contenu</div>
        <div id="block-editor">
          <div class="block-editor-toolbar" id="block-editor-toolbar">
            <div style="position:relative">
              <button type="button" id="add-block-btn" class="btn btn-secondary btn-sm">+ Ajouter un bloc</button>
              <div id="block-add-menu" class="block-add-menu">
                <button type="button" data-type="heading">Titre</button>
                <button type="button" data-type="text">Texte</button>
                <button type="button" data-type="image">Image</button>
                <button type="button" data-type="cta">Bouton CTA</button>
                <button type="button" data-type="html">HTML libre</button>
                <button type="button" data-type="video">Vidéo</button>
                <button type="button" data-type="file">Fichier</button>
                <button type="button" data-type="accordion">FAQ</button>
                <button type="button" data-type="quote">Citation</button>
                <button type="button" data-type="hero">Hero</button>
                <button type="button" data-type="services">Grille services</button>
                <button type="button" data-type="two-col">Deux colonnes</button>
                <button type="button" data-type="how">Comment ça marche</button>
                <button type="button" data-type="reviews">Avis clients</button>
                <button type="button" data-type="zone">Zone d'intervention</button>
                <button type="button" data-type="cta-final">CTA final</button>
                <button type="button" data-type="pricing">Grille de tarifs</button>
                <button type="button" data-type="before-after">Avant / Apres</button>
                <button type="button" data-type="logos">Logos partenaires</button>
                <button type="button" data-type="seo-content">Contenu SEO</button>
                <button type="button" data-type="domicile-vs-atelier">Domicile vs Atelier</button>
                <button type="button" data-type="sofa-simulator">Simulateur canapé</button>
              </div>
            </div>
          </div>
          <div id="block-list"></div>
        </div>
        <!-- Hidden field serialized by JS -->
        <input type="hidden" name="content" value="<?= htmlspecialchars($page['content'] ?? '') ?>">
      </div>
    </div>

    <div>
      <!-- SEO -->
      <div class="card">
        <div class="card-title">SEO</div>

        <div class="form-group">
          <label for="meta_title">Meta title</label>
          <input type="text" id="meta_title" name="meta_title"
            value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>"
            data-maxlength="70"
            placeholder="Titre optimisé pour les moteurs de recherche">
        </div>

        <div class="form-group">
          <label for="meta_description">Meta description</label>
          <textarea id="meta_description" name="meta_description"
            data-maxlength="160"
            rows="3"
            placeholder="Description pour les résultats de recherche…"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="og_image">Image OG (URL)</label>
          <input type="text" id="og_image" name="og_image"
            value="<?= htmlspecialchars($page['og_image'] ?? '') ?>"
            placeholder="/uploads/image.jpg">
        </div>

        <div class="form-group">
          <label for="canonical_url">URL canonique</label>
          <input type="text" id="canonical_url" name="canonical_url"
            value="<?= htmlspecialchars($page['canonical_url'] ?? '') ?>"
            placeholder="https://…">
        </div>

        <div class="form-group">
          <label for="robots">Robots</label>
          <select id="robots" name="robots">
            <?php foreach (['index,follow' => 'index, follow', 'noindex,follow' => 'noindex, follow', 'noindex,nofollow' => 'noindex, nofollow'] as $v => $l): ?>
            <option value="<?= htmlspecialchars($v) ?>" <?= ($page['robots'] ?? 'index,follow') === $v ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" name="status" value="draft" class="btn btn-secondary">Enregistrer (brouillon)</button>
    <button type="submit" name="publish" value="1" class="btn">Publier</button>
    <?php if ($isEdit): ?>
    <a href="/admin/pages" class="btn btn-secondary">Annuler</a>
    <?php endif; ?>
  </div>
</form>
