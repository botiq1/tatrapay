<?php
namespace botiq\tatrapay\Verifier;
use botiq\tatrapay\Response;
use botiq\tatrapay\SignProvider\ISignProvider;

class DefaultVerifier
{

  protected $signProvider;

  public function __construct(ISignProvider $signProvider)
  {
    $this->signProvider = $signProvider;
  }

  public function verify($key, Response $payload)
  {
    $signbase = $this->createSignBase($payload);
    $sign = $this->signProvider->sign($key, $signbase);
    $signToVerify = $payload->getField('SIGN');

    return $sign===$signToVerify;
  }

  protected function createSignBase(Response $payload)
  {
    $signbase = $payload->getField('VS');
    $signbase .= !empty($payload->getField('SS')) ? $payload->getField('SS') : '';
    $signbase .= $payload->getField('RES');

    return $signbase;
  }

}
