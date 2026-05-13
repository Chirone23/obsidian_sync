<?php if (!defined('ABSPATH')) exit; ?>
</main>

<a class="chat-fab" href="<?php echo esc_url(home_url('/mybiblio/')); ?>">
  <span class="av">✨</span>
  <span>Chiedi a MyBibliò</span>
</a>

<footer class="site-footer">
  <div class="footer-inner">
    <div>
      <div class="nav-logo" style="color:var(--biblio-cream);font-size:28px;margin-bottom:12px;">Bibli<span class="accent">ò</span></div>
      <p style="color:rgba(245,241,232,.7);max-width:340px;">La tua biblioteca in un click. Acquista, noleggia, lasciati guidare da MyBibliò.</p>
    </div>
    <div>
      <h4>Esplora</h4>
      <a href="<?php echo esc_url(home_url('/catalogo/')); ?>">Catalogo</a>
      <a href="<?php echo esc_url(home_url('/plus/')); ?>">Plus</a>
      <a href="<?php echo esc_url(home_url('/noleggio-vs-acquisto/')); ?>">Noleggio</a>
    </div>
    <div>
      <h4>Aiuto</h4>
      <a href="<?php echo esc_url(home_url('/contatti/')); ?>">Contatti</a>
      <a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a>
      <a href="<?php echo esc_url(home_url('/spedizioni/')); ?>">Spedizioni</a>
    </div>
    <div>
      <h4>Legale</h4>
      <a href="<?php echo esc_url(home_url('/privacy/')); ?>">Privacy</a>
      <a href="<?php echo esc_url(home_url('/termini/')); ?>">Termini</a>
      <a href="<?php echo esc_url(home_url('/cookie/')); ?>">Cookie</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?php echo date('Y'); ?> Bibliò. Tutti i diritti riservati.</span>
    <span>Made with ❤ in Italia</span>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
