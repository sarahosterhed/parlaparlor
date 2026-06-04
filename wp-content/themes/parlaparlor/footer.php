<footer>
    <p>&copy; <?php echo date('Y'); ?> Pärlapärlor</p>
</footer>
<?php wp_footer(); ?>
<?php global $template; echo '<!-- Using template: ' . esc_html( basename( $template ) ) . ' -->'; ?>
</body>
</html>