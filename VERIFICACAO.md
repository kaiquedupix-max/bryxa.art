# Verificação da primeira versão

Executada em 7 de outubro de 2026, com PHP 8.5.11 local.

Passaram 33 verificações de backend, incluindo:

- Vitrine retorna 12 produtos e 6 páginas.
- Painel privado recusa acesso sem login.
- Escrita sem token CSRF é recusada.
- Cadastro, edição, ocultação e exclusão de produto persistem no banco.
- Alteração de textos aparece na API da vitrine.
- Cupons e cadastro/exclusão de e-mails funcionam.
- Upload de imagem válido é aceito; conteúdo PHP disfarçado de imagem é recusado.
- Caminhos de imagem fora da biblioteca são recusados.
- Token do Melhor Envio não aparece na API pública nem no retorno do painel.
- CEP inválido e cotação sem configuração retornam mensagens apropriadas.
- Logout invalida acesso ao painel; senha inválida é recusada.

PHP e JavaScript passaram pelas verificações de sintaxe. Os três banners gerados foram inspecionados como imagens.

## Pendente

- Revisão visual da loja e do painel no navegador: acesso bloqueado pela política de permissões da sessão.
- Cotação real do Melhor Envio: token, CEP de origem e e-mail técnico ainda não cadastrados.
- Checkout Mercado Pago: adiado por solicitação do usuário.
- Publicação na Hostinger: etapa posterior.
- Repositório remoto GitHub: operação de criação indisponível no conector, e abertura do GitHub bloqueada pelo navegador. Os commits foram salvos em um repositório Git local.

Os testes removeram os produtos, cupons, e-mails e uploads temporários criados durante a verificação.

## Atualização da loja

- Navegação atual: BRYXA > Rust e CS2; Arsenal > 3D. Dados do banco e seed atualizados; 7 páginas cadastradas.
- Página inicial exibe somente os produtos escolhidos como destaque no painel.
- 12 novas fotos conceituais, 1254 × 1254, inspecionadas. Três banners atualizados, 1672 × 941, com marca BRYXA; Rust na paleta violeta/magenta.
- Cards com animação no conjunto e botão Adicionar ao carrinho, preservando a seleção de tamanho.
- Personalização com upload visual, seleção por clique e arrastar arquivo.
- Fonte artística local, acompanhada da licença.
- Atendimento verificado: conversa privada por token, resposta protegida no painel e entrega da resposta ao visitante sem cadastro.
- Checkout visual sem cadastro implementado; pagamento, e-mail e conta de acompanhamento ainda dependem da próxima integração documentada.
- Verificações de sintaxe e as 33 verificações anteriores de backend passaram novamente após as alterações. Revisão da interface no navegador permanece pendente.
