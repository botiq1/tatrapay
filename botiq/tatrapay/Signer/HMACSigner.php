<?php
namespace botiq\tatrapay\Signer;
use botiq\tatrapay as tatrapay;

class HMACSigner extends DefaultSigner
{

  public function sign($key, tatrapay\Request $payload)
  {
    $signbase = $this->createSignBase($payload);
    $sign = $this->signProvider->sign($key, $signbase);
    $payload->addField('HMAC', $sign);
  }

  protected function createSignBase(tatrapay\Request $payload)
  {
    $signbase = '';
    $signbase .= $payload->getField('MID');
    $signbase .= $payload->getField('AMT');
    $signbase .= $payload->getField('CURR');
    $signbase .= $payload->getField('VS');
    $signbase .= !empty($payload->getField('SS')) ? $payload->getField('SS') : '';
    $signbase .= !empty($payload->getField('CS')) ? $payload->getField('CS') : '';
    $signbase .= $payload->getField('RURL');
    $signbase .= !empty($payload->getField('REM')) ? $payload->getField('REM') : '';
    $signbase .= $payload->getField('TIMESTAMP');

    return $signbase;
  }

}
