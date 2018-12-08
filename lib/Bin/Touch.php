<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Touch extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $output->writeln("touch: missing file operand");
      return;
    }
    if($currentView->touch($cmd->getArg(1))){
      $output->writeln("touch ".$cmd->getArg(1));
    }
    else{
      $output->writeln("could not touch");
    }
  }
}
