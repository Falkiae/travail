<?php
$shapes             = isset($config['shapes'])               ? $config['shapes']               : [];
$pricePerExtraSeat  = isset($config['price_per_extra_seat']) ? $config['price_per_extra_seat'] : 20;
$pricePerMeridienne = isset($config['price_per_meridienne']) ? $config['price_per_meridienne'] : 35;
$ctaText            = isset($config['cta_text'])             ? $config['cta_text']             : 'Prendre RDV';
?>
<div class="page-header">
  <h1>Simulateur canapé</h1>
</div>

<form method="POST" action="/admin/sofa" id="sofa-form">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="form-row">
    <div>
      <!-- Global settings -->
      <div class="card">
        <div class="card-title">Tarifs globaux</div>

        <div class="form-row">
          <div class="form-group">
            <label for="price_per_extra_seat">Prix par siège supplémentaire (€)</label>
            <input type="number" id="price_per_extra_seat" name="price_per_extra_seat"
              value="<?= (int)$pricePerExtraSeat ?>" min="0" step="1">
            <small style="color:var(--color-muted)">Ajouté pour chaque place au-delà du nombre de base par forme.</small>
          </div>
          <div class="form-group">
            <label for="price_per_meridienne">Prix par méridienne (€)</label>
            <input type="number" id="price_per_meridienne" name="price_per_meridienne"
              value="<?= (int)$pricePerMeridienne ?>" min="0" step="1">
          </div>
        </div>

        <div class="form-group">
          <label for="cta_text">Texte du bouton de réservation</label>
          <input type="text" id="cta_text" name="cta_text"
            value="<?= htmlspecialchars($ctaText) ?>" placeholder="Prendre RDV">
        </div>
      </div>

      <!-- Shapes -->
      <div class="card">
        <div class="card-title">Formes & tarifs de base</div>
        <p style="color:var(--color-muted);margin-bottom:1rem;font-size:.875rem">Chaque forme a son propre tarif de base (nombre de places incluses) et son URL de réservation.</p>

        <div id="sofa-shapes-list">
          <?php foreach ($shapes as $i => $shape): ?>
          <div class="sofa-shape-row card" style="margin-bottom:.75rem;padding:1rem">
            <div class="form-row" style="align-items:flex-end;gap:.75rem">
              <div class="form-group" style="flex:0 0 120px;margin:0">
                <label>Clé (identifiant)</label>
                <input type="text" name="shape_key[]" value="<?= htmlspecialchars($shape['key']) ?>" placeholder="droit" required>
              </div>
              <div class="form-group" style="flex:1;margin:0">
                <label>Libellé affiché</label>
                <input type="text" name="shape_label[]" value="<?= htmlspecialchars($shape['label']) ?>" placeholder="Droit" required>
              </div>
              <div class="form-group" style="flex:0 0 100px;margin:0">
                <label>Prix de base (€)</label>
                <input type="number" name="shape_base_price[]" value="<?= (int)$shape['base_price'] ?>" min="0" step="1" required>
              </div>
              <div class="form-group" style="flex:0 0 120px;margin:0">
                <label>Places incluses</label>
                <input type="number" name="shape_base_seats[]" value="<?= (int)$shape['base_seats'] ?>" min="1" step="1" required>
              </div>
              <div class="form-group" style="flex:2;margin:0">
                <label>URL de réservation</label>
                <input type="text" name="shape_cta_url[]" value="<?= htmlspecialchars($shape['cta_url']) ?>" placeholder="/rdv">
              </div>
              <button type="button" class="btn btn-danger btn-sm remove-shape-btn" title="Supprimer">×</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <button type="button" id="add-shape-btn" class="btn btn-secondary btn-sm" style="margin-top:.5rem">+ Ajouter une forme</button>
      </div>
    </div>

    <div>
      <div class="card">
        <div class="card-title">Comment ça marche</div>
        <ul style="color:var(--color-muted);font-size:.875rem;line-height:1.8;padding-left:1.25rem">
          <li>Chaque <strong>forme</strong> a un prix de base qui inclut un certain nombre de places.</li>
          <li>Si l'utilisateur sélectionne plus de places que le nombre inclus, le surplus est multiplié par <strong>prix/siège supplémentaire</strong>.</li>
          <li>Chaque méridienne ajoute le <strong>prix/méridienne</strong>.</li>
          <li>La clé identifiant (ex: <code>droit</code>) doit correspondre à l'icône SVG dans le bloc. Les clés prédéfinies sont : <code>droit</code>, <code>angle</code>, <code>u</code>, <code>fauteuil</code>.</li>
          <li>L'URL de réservation peut être une page du site ou un lien externe.</li>
        </ul>
        <hr style="margin:1rem 0;border:none;border-top:1px solid var(--color-border)">
        <p style="font-size:.8rem;color:var(--color-muted)">
          Exemple : Angle / L, prix de base 129 €, 3 places incluses.<br>
          Sélection : 5 places, 1 méridienne → 129 + (5-3)×<?= (int)$pricePerExtraSeat ?> + 1×<?= (int)$pricePerMeridienne ?> = <strong><?= 129 + 2 * (int)$pricePerExtraSeat + (int)$pricePerMeridienne ?> €</strong>
        </p>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Enregistrer</button>
  </div>
</form>

<script>
(function() {
  var shapeRowTemplate =
    '<div class="sofa-shape-row card" style="margin-bottom:.75rem;padding:1rem">' +
    '<div class="form-row" style="align-items:flex-end;gap:.75rem">' +
    '<div class="form-group" style="flex:0 0 120px;margin:0"><label>Clé</label><input type="text" name="shape_key[]" placeholder="custom" required></div>' +
    '<div class="form-group" style="flex:1;margin:0"><label>Libellé</label><input type="text" name="shape_label[]" placeholder="Ma forme" required></div>' +
    '<div class="form-group" style="flex:0 0 100px;margin:0"><label>Prix (€)</label><input type="number" name="shape_base_price[]" value="99" min="0" step="1" required></div>' +
    '<div class="form-group" style="flex:0 0 120px;margin:0"><label>Places incluses</label><input type="number" name="shape_base_seats[]" value="2" min="1" step="1" required></div>' +
    '<div class="form-group" style="flex:2;margin:0"><label>URL réservation</label><input type="text" name="shape_cta_url[]" placeholder="/rdv"></div>' +
    '<button type="button" class="btn btn-danger btn-sm remove-shape-btn" title="Supprimer">×</button>' +
    '</div></div>';

  document.getElementById('add-shape-btn').addEventListener('click', function() {
    var div = document.createElement('div');
    div.innerHTML = shapeRowTemplate;
    var row = div.firstChild;
    row.querySelector('.remove-shape-btn').addEventListener('click', function() { row.remove(); });
    document.getElementById('sofa-shapes-list').appendChild(row);
  });

  document.querySelectorAll('.remove-shape-btn').forEach(function(btn) {
    btn.addEventListener('click', function() { btn.closest('.sofa-shape-row').remove(); });
  });
})();
</script>
