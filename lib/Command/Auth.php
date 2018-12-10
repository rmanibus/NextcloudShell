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

namespace OCA\NextcloudShell\Command;

  use Symfony\Component\Console\Command\Command;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;

class Auth extends Command {
  protected function configure() {
    $this
    ->setName('nextcloudshell:auth')
    ->setDescription('Authenticate for Nextcloud shell.');

  }
    protected function execute(InputInterface $input, OutputInterface $output) {
      $output->writeln("hello:");
      $output->writeln(getenv('PAM_USER'));
      return 0;
    }
}
