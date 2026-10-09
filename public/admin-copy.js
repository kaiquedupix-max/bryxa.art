import {esc} from './shared.js';

export function copyEditor(settings,catalogue){
    const groups=[...new Set(Object.values(catalogue).map(x=>x.group))];
    const sections={hero:'Banner da página inicial',featured:'Produtos em destaque na página inicial',benefits:'Benefícios da loja',newsletter:'Cadastro para novidades',social:'Redes sociais no rodapé',support:'Botão do WhatsApp'};
    return `<form id="interface-copy-form"><div class="admin-card"><h2>O que aparece na loja</h2><p class="subtle">Marque as seções que sua cliente quer mostrar. Desmarcar não apaga os dados.</p>${Object.entries(sections).map(([key,label])=>`<label class="checkbox"><input type="checkbox" data-section="${key}" ${settings.sections?.[key]===false?'':'checked'}>${esc(label)}</label>`).join('')}</div><div class="admin-card"><h2>Textos dos botões, formulários e mensagens</h2><p class="subtle">Procure o texto que aparece na loja e escreva a nova versão. Os textos originais continuam aparecendo quando não há substituição. Preencha somente o que desejar mudar. Produtos, banners e políticas têm seus próprios campos no painel.</p><div class="field"><label for="copy-search">Buscar texto</label><input id="copy-search" type="search" placeholder="Exemplo: carrinho, entrega, atendimento"></div>${groups.map(group=>`<details class="copy-group"><summary>${esc(group)}</summary>${Object.entries(catalogue).filter(([,entry])=>entry.group===group).map(([key,entry])=>`<div class="field copy-field" data-copy-search="${esc(entry.text.toLocaleLowerCase('pt-BR'))}"><label for="copy-${key}">${esc(entry.text)}</label><textarea id="copy-${key}" data-copy-key="${key}" rows="2" maxlength="1000" placeholder="${esc(entry.text)}">${esc(settings.interface_copy?.[key]||'')}</textarea></div>`).join('')}</details>`).join('')}</div><div class="save-bar"><button class="btn" type="submit">Salvar aparência e textos</button><a class="btn secondary" href="/" target="_blank" rel="noopener">Ver loja</a></div></form>`;
}

export function bindCopyEditor(root,onSave){
    const form=root.querySelector('#interface-copy-form');if(!form)return;
    form.onsubmit=e=>{
        const interface_copy={},sections={};
        for(const el of form.querySelectorAll('[data-copy-key]'))if(el.value.trim())interface_copy[el.dataset.copyKey]=el.value.trim();
        for(const el of form.querySelectorAll('[data-section]'))sections[el.dataset.section]=el.checked;
        onSave(e,{interface_copy,sections});
    };
    const search=form.querySelector('#copy-search');
    search.oninput=()=>{
        const query=search.value.toLocaleLowerCase('pt-BR');
        for(const field of form.querySelectorAll('[data-copy-search]'))field.hidden=!field.dataset.copySearch.includes(query);
        for(const group of form.querySelectorAll('details')){group.hidden=![...group.querySelectorAll('.copy-field')].some(el=>!el.hidden);group.open=!!query;}
    };
}
