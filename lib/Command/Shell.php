<?php
/**
 * @copyright Copyright (c) 2017 Bjoern Schiessle <bjoern@schiessle.org>
 *
 * @author Bjoern Schiessle <bjoern@schiessle.org>
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
use OCP\IUserManager;
use OC\Files\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

declare(ticks = 1);

pcntl_signal(SIGINT, "signal_handler");

function signal_handler($signal) {
    switch($signal) {
        case SIGINT:
            print "Ctrl C\n";
    }
}

class Shell extends Command {
	/** @var  QuestionHelper */
	protected $questionHelper;
	/** @var IUserManager */
	protected $userManager;
	
	public function __construct(QuestionHelper $questionHelper, IUserManager $userManager) {
		$this->questionHelper = $questionHelper;
		$this->userManager = $userManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('nextcloudshell:run')
			->setDescription('Run Nextcloud shell.');
			
		$this->addArgument(
			'user',
			InputArgument::REQUIRED,
			'user which should be recovered'
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
			$uid = $input->getArgument('user');
			$userExists = $this->userManager->userExists($uid);

			if ($userExists === false) {
				$output->writeln('User "' . $uid . '" unknown.');
				return;
			}
			$user = $this->userManager->get($uid);

			$output->writeln('This is the shell');
			$output->writeln('last login: '.date(DATE_RFC2822, $user->getLastLogin()));
			$user->updateLastLoginTimestamp();
			
			$home = $user->getHome();
			
			FileSystem::init($uid, $home);
			
			//$output->writeln(Filesystem::getRoot());
			
			do{
				$question = new Question($user->getUID()."@nextcloud $ ");
				$command = $this->questionHelper->ask($input, $output, $question);
				switch($command) {
					case 'ls':
						$output->writeln("ls !");
						break;
					case 'cp':
						$output->writeln("cp !");
						break;
					case 'mv':
						$output->writeln("mv !");
						break;						
					case 'rm':
						$output->writeln("rm !");
						break;
					case 'cd':
						$output->writeln("cd !");
						break;
					case '':
						break;
					default:
						$output->writeln("$command: command not found");
				}
				
				 
			} while(1);


	}

}