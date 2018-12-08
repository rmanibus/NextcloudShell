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
use OC\Files\View;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

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

  protected function initCLI(OutputInterface $output){
    $outputStyle = new OutputFormatterStyle('cyan', 'black');
    $output->getFormatter()->setStyle('PS1_user', $outputStyle);
    $outputStyle = new OutputFormatterStyle('yellow', 'black');
    $output->getFormatter()->setStyle('PS1_path', $outputStyle);

    $outputStyle = new OutputFormatterStyle('green', 'black');
    $output->getFormatter()->setStyle('file', $outputStyle);
    $outputStyle = new OutputFormatterStyle('blue', 'green');
    $output->getFormatter()->setStyle('dir', $outputStyle);
  }

	protected function execute(InputInterface $input, OutputInterface $output) {
      // Check user
			$uid = $input->getArgument('user');
			$userExists = $this->userManager->userExists($uid);

			if ($userExists === false) {
				$output->writeln('User "' . $uid . '" unknown.');
				return;
			}
			$user = $this->userManager->get($uid);

      // Init CLI Style
      $this->initCLI($output);

      // Login Message
			$output->writeln('This is the shell');
			$output->writeln('last login: '.date(DATE_RFC2822, $user->getLastLogin()));
			$user->updateLastLoginTimestamp();

      // Init FileSystem
			$absoluteHome = $user->getHome();
      $home = '/' . $uid . '/files';

			FileSystem::init($uid,  $home);
			$homeView = Filesystem::getView();
      $currentView = new View($home);

      // CLI Loop
			do{

				$question = new Question('<PS1_user>'.$user->getUID()."@nextcloud</PS1_user>: <PS1_path>".$homeView->getRelativePath($currentView->getRoot())."</PS1_path> $ ");
				$cmd = $this->questionHelper->ask($input, $output, $question);

        // use get csv to preserve value inside quote
				$cmdArray = str_getcsv($cmd, ' ');

			switch($cmdArray[0]) {

					case 'ls':
            if(count($cmdArray) === 1){
              $cmdArray[1] = "";
            }
            //[TODO] Add some formating option (-l ...)
            array_walk ( $currentView->getDirectoryContent($cmdArray[1]) ,function ($fileInfo) use ($output)  {
              if($fileInfo->getType() ==='dir'){
                $output->writeln('<dir>'.$fileInfo->getName().'</dir>');
              }else{
                $output->writeln('<file>'.$fileInfo->getName().'</file>');
              }
            });

						break;

					case 'cp':

						if(count($cmdArray) === 1){
							$output->writeln("cp: missing file operand");
							break;
						}
						if(count($cmdArray) === 2){
							$output->writeln("cp: missing destination file operand after $cmdArray[1]");
							break;
						}

						// Check if inputfile exist ... (should use stat ?)
						if(!$currentView->file_exists($cmdArray[1])){
							$output->writeln("cp: cannot stat $cmdArray[1]: No such file or directory");
              break;
						}

            //[TODO] Check if destination directory exist
            //[TODO] Check if destination is a directory (in this case, keep filename & copy in dir)

						if($currentView->copy($cmdArray[1], $cmdArray[2])){
              $output->writeln("cp $cmdArray[1] => $cmdArray[2]");
            }else{
              $output->writeln("could not copy");
            }

						break;

					case 'mv':

            if(count($cmdArray) === 1){
              $output->writeln("mv: missing file operand");
              break;
            }
            if(count($cmdArray) === 2){
              $output->writeln("mv: missing destination file operand after $cmdArray[1]");
              break;
            }
            // Check if inputfile exist ... (should use stat ?)
            if(!$currentView->file_exists($cmdArray[1])){
              $output->writeln("mv: cannot stat $cmdArray[1]: No such file or directory");
              break;
            }

            //[TODO] Check if destination directory exist
            //[TODO] Check if destination is a directory (in this case, keep filename & copy in dir)

            if($currentView->rename($cmdArray[1], $cmdArray[2])){
              $output->writeln("mv $cmdArray[1] => $cmdArray[2]");
            }else{
              $output->writeln("could not move");
            }

						break;
            case 'touch':
              if(count($cmdArray) === 1){
                $output->writeln("touch: missing file operand");
                break;
              }
              if($currentView->touch($cmdArray[1])){
                $output->writeln("touch $cmdArray[1]");
              }
              else{
                $output->writeln("could not touch");
              }
  						break;
					case 'rm':
            if(count($cmdArray) === 1){
              $output->writeln("rm: missing file operand");
              break;
            }
            if($currentView->unlink($cmdArray[1])){
              $output->writeln("deleted $cmdArray[1]");
            }
            else{
              $output->writeln("could not remove");
            }
						break;
          case 'rmdir':
            $output->writeln("rmdir !");
            break;
          case 'mkdir':
            if(count($cmdArray) === 1){
              $output->writeln("mkdir: missing operand");
              break;
            }
            if($currentView->mkdir($cmdArray[1])){
              $output->writeln("created $cmdArray[1]");
            }else{
              $output->writeln("could not create dir");
            }
            break;
					case 'cd':

            if(count($cmdArray) === 1){
              $currentView->chroot($home);
              break;
            }
            if(!$currentView->file_exists($cmdArray[1])){
              $output->writeln("$cmdArray[1]: No such file or directory");
              break;
            }
            if(!$currentView->is_dir($cmdArray[1])){
              $output->writeln("$cmdArray[1]: Not a directory");
              break;
            }

            //[TODO] Still need to handle ".." in path

            $currentView->chroot($currentView->getRoot().'/'.$cmdArray[1]);

						break;
          case 'sh':
            //This should allow to execute a basic shell script: parse file passed in first operand, execute each line.
            $output->writeln("Not implemented yet");
            break;
					case '':
						break;
					default:
						$output->writeln("$command: command not found");
				}


			} while(1);


	}

}
