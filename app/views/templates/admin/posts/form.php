<?php
$isEdit = !empty($post);
$action = $isEdit ? '/admin/posts/' . (int)$post['id'] . '/edit' : '/admin/posts/new';
$existingBlocks = $isEdit && !empty($post['content']) ? json_decode($post['content'], true) : [];
?>

<?php if ($isEdit): ?>
<script>window.existingBlocks = <?= json_encode($existingBlocks) ?>;</script>
<?php endif; ?>

<div class="page-header">
  <h1><?= htmlspecialchars($title) ?></h1>
  <a href="/admin/posts" class="btn btn-secondary">← Retour</a>
</div>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="form-row">
    <div>
      <div class="card">
        <div class="form-group">
          <label for="title">Titre <span style="color:var(--color-danger)">*</span></label>
          <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="slug">Slug</label>
          <div class="input-row">
            <input type="text" id="slug" name="slug"
              value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
              <?= $isEdit ? 'readonly data-existing="1"' : '' ?>>
            <?php if ($isEdit): ?>
            <button type="button" id="slug-unlock" class="btn btn-secondary btn-sm">Modifier</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label for="excerpt">Extrait</label>
          <textarea id="excerpt" name="excerpt" rows="3" placeholder="Résumé court de l'article…"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="lang">Langue</label>
            <select id="lang" name="lang">
              <option value="fr" <?= ($post['lang'] ?? 'fr') === 'fr' ? 'selected' : '' ?>>Français</option>
              <option value="nl" <?= ($post['lang'] ?? 'fr') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
            </select>
          </div>
          <div class="form-group">
            <label for="category_id">Catégorie</label>
            <select id="category_id" name="category_id">
              <option value="">— Aucune —</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= (int)($post['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status">
              <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
              <option value="published" <?= ($post['status'] ?? 'draft') === 'published' ? 'selected' : '' ?>>Publié</option>
            </select>
          </div>
          <div class="form-group">
            <label for="published_at">Date de publication</label>
            <input type="datetime-local" id="published_at" name="published_at"
              value="<?= $post['published_at'] ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($post['published_at']))) : '' ?>">
          </div>
        </div>

        <div class="form-group">
          <label>Image à la une</label>
          <div class="input-row">
            <input type="text" id="featured_image_display" value="" readonly placeholder="Aucune image choisie">
            <input type="hidden" id="featured_image" name="featured_image" value="<?= (int)($post['featured_image'] ?? 0) ?>">
            <button type="button" class="btn btn-secondary btn-sm" onclick="openMediaModal(function(m){ document.getElementById('featured_image').value=m.id; document.getElementById('featured_image_display').value=m.original_name; })">Médiathèque</button>
          </div>
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
                <button type="button" data-type="accordion">Accordéon</button>
                <button type="button" data-type="quote">Citation</button>
                <button type="button" data-type="pricing">Grille de tarifs</button>
                <button type="button" data-type="before-after">Avant / Apres</button>
                <button type="button" data-type="logos">Logos partenaires</button>
                <button type="button" data-type="seo-content">Contenu SEO</button>
              </div>
            </div>
          </div>
          <div id="block-list"></div>
        </div>
        <input type="hidden" name="content" value="<?= htmlspecialchars($post['content'] ?? '') ?>">
      </div>
    </div>

    <div>
      <div class="card">
        <div class="card-title">SEO</div>

        <div class="form-group">
          <label for="meta_title">Meta title</label>
          <input type="text" id="meta_title" name="meta_title"
            value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>"
            data-maxlength="70">
        </div>

        <div class="form-group">
          <label for="meta_description">Meta description</label>
          <textarea id="meta_description" name="meta_description"
            data-maxlength="160" rows="3"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="og_image">Image OG (URL)</label>
          <input type="text" id="og_image" name="og_image"
            value="<?= htmlspecialchars($post['og_image'] ?? '') ?>">
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" name="status" value="draft" class="btn btn-secondary">Enregistrer (brouillon)</button>
    <button type="submit" name="publish" value="1" class="btn">Publier</button>
  </div>
</form>
