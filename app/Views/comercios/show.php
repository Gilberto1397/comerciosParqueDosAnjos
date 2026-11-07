<?php
use App\Core\Auth;
/** @var App\Entities\Comercio $comercio */
$ehDono = $comercio->pertenceA(Auth::user());
?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <a href="/comercios" class="btn btn-link px-0"><i class="bi bi-arrow-left"></i> Voltar</a>
    <?php if ($ehDono): ?>
        <a href="/comercios/<?= (int) $comercio->id ?>/editar" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil"></i> Editar
        </a>
    <?php endif; ?>
</div>
<div class="card shadow-sm overflow-hidden">
    <img src="<?= e(App\Core\Config::get('upload_url') . '/' . $comercio->imagemCapa) ?>"
         alt="Capa de <?= e($comercio->titulo) ?>" class="card-img-top object-fit-cover" style="max-height:360px">
    <div class="card-body p-4">
        <h1 class="h3"><?= e($comercio->titulo) ?></h1>

        <h2 class="h6 text-uppercase text-muted mt-4">Sobre</h2>
        <p><?= nl2br(e($comercio->descricao)) ?></p>

        <h2 class="h6 text-uppercase text-muted mt-4">Dias de atendimento</h2>
        <p>
            <?php foreach ($comercio->diasLegiveis() as $dia): ?>
                <span class="badge text-bg-primary me-1"><?= e($dia) ?></span>
            <?php endforeach; ?>
        </p>

        <h2 class="h6 text-uppercase text-muted mt-4">Horário</h2>
        <p class="mb-0"><i class="bi bi-clock"></i>
            <?= e($comercio->horarioAbertura) ?> às <?= e($comercio->horarioFechamento) ?></p>
    </div>
</div>
