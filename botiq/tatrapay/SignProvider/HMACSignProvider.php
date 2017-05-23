<?php
namespace botiq\tatrapay\SignProvider;

class HMACSignProvider implements ISignProvider
{

  function sign($key, $signbase)
  {
    $sign = hash_hmac('sha256', $signbase, $key);
    return $sign;
  }

}
