<?php
const BRYXA_API=true;$action='qa';$method='GET';
require __DIR__.'/../public/api/checkout-pro.php';
function check($value){if(!$value)throw new RuntimeException('Checkout Pro check failed');}
$o=['id'=>'TEST','total'=>101.25,'customer'=>['name'=>'QA','email'=>'qa@example.test']];
$p=proPreference($o,'bryxa.com.br');check($p['items'][0]['unit_price']===101.25);check($p['items'][0]['quantity']===1);check($p['external_reference']==='TEST');check($p['back_urls']['success']==='https://bryxa.com.br/acompanhar/?retorno=1');check($p['auto_return']==='approved');check(!isset($p['token']));
check(proCheckoutUrl(['init_point'=>'https://www.mercadopago.com.br/checkout/test'],false)==='https://www.mercadopago.com.br/checkout/test');
foreach(['http://www.mercadopago.com.br/test','https://evil.example','https://www.mercadopago.com.br.evil.example'] as $url){try{proCheckoutUrl(['init_point'=>$url],false);throw new LogicException('Unsafe redirect');}catch(RuntimeException $e){}}
echo "Checkout Pro preference totals, return URL and redirect validation passed.\n";
