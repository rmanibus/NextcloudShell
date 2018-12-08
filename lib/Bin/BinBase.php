<?php

namespace OCA\NextcloudShell\Bin;

abstract class BinBase implements IBin {

  protected $shell;

  public function __construct($shell){
    $this->shell = $shell;
  }

}
