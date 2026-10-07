# Checkout e acompanhamento

O checkout usa Payment Brick do Mercado Pago para Pix e cartão dentro do site. A compra não exige cadastro prévio; o acompanhamento permite criar senha depois da aprovação.

A implementação, proteção de credenciais, modelos de e-mail, fila de notificações, recuperação de acesso e sincronização de transportadora estão descritos em [INTEGRACOES-E-SEGURANCA.md](INTEGRACOES-E-SEGURANCA.md).

Pagamento real exige credenciais, HTTPS, webhook e homologação. E-mails não são enviados até escolher e conectar a plataforma. A simulação local permanece identificada e separada do pagamento real.
