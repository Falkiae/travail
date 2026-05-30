<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    private $templateContent = '';

    public function render(string $template, array $data = [], string $layout = 'public'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        $tplFile = APP_PATH . '/views/templates/' . $template . '.php';
        if (file_exists($tplFile)) {
            require $tplFile;
        } else {
            echo '<p>Template introuvable : ' . htmlspecialchars($template) . '</p>';
        }
        $this->templateContent = ob_get_clean();

        require APP_PATH . '/views/layouts/' . $layout . '.php';
    }

    public function content(): void
    {
        echo $this->templateContent;
    }

    public static function escape(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
