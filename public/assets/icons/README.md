# Icônes Keepnew

Déposez ici les fichiers SVG, **un par icône**, nommés en minuscules sans accent
ni espace : `sofa.svg`, `bed.svg`, `car.svg`, `home.svg`, `calendar.svg`…

Le nom du fichier (sans `.svg`) est le nom utilisé dans l'admin et dans les
blocs. Exemple : `public/assets/icons/canape.svg` → icône `canape`.

## Ce que fait le site avec ces fichiers

`knIcon('canape')` lit le fichier et l'injecte en SVG inline dans la page.
Aucune requête réseau supplémentaire, et l'icône hérite de la couleur du texte
qui l'entoure.

## Recommandations pour les fichiers

- `viewBox="0 0 24 24"` de préférence (n'importe quel viewBox carré fonctionne)
- Traits plutôt que aplats, `stroke-width` autour de 1.5 — charte Keepnew
- Utilisez `stroke="currentColor"` et/ou `fill="currentColor"` pour que
  l'icône prenne la couleur du contexte (navy sur crème, crème sur navy…).
  Si vos fichiers ont des couleurs en dur, elles seront respectées telles
  quelles et ne s'adapteront pas aux fonds sombres.
- Pas besoin de nettoyer les attributs `width`/`height` : ils sont réécrits
  à l'affichage.

## Repli

Si aucun fichier ne correspond au nom demandé, le site utilise le jeu Lucide
intégré (voir `knIconPaths()` dans `app/views/blocks/_block_helpers.php`),
puis l'étincelle ✦ de la marque en dernier recours.
