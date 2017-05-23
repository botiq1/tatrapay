<?php
include('autoloader.php');

use botiq\tatrapay\Request;
use botiq\tatrapay\Response;
use botiq\tatrapay\Verifier as Verifier;
use botiq\tatrapay\Signer as Signer;
use botiq\tatrapay\SignProvider as SignProvider;

$mid = 9999;
$key = pack("H*", '3132333435363738393031323334353637383930313233343536373839303132');
$amt = '1234.50';
$curr = 978;
$vs = 1111;
$rurl = 'https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp';
$rem = 'online_platby@obchodnik.sk';
$timestamp = '16092014132529';
$cs = '0308';
$ss = null;
$aredir = 1;
$lang = 'sk';

/*
* request AES256
*/
try
{
  $request = new Request($mid, $amt, $curr, $vs, $rurl, $lang, $ss, $cs, $rem, $aredir, $timestamp);

  $signer = new Signer\DefaultSigner(new SignProvider\AES256SignProvider());
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
* response AES256
*/
try
{
  // $data = $_GET; // bank data received from POST/GET/..
  $data = array(
    'VS' => 1111,
    'RES' => 'OK',
    'SIGN' => 'B723847953E02F70690FBA433173BBEC',
  );

  $response = new Response($data);

  $verifier = new Verifier\DefaultVerifier(new SignProvider\AES256SignProvider());
  if(!$verifier->verify($key, $response))
  {
    // sign not valid
    die('sign not valid');
  }

  // sign valid
  echo 'sign valid<br>';
  echo $response->getField('RES'),'<br>';
  echo $response->getField('VS');
}
catch(Exception $e)
{
  die($e->getMessage());
}
