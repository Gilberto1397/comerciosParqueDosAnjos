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
            (titulo, descricao, dias_atendimento, horario_abertura, horario_fechamento, imagem_capa)
            VALUES (:titulo, :descricao, :dias, :abertura, :fechamento, :imagem)');
        $stmt->execute([
            ':titulo'     => $c->titulo,
            ':descricao'  => $c->descricao,
            ':dias'       => implode(',', $c->diasAtendimento),
            ':abertura'   => $c->horarioAbertura,
            ':fechamento' => $c->horarioFechamento,
            ':imagem'     => $c->imagemCapa,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
