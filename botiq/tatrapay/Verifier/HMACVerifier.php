<?php
namespace botiq\tatrapay\Verifier;
use botiq\tatrapay\Response;

class HMACVerifier extends DefaultVerifier
{

  public function verify($key, Response $payload)
  {
    $signbase = $this->createSignBase($payload);

    $sign = $this->signProvider->sign($key, $signbase);
    $signToVerify = $payload->getField('HMAC');

    return $sign===$signToVerify;
  }

  protected function createSignBase(Response $payload)
  {
    $signbase = $payload->getField('AMT');
    $signbase .= $payload->getField('CURR');
    $signbase .= $payload->getField('VS');
    $signbase .= !empty($payload->getField('SS')) ? $payload->getField('SS') : '';
    $signbase .= !empty($payload->getField('CS')) ? $payload->getField('CS') : '';
    $signbase .= $payload->getField('RES');
    $signbase .= $payload->getField('TID');
    $signbase .= $payload->getField('TIMESTAMP');

    return $signbase;
  }

}
