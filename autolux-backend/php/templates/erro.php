<?php
/**
 * Página de erro amigável. Recebe $excecao (Throwable) e $titulo.
 * Usada pelas páginas quando a BD ou a API não estão disponíveis.
 */
require __DIR__ . '/cabecalho.php';
?>
<section class="cartao cartao-erro">
  <p class="rotulo">Ocorreu um problema</p>
  <h1><?= e($titulo) ?></h1>
  <p><?= e($excecao->getMessage()) ?></p>
  <details>
    <summary>Detalhes técnicos</summary>
    <pre><?= e(get_class($excecao)) ?> em <?= e($excecao->getFile()) ?>:<?= $excecao->getLine() ?></pre>
  </details>
  <a class="botao botao-secundario" href="index.php">Voltar ao painel</a>
</section>
<?php require __DIR__ . '/rodape.php'; ?>
