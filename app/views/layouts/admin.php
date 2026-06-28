<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($title) ? htmlspecialchars($title) . ' — Admin Keepnew' : 'Admin Keepnew'; ?></title>
<link rel="stylesheet" href="/public/assets/admin.css">
</head>
<body class="admin-layout<?php if (!\App\Core\Auth::isLoggedIn()) echo ' admin-layout--login'; ?>">
<?php if (\App\Core\Auth::isLoggedIn()): ?>
<aside class="sidebar">
  <div class="sidebar-logo">Keepnew</div>
  <nav>
    <a href="/admin">Dashboard</a>
    <a href="/admin/pages">Pages</a>
    <a href="/admin/posts">Blog</a>
    <a href="/admin/projects">Portfolio</a>
    <a href="/admin/media">Médiathèque</a>
    <a href="/admin/menus">Menus</a>
    <a href="/admin/blocks">Blocs</a>
    <a href="/admin/redirections">Redirections</a>
    <a href="/admin/settings">Réglages</a>
    <a href="/admin/error-logs">Erreurs 404</a>
  </nav>
  <div class="sidebar-footer"><a href="/admin/logout">Déconnexion</a></div>
</aside>
<?php endif; ?>
<main class="admin-main">
<?php
$flash = \App\Core\Auth::getFlash();
if ($flash): ?>
<div class="flash-<?php echo htmlspecialchars($flash['type']); ?>"><?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>
<?php $this->content(); ?>
</main>
<script src="/public/assets/admin.js"></script>
<?php if (\App\Core\Auth::isLoggedIn()): ?>
<div id="media-modal" class="modal-overlay hidden">
  <div class="modal-box">
    <div class="modal-header">
      <span>Médiathèque</span>
      <button class="modal-close" type="button">&times;</button>
    </div>
    <div class="modal-body">
      <div id="media-modal-grid" class="media-grid"></div>
    </div>
  </div>
</div>
<?php endif; ?>
</body>
</html>
