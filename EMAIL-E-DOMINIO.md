# Domínio e e-mail da BRYXA

Endereço principal: https://bryxa.com.br. Caixa de contato e remetente: contato@bryxa.com.br.

O Coolify continua gerenciando a aplicação. O Nginx do servidor encaminha os domínios para 127.0.0.1:18083; não iniciar um segundo proxy nas portas 80/443. HTTPS emitido pelo Certbot para o domínio principal e www, com renovação automática.

## SMTP Hostinger

- Servidor: smtp.hostinger.com, porta 465 com TLS e verificação de certificado.
- BRYXA_SMTP_PASSWORD: senha da caixa, somente variável de runtime no Coolify, nunca no Git.
- BRYXA_SMTP_ENABLED=true: ativa envio automático.
- BRYXA_SMTP_USERNAME e BRYXA_SMTP_FROM opcionais; padrão contato@bryxa.com.br.

O processo send-emails.php --loop é iniciado junto ao Apache como www-data. Processa até cinco mensagens por rodada, a cada 30 segundos. O painel E-mails e notificações mostra configuração e resultado. Falhas e envios interrompidos não são reenviados automaticamente, para evitar duplicação após resposta SMTP ambígua. Enviado significa aceito pelo servidor SMTP, sem garantir chegada à caixa de entrada.

Mensagens anteriores à integração permanecem preservadas sem disparo automático. Recuperação de senha expira em 30 minutos; outras mensagens pendentes expiram em sete dias. Credenciais e conteúdo de mensagens não são gravados nos logs. A fila contém dados pessoais e links privados; proteger o volume e seus backups.

MX, SPF e DKIM permanecem na zona DNS da Hostinger. O registro A do site não interfere no e-mail. Nunca substituir registros de e-mail ao apontar a loja.

Os links de pedidos, recuperação de senha, sitemap, metadados e webhook usam o domínio salvo nas configurações da loja. A publicação migra o domínio e o e-mail uma única vez, preservando o catálogo e pedidos existentes.
