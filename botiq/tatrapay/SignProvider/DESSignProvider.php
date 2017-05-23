<?php
namespace botiq\tatrapay\SignProvider;

class DESSignProvider implements ISignProvider
{

  function __construct()
  {
    if(!function_exists('mcrypt_module_open'))
    {
      throw new Exception('mcrypt module required for '.__CLASS__);
    }
  }

  function sign($key, $sigbase)
  {
    $hash = sha1($sigbase, true);
    $hash = substr($hash, 0, 8);

    $des = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");

    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($des), MCRYPT_RAND);

    mcrypt_generic_init($des, $key, $iv);

    $sign = mcrypt_generic($des, $hash);

    mcrypt_generic_deinit($des);
    mcrypt_module_close($des);

    $sign = strtoupper(bin2hex($sign));
    return $sign;
  }

}
