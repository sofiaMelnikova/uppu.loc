<?php

namespace App\Models;

use Slim\App;

abstract class AbstractModel {

	protected $app;

	public function __construct(App $app) {
		$this->app = $app;
	}

}