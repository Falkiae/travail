<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Formulaire de contact public (bloc « contact »).
 * Protections : même origine, jeton CSRF dédié, champ piège, délai minimum
 * signé, limite d'envois par IP, validation stricte, anti injection d'en-têtes.
 */
class ContactController extends BaseController
{
    private const MIN_SECONDS      = 3;
    private const MAX_SECONDS      = 43200;
    private const MAX_PER_IP_HOUR  = 5;
    private const MAX_PER_DAY      = 100;

    // Jeton de session propre au formulaire (distinct du jeton admin).
    public static function token(): string
    {
        if (empty($_SESSION['contact_csrf']) || !is_string($_SESSION['contact_csrf'])) {
            $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['contact_csrf'];
    }

    // Horodatage signé, rendu dans le formulaire pour mesurer le temps de remplissage.
    public static function stamp(): array
    {
        $ts = (string) time();
        return array('ts' => $ts, 'sig' => hash_hmac('sha256', $ts, self::token()));
    }

    // Le déploiement se fait par git sans migration : table créée à la demande.
    public static function ensureTable(\PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `kn_contact_messages` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `email` varchar(190) NOT NULL,
              `phone` varchar(30) DEFAULT NULL,
              `subject` varchar(80) DEFAULT NULL,
              `message` text NOT NULL,
              `page_url` varchar(255) DEFAULT NULL,
              `ip_hash` char(64) DEFAULT NULL,
              `mail_sent` tinyint NOT NULL DEFAULT 0,
              `status` enum('new','done') NOT NULL DEFAULT 'new',
              `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `idx_ip_time` (`ip_hash`,`created_at`),
              KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function wantsJson(): bool
    {
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? (string) $_SERVER['HTTP_ACCEPT'] : '';
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || strpos($accept, 'application/json') !== false;
    }

    private function host(): string
    {
        $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
        return strtolower(preg_replace('/:\d+$/', '', $host));
    }

    private function sameOrigin(): bool
    {
        $host = $this->host();
        if ($host === '') return false;
        $src = isset($_SERVER['HTTP_ORIGIN']) ? (string) $_SERVER['HTTP_ORIGIN'] : (isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '');
        if ($src === '') return false;
        $h = parse_url($src, PHP_URL_HOST);
        return is_string($h) && strtolower($h) === $host;
    }

    // Retour : JSON si appel AJAX, sinon redirection vers la page d'origine.
    private function reply(bool $ok, array $data, int $status = 200): void
    {
        if ($this->wantsJson()) {
            $this->jsonResponse(array_merge(array('ok' => $ok), $data), $status);
        }
        $back = '/';
        $ref  = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
        if ($ref !== '' && ($h = parse_url($ref, PHP_URL_HOST)) && strtolower((string) $h) === $this->host()) {
            $path  = (string) parse_url($ref, PHP_URL_PATH);
            $back  = ($path !== '' ? $path : '/');
        }
        $this->redirect($back . '?contact=' . ($ok ? 'ok' : 'err') . '#contact');
    }

    private function clean(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);
        return trim(mb_substr((string) $value, 0, $max));
    }

    public function send(): void
    {
        if (!$this->sameOrigin()) {
            $this->reply(false, array('error' => 'Requête refusée.'), 403);
        }

        $token = isset($_POST['_token']) ? (string) $_POST['_token'] : '';
        $sess  = isset($_SESSION['contact_csrf']) ? (string) $_SESSION['contact_csrf'] : '';
        if ($sess === '' || !hash_equals($sess, $token)) {
            $this->reply(false, array('error' => 'Session expirée : rechargez la page puis réessayez.'), 403);
        }

        // Robots : champ piège rempli, horodatage falsifié ou envoi trop rapide/trop ancien.
        // Réponse de succès factice : on n'apprend rien aux robots, rien n'est enregistré.
        $ts  = isset($_POST['ts'])  ? (string) $_POST['ts']  : '';
        $sig = isset($_POST['sig']) ? (string) $_POST['sig'] : '';
        $age = ctype_digit($ts) ? time() - (int) $ts : -1;
        $okSig = $sig !== '' && hash_equals(hash_hmac('sha256', $ts, $sess), $sig);
        if (!empty($_POST['website']) || !$okSig || $age < self::MIN_SECONDS || $age > self::MAX_SECONDS) {
            $this->reply(true, array('message' => 'ok'));
        }

        $pdo = $this->db();
        self::ensureTable($pdo);

        $ip     = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $ipHash = hash('sha256', $ip . '|kn_contact|' . (defined('DB_NAME') ? DB_NAME : ''));

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kn_contact_messages WHERE ip_hash = ? AND created_at >= ?");
        $stmt->execute(array($ipHash, date('Y-m-d H:i:s', time() - 3600)));
        $perIp = (int) $stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kn_contact_messages WHERE created_at >= ?");
        $stmt->execute(array(date('Y-m-d H:i:s', time() - 86400)));
        $perDay = (int) $stmt->fetchColumn();
        if ($perIp >= self::MAX_PER_IP_HOUR || $perDay >= self::MAX_PER_DAY) {
            $this->reply(false, array('error' => 'Vous avez envoyé plusieurs messages à la suite. Merci de réessayer un peu plus tard, ou appelez-nous directement.'), 429);
        }

        $name    = $this->clean(isset($_POST['name']) ? (string) $_POST['name'] : '', 100);
        $email   = trim(isset($_POST['email']) ? (string) $_POST['email'] : '');
        $phone   = $this->clean(isset($_POST['phone']) ? (string) $_POST['phone'] : '', 30);
        $subject = $this->clean(isset($_POST['subject']) ? (string) $_POST['subject'] : '', 80);
        $message = isset($_POST['message']) ? trim(str_replace("\0", '', (string) $_POST['message'])) : '';
        $page    = $this->clean(isset($_POST['page']) ? (string) $_POST['page'] : '', 255);

        $errors = array();
        if (mb_strlen($name) < 2) $errors['name'] = 'Indiquez votre nom.';
        if ($email === '' || strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Indiquez une adresse e-mail valide.';
        if ($phone !== '' && !preg_match('/^[0-9 +().\-]{6,30}$/', $phone)) $errors['phone'] = 'Numéro de téléphone invalide.';
        $len = mb_strlen($message);
        if ($len < 10) $errors['message'] = 'Décrivez votre demande en quelques mots (10 caractères minimum).';
        if ($len > 3000) $errors['message'] = 'Votre message est trop long (3000 caractères maximum).';
        if (empty($_POST['consent'])) $errors['consent'] = 'Merci de cocher la case pour que nous puissions vous répondre.';
        if ($errors) {
            $this->reply(false, array('errors' => $errors, 'error' => 'Merci de vérifier les champs signalés.'), 422);
        }
        if ($subject === '') $subject = 'Autre';

        $pdo->prepare(
            "INSERT INTO kn_contact_messages (name, email, phone, subject, message, page_url, ip_hash, mail_sent, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'new', ?)"
        )->execute(array($name, $email, $phone === '' ? null : $phone, $subject, $message, $page === '' ? null : $page, $ipHash, date('Y-m-d H:i:s')));
        $id = (int) $pdo->lastInsertId();

        $sent = $this->notify($pdo, $name, $email, $phone, $subject, $message, $page);
        if ($sent) {
            $pdo->prepare("UPDATE kn_contact_messages SET mail_sent = 1 WHERE id = ?")->execute(array($id));
        }

        $this->reply(true, array('message' => 'ok'));
    }

    private function notify(\PDO $pdo, string $name, string $email, string $phone, string $subject, string $message, string $page): bool
    {
        try {
            $rows = $pdo->query("SELECT `key`, `value` FROM kn_settings WHERE `key` IN ('contact_notify_email','contact_from_email','site_name')")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return false;
        }
        $set = array();
        foreach ($rows as $r) $set[$r['key']] = (string) $r['value'];

        $to = isset($set['contact_notify_email']) ? trim($set['contact_notify_email']) : '';
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $site = isset($set['site_name']) && $set['site_name'] !== '' ? $set['site_name'] : 'Keepnew';
        $site = trim(preg_replace('/[\x00-\x1F\x7F"<>]+/u', ' ', $site));
        $from = isset($set['contact_from_email']) ? trim($set['contact_from_email']) : '';
        if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $h = preg_replace('/[^a-z0-9.\-]/', '', $this->host());
            $from = 'no-reply@' . ($h !== '' ? $h : 'localhost');
        }

        $subjectLine = mb_encode_mimeheader('[' . $site . '] Nouveau message : ' . $subject, 'UTF-8', 'B', "\r\n");
        $headers = array(
            'From: ' . mb_encode_mimeheader($site, 'UTF-8', 'B', "\r\n") . ' <' . $from . '>',
            'Reply-To: ' . $email,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: Keepnew-contact',
        );
        $body = "Nouveau message reçu via le formulaire de contact\r\n\r\n"
              . 'Nom : ' . $name . "\r\n"
              . 'E-mail : ' . $email . "\r\n"
              . 'Téléphone : ' . ($phone !== '' ? $phone : '—') . "\r\n"
              . 'Sujet : ' . $subject . "\r\n"
              . 'Page : ' . ($page !== '' ? $page : '—') . "\r\n\r\n"
              . str_replace(array("\r\n", "\r"), "\n", $message) . "\r\n\r\n"
              . "—\r\nVous pouvez répondre directement à cet e-mail. Le message est aussi conservé dans l'admin (Messages).";

        return (bool) @mail($to, $subjectLine, $body, implode("\r\n", $headers));
    }
}
