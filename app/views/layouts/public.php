<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — Keepnew' : 'Keepnew' ?></title>
</head>
<body>
<?php $this->content(); ?>
</body>
</html>
