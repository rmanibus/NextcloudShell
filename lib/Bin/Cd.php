<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Cd extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){

    if($cmd->getNbArgs() === 1){
      $currentView->chroot($this->shell->getHomeView()->getRoot());
      return;
    }

    $currentLocationArray = explode(  "/" , $this->shell->getHomeView()->getRelativePath($currentView->getRoot()));

    if(end($currentLocationArray) === "" ){
        array_pop ( $currentLocationArray );
    }
    var_dump($currentLocationArray);

    $relativeTargetLocationArray = explode("/", $cmd->getArg(1));

    foreach($relativeTargetLocationArray as $item){
      if($item ===".."){
        array_pop ( $currentLocationArray );
      }else{
        array_push ($currentLocationArray, $item) ;
      }
    }
    var_dump($currentLocationArray);

    $targetLocation = implode("/", $currentLocationArray);

    if(!$this->shell->getHomeView()->file_exists($targetLocation)){
      $output->writeln($cmd->getArg(1).": No such file or directory");
      return;
    }
    if(!$this->shell->getHomeView()->is_dir($targetLocation)){
      $output->writeln($cmd->getArg(1).": Not a directory");
      return;
    }

    //[TODO] Still need to handle ".." in path

    $currentView->chroot($this->shell->getHomeView()->getRoot().$targetLocation);


  }
}
