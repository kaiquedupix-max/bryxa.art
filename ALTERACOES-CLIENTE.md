# Revisão das anotações da cliente

Implementado a partir das quatro folhas enviadas:

- Vitrine: removeu o benefício inicial Arte autoral e o Discord; atendimento pelo WhatsApp; nome Carrinho junto ao ícone; banners sem botão por padrão; filtros com nomes completos dos tamanhos; subcategorias em ordem alfabética; tema claro/escuro persistente.
- Personalize: texto explicativo editável, subcategorias próprias e valor adicional de personalização configurável.
- Promoções: campo Preço anterior no produto, com valor riscado; preço atual continua sendo o valor cobrado.
- Carrinho: sugestão de uma peça disponível que ainda não está na sacola; opção de ocultá-la pelo painel.
- Checkout: nick gratuito e opcional por unidade, inclusive para duas unidades da mesma peça. Os nicks ficam no pedido e no acompanhamento.
- Brinde: configurar nome, mínimo, peso e medidas em Experiência de compra. Uma unidade por pedido, sem cobrança. Elegibilidade calculada no servidor sobre produtos após cupom, sem frete; recálculo remove o brinde se ficar abaixo do mínimo. Peso e medidas entram no cálculo do frete. O brinde é separado na preparação, sem baixar estoque de produtos da vitrine.
- E-mails: assunto, título, mensagem e botão editáveis por modelo; dados do pedido substituem as variáveis. E-mails já preparados preservam o texto anterior. A plataforma de envio ainda precisa ser conectada.
- Usuários: acessos individuais para atendimento/perfil ou pedidos/rastreio; proprietária gerencia permissões e desativação. Funcionários não acessam pagamentos, configurações ou a lista de e-mails. Nenhum funcionário real foi criado automaticamente.
- Barra superior: o campo já existente de aviso da loja fica em Textos da loja. Não foi inventado valor de frete grátis.

A loja permanece com seus dados reais. Valores, telefones, brindes e pedidos usados nos testes ficam apenas no banco temporário local.

A cliente deve preencher WhatsApp, preço de personalização (se houver), campanha do brinde e promoções reais. Compra sem cadastro é suportada; a simulação de cobrança permanece exclusiva do servidor local de testes.

Validação: sintaxe PHP e JavaScript, hierarquia de categorias, reconciliação de pagamentos, fronteiras do brinde com cupom, checkout simulado com dois nicks distintos, persistência dos modelos de e-mail, restrições e revogação de acessos.
