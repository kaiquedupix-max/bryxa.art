const fs=require('fs'),vm=require('vm'),assert=require('assert');
const source=fs.readFileSync('public/commerce.js','utf8').replace(/^import .*?;\s*/,'const {api,esc,money,toast,getLocal,setLocal}=mock;\n').replace(/export /g,'')+'\nglobalThis.testFns={mountPayment,paymentStyle};';
async function scenario(outcome){
 let callbacks,stored={},busy=false;
 const feedback={innerHTML:'',className:'',hidden:false,scrollIntoView(){}},container={hidden:false,setAttribute(){busy=true},removeAttribute(){busy=false}},shipping={value:'pickup',disabled:false};
 const target={innerHTML:'',querySelector(s){return s==='select'?shipping:s==='#mp-result'?feedback:s==='#mp-brick'?container:null;}};
 const context={mock:{api:async action=>action==='mp-config'?{enabled:true}:action==='checkout-prepare'?{draft_id:'unit-test',amount:1,public_key:'public-test'}:typeof outcome==='function'?outcome():outcome,esc:s=>String(s??'').replace(/</g,'&lt;'),money:v=>String(v),toast(){},getLocal(){return null},setLocal(k,v){stored[k]=v}},document:{documentElement:{dataset:{theme:'light'}},querySelector:s=>s==='#payment-preview'?target:null},window:{MercadoPago:class{bricks(){return {create:async(a,b,settings)=>{callbacks=settings.callbacks;assert.equal(settings.customization.visual.style.customVariables.buttonTextColor,'#ffffff');return {unmount:async()=>{}};}};}}},FormData:class{*[Symbol.iterator](){yield ['email','test@example.test'];}},console};
 vm.runInNewContext(source,context);await context.testFns.mountPayment({email:{value:'test@example.test'}},[],[{id:'pickup',name:'Retirada',price:0}]);
 try{await callbacks.onSubmit({formData:{}});}catch(e){assert.equal(e.message,'Provider unavailable');}
 assert.equal(busy,false);return {feedback,container,stored};
}
(async()=>{
 let r=await scenario(()=>{throw Error('Provider unavailable');});assert.match(r.feedback.innerHTML,/Não foi possível concluir/);assert.match(r.feedback.innerHTML,/Provider unavailable/);assert.equal(r.container.hidden,false);
 r=await scenario({order:{id:'TEST',payment_status:'approved'},token:'test'});assert.match(r.feedback.innerHTML,/Pagamento aprovado/);assert.equal(r.container.hidden,true);assert.deepEqual(Array.from(r.stored['bryxa-cart']),[]);
 r=await scenario({order:{id:'TEST',payment_status:'pending'},token:'test',qr_code:'pix-test'});assert.match(r.feedback.innerHTML,/pix-test/);assert.equal(r.container.hidden,true);assert.equal(r.stored['bryxa-cart'],undefined);
 console.log('PASS: visible payment errors, approval, Pix pending, cart preservation and branded styles. No payment requests sent.');
})().catch(e=>{console.error(e);process.exit(1);});
