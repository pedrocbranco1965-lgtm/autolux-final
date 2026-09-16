<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `funcionarios` (utilizadores da aplicação). */
final class FuncionarioRepository
{
    public const PERFIS = ['admin' => 'Administrador', 'funcionario' => 'Funcionário'];

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    public function listar(): array
    {
        return $this->pdo->query(
            'SELECT f.*, COUNT(v.id) AS num_vendas
               FROM funcionarios f
               LEFT JOIN vendas v ON v.funcionario_id = f.id
              GROUP BY f.id
              ORDER BY f.nome'
        )->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Procura pelo nome de utilizador (usado no login). Inclui inativos: quem chama decide. */
    public function obterPorUtilizador(string $utilizador): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM funcionarios WHERE utilizador = :u');
        $stmt->execute(['u' => $utilizador]);
        return $stmt->fetch() ?: null;
    }

    /** @return array<string,string> erros por campo (vazio = OK) */
    public function validar(array $dados, bool $exigirPassword = true): array
    {
        $erros = [];
        if (mb_strlen(trim($dados['nome'] ?? '')) < 3) {
            $erros['nome'] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!preg_match('/^[a-z0-9._-]{3,40}$/i', $dados['utilizador'] ?? '')) {
            $erros['utilizador'] = 'Utilizador: 3 a 40 caracteres (letras, números, ponto, hífen ou underscore).';
        }
        if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Introduza um email válido.';
        }
        if (!isset(self::PERFIS[$dados['perfil'] ?? ''])) {
            $erros['perfil'] = 'Perfil inválido.';
        }
        if ($exigirPassword || ($dados['password'] ?? '') !== '') {
            $erroPw = self::validarPassword($dados['password'] ?? '');
            if ($erroPw) {
                $erros['password'] = $erroPw;
            }
        }
        return $erros;
    }

    public static function validarPassword(string $password): ?string
    {
        return mb_strlen($password) < 6 ? 'A password deve ter pelo menos 6 caracteres.' : null;
    }

    public function criar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO funcionarios (nome, utilizador, email, password_hash, perfil)
             VALUES (:nome, :utilizador, :email, :hash, :perfil)'
        );
        $stmt->execute([
            'nome'       => trim($dados['nome']),
            'utilizador' => strtolower(trim($dados['utilizador'])),
            'email'      => trim($dados['email']),
            // password_hash gera um hash bcrypt com salt aleatório; nunca guardamos a password em claro
            'hash'       => password_hash($dados['password'], PASSWORD_DEFAULT),
            'perfil'     => $dados['perfil'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function alterarPassword(int $id, string $novaPassword): void
    {
        $stmt = $this->pdo->prepare('UPDATE funcionarios SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => password_hash($novaPassword, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function alterarAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->pdo->prepare('UPDATE funcionarios SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => (int) $ativo, 'id' => $id]);
    }

    public function registarLogin(int $id): void
    {
        $this->pdo->prepare('UPDATE funcionarios SET ultimo_login = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function contarAdminsAtivos(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM funcionarios WHERE perfil = 'admin' AND ativo = 1")->fetchColumn();
    }
}
