<?php
/** @var array $settings */
?>
</main>
<footer class="site-footer" id="contato">
  <div class="container">
    <p><?= e($settings['footer_text'] ?? 'Atendimento consultivo para compra e venda de imóveis.') ?></p>
    <p>© <?= date('Y') ?> <?= e($settings['site_name'] ?? 'Aurora Imóveis') ?></p>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>
