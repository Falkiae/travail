<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Admin') ?> — Admin Keepnew</title>
  <link rel="stylesheet" href="/assets/admin.css">
</head>
<body class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar-logo">Keepnew</div>
    <nav>
      <a href="/admin">Dashboard</a>
      <a href="/admin/pages">Pages</a>
      <a href="/admin/posts">Blog</a>
      <a href="/admin/projects">Portfolio</a>
      <a href="/admin/media">Médiathèque</a>
      <a href="/admin/menus">Menus</a>
      <a href="/admin/redirections">Redirections</a>
      <a href="/admin/settings">Réglages</a>
      <a href="/admin/error-logs">Erreurs 404</a>
    </nav>
    <div class="sidebar-footer">
      <a href="/admin/logout">Déconnexion</a>
    </div>
  </aside>
  <main class="admin-main">
    <?php $flash = \App\Core\Auth::getFlash(); ?>
    <?php if ($flash): ?>
      <div class="flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <?php $this->content(); ?>
  </main>
  <script src="/assets/admin.js"></script>

  <!-- Media Modal (shared) -->
  <div id="media-modal" class="modal-overlay hidden">
    <div class="modal-box">
      <div class="modal-header">
        <span class="modal-title">Médiathèque</span>
        <button class="modal-close" type="button">&times;</button>
      </div>
      <div class="modal-body">
        <div id="media-modal-grid" class="media-grid"></div>
      </div>
    </div>
  </div>
</body>
</html>
