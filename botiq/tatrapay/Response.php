<?php
namespace botiq\tatrapay;

class Response
{

  protected $data;

  public function __construct($data)
  {
    $whitelist = array('VS', 'RES', 'SS', 'CS', 'AMT','CURR', 'TID', 'TIMESTAMP', 'HMAC', 'SIGN', 'ECDSA_KEY', 'ECDSA');

    foreach($data as $k=>$v)
    {
      if(in_array($k, $whitelist))
      {
        $this->data[$k] = $v;
      }
    }

    if(empty($this->data['VS']))
    {
      throw new \Exception('missing parameter VS');
    }

    if(empty($this->data['RES']) || !in_array($this->data['RES'], array('OK', 'FAIL', 'TOUT')))
    {
      throw new \Exception('missing parameter RES');
    }

    if(empty($this->data['SIGN']) && empty($this->data['HMAC']))
    {
      throw new \Exception('missing parameter SIGN/HMAC');
    }

    $this->data['VS'] = (int)$this->data['VS'];
  }

  public function getField($field)
  {
    if(isset($this->data[$field]))
    {
      return $this->data[$field];
    }

    return null;
  }

}
