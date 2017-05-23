<?php
include('autoloader.php');

use botiq\tatrapay\Request;
use botiq\tatrapay\Response;
use botiq\tatrapay\Verifier as Verifier;
use botiq\tatrapay\Signer as Signer;
use botiq\tatrapay\SignProvider as SignProvider;

$mid = 9999;
$key = pack("H*", '31323334353637383930313233343536373839303132333435363738393031323132333435363738393031323334353637383930313233343536373839303132');
$amt = '1234.50';
$curr = 978;
$vs = 1111;
$rurl = 'https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp';
$rem = null;
$timestamp = '01092014125505';
$cs = '0308';

/*
* request HMAC
*/
try
{
  $request = new Request($mid, $amt, $curr, $vs, $rurl, 'sk', null, $cs, $rem, 1, $timestamp);

  $signer = new Signer\HMACSigner(new SignProvider\HMACSignProvider());
  $signer->sign($key, $request);

  $url = $request->getRedirectUrl();

  echo '<pre>',$url,'</pre>';
  // redirect to $url
}
catch(Exception $e)
{
  // handle errors
  die($e->getMessage());
}

/*
* response HMAC
*/
try
{
  //$data = $_GET; // bank data received from POST/GET/..
  $data = array(
    'AMT' => '1234.50',
    'CURR' => 978,
    'VS' => 1111,
    'CS' => '0308',
    'RES' => 'OK',
    'TID' => 1,
    'TIMESTAMP' => '01092014125505',
    'HMAC' => 'ff1780ef346419d8460dd7f9dec48506524effdb6d2c9739ac44bab07a28b80f',
    'ECDSA_KEY' => 1,
    'ECDSA' => '304402204b7e92ee619fe475ac11d4516fcc16eb3df03c3bc45178ac45f18693516d78a102204c7d1906b5e4ea55e49599273a2cec15c3275ea6884a2006d34be77fd573768a',
  );

  $response = new Response($data);

  $verifier = new Verifier\HMACVerifier(new SignProvider\HMACSignProvider());
  if(!$verifier->verify($key, $response))
  {
    // handle sign not valid
    die('hmac not valid');
  }

  // additional TB ECDSA verification for HMAC

  $ecdsaData = '
KEY_ID: 1
-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEozvFM1FJP4igUQ6kP8ofnY7ydIWksMDk1IKXyr/T
RDoX4sTMmmdiIrpmCZD4CLDtP0j2LfD7saSIc8kZUwfILg==
-----END PUBLIC KEY-----
'; // ecdsa test key

  //$ecdsaData = file_get_contents('https://moja.tatrabanka.sk/e-commerce/ecdsa_keys.txt');

  $ecdsa = new Verifier\ECDSAVerifier();
  if(!$ecdsa->verify($ecdsaData, $response))
  {
    // handle ecdsa not valid
    die('ecdsa not valid');
  }

  // handle sign & ecdsa valid
  echo 'hmac & ecdsa valid<br>';
  echo $response->getField('RES'),'<br>'; // has to be 'OK'
  echo $response->getField('VS'),'<br>';
  echo $response->getField('CURR'),'<br>';
  echo $response->getField('AMT');
}
catch(Exception $e)
{
  // handle errors
  die($e->getMessage());
}

/*

Request constructor values:

required:
  MID - merchant ID
  AMT - amount
  CURR - currency, supports only 978 (EUR)
  VS - variable symbol
  RURL - return url

optional:
  LANG - language (en/sk)
  SS - specific symbol
  CS - constant symbol
  REM - return email
  AREDIR - automatic redirect after payment (1/0)
  TIMESTAMP - current dmYHis

*/

