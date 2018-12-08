<?php
/**
 * @copyright Copyright (c) 2018 Loïc Hermann <loic.hermann@free.fr>
 *
 * @author Loïc Hermann
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\NextcloudShell\Bin;

use OCA\NextcloudShell\Util\Cmd;
use Symfony\Component\Console\Output\OutputInterface;
use OC\Files\View;

class Mv extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $output->writeln("mv: missing file operand");
      return;
    }
    if($cmd->getNbArgs() === 2){
      $output->writeln("mv: missing destination file operand after ".$cmd->getArg(1));
      return;
    }
    // Check if inputfile exist ... (should use stat ?)
    if(!$currentView->file_exists($cmd->getArg(1))){
      $output->writeln("mv: cannot stat ".$cmd->getArg(1).": No such file or directory");
      return;
    }

    //[TODO] Check if destination directory exist
    //[TODO] Check if destination is a directory (in this case, keep filename & copy in dir)

    if($currentView->rename($cmd->getArg(1), $cmd->getArg(2))){
      $output->writeln("mv ".$cmd->getArg(1)." => ".$cmd->getArg(2));
    }else{
      $output->writeln("could not move");
    }

  }
}
