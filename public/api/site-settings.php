<?php
declare(strict_types=1);

// Pure validation shared by the API and regression checks.
function validateStorePages(array $input, array $products = []): array {
    if (count($input) > 80) throw new InvalidArgumentException('Use até 80 categorias e páginas.');
    $reserved = ['todos','favoritos','sacola','checkout','acompanhar','sobre','contato','trocas','privacidade','termos','esqueci-senha','recuperar-senha','api','assets','uploads','admin','index','seo','sitemap','robots'];
    $result = [];
    foreach ($input as $slug => $page) {
        if (!is_string($slug) || !preg_match('/^[a-z0-9-]{1,40}$/', $slug) || in_array($slug, $reserved, true)) throw new InvalidArgumentException('Escolha outro endereço para esta categoria.');
        if (!is_array($page)) throw new InvalidArgumentException('Categoria inválida.');
        $clean = [];
        foreach (['label','eyebrow','title','description','button','target'] as $field) {
            $value = $page[$field] ?? '';
            if (!is_string($value) || strlen($value) > 1000) throw new InvalidArgumentException('Texto da categoria inválido.');
            $clean[$field] = trim($value);
        }
        if (!$clean['label']) throw new InvalidArgumentException('Informe o nome da categoria.');
        $parent = $page['parent'] ?? '';
        if (!is_string($parent) || ($parent && !isset($input[$parent]))) throw new InvalidArgumentException('Categoria principal inválida.');
        if ($parent === $slug) throw new InvalidArgumentException('Uma categoria não pode ser sua própria categoria principal.');
        if ($parent && (!empty($input[$parent]['parent']) || in_array($parent, ['inicio'], true))) throw new InvalidArgumentException('Escolha uma categoria do menu principal. Subcategorias têm apenas um nível.');
        if (in_array($slug, ['inicio','personalizado'], true) && $parent) throw new InvalidArgumentException('Esta página deve ficar no menu principal.');
        $order = filter_var($page['order'] ?? 0, FILTER_VALIDATE_INT);
        if ($order === false || $order < 0 || $order > 999) throw new InvalidArgumentException('Use uma ordem entre 0 e 999.');
        $clean['parent'] = $parent;
        $clean['order'] = $order;
        $clean['enabled'] = (bool)($page['enabled'] ?? true);
        $clean['banner'] = $page['banner'] ?? '';
        $result[$slug] = $clean;
    }
    foreach (['inicio','roupas','cs2','rust','personalizado','arsenal'] as $slug) {
        if (!isset($result[$slug])) throw new InvalidArgumentException('As páginas iniciais devem permanecer cadastradas. Você pode ocultá-las.');
    }
    foreach ($products as $product) {
        if (!isset($result[$product['collection'] ?? ''])) throw new InvalidArgumentException('Mova os produtos para outra categoria antes de excluir esta categoria.');
    }
    return $result;
}
