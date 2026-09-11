<?php

namespace App\Controllers;

class Equipo extends BaseController
{
    public function index()
    {
        $integrantes = [
            [
                'nombre'    => 'Yourgen Thommel',
                'rol'       => 'Coordinador',
                'iniciales' => 'YT',
                'foto'      => 'yourgen.jpg',
            ],
            [
                'nombre'    => 'Karen Jiménez',
                'rol'       => 'Desarrolladora',
                'iniciales' => 'KJ',
                // Para poner su foto, guarda el archivo en public/assets/img/equipo/
                // y escribe aqui el nombre exacto, ej: 'karen.jpg'
                'foto'      => 'karen.jpg',
            ],
            [
                'nombre'    => 'Oliver Godoy',
                'rol'       => 'Desarrollador',
                'iniciales' => 'OG',
                'foto'      => 'oliver.jpg',
            ],
            [
                'nombre'    => 'Enner Godoy',
                'rol'       => 'Desarrollador',
                'iniciales' => 'EG',
                'foto'      => 'enner.jpg',
            ],
        ];

        return view('equipo/index', [
            'title'       => 'Equipo - Oficina del Agua',
            'body_class'  => 'g-sidenav-show bg-gray-100',
            'integrantes' => $integrantes,
        ]);
    }
}