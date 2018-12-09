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

use OCP\IUser;
use OC\Files\View;
use OCA\NextcloudShell\Bin\IBin;
use Symfony\Component\Console\Output\OutputInterface;

class Context {

  /** @var OutputInterface */
  protected $output;

  /** @var IUser */
	protected $user;
  /** @var View */
  protected $homeView ;
  /** @var View */
  protected $currentView ;
  /** @var array IBin */
  protected $programs = array();

  protected $initialized = false;

  public function __construct(){


  }
  public function isInitialized(){
    return isset($output) && isset($user) && isset($homeView) && isset($currentView) && isset($programs);
  }
  public function setOutput(OutputInterface $output){
    $this->output = $output;
  }
  public function getOutput(){
    return $this->output;
  }
  public function getUser(){
    return $this->user;
  }
  public function setUser(IUser $user){
    $this->user = $user;
  }
  public function setHomeView(View $view){
    $this->homeView = $view;
  }
  public function getHomeView(){
    return $this->homeView;
  }

  public function setcurrentView(View $view){
    $this->currentView = $view;
  }
  public function getcurrentView(){
    return $this->currentView;
  }
  public function addProgram(IBin $program){
    $this->programs[$program->getName()] = $program;
  }
  public function getProgram(String $key){
    return $this->programs[$key];
  }
  public function getPrograms(){
    return $this->programs;
  }
}
