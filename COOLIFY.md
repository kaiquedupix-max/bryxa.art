# Hospedagem no Coolify

- Repositório: `kaiquedupix-max/bryxa.art`, branch `main`.
- Build: Dockerfile da raiz; porta interna **80**.
- PHP e Apache servem exclusivamente `public/`. O banco e a configuração ficam fora da raiz pública.
- Volumes persistentes: `/var/www/html/storage` e `/var/www/html/public/uploads`.
- Use apenas uma instância da aplicação, pois o banco SQLite é local.

## Primeiro acesso

Defina uma chave aleatória forte na variável de ambiente de execução `BRYXA_SETUP_KEY` do Coolify. Faça um novo deploy e abra `/admin.html`. Informe essa chave, o e-mail e uma senha para criar o primeiro administrador. Após concluir, remova a variável e faça um novo deploy. A imagem não contém chave ou senha padrão.

O banco inicia com o catálogo ilustrativo. Dados locais não são copiados para a imagem. Cadastre credenciais de frete, pagamentos e notificações pelo painel e teste antes de liberar vendas.

Faça backup consistente do banco SQLite, de `credentials.key` e dos uploads. Os volumes preservam dados durante os deploys, mas não substituem backups.

## Painel para a cliente

- **Categorias e banners**: criar categorias, criar subcategorias dentro de uma categoria principal, editar banners e textos, definir ordem no menu, ocultar páginas e excluir categorias novas que estejam vazias. As páginas iniciais podem ser ocultadas.
- **Produtos**: escolher categoria ou subcategoria, preço, imagem, descrição, tamanhos, estoque, destaque e permissão de personalização.
- **Textos da loja**: títulos, história, políticas e até seis benefícios.
- **Aparência e textos dos botões**: mostrar ou ocultar seções e pesquisar os textos da interface para editar botões, campos e mensagens. Deixar uma substituição vazia restaura o texto original.
- **Configurações**: marca, logo, ícone da aba, cores, contatos, domínio, SEO e integrações.

Mudanças de conteúdo aparecem ao atualizar a loja, sem novo deploy. Estrutura de layout e novas funcionalidades continuam sendo alterações de código. O painel não expõe HTML, CSS ou JavaScript à cliente.

## Verificação

`php tests/catalog-settings.php` valida a hierarquia e a proteção contra exclusão de categorias com produtos. O build também verifica a sintaxe PHP, as extensões necessárias e a configuração do Apache.

Após adicionar textos à interface no código, execute `node scripts/catalogue-copy.cjs` para atualizar o catálogo de campos editáveis.
