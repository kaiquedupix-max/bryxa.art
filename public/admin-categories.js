import {esc,toast} from './shared.js';

export const reservedCategorySlugs = ['todos','favoritos','sacola','checkout','acompanhar','sobre','contato','trocas','privacidade','termos','esqueci-senha','recuperar-senha','api','assets','uploads','admin','index','seo','sitemap','robots'];
export const categorySlug = label => label.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'').slice(0,40);
export const orderedPages = pages => Object.entries(pages).sort((a,b)=>(a[1].order||0)-(b[1].order||0));
export const categoryLabel = (pages,key) => pages[key]?.parent ? `${pages[pages[key].parent]?.label || ''} › ${pages[key].label}` : pages[key]?.label || key;

export function categoryOverview(data,selected) {
    const pages=data.settings.pages;
    const row=(slug,page,child=false)=>`<div class="category-row ${child?'category-child':''}"><button type="button" class="table-action ${selected===slug?'category-selected':''}" data-page="${esc(slug)}">${esc(page.label)}</button><span class="status-pill ${page.enabled?'active':''}">${page.enabled?'Visível':'Oculta'}</span><span class="subtle">${data.products.filter(p=>p.collection===slug).length} produtos</span>${!child&&!['inicio','personalizado'].includes(slug)?`<button type="button" class="table-action" data-new-subcategory="${esc(slug)}">+ Subcategoria</button>`:''}</div>`;
    return `<div class="admin-card"><div class="section-head"><div><h2>Categorias e subcategorias</h2><p class="subtle">Clique em um nome para editar. Use “Subcategoria” para criar uma seção dentro de outra categoria.</p></div><button type="button" class="btn secondary small" id="new-collection">+ Nova categoria</button></div><div class="category-tree">${orderedPages(pages).filter(([,p])=>!p.parent).map(([slug,p])=>row(slug,p)+orderedPages(pages).filter(([,child])=>child.parent===slug).map(([key,child])=>row(key,child,true)).join('')).join('')}</div></div>`;
}

export function openCategoryDialog({data,parent='',write,refresh,onCreated}) {
    const pages=data.settings.pages;
    const dialog=document.createElement('dialog');
    dialog.className='media-dialog category-dialog';
    dialog.innerHTML=`<form id="new-category-form"><div class="section-head"><h2>${parent?'Nova subcategoria':'Nova categoria'}</h2><button class="table-action" type="button" data-close>Fechar</button></div><p class="subtle">${parent?`Esta subcategoria aparecerá dentro de ${esc(pages[parent].label)}.`:'A categoria aparecerá no menu principal da loja.'}</p><div class="field"><label for="new-category-name">Nome</label><input id="new-category-name" name="label" maxlength="120" required autofocus placeholder="Exemplo: Acessórios"></div><div class="field"><label for="new-category-parent">Onde mostrar</label><select id="new-category-parent" name="parent"><option value="">Menu principal (categoria)</option>${orderedPages(pages).filter(([key,p])=>!p.parent&&!['inicio','personalizado'].includes(key)).map(([key,p])=>`<option value="${esc(key)}" ${key===parent?'selected':''}>Dentro de ${esc(p.label)} (subcategoria)</option>`).join('')}</select></div><p class="subtle">Depois de criar, você poderá escolher o banner e editar os textos.</p><p class="subtle" role="alert" id="category-error"></p><button class="btn" type="submit">Criar ${parent?'subcategoria':'categoria'}</button></form>`;
    document.body.append(dialog);
    dialog.querySelector('[data-close]').onclick=()=>dialog.close();
    dialog.onclose=()=>dialog.remove();
    dialog.querySelector('form').onsubmit=async e=>{
        e.preventDefault();
        const form=e.target,label=form.label.value.trim(),slug=categorySlug(label);
        const error=dialog.querySelector('#category-error');
        if(!slug||reservedCategorySlugs.includes(slug)||Object.hasOwn(pages,slug)){error.textContent='Este nome já está em uso. Escolha outro nome.';return;}
        const button=form.querySelector('[type=submit]');button.disabled=true;
        const category={label,parent:form.parent.value,enabled:true,order:Object.keys(pages).length,eyebrow:data.settings.brand,title:label.toUpperCase(),description:'',banner:'assets/hero-lightning.webp',button:'Ver peças',target:'produtos'};
        try{await write('settings',{pages:{...pages,[slug]:category}});await refresh();dialog.close();onCreated(slug);toast('Categoria criada. Escolha o banner e cadastre os produtos.');}catch(err){error.textContent=err.message;}finally{button.disabled=false;}
    };
    dialog.showModal();
}
