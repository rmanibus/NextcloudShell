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

namespace OCA\NextcloudShell\Util;


use OC\Files\Filesystem;
use OC\Files\View;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use OCA\NextcloudShell\Bin\Cat;
use OCA\NextcloudShell\Bin\Cd;
use OCA\NextcloudShell\Bin\Cp;
use OCA\NextcloudShell\Bin\Ls;
use OCA\NextcloudShell\Bin\Mkdir;
use OCA\NextcloudShell\Bin\Mv;
use OCA\NextcloudShell\Bin\Rm;
use OCA\NextcloudShell\Bin\Sh;
use OCA\NextcloudShell\Bin\Touch;
use OCA\NextcloudShell\Bin\IBin;

class Configurator {

  /** @var Context */
  protected $context;


  public function __construct(Context $context){
    $this->context = $context;

  }

  public function configure($uid){
    // Intercept Ctrl-C signal
    $this->listen();

    $this->initFileSystem($uid);
    $this->initCLI($this->context->getOutput());
    $this->loadPrograms();
  }

  protected function initCLI(OutputInterface $output){
    //[TODO] allow custom setings thru nextcloudrc file.
    $outputStyle = new OutputFormatterStyle('cyan', 'black');
    $output->getFormatter()->setStyle('PS1_user', $outputStyle);
    $outputStyle = new OutputFormatterStyle('yellow', 'black');
    $output->getFormatter()->setStyle('PS1_path', $outputStyle);

    $outputStyle = new OutputFormatterStyle('green', 'black');
    $output->getFormatter()->setStyle('file', $outputStyle);
    $outputStyle = new OutputFormatterStyle('blue', 'green');
    $output->getFormatter()->setStyle('dir', $outputStyle);
  }

  protected function initFileSystem($uid){
    $home = '/' . $uid . '/files';
    FileSystem::init($uid,  $home);
    $this->context->setHomeView( Filesystem::getView());
    $this->context->setCurrentView( new View($home));
  }
  protected function loadPrograms(){
    //[TODO] get this by reflexion (all class implementing IBin).
    $this->context->addProgram( new Cat($this->context));
    $this->context->addProgram( new Cp($this->context));
    $this->context->addProgram( new Ls($this->context));
    $this->context->addProgram( new Cd($this->context));
    $this->context->addProgram( new Mv($this->context));
    $this->context->addProgram( new Rm($this->context));
    $this->context->addProgram( new Sh($this->context));
    $this->context->addProgram( new Touch($this->context));
    $this->context->addProgram( new Mkdir($this->context));
  }

  private function listen()
  {
    $handler = function ($code) {
      // Don't do anything for now
      // We should allow to Ctrl + c to kill the execution of a command.
    };
    // Ctrl + C
    pcntl_signal(SIGINT, $handler);

  }
}
