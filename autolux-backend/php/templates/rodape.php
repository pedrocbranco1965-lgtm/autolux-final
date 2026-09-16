</main>

<footer class="rodape">
  <div>
    <strong>AutoLux</strong> · Projeto final de Backend · PHP <?= PHP_VERSION ?> + MySQL + Node.js
  </div>
  <div class="rodape-servicos">
    BD1 (vendas): <code><?= e($GLOBALS['config']['DB1_NAME']) ?></code> ·
    API fornecedores: <code><?= e($GLOBALS['config']['API_BASE_URL']) ?></code>
  </div>
</footer>
<script src="assets/app.js"></script>
</body>
</html>
