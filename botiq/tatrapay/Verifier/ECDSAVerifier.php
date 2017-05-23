<?php
namespace botiq\tatrapay\Verifier;
use botiq\tatrapay\Response;

class ECDSAVerifier extends HMACVerifier
{

  public function __construct()
  {
    if(!function_exists('openssl_verify'))
    {
      throw new \Exception('openssl module required for '.__CLASS__);
    }
  }

  public function verify($ECDSA, Response $payload)
  {
    $signbase = $this->createSignBase($payload);
    $signbase .= $payload->getField('HMAC');

    $payloadECDSA = pack('H*', $payload->getField('ECDSA'));
    $payloadECDSAId = (int)$payload->getField('ECDSA_KEY');
    $keyECDSA = $this->getECDSAKey($ECDSA, $payloadECDSAId);

    $result = openssl_verify($signbase, $payloadECDSA, $keyECDSA, 'sha256');

    return $result===1;
  }

  protected function getECDSAKey($ECDSA, $id)
  {
    if(empty($ECDSA))
    {
      throw new \Exception('ECDSA empty');
    }

    $pattern = '/KEY_ID: '.$id.'\b/';
    $result = preg_match($pattern, $ECDSA, $matches, PREG_OFFSET_CAPTURE);

    if(!$result || !isset($matches[0][1]))
    {
      throw new \Exception('ECDSA invalid');
    }

    $pos = $matches[0][1];
    $data = substr($ECDSA, $pos);

    $start = strpos($data, '-----BEGIN PUBLIC KEY-----');
    $end = strpos($data, '-----END PUBLIC KEY-----');

    if($start===false || $end===false)
    {
      throw new \Exception('ECDSA invalid');
    }

    $end += 24;
    $length = $end-$start;

    $data = trim(substr($data, $start, $length));
    return $data;
  }

}
