<?php
require __DIR__.'/../public/api/shop-options.php';
$docs=['settings'=>['personalization_price'=>10,'gift'=>['enabled'=>true,'name'=>'Adesivo','minimum'=>100,'weight'=>0.02,'width'=>10,'height'=>1,'length'=>10]],'products'=>[['id'=>'p1','active'=>true,'price'=>50,'customizable'=>true]],'coupons'=>[['active'=>true,'code'=>'DEZ','percent'=>10]]];
function loadDoc($key){global $docs;return $docs[$key]??[];}
function fail($message){throw new RuntimeException($message);}
function textValue($value,$max=200){if(!is_string($value)||strlen($value)>$max)fail('Invalid');return trim($value);}
function check($condition,$message){if(!$condition)throw new RuntimeException($message);}
$items=[['id'=>'p1','quantity'=>2]];
check(cartPricing($items)['gift']['name']==='Adesivo','Gift missing at exact threshold');
check(cartPricing([['id'=>'p1','quantity'=>1]])['gift']===null,'Gift not removed below threshold');
check(cartPricing($items,'DEZ')['gift']===null,'Coupon must affect eligibility');
check(cartPricing($items,'DEZ')['net']===90,'Discount calculated incorrectly');
$items[0]['custom']=['phrase'=>'Nick'];
check(cartPricing($items,'DEZ')['net']===108,'Customization surcharge missing');
check(cartPricing($items,'DEZ')['gift']!==null,'Gift threshold with personalization incorrect');
try {cartPricing($items,'INATIVO');throw new Exception('Invalid coupon accepted');}catch(RuntimeException $e){}
check(cleanItemNick(['nick'=>'  Alice  '])==='Alice','Nick not sanitized');
$docs['settings']['nick_enabled']=false;
check(cleanItemNick(['nick'=>'Alice'])==='','Disabled nick not ignored');
echo "Promotion, coupon, gift threshold and per-piece nick checks passed.\n";
