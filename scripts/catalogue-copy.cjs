const fs=require('node:fs');
const crypto=require('node:crypto');
const sources={'public/app.js':'Vitrine, sacola e personalização','public/checkout.js':'Checkout e pagamento','public/commerce.js':'Acompanhamento e acesso','public/chat.js':'Atendimento'};
const catalogue={};
for(const [path,group] of Object.entries(sources)){
    const source=fs.readFileSync(path,'utf8');
    const texts=[];
    for(const match of source.matchAll(/<[a-zA-Z][^<>]*>([^<>{}$`]+)</g))texts.push(match[1]);
    for(const match of source.matchAll(/(?:placeholder|aria-label)="([^"${}]+)"/g))texts.push(match[1]);
    for(const match of source.matchAll(/(?:toast\(|\.textContent=)'([^'\\]+)'/g))texts.push(match[1]);
    for(const original of texts){
        const text=original.trim();
        if(text.length<2||text.length>1000||!/[a-zA-ZÀ-ÿ]/.test(text)||/[{}<>`]|^[:;]|\b(?:function|querySelector)\b/.test(text))continue;
        const key=crypto.createHash('sha256').update(text).digest('hex').slice(0,24);
        catalogue[key]??={text,group};
    }
}
fs.writeFileSync('public/copy-catalogue.json',JSON.stringify(catalogue,null,2)+'\n');
console.log(`${Object.keys(catalogue).length} editable interface texts catalogued.`);
