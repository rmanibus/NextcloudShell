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
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use OC\Files\View;

class Sh extends BinBase {

  public function getName() : String {
    return 'sh';
  }

  public function exec(Cmd $cmd){


    if($cmd->getNbArgs() === 1){
      $this->doSh($this->context->getInput());
      return;

    }else{

      $destinationAbsolutePath = $this->getAbsolutePath($cmd->getArg(1));

      if(!$this->context->getHomeView()->file_exists( $destinationAbsolutePath  )){
        $this->writeln("sh: ".$cmd->getArg(1).": No such file or directory");
        return;
      }
      $handle = $this->context->getHomeView()->fopen($destinationAbsolutePath, 'r');
      if ($handle) {
        $input = new StringInput('');
        $input->setStream($handle);
        $this->doSh($input);

        fclose($handle);
      }
      else {
        $this->writeln("Could not open file ".$cmd->getArg(1));
      }
    }



    //This should allow to execute a basic shell script: parse file passed in first operand, execute each line.


  }

  protected function doSh($input){
    $questionHelper = new QuestionHelper();
    while (!$this->eof($input)) {
      $question = new Question($this->getPS1());

      $lineCmd = new Cmd($questionHelper->ask($input, $this->context->getOutput(), $question));
      if($lineCmd->getProgram() === "exit"){
        return;
      }
      if($lineCmd->getProgram() === null){
        continue;
      }
      if(array_key_exists ( $lineCmd->getProgram() , $this->context->getPrograms() )){
        $this->context->getProgram($lineCmd->getProgram())->exec($lineCmd);
      }else{
        $output->writeln($lineCmd->getProgram().": command not found");
      }
    }
  }

  protected function eof($input){
    if(!$input->getStream()){
      return false;
    }
    return feof($input->getStream());
  }
  protected function getPS1(){
    return '<PS1_user>'.$this->context->getUser()->getUID()."@nextcloud</PS1_user>: <PS1_path>".$this->context->getHomeView()->getRelativePath($this->context->getCurrentView()->getRoot())."</PS1_path> $ ";
  }
}
