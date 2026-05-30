<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — Admin Keepnew' : 'Admin Keepnew' ?></title>
</head>
<body>
<header style="background:#111;color:#fff;padding:1rem 2rem">
    <strong>Admin Keepnew</strong>
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    — <a href="/admin/logout" style="color:#aaa">Déconnexion</a>
    <?php endif; ?>
</header>
<main style="padding:2rem">
<?php $this->content(); ?>
</main>
</body>
</html>
