<?php
declare(strict_types=1);
namespace App\Entities;

final class Comercio
{
    public const DIAS = [
        'seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta', 'qui' => 'Quinta',
        'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo',
    ];

    public function __construct(
        public ?int $id,
        public string $titulo,
        public string $descricao,
        public array $diasAtendimento,
        public string $horarioAbertura,
        public string $horarioFechamento,
        public string $imagemCapa,
        public ?string $usuario = null, // dono do comércio (login)
        public ?string $criadoEm = null,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            $row['titulo'],
            $row['descricao'],
            array_values(array_filter(explode(',', $row['dias_atendimento']))),
            $row['horario_abertura'],
            $row['horario_fechamento'],
            $row['imagem_capa'],
            $row['usuario'] ?? null,
            $row['criado_em'] ?? null,
        );
    }

    /** Somente o usuário que cadastrou o comércio é o dono. */
    public function pertenceA(?string $username): bool
    {
        return $username !== null && $this->usuario !== null && hash_equals($this->usuario, $username);
    }

    /** @return string[] Nomes legíveis dos dias de atendimento */
    public function diasLegiveis(): array
    {
        return array_map(fn ($d) => self::DIAS[$d] ?? $d, $this->diasAtendimento);
    }
}
