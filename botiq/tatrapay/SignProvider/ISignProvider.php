<?php
namespace botiq\tatrapay\SignProvider;

interface ISignProvider
{

  public function sign($key, $sigbase);

}
