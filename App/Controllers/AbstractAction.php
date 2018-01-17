<?php

namespace App\Controllers;

use Slim\App;

abstract class AbstractAction {

	protected $app;

	public function __construct(App $app) {
		$this->app = $app;
	}

}