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

class Ls extends BinBase {

  public function exec(Cmd $cmd, OutputInterface $output, View $currentView){
    if($cmd->getNbArgs() === 1){
      $cmd->setArg(1, "");
    }
    $destinationAbsolutePath = $this->getAbsolutePath($currentView , $cmd->getArg(1));

    //[TODO] Add some formating option (-l ...)
    array_walk ( $this->shell->getHomeView()->getDirectoryContent($destinationAbsolutePath) ,function ($fileInfo) use ($output)  {
      if($fileInfo->getType() ==='dir'){
        $output->writeln('<dir>'.$fileInfo->getName().'</dir>');
      }else{
        $output->writeln('<file>'.$fileInfo->getName().'</file>');
      }
    });
  }
}
