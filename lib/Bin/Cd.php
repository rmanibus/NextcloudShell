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
    if(!$currentView->file_exists($cmd->getArg(1))){
      $output->writeln($cmd->getArg(1).": No such file or directory");
      return;
    }
    if(!$currentView->is_dir($cmd->getArg(1))){
      $output->writeln($cmd->getArg(1).": Not a directory");
      return;
    }

    //[TODO] Still need to handle ".." in path

    $currentView->chroot($currentView->getRoot().'/'.$cmd->getArg(1));


  }
}
