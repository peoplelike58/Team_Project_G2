<?php

function verifyRecaptcha ($recaptchaToken){
    //使用者回應的金鑰
    $secretKey = "6LepsL4rAAAAAEkJMt7-oobIWoAgIZrxAcPq7wV4";

    //API 要求(FM GOOGLE)
    //網址：https://www.google.com/recaptcha/api/siteverify
    //方法：POST
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $res = file_get_contents($url . "?secret=" . $secretKey . "&response=" . $recaptchaToken);
    $result = json_decode($res, true);

    return $result['success'] ?? false;
}


//API 回應JSON物件
// {
//     "success": true|false,
//     "challenge_ts": timestamp,  // timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
//     "hostname": string,         // the hostname of the site where the reCAPTCHA was solved
//     "error-codes": [...]        // optional
//   }