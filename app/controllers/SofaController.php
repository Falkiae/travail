<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class SofaController extends BaseController
{
    private function defaultConfig(): array
    {
        return [
            'shapes' => [
                ['key' => 'droit',    'label' => 'Droit',     'base_price' => 89,  'base_seats' => 2, 'cta_url' => '/rdv', 'image_url' => ''],
                ['key' => 'angle',    'label' => 'Angle / L', 'base_price' => 129, 'base_seats' => 3, 'cta_url' => '/rdv', 'image_url' => ''],
                ['key' => 'u',        'label' => 'En U',      'base_price' => 169, 'base_seats' => 5, 'cta_url' => '/rdv', 'image_url' => ''],
                ['key' => 'fauteuil', 'label' => 'Fauteuil',  'base_price' => 49,  'base_seats' => 1, 'cta_url' => '/rdv', 'image_url' => ''],
            ],
            'price_per_extra_seat' => 20,
            'price_per_meridienne' => 35,
            'cta_text'             => 'Prendre RDV',
        ];
    }

    public function index(): void
    {
        $this->requireLogin();
        $pdo  = $this->db();
        $stmt = $pdo->prepare("SELECT `value` FROM kn_settings WHERE `key` = 'sofa_config' LIMIT 1");
        $stmt->execute();
        $row    = $stmt->fetch(\PDO::FETCH_ASSOC);
        $config = ($row && $row['value']) ? json_decode($row['value'], true) : null;
        if (!is_array($config)) {
            $config = $this->defaultConfig();
        }

        $this->view->render('admin/sofa/index', [
            'title'      => 'Simulateur canapé',
            'config'     => $config,
            'csrf_token' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    public function update(): void
    {
        $this->requireLogin();

        if (!Auth::verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
            Auth::setFlash('error', 'Token CSRF invalide.');
            $this->redirect('/' . ADMIN_PATH . '/sofa');
        }

        $pdo    = $this->db();
        $shapes = [];
        $keys      = isset($_POST['shape_key'])        ? (array)$_POST['shape_key']        : [];
        $labels    = isset($_POST['shape_label'])      ? (array)$_POST['shape_label']      : [];
        $prices    = isset($_POST['shape_base_price']) ? (array)$_POST['shape_base_price'] : [];
        $seats     = isset($_POST['shape_base_seats']) ? (array)$_POST['shape_base_seats'] : [];
        $ctaUrls   = isset($_POST['shape_cta_url'])    ? (array)$_POST['shape_cta_url']    : [];
        $imageUrls = isset($_POST['shape_image_url'])  ? (array)$_POST['shape_image_url']  : [];

        $count = count($keys);
        for ($i = 0; $i < $count; $i++) {
            $k = trim($keys[$i]);
            if ($k === '') continue;
            $shapes[] = [
                'key'        => $k,
                'label'      => trim(isset($labels[$i])     ? $labels[$i]     : ''),
                'base_price' => max(0, (int)(isset($prices[$i]) ? $prices[$i] : 0)),
                'base_seats' => max(1, (int)(isset($seats[$i])  ? $seats[$i]  : 1)),
                'cta_url'    => trim(isset($ctaUrls[$i])   ? $ctaUrls[$i]   : ''),
                'image_url'  => trim(isset($imageUrls[$i]) ? $imageUrls[$i] : ''),
            ];
        }

        $config = [
            'shapes'               => $shapes,
            'price_per_extra_seat' => max(0, (int)(isset($_POST['price_per_extra_seat']) ? $_POST['price_per_extra_seat'] : 0)),
            'price_per_meridienne' => max(0, (int)(isset($_POST['price_per_meridienne']) ? $_POST['price_per_meridienne'] : 0)),
            'cta_text'             => trim(isset($_POST['cta_text']) ? $_POST['cta_text'] : 'Prendre RDV'),
        ];

        $json = json_encode($config, JSON_UNESCAPED_UNICODE);
        $pdo->prepare("INSERT INTO kn_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?")
            ->execute(['sofa_config', $json, $json]);

        Auth::setFlash('success', 'Configuration du simulateur sauvegardée.');
        $this->redirect('/' . ADMIN_PATH . '/sofa');
    }
}
