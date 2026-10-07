<?php
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if(in_array($path,['/sitemap.xml','/robots.txt'])){if($path==='/robots.txt')$_GET['robots']=1;require __DIR__.'/public/sitemap.php';return true;}
if($path==='/'||preg_match('#^/([a-z0-9-]+)/?$#',$path,$m)){
 $_GET['route']=$m[1]??'inicio';require __DIR__.'/public/seo.php';return true;
}
if(is_file(__DIR__.'/public'.$path))return false;
http_response_code(404);echo 'Página não encontrada.';
