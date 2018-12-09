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

use OCA\NextcloudShell\Util\Cmd;

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
use OCA\NextcloudShell\Util\Context;

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
  /** @var Context */
  protected $context ;

	public function __construct(QuestionHelper $questionHelper, IUserManager $userManager, Context $context) {
		$this->questionHelper = $questionHelper;
		$this->userManager = $userManager;
    $this->context = $context ;

    // We need a way to get all class implementing IBin. That would make this way simpler.
    $this->loadPrograms($context);

		parent::__construct();

    if (!extension_loaded('pcntl')) {
      throw new \RuntimeException('PCNTL extension is not loaded.');
    }

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

      //[TODO] Autocompletion (might have to extends the Question helper for this)
      //[TODO] History
      //[TODO] Redirection (<>)

      // Intercept Ctrl-C signal
      $this->listen();

      // Check user

			$uid = $input->getArgument('user');
			$userExists = $this->userManager->userExists($uid);

			if ($userExists === false) {
				$output->writeln('User "' . $uid . '" unknown.');
				return;
			}
			$this->context->setUser( $this->userManager->get($uid));
			$this->context->getUser()->updateLastLoginTimestamp();

      $home = '/' . $uid . '/files';
			FileSystem::init($uid,  $home);
			$this->context->setHomeView( Filesystem::getView());
      $this->context->setCurrentView( new View($home));
      $this->context->setOutput($output);

      // Init CLI Style
      $this->initCLI($output);

      // Login Message
			$output->writeln('This is the shell');
			$output->writeln('last login: '.date(DATE_RFC2822, $this->context->getUser()->getLastLogin()));

      //var_dump(getenv('PATH'));
      // CLI Loop

			do{

				$question = new Question('<PS1_user>'.$this->context->getUser()->getUID()."@nextcloud</PS1_user>: <PS1_path>".$this->context->getHomeView()->getRelativePath($this->context->getCurrentView()->getRoot())."</PS1_path> $ ");

        $cmd = new Cmd($this->questionHelper->ask($input, $output, $question));
        if($cmd->getProgram() === "exit"){
          break;
        }
        if($cmd->getProgram() === null){
          continue;
        }
        if(array_key_exists ( $cmd->getProgram() , $this->context->getPrograms() )){
          $this->context->getProgram($cmd->getProgram())->exec($cmd);
        }else{
          $output->writeln($cmd->getProgram().": command not found");
        }


			} while(1);
	}

  private function loadPrograms(){
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

  private function listen()
  {
      $handler = function ($code) {
        // Don't do anything for now
        // We should allow to Ctrl + c to kill the execution of a command.
      };
      // Ctrl + C
      pcntl_signal(SIGINT, $handler);

  }

  public function getHomeView(){
    return $this->homeView;
  }
  public function getPrograms(){
    return $this->programs;
  }
}
