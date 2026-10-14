<?php
if (!defined('BRYXA_API')) exit;
function proPreference(array $order, string $domain): array {
    $base = 'https://' . preg_replace('/[^a-zA-Z0-9.-]/', '', $domain);
    // One authoritative total includes customization, discount and shipping.
    // Individual pieces, sizes and nicks remain in the store order.
    return [
        'items' => [['id'=>$order['id'],'title'=>'Pedido '.$order['id'].' · BRYXA','quantity'=>1,'currency_id'=>'BRL','unit_price'=>round($order['total'],2)]],
        'payer' => ['name'=>$order['customer']['name'],'email'=>$order['customer']['email']],
        'external_reference' => $order['id'],
        'notification_url' => $base.'/api/index.php?action=mp-webhook&source_news=webhooks',
        'back_urls' => array_fill_keys(['success','failure','pending'],$base.'/acompanhar/?retorno=1'),
        'auto_return' => 'approved',
        'payment_methods' => ['installments'=>12],
    ];
}
function proCheckoutUrl(array $preference, bool $sandbox): string {
    $url = $preference[$sandbox?'sandbox_init_point':'init_point']??'';
    $parts = parse_url($url);
    if (($parts['scheme']??'')!=='https' || !in_array($parts['host']??'', ['www.mercadopago.com.br','sandbox.mercadopago.com.br','www.mercadopago.com','sandbox.mercadopago.com'],true)) throw new RuntimeException('Invalid Mercado Pago checkout URL');
    return $url;
}
if ($action==='mp-pro' && $method==='POST') {
    requestLimit('mp-pro',15);
    if (!mpEnabled() || demoMode()) fail('Pagamento indisponível.',503);
    $b=body();
    $q=$db->prepare('SELECT * FROM checkout_drafts WHERE id=? AND session_id=? AND expires>?');
    $q->execute([$b['draft_id']??'',session_id(),time()]);
    $row=$q->fetch(PDO::FETCH_ASSOC);
    if (!$row) fail('Sessão expirada. Recalcule o frete.',403);
    $draft=json_decode($row['data'],true);
    if (($draft['shipping']['id']??'')==='pickup' && empty(mpConfig()['pickup_enabled'])) fail('Retirada desativada. Recalcule o frete.');
    if ((bool)$draft['sandbox']!==(bool)(mpConfig()['mp_sandbox']??true)) fail('O ambiente foi alterado. Atualize a página.');
    $db->beginTransaction();
    $q=$db->prepare('SELECT data FROM orders WHERE id=?');$q->execute([$draft['id']]);
    $existing=$q->fetchColumn();
    if ($existing) $order=json_decode($existing,true);
    else {
        $order=$draft;$token=bin2hex(random_bytes(32));
        $order['access_link']='https://'.loadDoc('settings')['domain'].'/acompanhar/#'.$token;
        $order['checkout_mode']='pro';
        $products=loadDoc('products');
        foreach ($order['items'] as $item) {
            $found=false;
            foreach ($products as &$p) if ($p['id']===$item['id']) {
                if (($p['stock'][$item['size']]??0)<$item['quantity']) {$db->rollBack();fail('Estoque alterado. Refaça a compra.',409);}
                $p['stock'][$item['size']]-=$item['quantity'];$found=true;
            }
            unset($p);
            if (!$found) {$db->rollBack();fail('Produto indisponível.');}
        }
        saveDoc('products',$products);$order['reserved_stock']=true;
        $db->prepare('INSERT INTO order_access VALUES(?,?)')->execute([$order['id'],hash('sha256',$token)]);
        $db->prepare('INSERT INTO orders VALUES(?,?,?)')->execute([$order['id'],json_encode($order),date('c')]);
    }
    $db->commit();
    if (!empty($order['checkout_url'])) $url=proCheckoutUrl(['init_point'=>$order['checkout_url'],'sandbox_init_point'=>$order['checkout_url']],$order['sandbox']);
    else {
        // Recover a preference after an interrupted response rather than duplicate it.
        $preference=null;
        if ($existing) {
            $search=mpRequest('/checkout/preferences/search?external_reference='.urlencode($order['id']));
            $candidate=$search['elements'][0]['id']??null;
            if ($candidate) $preference=mpRequest('/checkout/preferences/'.rawurlencode($candidate));
        }
        $preference??=mpRequest('/checkout/preferences',proPreference($order,loadDoc('settings')['domain']),$order['id']);
        $url=proCheckoutUrl($preference,$order['sandbox']);
        // Preserve a concurrently received webhook update.
        $db->beginTransaction();$q=$db->prepare('SELECT data FROM orders WHERE id=?');$q->execute([$order['id']]);$order=json_decode($q->fetchColumn(),true);
        $order['preference_id']=$preference['id'];$order['checkout_url']=$url;
        saveOrder($order);orderEmail($order,'order_created',$order['id'].':created');$db->commit();
    }
    reply(['checkout_url'=>$url,'token'=>substr($order['access_link'],strrpos($order['access_link'],'#')+1)]);
}
if ($action==='payment-refresh' && $method==='POST') {
    requestLimit('payment-refresh',15);$b=body();$token=textValue($b['token']??'',100);$paymentId=textValue((string)($b['payment_id']??''),40);
    if (!preg_match('/^[0-9]+$/',$paymentId)) fail('Pagamento inválido.');
    $q=$db->prepare('SELECT order_id FROM order_access WHERE token_hash=?');$q->execute([hash('sha256',$token)]);$orderId=$q->fetchColumn();
    if (!$orderId) fail('Link inválido.',403);
    $payment=mpRequest('/v1/payments/'.$paymentId);
    if (($payment['external_reference']??'')!==$orderId) fail('Pagamento não corresponde ao pedido.',403);
    paymentUpdate($payment);reply(['ok'=>true]);
}
