# Pagamentos e pós-compra

## Mercado Pago

Em **Configurações → Mercado Pago**, cadastre Public Key, Access Token e segredo do webhook. Campos secretos vazios preservam as credenciais existentes; os controles de remoção limpam os valores. Comece com credenciais de teste. A ativação substitui a simulação local. Produção exige HTTPS.

Webhook da aplicação: `https://bryxa.art/api/index.php?action=mp-webhook`, evento `payment`. A assinatura HMAC é validada antes de consultar o pagamento na API oficial. A confirmação confere identificador, valor, moeda e ambiente. Atualizações repetidas não duplicam a aprovação nem sua notificação. O cartão é tokenizado pelo SDK oficial; a loja não recebe número completo ou CVV. O Pix mostra QR Code e copia e cola dentro do checkout.

Não há credenciais reais cadastradas. A integração ainda precisa ser homologada com a aplicação da cliente, compras de teste, notificações e HTTPS público antes de receber dinheiro. A interface do Brick também precisa de conferência visual com credenciais de teste. Nunca use aprovação simulada para vendas reais.

## Credenciais e armazenamento

Access Token, segredo do webhook e token do Melhor Envio são salvos com AES-256-GCM. OpenSSL deve estar ativo no PHP. A chave fica em `storage/credentials.key`, fora de `public`, e não entra no Git. Guarde um backup privado da chave junto com o banco: perder a chave impede recuperar credenciais criptografadas. Criptografia protege uma cópia isolada do banco; alguém com acesso completo ao servidor e à chave ainda pode descriptografá-lo.

Na Hostinger, a raiz web deve apontar somente para `public`. Bloqueie acesso externo ao armazenamento e à configuração. O GitHub contém código, templates e artes; exclui banco, usuários, credenciais, chave de criptografia e uploads particulares. Dados da loja precisam de backup privado separado do código.

## Pedidos e acompanhamento

Pedidos têm histórico de aprovação, produção, envio, trânsito e entrega. O painel permite editar transportadora, código de rastreio e UUID da etiqueta. Etapas anteriores e pedidos finalizados não são reaplicados. Cancelar no painel não executa um estorno financeiro; o estorno é confirmado pelo Mercado Pago.

A página `/acompanhar/` usa o token do link preparado no e-mail e permite criar senha somente depois da aprovação. Senhas são armazenadas por hash. A recuperação usa `/esqueci-senha/` e `/recuperar-senha/`, com link de 30 minutos e uso único. A resposta de solicitação é igual para e-mails existentes e inexistentes. Alterar ou recuperar uma senha invalida sessões anteriores da respectiva conta.

## E-mails

Há 15 modelos HTML e texto simples: pedido recebido, pagamento aprovado, pendente, rejeitado, produção, enviado, trânsito, entregue, cancelamento, estorno, conta criada, recuperação e alteração de senha, aprovação de arte e resposta de atendimento. As últimas duas são modelos preparados para conexão posterior ao fluxo de arte/atendimento. Confira as prévias em **E-mails e notificações**.

Os eventos de pedido e conta são colocados em `email_outbox`, com estado **Aguardando plataforma de e-mail**. Não são enviados e não há indicador falso de entrega. Após escolher a plataforma, será necessário conectar um consumidor dessa fila, confirmar envio pelo provedor, implementar tentativas e tratar falhas. Links de recuperação só chegam ao cliente após essa integração; prévias não substituem entrega real.

## Transportadora

Cadastre o token do Melhor Envio, e-mail técnico e UUID da etiqueta de cada pedido. O botão **Atualizar pela transportadora** consulta a API do Melhor Envio. O script `php /caminho/do/projeto/sync-shipments.php` pode ser agendado no cron da Hostinger, por exemplo a cada hora. Somente execução CLI é aceita. A etiqueta e sua transportadora devem existir na conta configurada. Status `posted` e `delivered` atualizam envio e entrega; eventos mais detalhados dependem dos dados disponibilizados pela transportadora.

## Validação feita

Passaram os testes locais de criptografia e ausência de segredo nas respostas, autorização e CSRF, rejeição de assinatura inválida, aprovação vinculada ao valor/moeda/ambiente/ID correto, repetição de evento, acesso por link, conta pós-compra, recuperação de uso único, histórico de status, rastreio e fila de e-mails. As 33 verificações anteriores de backend também passaram. PHP e JavaScript sem erros de sintaxe. Não houve cobrança real. Testes de integração externa e conferência visual no navegador continuam pendentes.

Documentação oficial: [Payment Brick](https://github.com/mercadopago/sdk-js/blob/main/docs/bricks/payment-guest.md), [Webhooks](https://www.mercadopago.com.br/developers/en/docs/checkout-pro-preferences/additional-content/notifications/webhooks), [Etiqueta Melhor Envio](https://docs.melhorenvio.com.br/reference/listar-informacoes-de-uma-etiqueta).
