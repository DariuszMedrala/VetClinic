<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected View $view;
    protected Session $session;
    protected Auth $auth;

    public function __construct()
    {
        $this->view = new View(APP_ROOT . '/src/Views');
        $this->session = new Session();
        $this->session->start();
        $this->auth = new Auth($this->session);
    }

    protected function view(string $template, array $data = [], ?string $layout = null): Response
    {
        return (new Response())->html($this->view->render($template, $data, $layout));
    }

    protected function json(array $data, int $status = 200): Response
    {
        return (new Response())->status($status)->json($data);
    }

    protected function redirect(string $location): Response
    {
        return (new Response())->redirect($location);
    }
}
