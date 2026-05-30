<?php
return [
    'GET /'                           => ['FrontController', 'home'],
    'GET /blog'                       => ['FrontController', 'blogList'],
    'GET /blog/{slug}'                => ['FrontController', 'blogPost'],
    'GET /portfolio'                  => ['FrontController', 'portfolioList'],
    'GET /portfolio/{slug}'           => ['FrontController', 'portfolioItem'],

    'GET /admin'                      => ['AdminController', 'dashboard'],
    'GET /admin/login'                => ['AdminController', 'loginForm'],
    'POST /admin/login'               => ['AdminController', 'loginPost'],
    'GET /admin/logout'               => ['AdminController', 'logout'],

    'GET /admin/pages'                => ['PageController', 'index'],
    'GET /admin/pages/new'            => ['PageController', 'create'],
    'POST /admin/pages/new'           => ['PageController', 'store'],
    'GET /admin/pages/{id}/edit'      => ['PageController', 'edit'],
    'POST /admin/pages/{id}/edit'     => ['PageController', 'update'],
    'POST /admin/pages/{id}/delete'   => ['PageController', 'delete'],

    'GET /admin/posts'                => ['PostController', 'index'],
    'GET /admin/posts/new'            => ['PostController', 'create'],
    'POST /admin/posts/new'           => ['PostController', 'store'],
    'GET /admin/posts/{id}/edit'      => ['PostController', 'edit'],
    'POST /admin/posts/{id}/edit'     => ['PostController', 'update'],
    'POST /admin/posts/{id}/delete'   => ['PostController', 'delete'],

    'GET /admin/projects'             => ['ProjectController', 'index'],
    'GET /admin/projects/new'         => ['ProjectController', 'create'],
    'POST /admin/projects/new'        => ['ProjectController', 'store'],
    'GET /admin/projects/{id}/edit'   => ['ProjectController', 'edit'],
    'POST /admin/projects/{id}/edit'  => ['ProjectController', 'update'],
    'POST /admin/projects/{id}/delete'=> ['ProjectController', 'delete'],

    // Doit être en dernier (catch-all pour les slugs de pages)
    'GET /{slug}'                     => ['FrontController', 'page'],
];
