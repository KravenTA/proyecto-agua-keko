<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        log_message('error', 'Prueba manual de log a base de datos');

        return view('welcome_message');
    }
}
