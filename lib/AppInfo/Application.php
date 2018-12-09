<?php
namespace OCA\NextcloudShell\AppInfo;

use OCP\AppFramework\App;
use OCA\NextcloudShell\Util\Context;

class Application extends App {

  public function __construct(array $urlParams=array()) {
		parent::__construct('nextcloudShell', $urlParams);
		$container = $this->getContainer();
		$server = $container->getServer();

    $context = $c->query('Context');
    var_dump('ooooh');
  }
}
