<?php use App\Core\Auth; ?>
<div class="row justify-content-center mt-5">
    <div class="col-12 col-sm-8 col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <i class="bi bi-shop fs-1 text-primary"></i>
            <h1 class="h4 mt-2">Comércios Locais</h1>
            <p class="text-muted">Faça login para continuar</p>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="post" action="/login" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= e(Auth::csrfToken()) ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($username) ?>" required autofocus autocomplete="username">
                        <div class="invalid-feedback">Informe o usuário.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password"
                               required autocomplete="current-password">
                        <div class="invalid-feedback">Informe a senha.</div>
                    </div>
                    <button class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
