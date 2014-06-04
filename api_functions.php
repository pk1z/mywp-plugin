<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.04.14
 * Time: 17:54
 */


const HOST = 'http://dev3.lembrd.com:7070/';

/**
 * @param $email
 * @param $partnerId 'cms' for cms modules
 * @param $projectId  'cms'.site name
 * @return bool|string 'if false - somthing wrong, if string - it's cryptkey'
 */
function userReg($email, $partnerId, $projectId){

    if ($email !== '' && $partnerId !== '' && $projectId !== '') {
        $url = 'http://dev3.lembrd.com:7070/api/getCryptKeyWithUserReg.json?'.http_build_query(array(
                'email' => $email,
                'partner' => $partnerId,
                'projectId' => $projectId));

        $jsonAnswer = file_get_contents($url);
        if (false !== $jsonAnswer) {
            $answer = json_decode($jsonAnswer);
            //echo $answer->statusCode;
            return $answer->cryptKey;
        }   else return $jsonAnswer;//false; //'server return null';

    } else return 'one of params is empty';//false; //'one or more of input params are null'
}

/**
 * @param $partnerId String'cms' for cms modules
 * @param $email String
 * @param $cryptKey String
 */
function statIframe($partnerId, $mail, $cryptKey) {
    $params = array(
        'mail' => $mail,
        'partner' => $partnerId
    );
    $paramsStr = 'mail='.$mail.'&partner='.$partnerId;
    $signature = md5($paramsStr.$cryptKey);
    $params['signature'] = $signature;
    $finalUrl = 'http://dev3.lembrd.com:7070/api/statistics.html?'.http_build_query($params);

    return $finalUrl;
    //return file_get_contents($finalUrl);
}

/**
 * @param $mail user email
 * @param $partnerId 'cms' for cms sites
 * @param $projectId 'cms'.site_name
 * @param $cryptKey crypt key, received from server
 * @return string
 */
function constructorIframe($mail, $partnerId, $projectId, $cryptKey) {
    //TODO костыль-костыльчик. на серверсайде мд5 берется с собачкой в мыле.
    //а вот в статистике - с %40

    $params = array ('mail' => $mail,
        'partner' => $partnerId,
        'projectId' => $projectId);

    $paramsStr = 'mail='.$mail.'&partner='.$partnerId.'&projectId='.$projectId.$cryptKey;
    //echo http_build_query($params).$cryptKey;
    $signature = md5($paramsStr);
    $params['signature'] = $signature;
    $finalUrl = 'http://dev3.lembrd.com:7070/api/constructor.html?'.http_build_query($params);

    return $finalUrl;
}


//good
//echo userReg('pk1z@yandex.ru', 'cms', 'cmslocal');
//good
//echo statIframe('cms', 'pk1z@yandex.ru', 'z4eWFpVXWaxqNBJU322Ukkq3jHp3PURzSiOFvwVq0pRY8pHVEE2iXLKpbjBsreLi').PHP_EOL;

//echo constructorIframe('pk1z@yandex.ru', 'cms', 'cmslocal', 'z4eWFpVXWaxqNBJU322Ukkq3jHp3PURzSiOFvwVq0pRY8pHVEE2iXLKpbjBsreLi').PHP_EOL;
