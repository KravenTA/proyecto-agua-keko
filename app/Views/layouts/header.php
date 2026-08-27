<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?= esc($title ?? 'Oficina del Agua') ?></title>

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <!-- Iconos Nucleo (vienen con Soft UI Dashboard) -->
    <link href="<?= base_url('assets/css/nucleo-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/nucleo-svg.css') ?>" rel="stylesheet">

    <!-- CSS principal de Soft UI Dashboard -->
    <link id="pagestyle" href="<?= base_url('assets/css/soft-ui-dashboard.css') ?>" rel="stylesheet">
</head>
<body class="<?= $body_class ?? 'bg-gray-100' ?>">