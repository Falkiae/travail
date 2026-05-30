<?php
declare(strict_types=1);

namespace App\Controllers;

class FrontController extends BaseController
{
    public function home(): void
    {
        $this->view->render('home', ['title' => 'Accueil']);
    }

    public function blogList(): void
    {
        $this->view->render('blog-list', ['title' => 'Blog']);
    }

    public function blogPost(string $slug): void
    {
        $this->view->render('blog-post', ['title' => $slug, 'slug' => $slug]);
    }

    public function portfolioList(): void
    {
        $this->view->render('portfolio', ['title' => 'Portfolio']);
    }

    public function portfolioItem(string $slug): void
    {
        $this->view->render('portfolio', ['title' => $slug, 'slug' => $slug]);
    }

    public function page(string $slug): void
    {
        $this->view->render('page', ['title' => $slug, 'slug' => $slug]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view->render('page', ['title' => 'Page introuvable']);
    }
}
