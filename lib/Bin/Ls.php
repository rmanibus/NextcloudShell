<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Ls extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $cmd->setArg(1, "");
    }
    //[TODO] Add some formating option (-l ...)
    array_walk ( $currentView->getDirectoryContent($cmd->getArg(1)) ,function ($fileInfo) use ($output)  {
      if($fileInfo->getType() ==='dir'){
        $output->writeln('<dir>'.$fileInfo->getName().'</dir>');
      }else{
        $output->writeln('<file>'.$fileInfo->getName().'</file>');
      }
    });
  }
}
