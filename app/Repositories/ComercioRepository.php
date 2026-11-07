<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;
use App\Entities\Comercio;
use PDO;

final class ComercioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** @return Comercio[] */
    public function paginate(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare('SELECT * FROM comercios ORDER BY titulo COLLATE NOCASE LIMIT :l OFFSET :o');
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([Comercio::class, 'fromRow'], $stmt->fetchAll());
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM comercios')->fetchColumn();
    }

    public function find(int $id): ?Comercio
    {
        $stmt = $this->db->prepare('SELECT * FROM comercios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? Comercio::fromRow($row) : null;
    }

    public function create(Comercio $c): int
    {
        $stmt = $this->db->prepare('INSERT INTO comercios
            (titulo, descricao, dias_atendimento, horario_abertura, horario_fechamento, imagem_capa, usuario)
            VALUES (:titulo, :descricao, :dias, :abertura, :fechamento, :imagem, :usuario)');
        $stmt->execute([
            ':titulo'     => $c->titulo,
            ':descricao'  => $c->descricao,
            ':dias'       => implode(',', $c->diasAtendimento),
            ':abertura'   => $c->horarioAbertura,
            ':fechamento' => $c->horarioFechamento,
            ':imagem'     => $c->imagemCapa,
            ':usuario'    => $c->usuario,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Atualiza apenas se o comércio pertencer ao usuário (defesa extra no banco). */
    public function update(Comercio $c): bool
    {
        $stmt = $this->db->prepare('UPDATE comercios SET
                titulo = :titulo, descricao = :descricao, dias_atendimento = :dias,
                horario_abertura = :abertura, horario_fechamento = :fechamento, imagem_capa = :imagem
            WHERE id = :id AND usuario = :usuario');
        $stmt->execute([
            ':titulo'     => $c->titulo,
            ':descricao'  => $c->descricao,
            ':dias'       => implode(',', $c->diasAtendimento),
            ':abertura'   => $c->horarioAbertura,
            ':fechamento' => $c->horarioFechamento,
            ':imagem'     => $c->imagemCapa,
            ':id'         => $c->id,
            ':usuario'    => $c->usuario,
        ]);

        return $stmt->rowCount() === 1;
    }
}
