<?php
require __DIR__ . '/../public/api/site-settings.php';
$seed = require __DIR__ . '/../seed.php';
$pages = $seed['settings']['pages'];
$pages['novidades'] = ['label'=>'Novidades', 'order'=>4];
$pages['acessorios-novos'] = ['label'=>'Acessórios novos', 'parent'=>'novidades', 'order'=>2];
$valid = validateStorePages($pages, $seed['products']);
if ($valid['acessorios-novos']['parent'] !== 'novidades' || $valid['novidades']['order'] !== 4) throw new RuntimeException('Hierarchy was not preserved.');
function rejectPages(array $pages, array $products = []): void {
    try { validateStorePages($pages, $products); } catch (InvalidArgumentException $e) { return; }
    throw new RuntimeException('Invalid hierarchy was accepted.');
}
$bad=$pages; $bad['novidades']['parent']='acessorios-novos'; rejectPages($bad);
$bad=$pages; $bad['novidades']['parent']='novidades'; rejectPages($bad);
$bad=$pages; $bad['acessorios-novos']['parent']='missing'; rejectPages($bad);
$bad=$pages; $bad['nivel-tres']=['label'=>'Outro','parent'=>'acessorios-novos']; rejectPages($bad);
$bad=$pages; $bad['checkout']=['label'=>'Reservado']; rejectPages($bad);
$bad=$pages; unset($bad['canecas']); rejectPages($bad,$seed['products']);
$bad=$pages; unset($bad['novidades']); rejectPages($bad);
$bad=$pages; $bad['novidades']['order']=-1; rejectPages($bad);
$bad=$pages; $bad['inicio']['parent']='roupas'; rejectPages($bad);
echo "Category hierarchy checks passed.\n";
