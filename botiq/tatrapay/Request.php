<?php
namespace botiq\tatrapay;

class Request
{
  const GATEWAY_URL = 'https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay';

  public function __construct($MID, $AMT, $CURR, $VS, $RURL, $LANG = 'sk', $SS = null, $CS = null, $REM = null, $AREDIR = 0, $TIMESTAMP = null)
  {
    if(preg_match('/^[0-9a-z]{3,4}$/i', $MID)!==1)
    {
      throw new \Exception('invalid parameter MID');
    }

    if(preg_match('/^[0-9]+(\\.[0-9]+)?$/', $AMT)!==1)
    {
      throw new \Exception('invalid parameter AMT');
    }

    if(strlen($VS)>10 || preg_match('/^[0-9]+$/', $VS)!==1)
    {
      throw new \Exception('invalid parameter VS');
    }

    if(!empty($CS))
    {
      if(strlen($CS)>4 || preg_match('/^[0-9]+$/', $CS)!==1)
      {
        throw new \Exception('invalid parameter CS');
      }
    }

    if(empty($RURL))
    {
      throw new \Exception('invalid parameter RURL');
    }

    $restricted = array('&', '?', ';', '=', '+', '%');
    foreach($restricted as $r)
    {
      if(false!==strpos($RURL, $r))
      {
        throw new \Exception('invalid parameter RURL');
      }
    }

    if(!empty($REM))
    {
      if(!filter_var($REM, FILTER_VALIDATE_EMAIL))
      {
        throw new \Exception('invalid parameter REM');
      }
    }

    if(!empty($LANG))
    {
      if(!in_array($LANG, array('sk', 'en')))
      {
        $LANG = 'sk';
      }
    }

    if(!in_array($AREDIR, array(0, 1)))
    {
      $AREDIR = 0;
    }

    if(!isset($TIMESTAMP) || preg_match('/^[0-9]{14}$/i', $TIMESTAMP)!==1)
    {
      $TIMESTAMP = date('dmYHis', time());
    }

    $this->data['MID'] = $MID;
    $this->data['AMT'] = $AMT;
    $this->data['CURR'] = $CURR;
    $this->data['VS'] = $VS;
    $this->data['RURL'] = $RURL;
    $this->data['LANG'] = $LANG;
    $this->data['SS'] = $SS;
    $this->data['CS'] = $CS;
    $this->data['REM'] = $REM;
    $this->data['AREDIR'] = $AREDIR;
    $this->data['TIMESTAMP'] = $TIMESTAMP;
  }

  public function addField($field, $data)
  {
    if(isset($this->data[$field]))
    {
      throw new \Exception("Request::addField(): field {$field} already exists");
    }

    $this->data[$field] = $data;
  }

  public function getField($field)
  {
    if(isset($this->data[$field]))
    {
      return $this->data[$field];
    }

    return null;
  }

  public function getRedirectUrl()
  {
    return self::GATEWAY_URL.'?'.http_build_query($this->data);
  }

}
