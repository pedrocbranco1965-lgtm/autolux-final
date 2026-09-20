<?php
declare(strict_types=1);

namespace AutoLux;

use AutoLux\Repositorios\FuncionarioRepository;

/**
 * Autenticação de funcionários baseada em sessão PHP.
 *
 * Fluxo:
 *   login.php  -> Auth::entrar(utilizador, password)  -> guarda o id na sessão
 *   qualquer página -> Auth::exigirAutenticacao() (chamado no bootstrap)
 *   logout.php -> Auth::sair()
 *
 * Só o id do funcionário fica na sessão; os dados são relidos da BD em cada
 * pedido, para que desativar um funcionário tenha efeito imediato.
 */
final class Auth
{
    private const CHAVE_SESSAO = 'funcionario_id';

    /** Nº máximo de tentativas falhadas seguidas antes de bloquear temporariamente. */
    private const MAX_TENTATIVAS = 5;
    private const BLOQUEIO_SEGUNDOS = 60;

    private static ?array $utilizadorAtual = null;
    private static bool $carregado = false;

    /** Funcionário autenticado (array da tabela) ou null. */
    public static function utilizador(): ?array
    {
        if (!self::$carregado) {
            self::$carregado = true;
            $id = $_SESSION[self::CHAVE_SESSAO] ?? null;
            if ($id) {
                $f = (new FuncionarioRepository())->obter((int) $id);
                if ($f && $f['ativo']) {
                    self::$utilizadorAtual = $f;
                } else {
                    unset($_SESSION[self::CHAVE_SESSAO]); // conta apagada ou desativada entretanto
                }
            }
        }
        return self::$utilizadorAtual;
    }

    public static function autenticado(): bool
    {
        return self::utilizador() !== null;
    }

    public static function ehAdmin(): bool
    {
        return (self::utilizador()['perfil'] ?? '') === 'admin';
    }

    /**
     * Tenta autenticar. Devolve null em caso de sucesso ou a mensagem de erro.
     * A mensagem é propositadamente genérica para não revelar se o utilizador existe.
     */
    public static function entrar(string $utilizador, string $password): ?string
    {
        if (self::bloqueado()) {
            return 'Demasiadas tentativas falhadas. Aguarde ' . self::BLOQUEIO_SEGUNDOS . ' segundos e tente novamente.';
        }

        $repo = new FuncionarioRepository();
        $f = $repo->obterPorUtilizador(strtolower(trim($utilizador)));

        // password_verify compara a password com o hash bcrypt em tempo constante
        if (!$f || !password_verify($password, $f['password_hash'])) {
            self::registarFalha();
            return 'Utilizador ou password incorretos.';
        }
        if (!$f['ativo']) {
            return 'Esta conta está desativada. Contacte o administrador.';
        }

        // Novo id de sessão após login: evita ataques de "session fixation"
        session_regenerate_id(true);
        $_SESSION[self::CHAVE_SESSAO] = (int) $f['id'];
        unset($_SESSION['login_falhas'], $_SESSION['login_bloqueio_ate']);
        $repo->registarLogin((int) $f['id']);

        self::$carregado = false;
        return null;
    }

    /**
     * Termina a sessão: apaga todos os dados e troca o id de sessão (o antigo
     * é destruído no servidor). Mantém-se uma sessão vazia para poder mostrar
     * a mensagem "sessão terminada" na página de login.
     */
    public static function sair(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        self::$utilizadorAtual = null;
        self::$carregado = true;
    }

    /** Redireciona para o login (guardando a página pedida) se não houver sessão. */
    public static function exigirAutenticacao(): void
    {
        if (self::autenticado()) {
            return;
        }
        $destino = ltrim($_SERVER['REQUEST_URI'] ?? '', '/') ?: 'index.php';
        header('Location: login.php?redirect=' . urlencode($destino));
        exit;
    }

    /** Bloqueia a página a quem não for administrador. */
    public static function exigirAdmin(): void
    {
        self::exigirAutenticacao();
        if (!self::ehAdmin()) {
            http_response_code(403);
            flash('erro', 'Não tem permissões para aceder a essa página (apenas administradores).');
            header('Location: index.php');
            exit;
        }
    }

    // ----- Proteção simples contra força bruta (por sessão) -----------------

    private static function bloqueado(): bool
    {
        $ate = $_SESSION['login_bloqueio_ate'] ?? 0;
        if ($ate > time()) {
            return true;
        }
        if ($ate) {
            unset($_SESSION['login_bloqueio_ate'], $_SESSION['login_falhas']);
        }
        return false;
    }

    private static function registarFalha(): void
    {
        $_SESSION['login_falhas'] = ($_SESSION['login_falhas'] ?? 0) + 1;
        if ($_SESSION['login_falhas'] >= self::MAX_TENTATIVAS) {
            $_SESSION['login_bloqueio_ate'] = time() + self::BLOQUEIO_SEGUNDOS;
        }
    }
}
