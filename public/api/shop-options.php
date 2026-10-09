<?php
declare(strict_types=1);
function cartPricing(array $items,string $couponCode=''):array {
 $catalog=array_column(loadDoc('products'),null,'id');$cents=0;$s=loadDoc('settings');
 foreach($items as $item){$p=$catalog[$item['id']??'']??null;$qty=$item['quantity']??0;if(!$p||empty($p['active'])||!is_int($qty)||$qty<1||$qty>100)fail('Produto ou quantidade inválida.');$custom=!empty($item['custom']);if($custom&&!($p['customizable']??in_array($p['category'],['Moletom','Camiseta','Bermuda'],true)))fail('Esta peça não permite personalização.');$cents+=((int)round($p['price']*100)+($custom?(int)round(($s['personalization_price']??0)*100):0))*$qty;}
 $discount=0;$couponCode=strtoupper(trim($couponCode));if($couponCode!==''){$found=false;foreach(loadDoc('coupons') as $c)if($c['active']&&$c['code']===$couponCode){$discount=(int)round($cents*$c['percent']/100);$found=true;break;}if(!$found)fail('O cupom não está mais disponível. Remova-o ou escolha outro.');}
 $gift=$s['gift']??null;if(!$gift||empty($gift['enabled'])||$cents-$discount<(int)round($gift['minimum']*100)||$cents-$discount<=0)$gift=null;
 return ['subtotal'=>$cents/100,'discount'=>$discount/100,'net'=>($cents-$discount)/100,'gift'=>$gift?['name'=>$gift['name'],'quantity'=>1,'price'=>0,'weight'=>$gift['weight'],'width'=>$gift['width'],'height'=>$gift['height'],'length'=>$gift['length']]:null];
}
function cleanItemNicks(array $item):array{if(!(loadDoc('settings')['nick_enabled']??true))return []; $nicks=$item['nicks']??[($item['nick']??'')];if(!is_array($nicks)||count($nicks)>($item['quantity']??1))fail('Informe até um nick por peça.');return array_map(fn($nick)=>textValue($nick,60),array_values($nicks));}
function cleanItemNick(array $item):string{return implode(', ',array_filter(cleanItemNicks($item)));}
