# Fluxo de compra definido para BRYXA

## Compra sem cadastro

Não solicitar conta ou senha antes do pagamento. Pedir nome, e-mail e endereço de entrega (CEP, rua, número, bairro, cidade e estado). Complemento é opcional. Documento deve ser solicitado pelo formulário seguro de pagamento apenas quando necessário ao processamento; nenhum dado de cartão deverá passar por um formulário próprio da loja.

## Pagamento dentro da loja

Integração escolhida: Mercado Pago Checkout Transparente / Payment Brick, com Pix e cartão. Usar tema escuro, cores BRYXA, sem Checkout Pro ou redirecionamento comercial. O QR Code e o Pix copia e cola deverão aparecer na própria página.

O checkout visual já existe em `#checkout`, acessível pela sacola. Ainda não há processamento de pagamento, criação de pedidos ou envio de e-mails nessa tela. Não solicitar número de cartão, CVV ou dados bancários enquanto o SDK oficial não estiver conectado. Nenhum pagamento é simulado como aprovado.

## Confirmação e acompanhamento

Depois de confirmação verificada no servidor, enviar e-mail com número do pedido, itens, total, endereço e link de acompanhamento. Não confiar somente no retorno do navegador para marcar uma compra como paga.

Ao abrir o link de acompanhamento, permitir que o cliente crie senha usando o e-mail da compra, mediante link assinado, de uso único e prazo de validade. A criação da conta ocorre depois da compra. Não permitir associar pedidos apenas digitando um e-mail sem comprovar sua posse.

## Próxima implementação

- Public Key e Access Token do Mercado Pago, conta de teste e segredo de Webhook.
- Domínio HTTPS para recebimento dos Webhooks.
- SMTP/serviço de e-mail com remetente autenticado.
- Recalcular preços, cupons e frete no servidor; validar variantes e estoque.
- Reserva de estoque, idempotência de pagamento, validação da assinatura do Webhook e consulta do pagamento no Mercado Pago.
- Pix com atualização de status; tokenização segura de cartão pelo SDK.
- Pedidos persistidos e confirmação por e-mail após aprovação.
- Conta opcional via link de acompanhamento, autenticação e recuperação de senha.
- Upload privado da arte personalizada associado ao pedido.

Documentação oficial consultada:

- https://github.com/mercadopago/sdk-js/blob/main/docs/bricks/payment-guest.md
- https://www.mercadopago.com.br/developers/pt/docs/checkout-api-payments/integration-configuration/integrate-pix
