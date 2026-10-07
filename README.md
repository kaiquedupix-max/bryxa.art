# BRYXA · bryxa.art

Primeira versão local de uma loja de streetwear com painel de administração em português. PHP 8.2+ com PDO SQLite, cURL e Fileinfo. Frontend em HTML/CSS/JavaScript, sem dependências de build.

## O que funciona

- Página inicial, BRYXA (com subcategorias Rust e CS2), Personalizado e Arsenal (com subcategoria 3D); novas coleções e hierarquia pelo painel.
- Catálogo com filtros, preço, detalhes e tamanhos; favoritos e sacola persistidos neste navegador.
- Prévia ilustrativa de personalização, frase, posição e observações. A arte não é enviada ao servidor nesta etapa; o upload final do cliente será integrado ao checkout.
- Painel com login, criação/edição/exclusão de produtos, estoque por tamanho, foto, destaque e visibilidade.
- Banners, textos, coleções, logo, cores, atendimento, políticas, benefícios, biblioteca de imagens, cupons e lista de e-mails administráveis.
- Cálculo de frete via Melhor Envio por backend; token, origem e contato técnico configurados no painel. Sem cotações fictícias quando faltam credenciais.
- Estrutura de pedidos no painel. Recebimento de pedidos e pagamentos ainda não habilitado, conforme combinado.
- Checkout visual sem cadastro em `#checkout`. Pix/cartão, e-mails de confirmação e conta posterior para acompanhamento seguem o fluxo documentado em `CHECKOUT-E-ACOMPANHAMENTO.md`; a integração real ainda está pendente.
- Balão de atendimento com conversas privadas por token e respostas pelo painel, atualizado periodicamente sem exigir cadastro.

## Executar localmente

1. Instale PHP com `pdo_sqlite`, `sqlite3`, `curl` e `fileinfo` habilitados.
2. Copie `config.example.php` para `config.local.php` e preencha uma chave aleatória de instalação. O arquivo fica fora da pasta pública e não deve entrar no Git.
3. Na raiz deste projeto, execute `php -S 127.0.0.1:8080 -t public router.php`.
4. Abra `http://127.0.0.1:8080` e `http://127.0.0.1:8080/admin.html`.
5. Se o banco estiver vazio, crie o primeiro administrador usando a chave de instalação. Use senha de pelo menos 12 caracteres. Não existe senha padrão no código.

O ambiente local entregue já está configurado. O acesso local está em `ACESSO-LOCAL.txt`, ignorado pelo Git. O banco local está em `storage/` e também é ignorado pelo Git.

## Hospedagem Hostinger (etapa posterior)

Não houve publicação. Organização recomendada:

```
/bryxa-app/seed.php
/bryxa-app/config.local.php
/bryxa-app/storage/
/bryxa-app/public/  <-- document root do domínio
```

Configure a raiz pública do domínio para `public/`, mantendo configuração e banco fora dela. Se o plano só permitir `public_html`, coloque os arquivos de `public/` ali e coloque `seed.php`, `config.local.php` e `storage/` no diretório imediatamente acima de `public_html`; o backend já resolve essa estrutura. Não envie o projeto inteiro para dentro de `public_html`.

Ative HTTPS e PHP 8.2+ com as extensões mencionadas. Permita escrita do processo PHP apenas em `storage/` e `public/uploads/`. Ajuste `upload_max_filesize=10M` e `post_max_size=12M`. Preserve os `.htaccess`. Nunca sirva `storage/` por HTTP.

Na instalação nova, gere uma chave de instalação forte e única e cadastre o acesso da cliente. Remova o valor da chave de instalação após criar o administrador. Não transporte as credenciais locais para produção. Faça backup do banco SQLite com backup consistente (incluindo WAL quando presente) e da pasta de uploads. Os commits de código não substituem os backups dos dados.

## Antes do lançamento

- Substituir os produtos e fotos ilustrativos pelas peças reais. Fotos conceituais geradas para apresentação; não representam estoque real.
- Conferir preços, peso e medidas embaladas, estoque, direitos de uso das artes e dados comerciais.
- Preencher atendimento e políticas reais da loja.
- Configurar token, CEP de origem e e-mail técnico do Melhor Envio; validar em Sandbox e depois produção.
- Implementar Mercado Pago, validação de valores no servidor, webhook idempotente, pedidos, reserva de estoque e upload privado das artes dos clientes.
- Configurar envio de e-mails; a lista atual salva os cadastros, mas não dispara campanhas.
- Validar layout no navegador em desktop e celular. A abertura de navegador para revisão foi bloqueada pela política de permissões desta sessão; validação visual final ainda pendente.

## Banners

Três banners originais gerados: Lightning, Skull 3D e Rust, em 1672 × 941. Versões WebP de alta qualidade ficam em `public/assets/`. Tipografia separada da imagem para adaptação responsiva.

## Segurança e limites atuais

Login com hash de senha, sessão HttpOnly/SameSite, proteção CSRF no painel, validação de arquivos e caminhos, token de frete privado, limitação de tentativas de login e cotação. As dimensões e valores de frete vêm do catálogo no servidor. O catálogo e as configurações são persistidos em SQLite com transações nas alterações de produtos.

Os valores da sacola no navegador servem para prévia e deverão ser recalculados no backend do futuro checkout. O projeto não processa transações. Não foi realizado um teste real de cotação porque o token não foi fornecido.

Documentação usada para a cotação: https://docs.melhorenvio.com.br/reference/calculo-de-fretes-por-produtos
