<?php
set_time_limit(0);

$user = "USERNAME123";
$pass = "123456";

$login = curl_init("http://www.neopets.com/login.phtml");

curl_setopt_array($login, array(
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => "username=".$user."&password=".$pass,
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_SSL_VERIFYHOST => FALSE,
    CURLOPT_HEADER => TRUE,
    CURLOPT_REFERER => "http://www.neopets.com/",
    CURLOPT_RETURNTRANSFER => TRUE,
));

//regex noob
preg_match_all("/Set-Cookie: ([^\n]+)/sim", curl_exec($login), $cookies);
$cookie = "";
foreach($cookies[1] as $coookie){
    $cookie.=$coookie.";";
}
curl_close($login);


while(1){
    $cache = rand();
    $listpets = curl_init();
    curl_setopt($listpets, CURLOPT_URL, "http://www.neopets.com/pound/get_adopt.phtml?r=".$cache."");
    curl_setopt($listpets, CURLOPT_COOKIE, $cookie);
    curl_setopt($listpets, CURLOPT_RETURNTRANSFER, TRUE);

    $pets = json_decode(curl_exec($listpets), true);
    curl_close($listpets);

    if(count($pets) != "0"){
        foreach($pets as $pet){
            $sum = $pet['str'] + $pet['def'];
            ///////////////////////REQUIREMENTS HERE
            if( strtolower($pet['species']) == "lupe" || //Species = lupe
                strlen($pet['name']) == "2" || //Name with 2 digits
                $sum > "200" || //Strength + defense > 200
                (strtolower($pet['species']) == "quiggle" && strtolower($pet['color']) == "island")){ //Island Quiggle
            ///////////////////////////////////////
                adopt($pet['name']);
            }
        }
    }
    sleep(1);
}

function adopt($petname){
    global $cookie;
    $adopt = curl_init("http://www.neopets.com/pound/process_adopt.phtml");

    curl_setopt_array($adopt, array(
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => "pet_name=".$petname,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_COOKIE => $cookie,
        CURLOPT_RETURNTRANSFER => TRUE
        ));
    curl_exec($adopt);
    curl_close($adopt);

    die($petname." successfully adopted");
}
?>