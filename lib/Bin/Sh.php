<?php

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Sh extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){

    //This should allow to execute a basic shell script: parse file passed in first operand, execute each line.
    $output->writeln("Not implemented yet");

  }
}
