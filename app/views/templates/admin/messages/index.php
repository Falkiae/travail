<div class="page-header">
  <h1>Messages</h1>
</div>

<div class="card">
  <?php if (empty($messages)): ?>
    <p class="text-muted">Aucun message pour l'instant. Les messages envoyés depuis le bloc « Contact » apparaîtront ici.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Reçu le</th><th>De</th><th>Sujet et message</th><th>Statut</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $m): ?>
        <tr<?= $m['status'] === 'new' ? ' style="background:#fffaf0"' : '' ?>>
          <td style="white-space:nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($m['created_at']))) ?></td>
          <td>
            <strong><?= htmlspecialchars($m['name']) ?></strong><br>
            <a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a>
            <?php if (!empty($m['phone'])): ?><br><a href="tel:<?= htmlspecialchars(preg_replace('/[^+0-9]/', '', $m['phone'])) ?>"><?= htmlspecialchars($m['phone']) ?></a><?php endif; ?>
          </td>
          <td style="max-width:420px">
            <strong><?= htmlspecialchars((string) $m['subject']) ?></strong>
            <div style="white-space:pre-wrap;margin-top:.25rem"><?= htmlspecialchars($m['message']) ?></div>
            <?php if (!empty($m['page_url'])): ?><small class="text-muted">Depuis : <?= htmlspecialchars($m['page_url']) ?></small><?php endif; ?>
          </td>
          <td>
            <span class="badge badge-<?= $m['status'] === 'new' ? 'draft' : 'published' ?>"><?= $m['status'] === 'new' ? 'Nouveau' : 'Traité' ?></span>
            <?php if (!(int) $m['mail_sent']): ?><br><small style="color:#b3453e" title="La notification par e-mail n'a pas pu être envoyée : le message est conservé ici.">e-mail non envoyé</small><?php endif; ?>
          </td>
          <td>
            <div class="d-flex gap-sm">
              <form method="POST" action="/admin/messages/<?= (int) $m['id'] ?>/status">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="status" value="<?= $m['status'] === 'new' ? 'done' : 'new' ?>">
                <button type="submit" class="btn btn-secondary btn-sm"><?= $m['status'] === 'new' ? 'Marquer traité' : 'Rouvrir' ?></button>
              </form>
              <form method="POST" action="/admin/messages/<?= (int) $m['id'] ?>/delete" class="confirm-delete">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
