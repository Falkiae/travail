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

$pages = [
    [
        'slug'             => 'home',
        'lang'             => 'fr',
        'title'            => 'Accueil',
        'template'         => 'home',
        'status'           => 'published',
        'meta_title'       => 'Nettoyage de canapés, matelas & voitures à domicile | Keepnew',
        'meta_description' => 'Keepnew — Service de nettoyage professionnel à domicile en Belgique. Canapés, matelas, voitures, terrasses. Zone Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
        'content'          => '[]',
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

$stmt = $pdo->prepare(
    'INSERT IGNORE INTO kn_pages (slug, lang, title, template, status, meta_title, meta_description, content, sort_order)
     VALUES (:slug, :lang, :title, :template, :status, :meta_title, :meta_description, :content, :sort_order)'
);

foreach ($pages as $page) {
    $stmt->execute($page);
    echo "Page créée : " . $page['title'] . " (" . $page['slug'] . ")\n";
}

echo "\nInitialisation terminée.\n";
