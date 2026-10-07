<?php
// Copie para config.local.php, FORA da pasta public. Nunca envie credenciais ao GitHub.
return [
  'demo_mode' => false, // Somente o servidor PHP local pode ativar simulações.
  'setup_key' => '', // Gere com: php -r "echo bin2hex(random_bytes(24));"
  'storage_path' => __DIR__ . '/storage',
];
