# Atualizações da prévia

- Banner inicial focado em CS2, com identidade BRYXA. Banners originais em 1672 × 941; arquivos PNG de criação preservados em design/banners.
- Endereços próprios para coleções, título/descrição, canonical, Open Graph, dados estruturados, sitemap.xml e robots.txt. Edite a presença no Google em Configurações; nome e descrição das coleções também são administráveis. O Google define os sitelinks. Indexação exige publicação e poderá ser acompanhada no Search Console.
- Instagram, WhatsApp e Discord prontos no rodapé. Canais sem configuração ficam indisponíveis, sem destinos inventados. Preencha os dados em Configurações. Ícones: Simple Icons, CC0.
- Atendimento pede e-mail e WhatsApp, permite perfil com foto/nome, leitura e digitação reais via consulta periódica. Administração pode criar acessos limitados para atendentes.
- A prévia local tem frete simulado e aprovação automática de compra. Escolha Pix ou cartão simulado; nenhum dado de cartão é solicitado. Após aprovação aparece a prévia do e-mail e o acompanhamento permite criar senha. Pedidos de teste aparecem identificados no painel. Nenhum e-mail é enviado e nenhum valor é cobrado.
- Para ativar testes em outra máquina, configure demo_mode como true em config.local.php e execute o servidor PHP com router.php. A simulação só funciona no servidor local de desenvolvimento do PHP; fica bloqueada em produção.
- Mercado Pago integrado no código, com campos protegidos no painel; ativação e homologação real dependem de credenciais. Envio real de e-mails e hospedagem Hostinger continuam para a etapa de configuração/publicação.

## Validação

33 verificações de backend passaram, incluindo autenticação, CSRF, CRUD, uploads, cupons, inscrição e frete. Também passaram verificações de rotas/SEO, cálculo de preços no servidor, duplicação de pagamento, conta pós-compra, privacidade do chat, indicadores de leitura/digitação e restrição de acesso de atendentes. PHP e JavaScript sem erros de sintaxe. Conferência visual no navegador permanece pendente.
