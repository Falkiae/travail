<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
require_once CONFIG_PATH . '/config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$homeContent = '[{"type":"hero","eyebrow":"NETTOYAGE À DOMICILE · LIÈGE · NAMUR · BRUXELLES","h1":"Nettoyage de canapés, matelas & voitures","h1_sub":"à domicile ou en atelier.","social_proof":"4,9\/5 · +110 avis · +400 canapés · +250 voitures"},{"type":"services","eyebrow":"NOS SERVICES","h2":"Nettoyage de canapés, matelas & voitures à domicile","items":[{"name":"Canapé","desc":"Aspiration profonde, vapeur, anti-odeurs. Résultat garanti.","icon":"🛋️"},{"name":"Matelas","desc":"Nettoyage en profondeur, anti-acariens et désinfection.","icon":"🛏️"},{"name":"Voiture","desc":"Intérieur complet, sièges, moquettes et plastiques.","icon":"🚗"},{"name":"Polissage & Céramique","desc":"Protection longue durée pour votre carrosserie.","icon":"✨"},{"name":"Terrasse","desc":"Haute pression et traitement anti-mousse.","icon":"🏠"},{"name":"Atelier Visé","desc":"Traitements approfondis dans notre atelier à Visé.","icon":"🔧"}]},{"type":"two-col","eyebrow":"DEUX FAÇONS DE TRAVAILLER","h2":"On vient chez vous — ou vous venez chez nous.","left_title":"À domicile","left_body":"Nous intervenons directement chez vous dans toute la province de Liège, Namur, Luxembourg et à Bruxelles. Pas besoin de vous déplacer — on s\'occupe de tout sur place.","right_title":"Atelier à Visé","right_body":"Pour les traitements les plus exigeants (polissage, céramique, camping-cars), notre atelier de Visé dispose de l\'équipement professionnel nécessaire. Déposez votre véhicule et récupérez-le impeccable."},{"type":"how","eyebrow":"RÉSERVATION EN LIGNE","h2":"Réservez votre nettoyage à domicile en 2 minutes","steps":[{"title":"Choisissez votre service en ligne","body":"Sélectionnez le service souhaité et réservez directement en ligne — devis inclus, sans surprise."},{"title":"On passe chez vous","body":"Notre équipe arrive à l\'heure convenue avec tout le matériel. Vous n\'avez rien à préparer."},{"title":"Résultat garanti","body":"Satisfait ou on revient. Chaque intervention est garantie et documentée avec photos avant\/après."}]},{"type":"reviews","eyebrow":"ILS NOUS FONT CONFIANCE","h2":"4,9\/5 · +110 avis · +400 canapés nettoyés"},{"type":"zone","eyebrow":"OÙ ON INTERVIENT","h2":"Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg","body":"Keepnew intervient dans toute la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Waremme), en province de Namur, dans la province du Luxembourg et à Bruxelles. Vous n\'êtes pas sûr que nous intervenons chez vous ? Contactez-nous — nous faisons notre possible pour vous répondre.","pills":[{"label":"Liège"},{"label":"Namur"},{"label":"Bruxelles"},{"label":"Luxembourg"},{"label":"Visé"},{"label":"Seraing"},{"label":"Herstal"}]},{"type":"accordion","items":[{"question":"Combien coûte un nettoyage de canapé à domicile ?","answer":"Le tarif dépend de la taille et du type de canapé. Nous proposons un devis gratuit en 2 minutes en ligne — sans engagement."},{"question":"Quels produits utilisez-vous ?","answer":"Nous utilisons des produits professionnels écologiques, sans solvants agressifs, adaptés à chaque matière (tissu, cuir, microfibre)."},{"question":"Vous intervenez vraiment à domicile ?","answer":"Oui, nous nous déplaçons directement chez vous avec tout notre matériel. Aucun déplacement nécessaire de votre côté."},{"question":"Combien de temps dure une intervention ?","answer":"Entre 1h et 3h selon le service et la superficie. Nous vous communiquons une estimation précise lors de la réservation."},{"question":"Proposez-vous un devis gratuit ?","answer":"Absolument. Le devis est gratuit, en ligne, et sans engagement. Vous connaissez le prix avant de confirmer."},{"question":"Quelle zone géographique couvrez-vous ?","answer":"Nous couvrons la province de Liège, Namur, le Luxembourg belge et Bruxelles. Contactez-nous pour vérifier votre commune."}]},{"type":"cta-final","eyebrow":"📅 RÉSERVATION EN LIGNE","title":"Prendre RDV en 2 min"}]';

$pages = [
    [
        'slug'             => 'home',
        'lang'             => 'fr',
        'title'            => 'Accueil',
        'template'         => 'home',
        'status'           => 'published',
        'meta_title'       => 'Nettoyage de canapés, matelas & voitures à domicile | Keepnew',
        'meta_description' => 'Keepnew — Service de nettoyage professionnel à domicile en Belgique. Canapés, matelas, voitures, terrasses. Zone Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
        'content'          => $homeContent,
        'sort_order'       => 0,
    ],
    [
        'slug'             => 'blog',
        'lang'             => 'fr',
        'title'            => 'Blog',
        'template'         => 'blog-list',
        'status'           => 'published',
        'meta_title'       => 'Blog nettoyage à domicile — Conseils & astuces | Keepnew',
        'meta_description' => 'Conseils, astuces et actualités sur le nettoyage à domicile par Keepnew. Canapés, matelas, voitures et terrasses.',
        'content'          => '[]',
        'sort_order'       => 2,
    ],
    [
        'slug'             => 'portfolio',
        'lang'             => 'fr',
        'title'            => 'Réalisations',
        'template'         => 'portfolio',
        'status'           => 'published',
        'meta_title'       => 'Nos réalisations — Avant / Après | Keepnew',
        'meta_description' => 'Découvrez les réalisations de Keepnew : canapés, matelas et voitures nettoyés à domicile. Résultats garantis.',
        'content'          => '[]',
        'sort_order'       => 3,
    ],
];

// Update home page content if it already exists
$updateHome = $pdo->prepare(
    'UPDATE kn_pages SET content = :content WHERE slug = :slug AND lang = :lang'
);
$updateHome->execute(array('content' => $homeContent, 'slug' => 'home', 'lang' => 'fr'));

$stmt = $pdo->prepare(
    'INSERT IGNORE INTO kn_pages (slug, lang, title, template, status, meta_title, meta_description, content, sort_order)
     VALUES (:slug, :lang, :title, :template, :status, :meta_title, :meta_description, :content, :sort_order)'
);

foreach ($pages as $page) {
    $stmt->execute($page);
    echo "Page créée/mise à jour : " . $page['title'] . " (" . $page['slug'] . ")\n";
}

echo "\nInitialisation terminée.\n";
