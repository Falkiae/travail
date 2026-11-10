<?php
/**
 * Configuration du simulateur auto (bloc car-advisor) : valeurs par défaut,
 * normalisation et chargement. Partagé entre la vue publique et l'admin
 * (CarAdvisorController). Stockée en JSON dans kn_settings (car_advisor_config).
 */

if (!function_exists('knCaDefaultConfig')) {

function knCaSizes(): array  { return array('citadine' => 'Citadine', 'berline' => 'Berline / Break', 'suv' => 'SUV / 4×4', 'utilitaire' => 'Utilitaire'); }
function knCaPlaces(): array { return array('domicile' => 'Domicile', 'atelier' => 'Atelier'); }
function knCaTags(): array
{
    return array(
        'sieges'    => 'Sièges (shampoing)',
        'interieur' => 'Habitacle (entretien)',
        'profond'   => 'Intérieur en profondeur',
        'exterieur' => 'Extérieur',
    );
}
function knCaStateKeys(): array { return array('propre', 'quotidien', 'enfants', 'marque'); }
function knCaGoalKeys(): array  { return array('entretien', 'sieges', 'interieur', 'neuf'); }

function knCaDefaultConfig(): array
{
    return array(
        'formulas' => array(
            array('key' => 'shampoing', 'name' => 'Shampoing des sièges', 'badge' => 'NOUVEAU',
                  'tagline' => 'Des sièges propres et sans taches, sans payer un nettoyage complet.',
                  'points' => array('Aspiration des sièges', 'Shampoing des sièges en profondeur', 'Parfum longue durée'),
                  'duration' => '≈ 1 h', 'cta_url' => '', 'covers' => array('sieges'),
                  'prices' => array('citadine_domicile' => 80)),
            array('key' => 'ie', 'name' => 'Intérieur & Extérieur', 'badge' => 'LE PLUS DEMANDÉ',
                  'tagline' => "L'entretien complet pour une voiture propre dedans comme dehors.",
                  'points' => array("Dépoussiérage et aspiration complète de l'habitacle", 'Plastiques intérieurs et vitres sans traces', 'Prélavage mousse active et lavage manuel', 'Jantes, passages de roues et brillant pneus', 'Protection hydrophobe', "Parfum d'ambiance"),
                  'duration' => '≈ 1 h 30', 'cta_url' => '', 'covers' => array('interieur', 'exterieur'),
                  'prices' => array('citadine_domicile' => 95)),
            array('key' => 'ran_int', 'name' => 'Remise à neuf intérieur', 'badge' => '',
                  'tagline' => 'Un habitacle repris en profondeur, comme au premier jour.',
                  'points' => array("Tout l'intérieur de la formule Intérieur & Extérieur", 'Shampoing des sièges et des carpettes', 'Nettoyage des cuirs', 'Vitres, pare-brise et volant dégraissé', 'Parfum longue durée'),
                  'duration' => "Selon l'état", 'cta_url' => '', 'covers' => array('sieges', 'interieur', 'profond'),
                  'prices' => array('citadine_domicile' => 180)),
            array('key' => 'ran_full', 'name' => 'Remise à neuf complète', 'badge' => 'IDÉAL FIN DE LEASING',
                  'tagline' => 'La formule la plus complète, pour une voiture comme neuve.',
                  'points' => array('Toute la remise à neuf intérieure', 'Extérieur complet : prélavage, lavage manuel, jantes', 'Plastiques extérieurs traités', 'Protection hydrophobe'),
                  'duration' => "Jusqu'à ≈ 4 h", 'cta_url' => '', 'covers' => array('sieges', 'interieur', 'profond', 'exterieur'),
                  'prices' => array('citadine_domicile' => 240)),
        ),
        'extras' => array(
            array('key' => 'ozone', 'label' => 'Option ozone', 'desc' => 'Supprime les odeurs tenaces à la source (tabac, animaux, humidité).', 'icon' => 'wind', 'price' => null, 'atelier' => false, 'url' => '', 'included_in' => array()),
            array('key' => 'carpettes', 'label' => 'Shampoing des carpettes', 'desc' => 'Pour retrouver des tapis de sol sans traces.', 'icon' => 'droplets', 'price' => null, 'atelier' => false, 'url' => '', 'included_in' => array('ran_int', 'ran_full')),
            array('key' => 'poils', 'label' => "Poils d'animaux", 'desc' => 'Retrait minutieux des poils incrustés dans les textiles.', 'icon' => 'heart', 'price' => null, 'atelier' => false, 'url' => '', 'included_in' => array()),
            array('key' => 'polissage', 'label' => 'Polissage / céramique', 'desc' => 'Correction des micro-rayures et protection longue durée, en atelier à Visé.', 'icon' => 'sparkles', 'price' => null, 'atelier' => true, 'url' => '/polissage-voiture-liege', 'included_in' => array()),
        ),
        'states' => array(
            array('key' => 'propre',    'label' => 'Plutôt propre',     'sub' => 'Un coup de frais suffit'),
            array('key' => 'quotidien', 'label' => 'Usage quotidien',   'sub' => 'Poussière, traces, miettes'),
            array('key' => 'enfants',   'label' => 'Enfants / animaux', 'sub' => 'Taches, poils, odeurs'),
            array('key' => 'marque',    'label' => 'Très marqué',       'sub' => 'Ça fait longtemps…'),
        ),
        'concerns' => array(
            array('key' => 'taches',     'label' => 'Taches sur les sièges',        'icon' => 'armchair', 'needs' => array('sieges'),    'extras' => array()),
            array('key' => 'poils',      'label' => "Poils d'animaux",              'icon' => 'heart',    'needs' => array(),            'extras' => array('poils')),
            array('key' => 'odeurs',     'label' => 'Mauvaises odeurs / tabac',     'icon' => 'wind',     'needs' => array(),            'extras' => array('ozone')),
            array('key' => 'tapis',      'label' => 'Tapis et moquettes sales',     'icon' => 'droplets', 'needs' => array(),            'extras' => array('carpettes')),
            array('key' => 'poussiere',  'label' => 'Poussière et miettes',         'icon' => 'spray',    'needs' => array('interieur'), 'extras' => array()),
            array('key' => 'carrosserie','label' => 'Carrosserie sale',             'icon' => 'car',      'needs' => array('exterieur'), 'extras' => array()),
            array('key' => 'rayures',    'label' => 'Micro-rayures / peinture terne','icon' => 'sparkles', 'needs' => array(),           'extras' => array('polissage')),
        ),
        'goals' => array(
            array('key' => 'entretien', 'label' => 'Un entretien régulier',        'sub' => 'Garder une voiture propre'),
            array('key' => 'sieges',    'label' => 'Des sièges propres',           'sub' => 'Taches sur les assises'),
            array('key' => 'interieur', 'label' => 'Un grand nettoyage intérieur', 'sub' => "Tout l'habitacle en profondeur"),
            array('key' => 'neuf',      'label' => 'Une voiture comme neuve',      'sub' => 'Revente ou fin de leasing'),
        ),
        'matrix' => array(
            'entretien' => array('propre' => 'ie', 'quotidien' => 'ie', 'enfants' => 'ie', 'marque' => 'ran_int'),
            'sieges'    => array('propre' => 'shampoing', 'quotidien' => 'shampoing', 'enfants' => 'shampoing', 'marque' => 'ran_int'),
            'interieur' => array('propre' => 'ran_int', 'quotidien' => 'ran_int', 'enfants' => 'ran_int', 'marque' => 'ran_int'),
            'neuf'      => array('propre' => 'ran_full', 'quotidien' => 'ran_full', 'enfants' => 'ran_full', 'marque' => 'ran_full'),
        ),
        'txt' => array(
            'bot'  => 'Keepnew',
            'q1'   => 'Bonjour ! Pour commencer, quel véhicule souhaitez-vous faire nettoyer ?',
            'q2'   => 'Très bien. Dans quel état est-il en ce moment ?',
            'q3'   => "Qu'est-ce qui vous gêne le plus ? Plusieurs réponses possibles.",
            'q4'   => "Et qu'aimeriez-vous obtenir ?",
            'q5'   => 'Merci ! Voici ce que je vous recommande :',
            'q6'   => 'Un petit plus pour un résultat parfait ?',
            'q7'   => 'Dernière question : où souhaitez-vous le nettoyage ?',
            'q8'   => 'Votre nettoyage est prêt à être réservé !',
            'cta'  => 'Prendre RDV en ligne',
            'note' => "Prix « à partir de », TVA comprise. Le montant exact est confirmé à la réservation selon l'état du véhicule.",
        ),
    );
}

function knCaStr($v, int $max = 300): string
{
    $v = trim(str_replace(array("\r", "\0"), '', (string) $v));
    return function_exists('mb_substr') ? mb_substr($v, 0, $max) : substr($v, 0, $max);
}
function knCaKey($v): string
{
    $v = strtolower(knCaStr($v, 40));
    $v = strtr($v, array('é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'ç' => 'c', 'ô' => 'o', 'î' => 'i', 'û' => 'u'));
    return trim(preg_replace('/[^a-z0-9_]+/', '_', $v), '_');
}
function knCaPrice($v): ?float
{
    if ($v === null) return null;
    $v = str_replace(array(',', '€', ' ', "\xc2\xa0"), array('.', '', '', ''), trim((string) $v));
    if ($v === '' || !is_numeric($v)) return null;
    $f = (float) $v;
    return $f >= 0 && $f < 100000 ? round($f, 2) : null;
}
function knCaList($v): array
{
    if (is_array($v)) $items = $v;
    else $items = preg_split('/;|\r\n|\r|\n/', (string) $v);
    $out = array();
    foreach ($items as $it) { $it = knCaStr($it, 200); if ($it !== '') $out[] = $it; }
    return array_slice($out, 0, 20);
}

/** Normalise une configuration (y compris l'ancien format des blocs). */
function knCaNormalize($cfg): array
{
    $def = knCaDefaultConfig();
    if (!is_array($cfg)) return $def;
    $tags = array_keys(knCaTags());
    $defCovers = array();
    foreach ($def['formulas'] as $f) $defCovers[$f['key']] = $f['covers'];

    // Formules
    $formulas = array(); $seen = array();
    foreach ((isset($cfg['formulas']) && is_array($cfg['formulas']) ? $cfg['formulas'] : $def['formulas']) as $i => $f) {
        if (!is_array($f) || knCaStr(isset($f['name']) ? $f['name'] : '') === '') continue;
        $key = knCaKey(isset($f['key']) && $f['key'] !== '' ? $f['key'] : $f['name']);
        if ($key === '' || isset($seen[$key])) $key = 'formule_' . ($i + 1);
        $seen[$key] = true;
        $prices = array();
        foreach (array_keys(knCaSizes()) as $s) foreach (array_keys(knCaPlaces()) as $p) {
            $k = $s . '_' . $p;
            $raw = isset($f['prices'][$k]) ? $f['prices'][$k] : (isset($f['price_' . $k]) ? $f['price_' . $k] : null);
            $prices[$k] = knCaPrice($raw);
        }
        $covers = isset($f['covers']) && is_array($f['covers']) ? array_values(array_intersect($tags, $f['covers'])) : (isset($defCovers[$key]) ? $defCovers[$key] : array());
        $formulas[] = array(
            'key' => $key, 'name' => knCaStr($f['name'], 80),
            'badge' => knCaStr(isset($f['badge']) ? $f['badge'] : '', 40),
            'tagline' => knCaStr(isset($f['tagline']) ? $f['tagline'] : '', 200),
            'points' => knCaList(isset($f['points']) ? $f['points'] : array()),
            'duration' => knCaStr(isset($f['duration']) ? $f['duration'] : '', 40),
            'cta_url' => knCaStr(isset($f['cta_url']) ? $f['cta_url'] : '', 300),
            'covers' => $covers, 'prices' => $prices,
        );
    }
    if (!$formulas) $formulas = knCaNormalize(array('formulas' => $def['formulas']))['formulas'];
    $fKeys = array_column($formulas, 'key');

    // Suppléments
    $extras = array(); $seen = array();
    foreach ((isset($cfg['extras']) && is_array($cfg['extras']) ? $cfg['extras'] : $def['extras']) as $i => $e) {
        if (!is_array($e) || knCaStr(isset($e['label']) ? $e['label'] : '') === '') continue;
        $key = knCaKey(isset($e['key']) && $e['key'] !== '' ? $e['key'] : $e['label']);
        if ($key === '' || isset($seen[$key])) $key = 'supplement_' . ($i + 1);
        $seen[$key] = true;
        $atelier = isset($e['atelier']) ? !empty($e['atelier']) : (!empty($e['atelier_only']) && (string) $e['atelier_only'] !== '0');
        $extras[] = array(
            'key' => $key, 'label' => knCaStr($e['label'], 80),
            'desc' => knCaStr(isset($e['desc']) ? $e['desc'] : '', 200),
            'icon' => knCaKey(isset($e['icon']) && $e['icon'] !== '' ? $e['icon'] : 'sparkles') ?: 'sparkles',
            'price' => knCaPrice(isset($e['price']) ? $e['price'] : null),
            'atelier' => $atelier,
            'url' => knCaStr(isset($e['url']) ? $e['url'] : '', 300),
            'included_in' => isset($e['included_in']) && is_array($e['included_in']) ? array_values(array_intersect($fKeys, $e['included_in'])) : array(),
        );
    }
    $eKeys = array_column($extras, 'key');

    // États et objectifs : clés fixes, libellés éditables
    $pickFixed = function ($list, $defList, $keys) {
        $byKey = array();
        if (is_array($list)) foreach ($list as $it) if (is_array($it) && isset($it['key'])) $byKey[$it['key']] = $it;
        $out = array();
        foreach ($defList as $d) {
            $it = isset($byKey[$d['key']]) ? $byKey[$d['key']] : $d;
            $out[] = array('key' => $d['key'],
                'label' => knCaStr(isset($it['label']) && trim((string) $it['label']) !== '' ? $it['label'] : $d['label'], 60),
                'sub' => knCaStr(isset($it['sub']) ? $it['sub'] : $d['sub'], 80));
        }
        return $out;
    };
    $states = $pickFixed(isset($cfg['states']) ? $cfg['states'] : null, $def['states'], knCaStateKeys());
    $goals  = $pickFixed(isset($cfg['goals']) ? $cfg['goals'] : null, $def['goals'], knCaGoalKeys());

    // Soucis
    $concerns = array(); $seen = array();
    foreach ((isset($cfg['concerns']) && is_array($cfg['concerns']) ? $cfg['concerns'] : $def['concerns']) as $i => $c) {
        if (!is_array($c) || knCaStr(isset($c['label']) ? $c['label'] : '') === '') continue;
        $key = knCaKey(isset($c['key']) && $c['key'] !== '' ? $c['key'] : $c['label']);
        if ($key === '' || isset($seen[$key])) $key = 'souci_' . ($i + 1);
        $seen[$key] = true;
        $concerns[] = array(
            'key' => $key, 'label' => knCaStr($c['label'], 80),
            'icon' => knCaKey(isset($c['icon']) && $c['icon'] !== '' ? $c['icon'] : 'check') ?: 'check',
            'needs' => isset($c['needs']) && is_array($c['needs']) ? array_values(array_intersect($tags, $c['needs'])) : array(),
            'extras' => isset($c['extras']) && is_array($c['extras']) ? array_values(array_intersect($eKeys, $c['extras'])) : array(),
        );
    }

    // Matrice objectif × état
    $matrix = array();
    foreach (knCaGoalKeys() as $g) foreach (knCaStateKeys() as $s) {
        $v = isset($cfg['matrix'][$g][$s]) ? (string) $cfg['matrix'][$g][$s] : (isset($def['matrix'][$g][$s]) ? $def['matrix'][$g][$s] : '');
        $matrix[$g][$s] = in_array($v, $fKeys, true) ? $v : (in_array($def['matrix'][$g][$s], $fKeys, true) ? $def['matrix'][$g][$s] : $fKeys[0]);
    }

    // Textes
    $txt = array();
    foreach ($def['txt'] as $k => $d) {
        $v = isset($cfg['txt'][$k]) ? knCaStr($cfg['txt'][$k], 300) : '';
        $txt[$k] = $v !== '' ? $v : $d;
    }

    return array('formulas' => $formulas, 'extras' => $extras, 'states' => $states, 'concerns' => $concerns,
                 'goals' => $goals, 'matrix' => $matrix, 'txt' => $txt);
}

/** Lit la configuration globale ; à défaut, reprend celle d'un ancien bloc ($legacy). */
function knCaLoad(?array $legacy = null): array
{
    try {
        $db = \App\Core\Database::getInstance();
        $st = $db->prepare("SELECT `value` FROM kn_settings WHERE `key` = 'car_advisor_config' LIMIT 1");
        $st->execute();
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row && $row['value'] !== '') {
            $cfg = json_decode($row['value'], true);
            if (is_array($cfg)) return knCaNormalize($cfg);
        }
    } catch (\Throwable $e) {
        // base indisponible : on continue avec le repli
    }
    if ($legacy && (!empty($legacy['formulas']) || !empty($legacy['extras']))) {
        $cfg = array();
        if (!empty($legacy['formulas'])) $cfg['formulas'] = $legacy['formulas'];
        if (!empty($legacy['extras']))   $cfg['extras']   = $legacy['extras'];
        $map = array('q_vehicle' => 'q1', 'q_state' => 'q2', 'q_goal' => 'q4', 'q_reco' => 'q5', 'q_extras' => 'q6', 'q_place' => 'q7', 'q_result' => 'q8', 'cta_label' => 'cta', 'note' => 'note', 'bot_name' => 'bot');
        foreach ($map as $old => $new) if (!empty($legacy[$old])) $cfg['txt'][$new] = $legacy[$old];
        return knCaNormalize($cfg);
    }
    return knCaNormalize(knCaDefaultConfig());
}

}
