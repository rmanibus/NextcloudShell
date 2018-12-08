<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Mkdir extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $output->writeln("mkdir: missing operand");
      return;
    }
    if($currentView->mkdir($cmd->getArg(1))){
      $output->writeln("created ".$cmd->getArg(1));
    }else{
      $output->writeln("could not create dir");
    }
  }
}
