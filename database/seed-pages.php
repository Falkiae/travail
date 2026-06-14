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

$canapeBlocks = [
    [
        'type'         => 'hero',
        'bg'           => 'cream',
        'eyebrow'      => 'NETTOYAGE CANAPE · LIEGE · NAMUR · BRUXELLES',
        'h1'           => 'Nettoyage de <span class="kn-tape">canapés</span> à domicile',
        'body'         => '<p>Tissu, cuir, microfibre ou velours — on s\'occupe de votre canapé avec des produits écologiques et hypoallergéniques, directement chez vous.</p>',
        'social_proof' => '4,9/5 · +90 avis · +400 canapés nettoyés',
        'pills'        => [
            ['text' => '✓ À domicile', 'style' => 'white'],
            ['text' => '★ Résultat garanti', 'style' => 'yellow'],
            ['text' => '⚡ RDV en 2 min', 'style' => 'night'],
        ],
        'cta_eyebrow'  => 'RESERVATION EN LIGNE',
        'cta_title'    => 'Prendre RDV en 2 min',
        'layout'       => '1-1',
    ],
    [
        'type'   => 'heading',
        'level'  => 'h2',
        'text'   => 'Tissus, cuirs, velours ? Pas de souci, on s\'occupe de votre canapé !',
        'style'  => 'highlight',
        'bg'     => 'white',
    ],
    [
        'type'    => 'pricing',
        'bg'      => 'white',
        'eyebrow' => 'NOS TARIFS',
        'h2'      => 'Prix clairs et sans surprise',
        'items'   => [
            ['label' => 'Canapé 1 à 3 places', 'price' => '99 €', 'cta_url' => '#', 'cta_text' => 'Réserver'],
            ['label' => 'Canapé 4 à 5 places', 'price' => '140 €', 'cta_url' => '#', 'cta_text' => 'Réserver'],
            ['label' => 'Canapé 6 à 8 places', 'price' => '170 €', 'cta_url' => '#', 'cta_text' => 'Réserver'],
            ['label' => 'Canapé 8 places et +', 'price' => '200 €', 'cta_url' => '#', 'cta_text' => 'Réserver'],
        ],
    ],
    [
        'type'    => 'logos',
        'bg'      => 'alt',
        'eyebrow' => 'ILS NOUS FONT CONFIANCE',
        'items'   => [
            ['alt' => 'Auto Lana',                'image_url' => '', 'url' => ''],
            ['alt' => 'Delbecq BMW',              'image_url' => '', 'url' => ''],
            ['alt' => 'Simplicicar Liège',        'image_url' => '', 'url' => ''],
            ['alt' => 'Bipartner',                'image_url' => '', 'url' => ''],
            ['alt' => 'EDF Renewables Belgium',   'image_url' => '', 'url' => ''],
            ['alt' => 'Accardo',                  'image_url' => '', 'url' => ''],
            ['alt' => 'Stoler Immo',              'image_url' => '', 'url' => ''],
            ['alt' => 'MBS Solution',             'image_url' => '', 'url' => ''],
            ['alt' => 'Meuse Condroz Logement',   'image_url' => '', 'url' => ''],
        ],
    ],
    [
        'type'   => 'seo-content',
        'bg'     => 'white',
        'layout' => 'text-only',
        'h2'     => 'Société de nettoyage de canapé à domicile',
        'body'   => '<p>Keepnew est spécialisé dans le nettoyage professionnel de canapés à domicile en Belgique. Nos techniciens interviennent directement chez vous avec du matériel professionnel et des produits écologiques, hypoallergéniques et sûrs pour les enfants et les animaux.</p><p>Que votre canapé soit en tissu, en cuir, en microfibre ou en velours, nous adaptons notre traitement à chaque matière pour un résultat optimal. Injection-extraction, vapeur sèche, traitement anti-taches et désinfection : chaque intervention est complète et documentée avec photos avant/après.</p><p>Nous recommandons un nettoyage professionnel tous les 12 à 18 mois pour un usage standard, et plus fréquemment si vous avez des animaux ou des enfants.</p>',
    ],
    [
        'type'  => 'accordion',
        'bg'    => 'cream',
        'items' => [
            [
                'question' => 'Quels types de canapés peuvent être nettoyés ?',
                'answer'   => 'Tous les types de canapés : tissu, cuir, microfibre, velours. Nos techniciens adaptent le traitement à chaque matière pour un résultat optimal sans risque d\'endommagement.',
            ],
            [
                'question' => 'Le nettoyage élimine-t-il les mauvaises odeurs ?',
                'answer'   => 'Oui. Nos produits neutralisent les bactéries responsables des odeurs. Les odeurs de cigarette peuvent nécessiter plusieurs passages selon l\'ancienneté.',
            ],
            [
                'question' => 'Quels produits utilisez-vous pour le nettoyage ?',
                'answer'   => 'Nous utilisons des produits professionnels écologiques et hypoallergéniques, sûrs pour les enfants et les animaux de compagnie.',
            ],
            [
                'question' => 'Comment préparer mon canapé avant votre intervention ?',
                'answer'   => 'Retirez simplement les objets et coussins amovibles. Nos techniciens s\'occupent du reste avec leur propre matériel.',
            ],
            [
                'question' => 'À quelle fréquence devrais-je faire nettoyer mon canapé ?',
                'answer'   => 'Un nettoyage professionnel tous les 12 à 18 mois est recommandé pour un usage standard. Plus fréquent si vous avez des animaux ou des enfants.',
            ],
            [
                'question' => 'Quel est le temps de séchage après le nettoyage ?',
                'answer'   => 'Entre 12 et 24 heures selon l\'humidité ambiante et la capacité d\'absorption du tissu. Nous vous le précisons lors de l\'intervention.',
            ],
        ],
    ],
    [
        'type'    => 'before-after',
        'bg'      => 'alt',
        'eyebrow' => 'NOS RESULTATS',
        'h2'      => 'Quelques avant/apres de nos nettoyages canapés',
        'items'   => [
            ['image_before_url' => '', 'image_after_url' => '', 'caption' => 'Elimination des poils d\'animaux'],
            ['image_before_url' => '', 'image_after_url' => '', 'caption' => 'Traitement des taches tenaces'],
            ['image_before_url' => '', 'image_after_url' => '', 'caption' => 'Nettoyage en profondeur'],
        ],
    ],
    [
        'type'    => 'seo-content',
        'bg'      => 'blue',
        'layout'  => 'text-only',
        'h2'      => 'Nettoyez votre canapé et faites des économies !',
        'body'    => '<p>Un canapé mal entretenu se dégrade rapidement. Un nettoyage professionnel régulier peut prolonger sa durée de vie de <strong>7 ans</strong>, vous faisant économiser jusqu\'à <strong>1 500 €</strong> en remplacement de mobilier.</p><p>En plus des économies, un canapé propre améliore la qualité de l\'air intérieur et réduit les risques d\'allergies et de problèmes respiratoires liés aux acariens et aux bactéries.</p>',
    ],
    [
        'type'    => 'reviews',
        'bg'      => 'white',
        'eyebrow' => 'ILS NOUS FONT CONFIANCE',
        'h2'      => '4,9/5 · +90 avis · +400 canapés nettoyés',
    ],
    [
        'type'    => 'seo-content',
        'bg'      => 'white',
        'layout'  => 'text-only',
        'h2'      => 'Nettoyage de canapé professionnel à domicile en Belgique',
        'body'    => '<p>Keepnew intervient dans plus de 40 communes en Belgique. Nous couvrons la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Waremme, Verviers), la province de Namur, la province de Luxembourg et la région de Bruxelles-Capitale.</p><p>Vous payez uniquement après l\'intervention — aucune avance requise. Règlement par carte, virement ou QR code. Les professionnels bénéficient de la facturation.</p>',
    ],
    [
        'type'    => 'cta-final',
        'eyebrow' => 'RESERVATION EN LIGNE',
        'title'   => 'Prendre RDV en 2 min',
        'phone'   => '+32 (0)4 55 13 84 19',
    ],
];

$canapeContent = json_encode($canapeBlocks, JSON_UNESCAPED_UNICODE);

$pages[] = [
    'slug'             => 'nettoyage-canape-domicile',
    'lang'             => 'fr',
    'title'            => 'Nettoyage de canapés à domicile',
    'template'         => 'service',
    'status'           => 'published',
    'meta_title'       => 'Nettoyage de canapé à domicile - Tissu, Cuir, Velours | Keepnew',
    'meta_description' => 'Keepnew nettoie votre canapé à domicile en Belgique. Tissu, cuir, microfibre, velours. A partir de 99 €. Produits écologiques. Résultat garanti. Devis gratuit.',
    'content'          => $canapeContent,
    'sort_order'       => 10,
];

// Update home page content if it already exists
$updateHome = $pdo->prepare(
    'UPDATE kn_pages SET content = :content WHERE slug = :slug AND lang = :lang'
);
$updateHome->execute(array('content' => $homeContent, 'slug' => 'home', 'lang' => 'fr'));

$upsert = $pdo->prepare(
    'INSERT INTO kn_pages (slug, lang, title, template, status, meta_title, meta_description, content, sort_order)
     VALUES (:slug, :lang, :title, :template, :status, :meta_title, :meta_description, :content, :sort_order)
     ON DUPLICATE KEY UPDATE title = VALUES(title), template = VALUES(template), status = VALUES(status),
     meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), content = VALUES(content)'
);

foreach ($pages as $page) {
    $upsert->execute($page);
    echo "Page créée/mise à jour : " . $page['title'] . " (" . $page['slug'] . ")\n";
}

echo "\nInitialisation terminée.\n";
