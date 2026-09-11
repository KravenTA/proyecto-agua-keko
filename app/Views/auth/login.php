<?= view('layouts/header', ['title' => 'Iniciar sesion - Oficina del Agua', 'body_class' => '']) ?>

<style>
    .pantalla-login {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #04263D;
    }

    .paneles {
        display: grid;
        justify-items: center;
        width: 100%;
        max-width: 420px;
        padding: 0 1rem;
    }

    .panel-bienvenida,
    .panel-form {
        grid-area: 1 / 1;
        width: 100%;
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .panel-bienvenida {
        text-align: center;
    }

    .panel-bienvenida img {
        max-width: 280px;
        width: 75%;
        animation: flotar 3s ease-in-out infinite;
    }

    @keyframes flotar {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-10px); }
    }

    .panel-bienvenida h1 {
        color: #fff;
        font-weight: 600;
        margin: 1rem 0 0.25rem;
        animation: aparecer 0.8s ease 0.2s both;
    }

    .panel-bienvenida p {
        color: rgba(255,255,255,0.85);
        margin-bottom: 2rem;
        animation: aparecer 0.8s ease 0.4s both;
    }

    .panel-bienvenida .btn-comenzar {
        background: #FFD500;
        color: #332B00;
        border: none;
        font-weight: 600;
        padding: 0.85rem 2.5rem;
        border-radius: 50px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        animation: aparecer 0.8s ease 0.6s both;
    }

    .panel-bienvenida .btn-comenzar:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.25);
    }

    @keyframes aparecer {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .oculto {
        opacity: 0 !important;
        transform: translateY(-15px) scale(0.97) !important;
        pointer-events: none !important;
    }

    .invisible {
        opacity: 0;
        transform: translateY(20px) scale(0.97);
        pointer-events: none;
    }

    .visible {
        opacity: 1 !important;
        transform: translateY(0) scale(1) !important;
        pointer-events: auto !important;
    }

    .mensaje-flash {
        border: none;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 14px;
        margin-bottom: 1rem;
    }

    .mensaje-flash.error {
        background: rgba(220, 53, 69, 0.15);
        color: #fff;
        border-left: 4px solid #FF6B6B;
    }

    .mensaje-flash.exito {
        background: rgba(255, 213, 0, 0.15);
        color: #04263D;
        border-left: 4px solid #FFD500;
    }
</style>

<div class="pantalla-login">
    <div class="paneles">

        <div class="panel-bienvenida visible" id="panelBienvenida">
            <img src="<?= base_url('assets/img/logo_keko.png') ?>" alt="Keko">
            <h1>Bienvenido a Keko</h1>
            <p>Sistema de Gestion de la Oficina del Agua</p>
            <button type="button" class="btn btn-comenzar" id="btnComenzar">Iniciar sesion</button>
        </div>

        <div class="panel-form invisible" id="panelForm">
            <div class="card z-index-0">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="shadow-primary border-radius-lg py-3 pe-1"
                         style="background: #35A0D8;">
                        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                            Oficina del Agua
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="mensaje-flash error">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('mensaje')) : ?>
                        <div class="mensaje-flash exito">
                            <?= esc(session()->getFlashdata('mensaje')) ?>
                        </div>
                    <?php endif; ?>

                    <form role="form" action="<?= base_url('login') ?>" method="post" class="text-start" id="formLogin">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label ms-1">Correo</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= old('email') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label ms-1">Contrasena</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="text-center">
                            <button type="submit"
                                class="btn btn-lg w-100 mt-4 mb-0"
                                style="background: #FFD500; color: #332B00;">
                                Iniciar sesion
                            </button>
                            <button type="button" class="btn btn-link text-white mt-2" id="btnVolver">
                                Volver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    const bienvenida = document.getElementById('panelBienvenida');
    const formPanel   = document.getElementById('panelForm');
    const huboError = <?= (session()->getFlashdata('error') || session()->getFlashdata('mensaje')) ? 'true' : 'false' ?>;

    function mostrarForm() {
        bienvenida.classList.remove('visible');
        bienvenida.classList.add('oculto');
        formPanel.classList.remove('invisible');
        formPanel.classList.add('visible');
        setTimeout(function () {
            document.querySelector('input[name="email"]').focus();
        }, 300);
    }

    function mostrarBienvenida() {
        formPanel.classList.remove('visible');
        formPanel.classList.add('invisible');
        bienvenida.classList.remove('oculto');
        bienvenida.classList.add('visible');
    }

    document.getElementById('btnComenzar').addEventListener('click', mostrarForm);
    document.getElementById('btnVolver').addEventListener('click', mostrarBienvenida);

    if (huboError) {
        mostrarForm();
    }
})();
</script>

<?= view('layouts/footer') ?>