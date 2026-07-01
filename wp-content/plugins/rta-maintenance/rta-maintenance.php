<?php
/**
 * Plugin Name: RTA Manutenção
 * Description: Exibe página "Em Construção" para visitantes não logados.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', 'rta_maintenance_mode');
function rta_maintenance_mode() {
    if (is_user_logged_in() || is_admin() || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/wp-')) {
        return;
    }

    $logo_url = get_template_directory_uri() . '/logo.png';

    wp_die('<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Em Construção — Roteiro Turístico dos Aposentados</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    color: #333;
  }
  .container { text-align: center; padding: 2rem; max-width: 600px; }
  .logo {
    width: 180px; height: 180px;
    margin: 0 auto 2rem;
    object-fit: contain;
  }
  h1 { font-size: 2rem; margin-bottom: 1rem; font-weight: 800; color: #c40000; }
  p { font-size: 1.1rem; opacity: 0.9; line-height: 1.6; margin-bottom: 0.5rem; color: #555; }
  p strong { color: #333; }
  .spinner {
    width: 48px; height: 48px; border: 4px solid #ddd;
    border-top-color: #c40000; border-radius: 50%;
    animation: spin 1s linear infinite; margin: 2rem auto;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="container">
  <img src="' . esc_url($logo_url) . '" alt="Roteiro Turístico dos Aposentados" class="logo">
  <h1>Em Construção</h1>
  <p>Estamos preparando um novo visual para o</p>
  <p><strong>Roteiro Turístico dos Aposentados</strong></p>
  <div class="spinner"></div>
  <p style="font-size:0.9rem;opacity:0.7">Em breve estaremos no ar com novidades!</p>
</div>
</body>
</html>', 'Em Construção', ['response' => 503]);
}
