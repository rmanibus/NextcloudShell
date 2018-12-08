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

class Cmd {

  private $cmdString;
  private $cmdArray;

  public function __construct($cmdString){
    $this->cmdString = $cmdString;
    $this->cmdArray = $this->parseCmd($cmdString);

  }
  public function getProgram(){
    return $this->cmdArray[0];
  }
  public function getArgs(){
    return $this->cmdArray;
  }
  public function getArg($id){
    return $this->cmdArray[$id];
  }
  public function setArg($key, $value){
    $this->cmdArray[$key] = $value;
  }
  public function addArg($value){
    $this->cmdArray[] = $value;
  }
  public function getNbArgs(){
    return count($this->cmdArray);
  }

  private function parseCmd($cmd){
    return str_getcsv($cmd, ' ');
  }


}
