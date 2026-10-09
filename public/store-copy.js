let copy={},sections={};
let observer;
const originals=new WeakMap();
const attributeOriginals=new WeakMap();
const excluded='script,style,textarea,.support-messages,.chat-messages,.product-title,.product-meta,.price,.prose,.cart-item-info h3,.dialog-details h2';

// Values are written as text, never HTML. Product, customer and chat data stay
// in their own editors and are not part of the interface copy catalogue.
export function configureStoreCopy(settings,catalogue){
    copy={};sections=settings.sections||{};
    for(const [key,value] of Object.entries(settings.interface_copy||{})){
        const entry=catalogue[key];if(entry&&typeof value==='string'&&value.trim())copy[entry.text]=value;
    }
    applyStoreCopy();
    observer?.disconnect();
    observer=new MutationObserver(()=>applyStoreCopy());
    observer.observe(document.body,{childList:true,subtree:true,characterData:true});
}

export function applyStoreCopy(){
    const walker=document.createTreeWalker(document.body,NodeFilter.SHOW_TEXT);
    let node;
    while((node=walker.nextNode())){
        if(node.parentElement?.closest(excluded))continue;
        if(node.parentElement?.tagName==='OPTION'&&!node.parentElement.hasAttribute('value'))continue;
        const record=originals.get(node);
        const current=node.nodeValue;
        const original=record&&current===record.applied?record.original:current;
        const text=original.trim();
        const value=Object.hasOwn(copy,text)?original.replace(text,copy[text]):original;
        originals.set(node,{original,applied:value});
        if(current!==value)node.nodeValue=value;
    }
    for(const el of document.querySelectorAll('[placeholder],[aria-label]')){
        for(const attr of ['placeholder','aria-label']){
            if(!el.hasAttribute(attr))continue;
            const current=el.getAttribute(attr);
            const records=attributeOriginals.get(el)||{};
            const record=records[attr];
            const original=record&&current===record.applied?record.original:current;
            const value=copy[original]||original;
            records[attr]={original,applied:value};attributeOriginals.set(el,records);
            if(current!==value)el.setAttribute(attr,value);
        }
    }
    for(const el of document.querySelectorAll('.support-widget'))el.hidden=true;
}
