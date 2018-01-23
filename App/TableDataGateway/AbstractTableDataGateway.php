<?php

namespace App\TableDataGateway;


use Engine\DataBase;

abstract class AbstractTableDataGateway {

	/**
	 * @var DataBase
	 */
	protected $dataBase;

	/**
	 * RegistrationTdg constructor.
	 * @param DataBase $dataBase
	 */
	public function __construct(DataBase $dataBase) {
		$this->dataBase = $dataBase;
	}

}