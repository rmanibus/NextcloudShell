<?php

namespace OCA\NextcloudShell\Util;

class Cmd {

  private $cmdString;
  private $cmdArray;

  public function __construct($cmdString){
    $this->cmdString = $cmdString;
    $this->cmdArray = $this->parseCmd($cmdString);

  }
  public function getProgram(){
    return $this->cmdArray[0];
  }
  public function getArgs(){
    return $this->cmdArray;
  }
  public function getArg($id){
    return $this->cmdArray[$id];
  }
  public function setArg($key, $value){
    $this->cmdArray[$key] = $value;
  }
  public function addArg($value){
    $this->cmdArray[] = $value;
  }
  public function getNbArgs(){
    return count($this->cmdArray);
  }

  private function parseCmd($cmd){
    return str_getcsv($cmd, ' ');
  }


}
