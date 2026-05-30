<?php
// Stats are passed from controller or computed here
$pagesCount    = $stats['pages'] ?? 0;
$postsCount    = $stats['posts'] ?? 0;
$projectsCount = $stats['projects'] ?? 0;
$errors404     = $stats['errors404'] ?? 0;
?>

<div class="page-header">
  <h1>Dashboard</h1>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-value"><?= (int)$pagesCount ?></div>
    <div class="stat-label">Pages publiées</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int)$postsCount ?></div>
    <div class="stat-label">Articles publiés</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int)$projectsCount ?></div>
    <div class="stat-label">Projets publiés</div>
  </div>
  <div class="stat-card">
    <div class="stat-value" style="color:var(--color-danger)"><?= (int)$errors404 ?></div>
    <div class="stat-label">Erreurs 404</div>
  </div>
</div>

<div class="card">
  <div class="card-title">Accès rapides</div>
  <div class="d-flex gap-sm" style="flex-wrap:wrap">
    <a href="/admin/pages/new" class="btn btn-secondary">+ Nouvelle page</a>
    <a href="/admin/posts/new" class="btn btn-secondary">+ Nouvel article</a>
    <a href="/admin/projects/new" class="btn btn-secondary">+ Nouveau projet</a>
    <a href="/admin/media" class="btn btn-secondary">Médiathèque</a>
    <a href="/admin/settings" class="btn btn-secondary">Réglages</a>
    <a href="/admin/error-logs" class="btn btn-secondary">Erreurs 404</a>
  </div>
</div>
