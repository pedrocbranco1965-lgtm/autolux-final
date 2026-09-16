<?php
function utilizador_atual(): ?array
{
    return $_SESSION['funcionario'] ?? null;
}

function exigir_login(): void
{
    if (utilizador_atual() === null) {
        redirecionar('login.php');
    }
}

function autenticar(string $username, string $password): bool
{
    $stmt = Database::vendas()->prepare(
        'SELECT id, nome, username, password_hash FROM funcionarios WHERE username = :u LIMIT 1'
    );
    $stmt->execute(['u' => $username]);
    $func = $stmt->fetch();
    if (!$func || !password_verify($password, $func['password_hash'])) {
        return false;
    }
    $_SESSION['funcionario'] = [
        'id' => (int) $func['id'],
        'nome' => $func['nome'],
        'username' => $func['username'],
    ];
    return true;
}

function terminar_sessao(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
