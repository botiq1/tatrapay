<?php
include('autoloader.php');

use botiq\tatrapay\Request;
use botiq\tatrapay\Response;
use botiq\tatrapay\Verifier as Verifier;
use botiq\tatrapay\Signer as Signer;
use botiq\tatrapay\SignProvider as SignProvider;

$mid = 9999;
$key = '12345678';
$amt = '1234.50';
$curr = 978;
$vs = 1111;
$rurl = 'https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp';
$timestamp = null;
$cs = '0308';
$rem = null;
$ss = null;
$lang = 'sk';
$aredir = 1;

/*
* request DES
*/
try
{
  $request = new Request($mid, $amt, $curr, $vs, $rurl, $lang, $ss, $cs, $rem, $aredir, $timestamp);

  $signer = new Signer\DefaultSigner(new SignProvider\DESSignProvider());
  $signer->sign($key, $request);

  $url = $request->getRedirectUrl();
  echo '<pre>',$url,'</pre>';
  // redirect to $url
}
catch(\Exception $e)
{
  // handle errors
  die($e->getMessage());
}

/*
* response DES
*/
try
{
  // $data = $_GET; // bank data received from POST/GET/..
  $data = array(
    'VS' => 1111,
    'RES' => 'OK',
    'SIGN' => '810EE9A1BCE9CD94',
  );

  $response = new Response($data);

  $verifier = new Verifier\DefaultVerifier(new SignProvider\DESSignProvider());
  if(!$verifier->verify($key, $response))
  {
    // sign not valid
    die('sign not valid');
  }

  // sign valid
  echo '<p>sign valid</p>';

  echo $response->getField('RES'),'<br>',$response->getField('VS');
}
catch(Exception $e)
{
  die($e->getMessage());
}
