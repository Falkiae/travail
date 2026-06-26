<?php
// Token de protection — supprimer ce fichier après usage
if (!isset($_GET['token']) || $_GET['token'] !== 'kn2024seed') {
    http_response_code(403); exit('Forbidden');
}

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
require_once CONFIG_PATH . '/config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$blocks = [];

$blocks[] = ['type'=>'hero','bg'=>'cream','layout'=>'1-1','visual_type'=>'photo',
  'eyebrow'=>'NETTOYAGE AUTO · À DOMICILE · PROVINCE DE LIÈGE',
  'h1'=>'Nettoyage professionnel de voitures à domicile',
  'body'=>'Nos prestations sont aussi disponibles dans notre <strong>atelier de Visé (Liège)</strong>. La réservation est bouclée en 2 min et vous ne payez qu\'après la prestation !',
  'cta_eyebrow'=>'RÉSERVATION EN LIGNE','cta_title'=>'Prendre RDV en 2 min',
  'social_proof'=>'⭐ 4,9/5 · +250 voitures remises à neuf','image_url'=>'',
  'pills'=>[['text'=>'✓ À domicile','style'=>'white'],['text'=>'★ Garantie','style'=>'yellow'],['text'=>'⚡ Devis en 2 min','style'=>'night']],
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'two-col','bg'=>'white','layout'=>'1-1',
  'eyebrow'=>'NOS FORMULES','h2'=>'À domicile ou en atelier — vous choisissez',
  'left_title'=>'À domicile / sur le lieu de travail — Le plus pratique',
  'left_body'=>"Pratique : on vient à vous, sans déplacement.\nVous continuez à travailler ou à vaquer à vos occupations.\nService rapide et professionnel sur place.",
  'right_title'=>'En atelier Keepnew (Visé) — Le plus économique',
  'right_body'=>"Pas besoin d'espace chez vous ou au boulot.\nEspace d'attente dédié pour travailler ou se détendre.\nTarif plus avantageux qu'à domicile.",
  'visible'=>['desktop','tablet','mobile']];

$ph = '<div class="kn-pricing__grid" style="margin-top:0">';
$ph .= '<div class="kn-pricing-card"><div class="kn-pricing-card__body"><p style="font-size:.7rem;font-weight:700;letter-spacing:.08em;color:var(--kn-blue);margin-bottom:.5rem">NOUVEAU</p><p class="kn-pricing-card__label">Shampoing des sièges</p><p class="kn-pricing-card__price">À partir de 80€</p><p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem">La formule parfaite pour nettoyer en profondeur vos sièges et faire disparaître les taches.</p><ul style="font-size:.85rem;padding-left:1.2rem;color:#374151;margin-bottom:1.25rem"><li>Aspiration des sièges</li><li>Shampoing des sièges</li><li>Parfum longue durée</li><li style="color:#9ca3af">Shampoing des carpettes (supplément)</li></ul><a href="/reserver" class="kn-btn">Prenez RDV</a></div></div>';
$ph .= '<div class="kn-pricing-card" style="border:2px solid var(--kn-blue)"><div class="kn-pricing-card__body"><p style="font-size:.7rem;font-weight:700;letter-spacing:.08em;color:var(--kn-blue);margin-bottom:.5rem">LE PLUS DEMANDÉ</p><p class="kn-pricing-card__label">Intérieur &amp; Extérieur</p><p class="kn-pricing-card__price">À partir de 95€</p><p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem">Nettoyage intérieur et extérieur de votre voiture pour préserver votre véhicule dans les meilleures conditions.</p><ul style="font-size:.85rem;padding-left:1.2rem;color:#374151;margin-bottom:1.25rem"><li>Dépoussiérage &amp; aspiration complète de l\'habitacle</li><li>Traitement léger plastiques intérieurs</li><li>Nettoyage intérieur vitres sans traces</li><li>Prélavage mousse active et rinçage</li><li>Nettoyage jantes et passages de roues</li><li>Lavage manuel délicat</li><li>Séchage complet + seuils de portes</li><li>Application brillant pneus</li><li>Parfum d\'ambiance</li></ul><a href="/reserver" class="kn-btn">Prenez RDV</a></div></div>';
$ph .= '<div class="kn-pricing-card"><div class="kn-pricing-card__body"><p style="font-size:.7rem;font-weight:700;letter-spacing:.08em;color:#6b7280;margin-bottom:.5rem">INTÉRIEUR</p><p class="kn-pricing-card__label">Remise à neuf Intérieur</p><p class="kn-pricing-card__price">À partir de 180€</p><p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem">Cette formule permettra de retrouver un intérieur de véhicule comme neuf !</p><ul style="font-size:.85rem;padding-left:1.2rem;color:#374151;margin-bottom:1.25rem"><li>Aspiration du véhicule</li><li>Nettoyage de l\'habitacle</li><li>Shampoing sièges</li><li>Shampoing des carpettes</li><li>Nettoyage des cuirs</li><li>Nettoyage des vitres + pare-brise</li><li>Parfum longue durée</li></ul><a href="/reserver" class="kn-btn">Prenez RDV</a></div></div>';
$ph .= '<div class="kn-pricing-card"><div class="kn-pricing-card__body"><p style="font-size:.7rem;font-weight:700;letter-spacing:.08em;color:#6b7280;margin-bottom:.5rem">COMPLET</p><p class="kn-pricing-card__label">Remise à neuf complète</p><p class="kn-pricing-card__price">À partir de 240€</p><p style="font-size:.85rem;color:var(--kn-blue);font-weight:600;margin-bottom:.5rem">Idéal pour les fins de leasing</p><ul style="font-size:.85rem;padding-left:1.2rem;color:#374151;margin-bottom:1.25rem"><li>Formule intérieur &amp; extérieur</li><li>Nettoyage sièges</li><li>Shampoing tapis</li><li>Cire de protection</li><li>Dégraissage du volant</li><li>Traitement des plastiques extérieur</li><li>Suppression des odeurs*</li></ul><a href="/reserver" class="kn-btn">Prenez RDV</a></div></div>';
$ph .= '</div>';

$blocks[] = ['type'=>'html','bg'=>'alt',
  'html'=>'<section style="padding:0"><div class="container"><span class="kn-eyebrow" style="color:var(--kn-blue)">NOS TARIFS</span><h2 class="kn-section__title">Découvrez le tarif de nettoyage adapté à votre voiture</h2><p style="margin-bottom:2.5rem;color:#6b7280">Nos tarifs s\'adaptent à votre type de véhicule (citadine, berline, SUV/4x4, utilitaire) et à votre choix de prestation. Profitez d\'un tarif plus avantageux en optant pour un nettoyage en atelier.</p>'.$ph.'</div></section>',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'accordion','bg'=>'white',
  'eyebrow'=>'FAQ','h2'=>'Questions fréquentes',
  'intro'=>'Tout ce que vous devez savoir avant votre nettoyage',
  'items'=>[
    ['question'=>'Combien de temps dure un nettoyage de voiture à domicile ?','answer'=>'La durée varie selon la formule choisie : comptez environ 1h pour un shampoing des sièges, 1h30 pour un nettoyage intérieur-extérieur, et jusqu\'à 4h pour une remise à neuf complète. Le temps peut varier légèrement en fonction de la taille et de l\'état du véhicule.'],
    ['question'=>'Faut-il fournir l\'eau et l\'électricité pour le nettoyage à domicile ?','answer'=>'Oui, nous avons besoin d\'un accès à l\'eau (robinet extérieur ou tuyau d\'arrosage) et à une prise électrique à proximité du véhicule. Nous apportons tout le reste : matériel professionnel, produits et consommables.'],
    ['question'=>'Faut-il être présent pendant le nettoyage de ma voiture ?','answer'=>'Nous vous demandons d\'être présent au début de l\'intervention pour nous remettre les clés et indiquer les accès eau et électricité. Vous pouvez ensuite vaquer à vos occupations. Nous vous sollicitons à la fin pour vérifier ensemble le résultat avant le paiement.'],
    ['question'=>'Est-ce que toutes les taches sur les sièges disparaissent ?','answer'=>'Notre shampoing par injection-extraction élimine la grande majorité des taches courantes (nourriture, boissons, transpiration, animaux). Certaines taches très anciennes ou d\'origine chimique peuvent être atténuées sans disparaître totalement. Nous vous donnons un avis honnête dès le diagnostic sur place.'],
    ['question'=>'Combien de temps faut-il pour que les sièges sèchent après un shampoing ?','answer'=>'Le temps de séchage varie entre 4 et 8 heures selon le type de tissu, l\'humidité ambiante et la ventilation. Nous vous conseillons de laisser les fenêtres légèrement entrouvertes si possible pour accélérer le séchage.'],
    ['question'=>'Les produits utilisés sont-ils dangereux pour les enfants ou les animaux ?','answer'=>'Non, nous utilisons des produits professionnels écoresponsables et hypoallergéniques. Ils sont sans danger pour les enfants, les animaux de compagnie et ne dégagent aucune odeur nocive. Votre véhicule est utilisable dès la fin de la prestation.'],
    ['question'=>'Quelle est la différence entre un nettoyage à domicile et en atelier ?','answer'=>'La qualité du nettoyage est identique. L\'avantage du domicile est le confort : nous venons chez vous. L\'avantage de l\'atelier à Visé est le tarif plus avantageux et l\'accès aux services complémentaires comme le polissage et le traitement céramique.'],
    ['question'=>'À quelle fréquence faut-il faire nettoyer sa voiture par un professionnel ?','answer'=>'Nous recommandons un nettoyage professionnel tous les 2 à 3 mois pour un entretien régulier. Pour les fins de leasing, prévoyez une remise à neuf complète 2 à 4 semaines avant la restitution.'],
  ],
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'before-after','bg'=>'white','eyebrow'=>'AVANT / APRÈS',
  'h2'=>'Transformez votre voiture avec notre nettoyage professionnel',
  'items'=>[],'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'logos','bg'=>'alt','h2'=>'Ils nous font confiance',
  'items'=>[
    ['alt'=>'Auto Lana','url'=>'','image_url'=>''],
    ['alt'=>'Delbecq BMW','url'=>'','image_url'=>''],
    ['alt'=>'Simplicicar Liège','url'=>'','image_url'=>''],
    ['alt'=>'Bipartner','url'=>'','image_url'=>''],
    ['alt'=>'EDF Renewables Belgium','url'=>'','image_url'=>''],
    ['alt'=>'Accardo','url'=>'','image_url'=>''],
    ['alt'=>'Stoler Immo','url'=>'','image_url'=>''],
    ['alt'=>'MBS Solution','url'=>'','image_url'=>''],
    ['alt'=>'Meuse Condroz Logement','url'=>'','image_url'=>''],
  ],'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'text','bg'=>'white',
  'html'=>'<p>Nos services de nettoyage de voitures à domicile sont disponibles dans toute la <strong>Province de Liège</strong> (Amay, Anthisnes, Ans, Awans, Aywaille, Bassenge, Berloz, Beyne-Heusay, Blegny, Braives, Burdinne, Chaudfontaine, Clavier, Comblain-au-Pont, Crisnée, Dalhem, Donceel, Engis, Esneux, Faimes, Ferrières, Fexhe-le-Haut-Clocher, Flémalle, Fléron, Geer, Grâce-Hollogne, Hannut, Hamoir, Herstal, Héron, Huy, Juprelle, Liège, Lincent, Marchin, Modave, Nandrin, Neupré, Oreye, Ouffet, Oupeye, Remicourt, Saint-Georges-sur-Meuse, Saint-Nicolas, Seraing, Soumagne, Sprimont, Tinlot, Trooz, Verlaine, Villers-le-Bouillet, Visé, Wanze, Waremme, Wasseiges), de la <strong>Province de Namur</strong> et de la <strong>Province du Luxembourg</strong>.</p>',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'heading','bg'=>'blue','level'=>'h2',
  'text'=>'Voitures de société ?',
  'body'=>'Keepnew peut prendre en charge le nettoyage de vos véhicules de fonction et utilitaires professionnels. Profitez d\'un service 100% déductible pour votre société ou faites le bonheur de vos employés en leur octroyant le nettoyage de leurs véhicules sur leur lieu de travail !',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'text','bg'=>'cream',
  'html'=>'<p style="text-align:center"><strong>🏆 Keepnew finaliste du Trophée de l\'Indépendant 2024 par Orange !</strong></p><p>Nous sommes fiers d\'annoncer que Keepnew a été sélectionné parmi les 10 finalistes du Trophée de l\'Indépendant, une distinction qui célèbre les entrepreneurs les plus inspirants et innovants de l\'année. Plus de 850 entreprises ont participé à ce concours national.</p><p>Un grand merci à tous nos clients, partenaires et soutiens qui rendent cette aventure possible chaque jour ! 💙</p>',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'heading','bg'=>'white','level'=>'h2',
  'text'=>'Un Camping-car ou une caravane ?',
  'body'=>'Que vous partiez pour un week-end ou un long périple, votre camping-car ou caravane mérite le meilleur entretien. Profitez de notre service de nettoyage professionnel pour voyager dans un véhicule propre, confortable et sain.',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'heading','bg'=>'alt','level'=>'h2',
  'text'=>'Votre voiture de leasing arrive en fin de contrat ?',
  'body'=>'Que vous soyez un professionnel ou un particulier et que vous voulez éviter des frais inutiles en fin de contrat de leasing, contactez-nous pour remettre votre véhicule dans un état neuf.',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'heading','bg'=>'white','level'=>'h2',
  'text'=>'Demandez votre abonnement carwash !',
  'body'=>'Optez pour notre service de nettoyage sous abonnement et assurez-vous d\'une voiture impeccable à tout moment. Profitez de la tranquillité d\'esprit en choisissant notre formule d\'abonnement.',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'reviews','bg'=>'white',
  'eyebrow'=>'ILS NOUS FONT CONFIANCE','h2'=>'4,9/5 · +250 voitures nettoyées',
  'visible'=>['desktop','tablet','mobile']];

$blocks[] = ['type'=>'cta-final','bg'=>'blue',
  'eyebrow'=>'📅 RÉSERVATION EN LIGNE',
  'title'=>'Fixons un RDV ensemble dès aujourd\'hui !',
  'visible'=>['desktop','tablet','mobile']];

$content = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$sql = 'INSERT INTO kn_pages (slug, lang, title, template, status, content, meta_title, meta_description, sort_order)
        VALUES (:slug,:lang,:title,:template,:status,:content,:meta_title,:meta_description,:sort_order)
        ON DUPLICATE KEY UPDATE
          title=VALUES(title), template=VALUES(template), content=VALUES(content),
          meta_title=VALUES(meta_title), meta_description=VALUES(meta_description)';

$pdo->prepare($sql)->execute([
  ':slug'             => 'nettoyage-voiture-domicile',
  ':lang'             => 'fr',
  ':title'            => 'Nettoyage voiture à domicile',
  ':template'         => 'page',
  ':status'           => 'draft',
  ':content'          => $content,
  ':meta_title'       => 'Nettoyage professionnel de voitures à domicile | Keepnew',
  ':meta_description' => 'Keepnew nettoie votre voiture à domicile ou en atelier à Visé. Shampoing sièges, remise à neuf intérieur/extérieur. Devis gratuit, paiement après prestation.',
  ':sort_order'       => 10,
]);

echo '<p style="font-family:monospace;padding:2rem;font-size:1.1rem">✅ OK — Page <strong>nettoyage-voiture-domicile</strong> insérée (statut: draft).<br><br>Étapes suivantes :<br>1. Admin > Pages > "Nettoyage voiture à domicile"<br>2. Ajouter image hero, photos avant/après, logos<br>3. Publier<br>4. Supprimer ce fichier (public/seed-voiture.php)</p>';
