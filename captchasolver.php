<?php

//Esse script retorna as coordenadas corretas do captcha (pechincheiro)
//Basta fazer uma requisição com o preço e as coordenadas para comprar algo nas lojas de neopia
//Util pra comprar itens que dão avatar

$user = "USER123";
$pass = "123456";

function logar($user, $pass){
    $pag = acessa("http://www.neopets.com/login.phtml", 1, 1, "username=".$user."&password=".$pass."", 1);
    global $cookie;
    $cookie = "";
    preg_match_all("/Set-Cookie: ([^\n]+)/sim", $pag, $cookies);
    foreach($cookies[1] as $coookie){
        $cookie.=$coookie.";";
    }
}

function acessa($url , $return , $header, $post=0, $cookie=0, $referer=0){

    $acessa = curl_init();
    curl_setopt_array($acessa, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => $return,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HEADER => $header,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
    if($post){
        curl_setopt($acessa, CURLOPT_POST, TRUE);
        curl_setopt($acessa, CURLOPT_POSTFIELDS, $post);
    }
    if($cookie){
        curl_setopt($acessa, CURLOPT_COOKIE, $cookie);
    }
    if($referer){
        curl_setopt($acessa, CURLOPT_REFERER, $referer);
    }
    $pag = curl_exec($acessa);
    curl_close($acessa);
    return $pag;
}

logar($user,$pass);
$pixeldarkc = "1";
$pixeldark = array();
$imagem = acessa("http://www.neopets.com/captcha_show.phtml",1,0,0,$cookie);
$img = new Imagick();
$img->readImageBlob($imagem);
$largura = $img->getImageWidth();
$altura = $img->getImageHeight();
for($a=0;$a<$largura;$a++){
    for($b=0;$b<$altura;$b++){
        $pixel = $img->getImagePixelColor($a,$b);
        if($pixel->getHSL()['luminosity'] < $pixeldarkc){
            $pixeldarkc = $pixel->getHSL()['luminosity'];
            $pixeldark['x'] = $a;
            $pixeldark['y'] = $b;
        }
    }
}
print_r($pixeldark);
?>