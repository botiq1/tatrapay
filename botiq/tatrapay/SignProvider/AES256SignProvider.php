<?php
namespace botiq\tatrapay\SignProvider;

class AES256SignProvider implements ISignProvider
{

  function __construct()
  {
    if(!function_exists('mcrypt_encrypt'))
    {
      throw new \Exception('mcrypt module required for '.__CLASS__);
    }
  }

  function sign($key, $signbase)
  {
    $hash = sha1($signbase, true);
    $hash = substr($hash, 0, 16);

    $sign = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $hash, MCRYPT_MODE_ECB);
    $sign = strtoupper(bin2hex($sign));
    return $sign;
  }

}
