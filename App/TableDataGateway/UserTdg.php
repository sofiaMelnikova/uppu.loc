<?php

namespace App\TableDataGateway;


class UserTdg extends AbstractTableDataGateway {

	/**
	 * @param string $enterCookie
	 * @return array|bool
	 */
	public function selectIdNameCountUploadedFilesByEnterCookie(string $enterCookie) {
		$query = "SELECT `users`.`id`, `users`.`name`, COUNT(*) FROM `users` 
					RIGHT JOIN `downloads_info` ON `users`.`id`=`downloads_info`.`user_id` 
					WHERE `users`.`enter_cookie` = :enter_cookie GROUP BY users.id;";
		$params = [':enter_cookie' => $enterCookie];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @return array
	 */
	public function selectActiveUsersId(): array {
		$query = "SELECT `users`.`id` FROM `users` WHERE `users`.`is_delete` = 0;";
		return $this->dataBase->select($query);
	}

	/**
	 * @param int $userId
	 * @return array|bool
	 */
	public function selectNameCountUploadedFilesById(int $userId) {
		$query = "SELECT `users`.`id`, `users`.`name`, COUNT(*) FROM `users` 
					RIGHT JOIN `downloads_info` ON `users`.`id`=`downloads_info`.`user_id` 
					WHERE `users`.`id` = :users_id GROUP BY `users`.`id`;";
		$params = [':users_id' => $userId];
		return $this->dataBase->select($query, $params, false);
	}


}