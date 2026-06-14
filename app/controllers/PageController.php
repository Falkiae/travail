<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Page;

class PageController extends BaseController
{
    private $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
    }

    public function index(): void
    {
        $this->requireLogin();
        $pages     = array_merge($this->pageModel->findAll('fr'), $this->pageModel->findAll('nl'));
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/index', ['title' => 'Pages', 'pages' => $pages, 'csrf_token' => $csrfToken], 'admin');
    }

    public function create(): void
    {
        $this->requireLogin();
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', ['title' => 'Nouvelle page', 'csrf_token' => $csrfToken, 'page' => null], 'admin');
    }

    public function store(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/new');
        }
        $lang     = in_array(isset($_POST['lang']) ? $_POST['lang'] : 'fr', ['fr', 'nl']) ? $_POST['lang'] : 'fr';
        $slug     = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug     = $this->ensureUniqueSlug('pages', $slug, $lang);
        $template = in_array(isset($_POST['template']) ? $_POST['template'] : 'page', ['home','page','blog-list','portfolio']) ? $_POST['template'] : 'page';
        $status   = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft','published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';
        $content  = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;
        $robots   = in_array(isset($_POST['robots']) ? $_POST['robots'] : 'index,follow', ['index,follow','noindex,follow','noindex,nofollow']) ? $_POST['robots'] : 'index,follow';

        $this->pageModel->create([
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
            'canonical_url'    => trim(isset($_POST['canonical_url']) ? $_POST['canonical_url'] : ''),
            'robots'           => $robots,
        ]);
        Auth::setFlash('success', 'Page créée.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function edit(string $id): void
    {
        $this->requireLogin();
        $page = $this->pageModel->findById((int)$id);
        if (!$page) {
            Auth::setFlash('error', 'Page introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $csrfToken = Auth::generateCsrfToken();
        $this->view->render('admin/pages/form', ['title' => 'Modifier la page', 'csrf_token' => $csrfToken, 'page' => $page], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $page = $this->pageModel->findById((int)$id);
        if (!$page) {
            Auth::setFlash('error', 'Page introuvable.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
        if ($title === '') {
            Auth::setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('/' . ADMIN_PATH . '/pages/' . $id . '/edit');
        }
        $lang    = in_array(isset($_POST['lang']) ? $_POST['lang'] : $page['lang'], ['fr','nl']) ? $_POST['lang'] : $page['lang'];
        $slug    = $this->slugify(trim(isset($_POST['slug']) ? $_POST['slug'] : '') ?: $title);
        $slug    = $this->ensureUniqueSlug('pages', $slug, $lang, (int)$id);
        $this->saveRevision('page', (int)$id, $page['content']);
        if ($slug !== $page['slug']) {
            $pdo = $this->db();
            $pdo->prepare("INSERT INTO kn_redirections (from_url, to_url) VALUES (?, ?) ON DUPLICATE KEY UPDATE to_url = ?")
                ->execute(['/' . $page['slug'], '/' . $slug, '/' . $slug]);
        }
        $template = in_array(isset($_POST['template']) ? $_POST['template'] : 'page', ['home','page','blog-list','portfolio']) ? $_POST['template'] : 'page';
        $status   = in_array(isset($_POST['status']) ? $_POST['status'] : 'draft', ['draft','published']) ? $_POST['status'] : 'draft';
        if (isset($_POST['publish'])) $status = 'published';
        $content  = isset($_POST['content']) ? $_POST['content'] : null;
        if ($content !== null && json_decode($content) === null) $content = null;
        $robots   = in_array(isset($_POST['robots']) ? $_POST['robots'] : 'index,follow', ['index,follow','noindex,follow','noindex,nofollow']) ? $_POST['robots'] : 'index,follow';
        $this->pageModel->update((int)$id, [
            'lang'             => $lang,
            'slug'             => $slug,
            'title'            => $title,
            'template'         => $template,
            'status'           => $status,
            'content'          => $content,
            'meta_title'       => mb_substr(trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : ''), 0, 70),
            'meta_description' => mb_substr(trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : ''), 0, 160),
            'og_image'         => trim(isset($_POST['og_image']) ? $_POST['og_image'] : ''),
            'canonical_url'    => trim(isset($_POST['canonical_url']) ? $_POST['canonical_url'] : ''),
            'robots'           => $robots,
        ]);
        Auth::setFlash('success', 'Page mise à jour.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function delete(string $id): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }
        $this->pageModel->delete((int)$id);
        Auth::setFlash('success', 'Page supprimée.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }

    public function seed(): void
    {
        $this->requireLogin();
        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/pages');
        }

        $homeContent = '[{"type":"hero","bg":"cream","eyebrow":"CANAPÉ · VOITURE · MATELAS — À DOMICILE","h1":"Nettoyage à domicile de canapé, matelas et voitures.","body":"Keepnew nettoie vos <strong>canapés<\/strong>, <strong>matelas<\/strong>, <strong>voitures<\/strong> et <strong>terrasses<\/strong> directement chez vous, avec des <strong>produits écologiques<\/strong>. Plus de <strong>+400 canapés<\/strong> et <strong>+250 voitures<\/strong> remis à neuf en Wallonie et à Bruxelles.","pills":[{"text":"✓ À domicile","style":"white"},{"text":"★ Garantie","style":"yellow"},{"text":"⚡ Devis en 2 min","style":"night"}],"cta_eyebrow":"RÉSERVATION EN LIGNE","cta_title":"Prendre RDV en 2 min"},{"type":"services","eyebrow":"NOS SERVICES","h2":"Nettoyage de canapés, matelas & voitures à domicile","items":[{"name":"Canapé","desc":"Aspiration profonde, vapeur, anti-odeurs. Résultat garanti.","icon":"🛋️"},{"name":"Matelas","desc":"Nettoyage en profondeur, anti-acariens et désinfection.","icon":"🛏️"},{"name":"Voiture","desc":"Intérieur complet, sièges, moquettes et plastiques.","icon":"🚗"},{"name":"Polissage & Céramique","desc":"Protection longue durée pour votre carrosserie.","icon":"✨"},{"name":"Terrasse","desc":"Haute pression et traitement anti-mousse.","icon":"🏠"},{"name":"Atelier Visé","desc":"Traitements approfondis dans notre atelier à Visé.","icon":"🔧"}]},{"type":"two-col","eyebrow":"DEUX FAÇONS DE TRAVAILLER","h2":"On vient chez vous — ou vous venez chez nous.","left_title":"À domicile","left_body":"Nous intervenons directement chez vous dans toute la province de Liège, Namur, Luxembourg et à Bruxelles. Pas besoin de vous déplacer — on s\'occupe de tout sur place.","right_title":"Atelier à Visé","right_body":"Pour les traitements les plus exigeants (polissage, céramique, camping-cars), notre atelier de Visé dispose de l\'équipement professionnel nécessaire. Déposez votre véhicule et récupérez-le impeccable."},{"type":"how","eyebrow":"RÉSERVATION EN LIGNE","h2":"Réservez votre nettoyage à domicile en 2 minutes","steps":[{"title":"Choisissez votre service en ligne","body":"Sélectionnez le service souhaité et réservez directement en ligne — devis inclus, sans surprise."},{"title":"On passe chez vous","body":"Notre équipe arrive à l\'heure convenue avec tout le matériel. Vous n\'avez rien à préparer."},{"title":"Résultat garanti","body":"Satisfait ou on revient. Chaque intervention est garantie et documentée avec photos avant\/après."}]},{"type":"reviews","eyebrow":"ILS NOUS FONT CONFIANCE","h2":"4,9\/5 · +110 avis · +400 canapés nettoyés"},{"type":"zone","eyebrow":"OÙ ON INTERVIENT","h2":"Nettoyage à domicile à Liège, Namur, Bruxelles et Luxembourg","body":"Keepnew intervient dans toute la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Waremme), en province de Namur, dans la province du Luxembourg et à Bruxelles. Vous n\'êtes pas sûr que nous intervenons chez vous ? Contactez-nous — nous faisons notre possible pour vous répondre.","pills":[{"label":"Liège"},{"label":"Namur"},{"label":"Bruxelles"},{"label":"Luxembourg"},{"label":"Visé"},{"label":"Seraing"},{"label":"Herstal"}]},{"type":"accordion","items":[{"question":"Combien coûte un nettoyage de canapé à domicile ?","answer":"Le tarif dépend de la taille et du type de canapé. Nous proposons un devis gratuit en 2 minutes en ligne — sans engagement."},{"question":"Quels produits utilisez-vous ?","answer":"Nous utilisons des produits professionnels écologiques, sans solvants agressifs, adaptés à chaque matière (tissu, cuir, microfibre)."},{"question":"Vous intervenez vraiment à domicile ?","answer":"Oui, nous nous déplaçons directement chez vous avec tout notre matériel. Aucun déplacement nécessaire de votre côté."},{"question":"Combien de temps dure une intervention ?","answer":"Entre 1h et 3h selon le service et la superficie. Nous vous communiquons une estimation précise lors de la réservation."},{"question":"Proposez-vous un devis gratuit ?","answer":"Absolument. Le devis est gratuit, en ligne, et sans engagement. Vous connaissez le prix avant de confirmer."},{"question":"Quelle zone géographique couvrez-vous ?","answer":"Nous couvrons la province de Liège, Namur, le Luxembourg belge et Bruxelles. Contactez-nous pour vérifier votre commune."}]},{"type":"cta-final","eyebrow":"📅 RÉSERVATION EN LIGNE","title":"Prendre RDV en 2 min"}]';

        $canapeBlocks = array(
            array('type'=>'hero','bg'=>'cream','eyebrow'=>'NETTOYAGE CANAPE · LIEGE · NAMUR · BRUXELLES','h1'=>'Nettoyage de <span class="kn-tape">canapés</span> à domicile','body'=>'<p>Tissu, cuir, microfibre ou velours — on s\'occupe de votre canapé avec des produits écologiques et hypoallergéniques, directement chez vous.</p>','social_proof'=>'4,9/5 · +90 avis · +400 canapés nettoyés','pills'=>array(array('text'=>'✓ À domicile','style'=>'white'),array('text'=>'★ Résultat garanti','style'=>'yellow'),array('text'=>'⚡ RDV en 2 min','style'=>'night')),'cta_eyebrow'=>'RESERVATION EN LIGNE','cta_title'=>'Prendre RDV en 2 min','layout'=>'1-1'),
            array('type'=>'heading','level'=>'h2','text'=>'Tissus, cuirs, velours ? Pas de souci, on s\'occupe de votre canapé !','style'=>'highlight','bg'=>'white'),
            array('type'=>'pricing','bg'=>'white','eyebrow'=>'NOS TARIFS','h2'=>'Prix clairs et sans surprise','items'=>array(array('label'=>'Canapé 1 à 3 places','price'=>'99 €','cta_url'=>'#','cta_text'=>'Réserver'),array('label'=>'Canapé 4 à 5 places','price'=>'140 €','cta_url'=>'#','cta_text'=>'Réserver'),array('label'=>'Canapé 6 à 8 places','price'=>'170 €','cta_url'=>'#','cta_text'=>'Réserver'),array('label'=>'Canapé 8 places et +','price'=>'200 €','cta_url'=>'#','cta_text'=>'Réserver'))),
            array('type'=>'logos','bg'=>'alt','eyebrow'=>'ILS NOUS FONT CONFIANCE','items'=>array(array('alt'=>'Auto Lana','image_url'=>'','url'=>''),array('alt'=>'Delbecq BMW','image_url'=>'','url'=>''),array('alt'=>'Simplicicar Liège','image_url'=>'','url'=>''),array('alt'=>'Bipartner','image_url'=>'','url'=>''),array('alt'=>'EDF Renewables Belgium','image_url'=>'','url'=>''),array('alt'=>'Accardo','image_url'=>'','url'=>''),array('alt'=>'Stoler Immo','image_url'=>'','url'=>''),array('alt'=>'MBS Solution','image_url'=>'','url'=>''),array('alt'=>'Meuse Condroz Logement','image_url'=>'','url'=>''))),
            array('type'=>'seo-content','bg'=>'white','layout'=>'text-only','h2'=>'Société de nettoyage de canapé à domicile','body'=>'<p>Keepnew est spécialisé dans le nettoyage professionnel de canapés à domicile en Belgique. Nos techniciens interviennent directement chez vous avec du matériel professionnel et des produits écologiques, hypoallergéniques et sûrs pour les enfants et les animaux.</p><p>Que votre canapé soit en tissu, en cuir, en microfibre ou en velours, nous adaptons notre traitement à chaque matière pour un résultat optimal. Injection-extraction, vapeur sèche, traitement anti-taches et désinfection : chaque intervention est complète et documentée avec photos avant/après.</p><p>Nous recommandons un nettoyage professionnel tous les 12 à 18 mois pour un usage standard, et plus fréquemment si vous avez des animaux ou des enfants.</p>'),
            array('type'=>'accordion','bg'=>'cream','items'=>array(array('question'=>'Quels types de canapés peuvent être nettoyés ?','answer'=>'Tous les types de canapés : tissu, cuir, microfibre, velours. Nos techniciens adaptent le traitement à chaque matière pour un résultat optimal sans risque d\'endommagement.'),array('question'=>'Le nettoyage élimine-t-il les mauvaises odeurs ?','answer'=>'Oui. Nos produits neutralisent les bactéries responsables des odeurs. Les odeurs de cigarette peuvent nécessiter plusieurs passages selon l\'ancienneté.'),array('question'=>'Quels produits utilisez-vous pour le nettoyage ?','answer'=>'Nous utilisons des produits professionnels écologiques et hypoallergéniques, sûrs pour les enfants et les animaux de compagnie.'),array('question'=>'Comment préparer mon canapé avant votre intervention ?','answer'=>'Retirez simplement les objets et coussins amovibles. Nos techniciens s\'occupent du reste avec leur propre matériel.'),array('question'=>'À quelle fréquence devrais-je faire nettoyer mon canapé ?','answer'=>'Un nettoyage professionnel tous les 12 à 18 mois est recommandé pour un usage standard. Plus fréquent si vous avez des animaux ou des enfants.'),array('question'=>'Quel est le temps de séchage après le nettoyage ?','answer'=>'Entre 12 et 24 heures selon l\'humidité ambiante et la capacité d\'absorption du tissu. Nous vous le précisons lors de l\'intervention.'))),
            array('type'=>'before-after','bg'=>'alt','eyebrow'=>'NOS RESULTATS','h2'=>'Quelques avant/apres de nos nettoyages canapés','items'=>array(array('image_before_url'=>'','image_after_url'=>'','caption'=>'Elimination des poils d\'animaux'),array('image_before_url'=>'','image_after_url'=>'','caption'=>'Traitement des taches tenaces'),array('image_before_url'=>'','image_after_url'=>'','caption'=>'Nettoyage en profondeur'))),
            array('type'=>'seo-content','bg'=>'blue','layout'=>'text-only','h2'=>'Nettoyez votre canapé et faites des économies !','body'=>'<p>Un canapé mal entretenu se dégrade rapidement. Un nettoyage professionnel régulier peut prolonger sa durée de vie de <strong>7 ans</strong>, vous faisant économiser jusqu\'à <strong>1 500 €</strong> en remplacement de mobilier.</p><p>En plus des économies, un canapé propre améliore la qualité de l\'air intérieur et réduit les risques d\'allergies et de problèmes respiratoires liés aux acariens et aux bactéries.</p>'),
            array('type'=>'reviews','bg'=>'white','eyebrow'=>'ILS NOUS FONT CONFIANCE','h2'=>'4,9/5 · +90 avis · +400 canapés nettoyés'),
            array('type'=>'seo-content','bg'=>'white','layout'=>'text-only','h2'=>'Nettoyage de canapé professionnel à domicile en Belgique','body'=>'<p>Keepnew intervient dans plus de 40 communes en Belgique. Nous couvrons la province de Liège (Liège, Visé, Seraing, Herstal, Huy, Waremme, Verviers), la province de Namur, la province de Luxembourg et la région de Bruxelles-Capitale.</p><p>Vous payez uniquement après l\'intervention — aucune avance requise. Règlement par carte, virement ou QR code. Les professionnels bénéficient de la facturation.</p>'),
            array('type'=>'cta-final','eyebrow'=>'RESERVATION EN LIGNE','title'=>'Prendre RDV en 2 min','phone'=>'+32 (0)4 55 13 84 19'),
        );
        $canapeContent = json_encode($canapeBlocks, JSON_UNESCAPED_UNICODE);

        $defaults = [
            [
                'slug' => 'home', 'lang' => 'fr', 'title' => 'Accueil',
                'template' => 'home', 'status' => 'published',
                'meta_title' => 'Nettoyage de canapés, matelas & voitures à domicile | Keepnew',
                'meta_description' => 'Keepnew — Nettoyage professionnel à domicile : canapés, matelas, voitures, terrasses. Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
                'content' => $homeContent, 'sort_order' => 0,
            ],
            [
                'slug' => 'blog', 'lang' => 'fr', 'title' => 'Blog',
                'template' => 'blog-list', 'status' => 'published',
                'meta_title' => 'Blog nettoyage à domicile - Conseils & astuces | Keepnew',
                'meta_description' => 'Conseils et actualités sur le nettoyage à domicile par Keepnew.',
                'content' => '[]', 'sort_order' => 2,
            ],
            [
                'slug' => 'portfolio', 'lang' => 'fr', 'title' => 'Réalisations',
                'template' => 'portfolio', 'status' => 'published',
                'meta_title' => 'Nos réalisations - Avant / Après | Keepnew',
                'meta_description' => 'Découvrez les réalisations de Keepnew : canapés, matelas et voitures nettoyés.',
                'content' => '[]', 'sort_order' => 3,
            ],
            [
                'slug' => 'nettoyage-canape-domicile', 'lang' => 'fr', 'title' => 'Nettoyage de canapés à domicile',
                'template' => 'service', 'status' => 'published',
                'meta_title' => 'Nettoyage de canapé à domicile - Tissu, Cuir, Velours | Keepnew',
                'meta_description' => 'Keepnew nettoie votre canapé à domicile en Belgique. Tissu, cuir, microfibre, velours. A partir de 99 €. Produits écologiques. Résultat garanti.',
                'content' => $canapeContent, 'sort_order' => 10,
            ],
        ];

        $pdo = $this->db();

        // UPDATE home page if it already exists, INSERT otherwise
        $updateStmt = $pdo->prepare(
            'UPDATE kn_pages SET content = :content, title = :title, template = :template, status = :status,
             meta_title = :meta_title, meta_description = :meta_description
             WHERE slug = :slug AND lang = :lang'
        );
        $updateStmt->execute(array(
            'content'          => $homeContent,
            'title'            => 'Accueil',
            'template'         => 'home',
            'status'           => 'published',
            'meta_title'       => 'Nettoyage de canapés, matelas & voitures à domicile | Keepnew',
            'meta_description' => 'Keepnew — Nettoyage professionnel à domicile : canapés, matelas, voitures, terrasses. Liège, Namur, Bruxelles, Luxembourg. Devis gratuit.',
            'slug'             => 'home',
            'lang'             => 'fr',
        ));

        $stmt = $pdo->prepare(
            'INSERT INTO kn_pages (slug, lang, title, template, status, meta_title, meta_description, content, sort_order)
             VALUES (:slug, :lang, :title, :template, :status, :meta_title, :meta_description, :content, :sort_order)
             ON DUPLICATE KEY UPDATE title = VALUES(title), template = VALUES(template), status = VALUES(status),
             meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), content = VALUES(content)'
        );

        foreach ($defaults as $page) {
            $stmt->execute($page);
        }

        Auth::setFlash('success', 'Pages par défaut créées/mises à jour.');
        $this->redirect('/' . ADMIN_PATH . '/pages');
    }
}
