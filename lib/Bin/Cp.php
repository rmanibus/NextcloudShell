<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Cp extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $output->writeln("cp: missing file operand");
      return;
    }
    if($cmd->getNbArgs() === 2){
      $output->writeln("cp: missing destination file operand after ".$cmd->getArg(1));
      return;
    }

    // Check if inputfile exist ... (should use stat ?)
    if(!$currentView->file_exists($cmd->getArg(1))){
      $output->writeln("cp: cannot stat ".$cmd->getArg(1).": No such file or directory");
      return;
    }

    //[TODO] Check if destination directory exist
    //[TODO] Check if destination is a directory (in this case, keep filename & copy in dir)

    if($currentView->copy($cmd->getArg(1), $cmd->getArg(2))){
      $output->writeln("cp ".$cmd->getArg(1)." => ".$cmd->getArg(2));
    }else{
      $output->writeln("could not copy");
    }

  }
}
