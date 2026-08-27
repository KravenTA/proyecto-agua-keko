<?= view('layouts/header', ['title' => 'Iniciar sesion - Oficina del Agua', 'body_class' => '']) ?>

<main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100"
         style="background-image: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container my-auto">
            <div class="row">
                <div class="col-lg-4 col-md-8 col-12 mx-auto">
                    <div class="card z-index-0 fadeIn3 fadeInBottom">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1"
                                 style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                    Oficina del Agua
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')) : ?>
                                <div class="alert alert-danger text-white" role="alert">
                                    <?= esc(session()->getFlashdata('error')) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('mensaje')) : ?>
                                <div class="alert alert-success text-white" role="alert">
                                    <?= esc(session()->getFlashdata('mensaje')) ?>
                                </div>
                            <?php endif; ?>

                            <form role="form" action="<?= base_url('login') ?>" method="post" class="text-start">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label ms-1">Correo</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= old('email') ?>" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label ms-1">Contrasena</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="text-center">
                                    <button type="submit"
                                        class="btn btn-lg w-100 mt-4 mb-0 text-white"
                                        style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                        Iniciar sesion
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>