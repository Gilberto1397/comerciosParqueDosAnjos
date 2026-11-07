<?php
use App\Core\Auth;
use App\Entities\Comercio;

$comercio = $comercio ?? null;
$isEdit = $comercio !== null;
$action = $isEdit ? '/comercios/' . (int) $comercio->id : '/comercios';
$old = $old ?? [];
$diasMarcados = (array) ($old['dias'] ?? []);
$err = fn (string $k) => isset($errors[$k]) ? e($errors[$k]) : null;
$invalid = fn (string $k) => isset($errors[$k]) ? ' is-invalid' : '';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-3"><?= $isEdit ? 'Editar comércio' : 'Novo comércio' ?></h1>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data"
                      class="needs-validation" id="form-comercio" data-edit="<?= $isEdit ? '1' : '0' ?>" novalidate>
                    <input type="hidden" name="_csrf" value="<?= e(Auth::csrfToken()) ?>">

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control<?= $invalid('titulo') ?>" id="titulo" name="titulo"
                               value="<?= e($old['titulo'] ?? '') ?>" minlength="3" maxlength="100" required>
                        <div class="invalid-feedback"><?= $err('titulo') ?? 'Informe um título (3 a 100 caracteres).' ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control<?= $invalid('descricao') ?>" id="descricao" name="descricao"
                                  rows="5" minlength="10" maxlength="2000" required><?= e($old['descricao'] ?? '') ?></textarea>
                        <div class="d-flex justify-content-between">
                            <div class="invalid-feedback"><?= $err('descricao') ?? 'Informe uma descrição (mín. 10 caracteres).' ?></div>
                            <small class="text-muted ms-auto"><span id="contador">0</span>/2000</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label">Dias de atendimento</label>
                            <button type="button" class="btn btn-link btn-sm p-0" id="btn-todos">Marcar todos</button>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach (Comercio::DIAS as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input dia-check" type="checkbox" name="dias[]"
                                           value="<?= e($key) ?>" id="dia-<?= e($key) ?>"
                                           <?= in_array($key, $diasMarcados, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="dia-<?= e($key) ?>"><?= e($label) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-danger small mt-1 <?= isset($errors['dias']) ? '' : 'd-none' ?>" id="erro-dias">
                            <?= $err('dias') ?? 'Selecione ao menos um dia.' ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="horario_abertura" class="form-label">Abre às</label>
                            <input type="time" class="form-control<?= $invalid('horario') ?>" id="horario_abertura"
                                   name="horario_abertura" value="<?= e($old['horario_abertura'] ?? '') ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="horario_fechamento" class="form-label">Fecha às</label>
                            <input type="time" class="form-control<?= $invalid('horario') ?>" id="horario_fechamento"
                                   name="horario_fechamento" value="<?= e($old['horario_fechamento'] ?? '') ?>" required>
                        </div>
                        <div class="text-danger small <?= isset($errors['horario']) ? '' : 'd-none' ?>" id="erro-horario">
                            <?= $err('horario') ?? 'O horário de abertura deve ser anterior ao de fechamento.' ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="imagem_capa" class="form-label">
                            Imagem de capa<?= $isEdit ? ' <small class="text-muted">(opcional: envie apenas para trocar)</small>' : '' ?>
                        </label>
                        <?php if ($isEdit): ?>
                            <div class="mb-2">
                                <img src="<?= e(App\Core\Config::get('upload_url') . '/' . $comercio->imagemCapa) ?>"
                                     class="img-fluid rounded" style="max-height:160px" alt="Capa atual">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control<?= $invalid('imagem_capa') ?>" id="imagem_capa"
                               name="imagem_capa" accept="image/jpeg,image/png,image/webp" <?= $isEdit ? '' : 'required' ?>>
                        <div class="invalid-feedback"><?= $err('imagem_capa') ?? 'Envie uma imagem JPG, PNG ou WEBP de até 2 MB.' ?></div>
                        <img id="preview" class="img-fluid rounded mt-3 d-none" style="max-height:220px" alt="Pré-visualização">
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><?= $isEdit ? 'Salvar alterações' : 'Salvar' ?></button>
                        <a href="<?= $isEdit ? '/comercios/' . (int) $comercio->id : '/comercios' ?>"
                           class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
