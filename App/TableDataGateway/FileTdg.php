<?php

namespace App\TableDataGateway;


class FileTdg extends AbstractTableDataGateway {

	/**
	 * @param array $file
	 * @param array $downloadInfo
	 * @return string
	 * @throws \Exception
	 */
	public function addNewFileAnonym(array $file, array $downloadInfo): string {
		$this->dataBase->getConnection()->beginTransaction();

		try {
			try {
				$query = "INSERT INTO `files` (`original_name`, `original_extension`, `path_to`, `name`, `size`, `type_id`, `expire_time`, `updated_at`) 
							VALUES (:original_name, :original_extension, :path_to, :name, :size, :type_id, :expire_time, :updated_at);";
				$params = [
					':original_name'		=> $file['originalName'],
					':original_extension'	=> $file['originalExtension'],
					':path_to'				=> $file['pathTo'],
					':name'					=> $file['name'],
					':size'					=> $file['size'],
					':type_id'				=> $file['typeId'],
					':expire_time'			=> $file['expireTime'],
					':updated_at'			=> $file['updatedAt']
				];
				$fileId = $this->dataBase->insert($query, $params, true);

				$query = "INSERT INTO `downloads_info` (`added_file_cookie`, `file_id`, `download_date`) 
							VALUES (:added_file_cookie, :file_id, :download_date);";
				$params = [':added_file_cookie' => $downloadInfo['addedFileCookie'],
					':file_id' => $fileId,
					':download_date' => $downloadInfo['downloadDate']];

				$this->dataBase->insert($query, $params);

			} catch (\Error $error) {
				$this->dataBase->getConnection()->rollBack();
				throw new \Exception("Message:\n" . $error->getMessage() ."\n" . "Trace:\n" . $error->getTraceAsString());
			}
		} catch (\Exception $exception) {
			$this->dataBase->getConnection()->rollBack();
			throw new \Exception("Message:\n" . $exception->getMessage() ."\n" . "Trace:\n" . $exception->getTraceAsString());
		}

		$this->dataBase->getConnection()->commit();
		return $fileId;
	}

	/**
	 * @param string $typeName
	 * @return array|bool
	 */
	public function getTypeIdForFile(string $typeName) {
		$query = "SELECT `file_types`.`id` FROM `file_types` WHERE `file_types`.`type` = :type_name;";
		$params = [':type_name' => $typeName];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @return array|bool
	 */
	public function getAllFileTypes():array {
		$query = "SELECT `file_types`.`type` FROM `file_types`;";
		return $this->dataBase->select($query);
	}

	/**
	 * @param int $userId
	 * @param string $addedFileCookie
	 * @return int
	 */
	public function addUserIdToDownloadsInfoByCookie(int $userId, string $addedFileCookie): int {
		$query = "UPDATE `downloads_info` SET `user_id` = :user_id WHERE `added_file_cookie` = :added_file_cookie AND `user_id`IS NULL;";
		$params = [':user_id' => $userId,
					':added_file_cookie' => $addedFileCookie];
		return $this->dataBase->update($query, $params);
	}

	/**
	 * @param string $addedFileCookie
	 * @return array|bool
	 */
	public function getCountFilesByAddedFileCookie(string $addedFileCookie) {
		$query = "SELECT COUNT(*) FROM `downloads_info` WHERE `downloads_info`.`added_file_cookie` = :added_file_cookie";
		$params = [':added_file_cookie' => $addedFileCookie];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @param int $fileId
	 * @param string $description
	 * @param int $saveFileOnDays
	 * @return int - count changes string
	 */
	public function updateFile(int $fileId, string $description, int $saveFileOnDays): int {
		$query = "SELECT `download_date` FROM `downloads_info` WHERE `file_id` = :file_id;";
		$params = [':file_id' => $fileId];
		$downloadDate = $this->dataBase->select($query, $params, false)['download_date'];
		$query = "UPDATE `files` SET `description` = :description, 
				  `expire_time` = DATE_ADD(:download_date, INTERVAL :save_file_on_n_days DAY) WHERE id = :id";
		$params = [
			':download_date'		=> $downloadDate,
			':id'					=> $fileId,
			':description'			=> $description,
			':save_file_on_n_days'	=> $saveFileOnDays
		];
		return $this->dataBase->update($query, $params);
	}

	/**
	 * @param string $fileName
	 * @return array|bool
	 */
	public function selectIdPathToOriginalNameOriginalExtensionDescriptionLifeTimeFilesByName(string $fileName) {
		$query = "SELECT `files`.`id`, `files`.`path_to`, `files`.`original_name`, `files`.`original_extension`, `files`.`description`, 
					DATEDIFF(`files`.`expire_time`, `downloads_info`.`download_date`) AS `life_time`
					FROM `files` LEFT JOIN `downloads_info` ON `downloads_info`.`file_id` = `files`.`id` WHERE `files`.`name` = :name;";
		$params = [':name' => $fileName];
		return $this->dataBase->select($query, $params, false);
	}

}