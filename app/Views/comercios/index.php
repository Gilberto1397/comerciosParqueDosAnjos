<?php
use App\Core\Auth;
/** @var App\Entities\Comercio[] $items */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Comércios locais</h1>
    <span class="text-muted small"><?= (int) $total ?> cadastrado(s)</span>
</div>

<?php if (!$items): ?>
    <div class="card card-body text-center py-5">
        <p class="text-muted mb-3">Nenhum comércio cadastrado ainda.</p>
        <?php if (Auth::check()): ?>
            <div><a href="/comercios/novo" class="btn btn-primary">Cadastrar o primeiro</a></div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="list-group shadow-sm mb-4">
        <?php foreach ($items as $c): ?>
            <a href="/comercios/<?= (int) $c->id ?>"
               class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                <img src="<?= e(App\Core\Config::get('upload_url') . '/' . $c->imagemCapa) ?>"
                     alt="" width="64" height="64" class="rounded object-fit-cover flex-shrink-0">
                <span class="fs-5 fw-semibold flex-grow-1"><?= e($c->titulo) ?></span>
                <?php if ($c->pertenceA(Auth::user())): ?>
                    <span class="badge text-bg-success">Seu comércio</span>
                <?php endif; ?>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
    <nav aria-label="Paginação">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="/comercios?page=<?= $page - 1 ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="/comercios?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                <a class="page-link" href="/comercios?page=<?= $page + 1 ?>">Próxima</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
<?php endif; ?>
