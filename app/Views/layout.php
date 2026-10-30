<?php use App\Core\Auth; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Comércios locais') ?> · Comércios Locais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php if (Auth::check()): ?>
<nav class="navbar navbar-expand-lg bg-white border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><i class="bi bi-shop"></i> Comércios Locais</a>
        <div class="d-flex align-items-center gap-2">
            <a href="/comercios/novo" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Novo comércio</a>
            <span class="text-muted small d-none d-sm-inline"><?= e(Auth::user()) ?></span>
            <form method="post" action="/logout" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(Auth::csrfToken()) ?>">
                <button class="btn btn-outline-secondary btn-sm">Sair</button>
            </form>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="container pb-5">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    <?= $content ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
