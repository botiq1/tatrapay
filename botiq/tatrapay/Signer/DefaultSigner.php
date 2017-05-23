<?php
namespace botiq\tatrapay\Signer;
use botiq\tatrapay\SignProvider as SignProvider;
use botiq\tatrapay as tatrapay;

class DefaultSigner
{

  protected $signProvider;

  public function __construct(SignProvider\ISignProvider $signProvider)
  {
    $this->signProvider = $signProvider;
  }

  public function sign($key, tatrapay\Request $payload)
  {
    $signbase = $this->createSignBase($payload);
    $sign = $this->signProvider->sign($key, $signbase);
    $payload->addField('SIGN', $sign);
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

    return $signbase;
  }

}
