<h2>Connexion</h2>
<?php if (!empty($error)): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="POST" action="/admin/login">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <p>
        <label>Email<br>
        <input type="email" name="email" required autofocus></label>
    </p>
    <p>
        <label>Mot de passe<br>
        <input type="password" name="password" required></label>
    </p>
    <button type="submit">Se connecter</button>
</form>
