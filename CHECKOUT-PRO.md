# Mercado Pago Checkout Pro

A loja agora usa POST /checkout/preferences. O botão da BRYXA abre o init_point retornado pelo Mercado Pago; Pix e cartão são preenchidos exclusivamente na página do provedor. Não é necessário Public Key para este fluxo. Access Token e segredo do webhook continuam criptografados no servidor.

O total da preferência vem do cálculo validado pelo servidor e inclui personalização, cupom e frete. Peças, tamanhos, nicks e brinde permanecem no pedido da BRYXA. A preferência contém uma linha com o total final, evitando diferenças de arredondamento na aplicação de descontos.

Webhook: https://bryxa.com.br/api/index.php?action=mp-webhook. Habilitar o evento payment na mesma aplicação que fornece as credenciais. A assinatura e a consulta do pagamento ao Mercado Pago são obrigatórias antes de reconhecer uma aprovação.

Todos os retornos levam a /acompanhar/?retorno=1. O navegador conserva o token privado do pedido no armazenamento local antes do redirecionamento; os e-mails também têm o link privado de acompanhamento. O status da URL nunca aprova um pedido. Quando há payment_id no retorno, a loja consulta a API e verifica pedido, valor e ambiente; o webhook funciona mesmo se o comprador não voltar ao site.

Repetir o clique no mesmo rascunho reutiliza a preferência salva. Em resposta interrompida, o servidor procura uma preferência por external_reference antes de criar outra. Tentativas recusadas do Pro mantêm a reserva de estoque para permitir nova tentativa na mesma preferência. Pedidos abandonados precisam ser conferidos antes de cancelar/liberar reservas; não liberar estoque de um Pix pendente sem conferir seu estado no provedor.

As rotas antigas de cobrança direta respondem 410 para exigir recarregamento de abas antigas. Nenhuma cobrança real é feita nos testes automatizados.
