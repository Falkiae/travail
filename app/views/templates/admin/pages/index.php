<h2>Pages</h2>
<a href="/admin/pages/new">+ Nouvelle page</a>
<table>
<thead><tr><th>Titre</th><th>Slug</th><th>Statut</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($pages as $page): ?>
<tr>
    <td><?= htmlspecialchars($page['title']) ?></td>
    <td><?= htmlspecialchars($page['slug']) ?></td>
    <td><?= htmlspecialchars($page['status']) ?></td>
    <td><a href="/admin/pages/<?= $page['id'] ?>/edit">Modifier</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
