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
use OCA\NextcloudShell\Util\Configurator;
use OCA\NextcloudShell\Util\Context;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

use OCP\IUserManager;

class Shell extends Command {
  /** @var  QuestionHelper */
  protected $questionHelper;
  /** @var Context */
  protected $context ;
  /** @var Configurator */
  protected $configurator;
  /** @var IUserManager */
  protected $userManager;

  public function __construct(QuestionHelper $questionHelper, Context $context, Configurator $configurator, IUserManager $userManager) {

    $this->questionHelper = $questionHelper;
    $this->context = $context ;
    $this->configurator = $configurator;
    $this->userManager = $userManager;

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
      InputArgument::OPTIONAL,
      'user which should be recovered'
    );

  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    //[TODO] Autocompletion (might have to extends the Question helper for this)
    //[TODO] History
    //[TODO] Redirection (<>)

    // Check user
    if($input->getArgument('user')){
      $uid == $input->getArgument('user');
    }else if(getenv('USER')){
      $uid = getenv('USER');
    }else{
      $question = new Question('user ?');
      $uid = $this->questionHelper->ask($input, $output, $question);
      return;
    }

    $question = new Question('password ?');
    $question->setHidden(true);


    for($i = 1; $i <= 3; $i++){
      $password = $this->questionHelper->ask($input, $output, $question);
      $auth = $this->authenticate($uid, $password);
      if($auth){
        break;
      }
    }

    if(!$auth){
      $output->writeln('authentication failed');
      return;
    }

    $this->context->setUser( $auth);
    $this->context->getUser()->updateLastLoginTimestamp();
    $this->context->setInput($input);
    $this->context->setOutput($output);
    $this->configurator->configure($uid);

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

  protected function authenticate($uid, $password){
    return $this->userManager->checkPassword($uid, $password);
  }
}
