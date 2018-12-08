<?php


namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

interface Ibin {
  public function exec(Cmd $cmd, OutputInterface $output, View $currentView);
}
