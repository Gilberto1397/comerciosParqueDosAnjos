<?php /** @var App\Entities\Comercio $comercio */ ?>
<a href="/" class="btn btn-link px-0 mb-2"><i class="bi bi-arrow-left"></i> Voltar</a>
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
