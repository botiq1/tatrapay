<?php

spl_autoload_register(function($class) {

  $parts = explode('\\', $class);
  if(count($parts)>1 && $parts[0]=='botiq')
  {
    $path = __DIR__.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $parts);
    $path .= '.php';

    if(is_file($path))
    {
      include($path);
    }
    else
    {
      trigger_error('autoload error while loading: '.$path, E_USER_WARNING);
    }
  }

});
