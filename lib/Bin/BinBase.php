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

abstract class BinBase implements IBin {

  protected $shell;

  public function __construct($shell){
    $this->shell = $shell;
  }
  // This get the absolute path in the user context. (It mean that "/" is in fact "<user>/files/" )
  public function getAbsolutePath($currentView, $relativePath){

    $currentLocationArray = explode(  "/" , $this->shell->getHomeView()->getRelativePath($currentView->getRoot()));
    array_shift ( $currentLocationArray );

    if(end($currentLocationArray) === "" ){
        array_pop ( $currentLocationArray );
    }

    $relativeTargetLocationArray = explode("/", $relativePath);

    foreach($relativeTargetLocationArray as $item){
      if($item ===".."){
        array_pop ( $currentLocationArray );
      }else{
        array_push ($currentLocationArray, $item) ;
      }
    }

    return implode("/", $currentLocationArray);
  }

}
