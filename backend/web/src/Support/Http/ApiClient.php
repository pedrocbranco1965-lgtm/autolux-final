<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Config;

/**
 * Cliente HTTP usado pela interface de gestão para falar com o serviço Node.js
 * de compras. Toda a comunicação entre as duas camadas passa por aqui, o que
 * concentra num só sítio o tratamento de timeouts, erros e descodificação JSON.
 */
final class ApiClient
{
    private string $baseUrl;

    public function __construct(?string $baseUrl = null, private int $timeoutSegundos = 8)
    {
        $this->baseUrl = rtrim($baseUrl ?? Config::get('API_BASE_URL', 'http://127.0.0.1:3001/api'), '/');
    }

    /**
     * @param array<string, scalar|null> $parametros
     * @return array<string, mixed>
     */
    public function get(string $caminho, array $parametros = []): array
    {
        $filtrados = array_filter(
            $parametros,
            static fn ($valor) => $valor !== null && $valor !== '',
        );
        $query = $filtrados === [] ? '' : '?' . http_build_query($filtrados);

        return $this->pedido('GET', $caminho . $query);
    }

    /**
     * @param array<string, mixed> $corpo
     * @return array<string, mixed>
     */
    public function post(string $caminho, array $corpo): array
    {
        return $this->pedido('POST', $caminho, $corpo);
    }

    /**
     * @param array<string, mixed> $corpo
     * @return array<string, mixed>
     */
    public function patch(string $caminho, array $corpo): array
    {
        return $this->pedido('PATCH', $caminho, $corpo);
    }

    /** Indica se o serviço Node.js está acessível, sem lançar exceção. */
    public function disponivel(): bool
    {
        try {
            $this->get('/health');

            return true;
        } catch (ApiException) {
            return false;
        }
    }

    /**
     * @param array<string, mixed>|null $corpo
     * @return array<string, mixed>
     */
    private function pedido(string $metodo, string $caminho, ?array $corpo = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($caminho, '/');
        $json = $corpo === null ? null : json_encode($corpo, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        // A extensão cURL é a via preferida; sem ela o pedido segue por streams,
        // para que a aplicação corra também em instalações PHP mais reduzidas.
        [$resposta, $estado, $detalhe] = function_exists('curl_init')
            ? $this->viaCurl($metodo, $url, $json)
            : $this->viaStream($metodo, $url, $json);

        if ($resposta === null) {
            throw new ApiException(
                'Não foi possível contactar o serviço de compras (Node.js). '
                . 'Confirme que está a correr em ' . $this->baseUrl . '. Detalhe: ' . $detalhe,
                0,
            );
        }

        $dados = json_decode($resposta, true);
        if (!is_array($dados)) {
            throw new ApiException("Resposta inválida do serviço de compras ({$estado}).", $estado);
        }

        if ($estado >= 400) {
            throw new ApiException((string) ($dados['erro'] ?? "Erro {$estado} no serviço de compras."), $estado);
        }

        return $dados;
    }

    /** @return array{0: string|null, 1: int, 2: string} */
    private function viaCurl(string $metodo, string $url, ?string $json): array
    {
        $curl = curl_init($url);

        $opcoes = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $metodo,
            CURLOPT_TIMEOUT => $this->timeoutSegundos,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ];

        if ($json !== null) {
            $opcoes[CURLOPT_POSTFIELDS] = $json;
            $opcoes[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($curl, $opcoes);
        $resposta = curl_exec($curl);
        $estado = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $erro = curl_error($curl);
        curl_close($curl);

        return [$resposta === false ? null : (string) $resposta, $estado, $erro];
    }

    /** @return array{0: string|null, 1: int, 2: string} */
    private function viaStream(string $metodo, string $url, ?string $json): array
    {
        $cabecalhos = ['Accept: application/json'];
        if ($json !== null) {
            $cabecalhos[] = 'Content-Type: application/json';
        }

        $contexto = stream_context_create([
            'http' => [
                'method' => $metodo,
                'header' => implode("\r\n", $cabecalhos),
                'content' => $json ?? '',
                'timeout' => $this->timeoutSegundos,
                'ignore_errors' => true,
            ],
        ]);

        $resposta = @file_get_contents($url, false, $contexto);
        $estado = 0;

        // $http_response_header é preenchido pelo PHP com a resposta do servidor.
        foreach ($http_response_header ?? [] as $linha) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $linha, $partes) === 1) {
                $estado = (int) $partes[1];
            }
        }

        return [$resposta === false ? null : $resposta, $estado, 'ligação recusada'];
    }
}
