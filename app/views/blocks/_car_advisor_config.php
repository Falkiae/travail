<?php
/**
 * Configuration du simulateur auto (bloc car-advisor) : valeurs par défaut,
 * normalisation et chargement. Partagé entre la vue publique et l'admin
 * (CarAdvisorController). Stockée en JSON dans kn_settings (car_advisor_config).
 */

if (!function_exists('knCaDefaultConfig')) {

function knCaSizes(): array  { return array('citadine' => 'Citadine', 'berline' => 'Berline / Break', 'suv' => 'SUV / 4×4', 'utilitaire' => 'Utilitaire'); }
function knCaPlaces(): array { return array('domicile' => 'Domicile', 'atelier' => 'Atelier'); }
function knCaInteriorKeys(): array { return array('propre', 'taches', 'sale', 'encrasse'); }
function knCaExteriorKeys(): array { return array('non', 'lavage', 'neuf'); }

function knCaDefaultConfig(): array
{
    $g = function ($rec, $alt) { return array('rec' => $rec, 'alt' => $alt); };
    return array(
        'formulas' => array(
            array('key' => 'shampoing', 'name' => 'Shampoing des sièges', 'badge' => 'NOUVEAU',
                  'tagline' => 'Des sièges propres et sans taches, sans payer un nettoyage complet.',
                  'points' => array('Aspiration des sièges', 'Shampoing des sièges en profondeur (injection-extraction)', 'Parfum longue durée'),
                  'duration' => '≈ 1 h', 'cta_url' => '', 'prices' => array('citadine_domicile' => 80)),
            array('key' => 'ie', 'name' => 'Intérieur & Extérieur', 'badge' => 'LE PLUS DEMANDÉ',
                  'tagline' => "L'entretien complet pour une voiture propre dedans comme dehors.",
                  'points' => array("Aspiration des sièges et de tout l'habitacle", 'Dépoussiérage et plastiques intérieurs', 'Vitres intérieures sans traces', 'Prélavage mousse active et lavage manuel', 'Jantes, passages de roues et brillant pneus', 'Protection hydrophobe', "Parfum d'ambiance"),
                  'duration' => '≈ 1 h 30', 'cta_url' => '', 'prices' => array('citadine_domicile' => 95)),
            array('key' => 'ran_int', 'name' => 'Remise à neuf intérieur', 'badge' => '',
                  'tagline' => 'Un habitacle repris en profondeur, sièges shampouinés compris.',
                  'points' => array("Tout l'intérieur de la formule Intérieur & Extérieur", 'Shampoing des sièges et des carpettes', 'Nettoyage des cuirs', 'Vitres, pare-brise et volant dégraissé', 'Parfum longue durée'),
                  'duration' => "Selon l'état", 'cta_url' => '', 'prices' => array('citadine_domicile' => 180)),
            array('key' => 'ran_full', 'name' => 'Remise à neuf complète', 'badge' => 'IDÉAL FIN DE LEASING',
                  'tagline' => 'La remise à neuf intérieure, plus tout l\'extérieur : une voiture comme neuve.',
                  'points' => array('Toute la remise à neuf intérieure (shampoing des sièges inclus)', 'Extérieur complet : prélavage, lavage manuel, jantes', 'Plastiques extérieurs traités', 'Protection hydrophobe'),
                  'duration' => "Jusqu'à ≈ 4 h", 'cta_url' => '', 'prices' => array('citadine_domicile' => 240)),
        ),
        'extras' => array(
            array('key' => 'ozone', 'label' => 'Option ozone', 'desc' => 'Supprime les odeurs tenaces à la source (tabac, animaux, humidité).', 'icon' => 'wind', 'price' => null, 'atelier' => false, 'needs_exterior' => false, 'url' => '', 'included_in' => array()),
            array('key' => 'carpettes', 'label' => 'Shampoing des carpettes', 'desc' => 'Pour retrouver des tapis de sol sans traces.', 'icon' => 'droplets', 'price' => null, 'atelier' => false, 'needs_exterior' => false, 'url' => '', 'included_in' => array('ran_int', 'ran_full')),
            array('key' => 'poils', 'label' => "Poils d'animaux", 'desc' => 'Retrait minutieux des poils incrustés dans les textiles.', 'icon' => 'heart', 'price' => null, 'atelier' => false, 'needs_exterior' => false, 'url' => '', 'included_in' => array()),
            array('key' => 'polissage', 'label' => 'Polissage / céramique', 'desc' => 'Correction des micro-rayures et protection longue durée, en atelier à Visé.', 'icon' => 'sparkles', 'price' => null, 'atelier' => true, 'needs_exterior' => true, 'url' => '/polissage-voiture-liege', 'included_in' => array()),
        ),
        'interior' => array(
            array('key' => 'propre',   'label' => 'Propre, juste un coup de frais', 'sub' => 'Pas de taches sur les sièges', 'why' => 'Vos sièges ne sont pas tachés : un bon entretien suffit, sans shampoing', 'extras' => array()),
            array('key' => 'taches',   'label' => 'Sièges tachés, le reste ça va',   'sub' => 'Un shampoing est nécessaire',  'why' => 'Vos sièges sont tachés : il leur faut un shampoing en profondeur', 'extras' => array()),
            array('key' => 'sale',     'label' => 'Sièges tachés et habitacle sale', 'sub' => 'Poussière, plastiques, tapis', 'why' => 'Vos sièges sont tachés et l\'habitacle est sale : il faut reprendre tout l\'intérieur en profondeur', 'extras' => array()),
            array('key' => 'encrasse', 'label' => 'Très encrassé',                   'sub' => 'Enfants, animaux, odeurs, longtemps sans nettoyage', 'why' => 'Votre intérieur est très encrassé : une remise à neuf s\'impose, avec un traitement des odeurs et des poils', 'extras' => array('ozone', 'poils')),
        ),
        'exterior' => array(
            array('key' => 'non',    'label' => "Pas besoin",               'sub' => "On s'occupe de l'intérieur", 'why' => "et l'extérieur n'est pas nécessaire"),
            array('key' => 'lavage', 'label' => 'Oui, un lavage complet',   'sub' => 'Carrosserie, jantes, vitres', 'why' => "et vous voulez aussi l'extérieur"),
            array('key' => 'neuf',   'label' => 'Comme neuve',              'sub' => 'Revente ou fin de leasing',  'why' => 'et vous voulez une voiture comme neuve'),
        ),
        'grid' => array(
            'propre'   => array('non' => $g('ie', 'shampoing'), 'lavage' => $g('ie', 'ran_full'),        'neuf' => $g('ran_full', 'ie')),
            'taches'   => array('non' => $g('shampoing', 'ran_int'), 'lavage' => $g('ran_full', 'shampoing'), 'neuf' => $g('ran_full', 'ran_int')),
            'sale'     => array('non' => $g('ran_int', 'shampoing'), 'lavage' => $g('ran_full', 'ran_int'),   'neuf' => $g('ran_full', 'ran_int')),
            'encrasse' => array('non' => $g('ran_int', 'ran_full'),  'lavage' => $g('ran_full', 'ran_int'),   'neuf' => $g('ran_full', 'ran_int')),
        ),
        'txt' => array(
            'bot'  => 'Keepnew',
            'q1'   => 'Bonjour ! Pour commencer, quel véhicule souhaitez-vous faire nettoyer ?',
            'q2'   => "Très bien. Dans quel état est l'intérieur, et surtout les sièges ?",
            'q3'   => "Et l'extérieur ?",
            'q4'   => 'Merci ! Voici ce que je vous recommande :',
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

/** Normalise une configuration (y compris les anciens formats). */
function knCaNormalize($cfg): array
{
    $def = knCaDefaultConfig();
    if (!is_array($cfg)) $cfg = array();

    // Formules
    $formulas = array(); $seen = array();
    foreach ((isset($cfg['formulas']) && is_array($cfg['formulas']) && $cfg['formulas'] ? $cfg['formulas'] : $def['formulas']) as $i => $f) {
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
        $formulas[] = array(
            'key' => $key, 'name' => knCaStr($f['name'], 80),
            'badge' => knCaStr(isset($f['badge']) ? $f['badge'] : '', 40),
            'tagline' => knCaStr(isset($f['tagline']) ? $f['tagline'] : '', 200),
            'points' => knCaList(isset($f['points']) ? $f['points'] : array()),
            'duration' => knCaStr(isset($f['duration']) ? $f['duration'] : '', 40),
            'cta_url' => knCaStr(isset($f['cta_url']) ? $f['cta_url'] : '', 300),
            'prices' => $prices,
        );
    }
    if (!$formulas) return knCaNormalize($def);
    $fKeys = array_column($formulas, 'key');

    // Suppléments
    $extras = array(); $seen = array();
    $defExtras = array(); foreach ($def['extras'] as $d) $defExtras[$d['key']] = $d;
    foreach ((isset($cfg['extras']) && is_array($cfg['extras']) ? $cfg['extras'] : $def['extras']) as $i => $e) {
        if (!is_array($e) || knCaStr(isset($e['label']) ? $e['label'] : '') === '') continue;
        $key = knCaKey(isset($e['key']) && $e['key'] !== '' ? $e['key'] : $e['label']);
        if ($key === '' || isset($seen[$key])) $key = 'supplement_' . ($i + 1);
        $seen[$key] = true;
        $atelier = isset($e['atelier']) ? !empty($e['atelier']) : (!empty($e['atelier_only']) && (string) $e['atelier_only'] !== '0');
        $needsExt = isset($e['needs_exterior']) ? !empty($e['needs_exterior']) : (isset($defExtras[$key]) ? $defExtras[$key]['needs_exterior'] : $atelier);
        $extras[] = array(
            'key' => $key, 'label' => knCaStr($e['label'], 80),
            'desc' => knCaStr(isset($e['desc']) ? $e['desc'] : '', 200),
            'icon' => knCaKey(isset($e['icon']) && $e['icon'] !== '' ? $e['icon'] : 'sparkles') ?: 'sparkles',
            'price' => knCaPrice(isset($e['price']) ? $e['price'] : null),
            'atelier' => $atelier, 'needs_exterior' => $needsExt,
            'url' => knCaStr(isset($e['url']) ? $e['url'] : '', 300),
            'included_in' => isset($e['included_in']) && is_array($e['included_in']) ? array_values(array_intersect($fKeys, $e['included_in'])) : array(),
        );
    }
    $eKeys = array_column($extras, 'key');

    // États de l'intérieur et choix pour l'extérieur : clés fixes, textes éditables
    $pick = function ($list, $defList, $withExtras) use ($eKeys) {
        $byKey = array();
        if (is_array($list)) foreach ($list as $it) if (is_array($it) && isset($it['key'])) $byKey[$it['key']] = $it;
        $out = array();
        foreach ($defList as $d) {
            $it = isset($byKey[$d['key']]) ? $byKey[$d['key']] : $d;
            $row = array('key' => $d['key']);
            foreach (array('label' => 60, 'sub' => 80, 'why' => 200) as $f => $max) {
                $v = isset($it[$f]) ? knCaStr($it[$f], $max) : '';
                $row[$f] = $v !== '' ? $v : ($f === 'sub' && isset($it[$f]) ? '' : $d[$f]);
            }
            if ($withExtras) {
                $src = isset($it['extras']) && is_array($it['extras']) ? $it['extras'] : (isset($byKey[$d['key']]) ? array() : $d['extras']);
                $row['extras'] = array_values(array_intersect($eKeys, $src));
            }
            $out[] = $row;
        }
        return $out;
    };
    $interior = $pick(isset($cfg['interior']) ? $cfg['interior'] : null, $def['interior'], true);
    $exterior = $pick(isset($cfg['exterior']) ? $cfg['exterior'] : null, $def['exterior'], false);

    // Tableau état × extérieur → formule recommandée + alternative
    $valid = function ($k, $fallback, $allowEmpty) use ($fKeys) {
        $k = (string) $k;
        if ($allowEmpty && $k === '') return '';
        if (in_array($k, $fKeys, true)) return $k;
        if (in_array($fallback, $fKeys, true)) return $fallback;
        return $allowEmpty ? '' : $fKeys[0];
    };
    $grid = array();
    foreach (knCaInteriorKeys() as $in) foreach (knCaExteriorKeys() as $ex) {
        $d = $def['grid'][$in][$ex];
        $c = isset($cfg['grid'][$in][$ex]) && is_array($cfg['grid'][$in][$ex]) ? $cfg['grid'][$in][$ex] : $d;
        $rec = $valid(isset($c['rec']) ? $c['rec'] : '', $d['rec'], false);
        $alt = $valid(isset($c['alt']) ? $c['alt'] : $d['alt'], isset($c['alt']) ? '' : $d['alt'], true);
        if ($alt === $rec) $alt = '';
        $grid[$in][$ex] = array('rec' => $rec, 'alt' => $alt);
    }

    // Textes (ancien parcours en 7 étapes : seules les clés communes sont reprises)
    $srcTxt = isset($cfg['txt']) && is_array($cfg['txt']) ? $cfg['txt'] : array();
    if (!isset($cfg['interior'])) {
        $old = $srcTxt; $srcTxt = array();
        foreach (array('bot', 'q1', 'cta', 'note') as $k) if (isset($old[$k])) $srcTxt[$k] = $old[$k];
        if (isset($old['q5'])) $srcTxt['q4'] = $old['q5'];
    }
    $txt = array();
    foreach ($def['txt'] as $k => $d) {
        $v = isset($srcTxt[$k]) ? knCaStr($srcTxt[$k], 300) : '';
        $txt[$k] = $v !== '' ? $v : $d;
    }

    return array('formulas' => $formulas, 'extras' => $extras, 'interior' => $interior, 'exterior' => $exterior, 'grid' => $grid, 'txt' => $txt);
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
        $map = array('q_vehicle' => 'q1', 'q_reco' => 'q4', 'cta_label' => 'cta', 'note' => 'note', 'bot_name' => 'bot');
        foreach ($map as $old => $new) if (!empty($legacy[$old])) $cfg['txt'][$new] = $legacy[$old];
        return knCaNormalize($cfg);
    }
    return knCaNormalize(knCaDefaultConfig());
}

}
