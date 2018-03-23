<?php

namespace App\TableDataGateway;


use App\ValueObject\FileValueObject;

class FileTdg extends AbstractTableDataGateway {

	/**
	 * @param FileValueObject $fileValueObject
	 * @param int $typeId
	 * @param string $addedFileCookie
	 * @param string $downloadDate
	 * @return string
	 * @throws \Exception
	 */
	public function addNewFileAnonym(FileValueObject $fileValueObject, int $typeId, string $addedFileCookie, string $downloadDate): string {
		$this->dataBase->getConnection()->beginTransaction();

		try {
			try {

				$fileId = $this->insertIntoFilesNewFile($fileValueObject, $typeId);

				$query = "INSERT INTO `downloads_info` (`added_file_cookie`, `file_id`, `download_date`) 
							VALUES (:added_file_cookie, :file_id, :download_date);";
				$params = [':added_file_cookie' => $addedFileCookie,
					':file_id' => $fileId,
					':download_date' => $downloadDate];

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
	 * @param FileValueObject $fileValueObject
	 * @param int $typeId
	 * @param string $userId
	 * @param string $downloadDate
	 * @return string
	 * @throws \Exception
	 */
	public function addNewFileByLoginUser(FileValueObject $fileValueObject, int $typeId, string $userId, string $downloadDate): string {
		$this->dataBase->getConnection()->beginTransaction();

		try {
			try {

				$fileId = $this->insertIntoFilesNewFile($fileValueObject, $typeId);

				$query = "INSERT INTO `downloads_info` (`user_id`, `file_id`, `download_date`) 
							VALUES (:user_id, :file_id, :download_date);";
				$params = [':user_id' => $userId,
					':file_id' => $fileId,
					':download_date' => $downloadDate];

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
	 * @param FileValueObject $fileValueObject
	 * @param int $typeId
	 * @return string
	 */
	private function insertIntoFilesNewFile(FileValueObject $fileValueObject, int $typeId): string {
		$query = "INSERT INTO `files` (`original_name`, `original_extension`, `path_to`, `name`, `size`, `type_id`, `expire_time`, `updated_at`) 
							VALUES (:original_name, :original_extension, :path_to, :name, :size, :type_id, :expire_time, :updated_at);";
		$params = [
			':original_name'		=> $fileValueObject->getOriginalName(),
			':original_extension'	=> $fileValueObject->getOriginalExtension(),
			':path_to'				=> $fileValueObject->getPathTo(),
			':name'					=> $fileValueObject->getName(),
			':size'					=> $fileValueObject->getSize(),
			':type_id'				=> $typeId,
			':expire_time'			=> $fileValueObject->getExpireTime(),
			':updated_at'			=> $fileValueObject->getUpdatedAt()
		];
		return $this->dataBase->insert($query, $params, true);
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
		$query = "SELECT COUNT(*) FROM `downloads_info` WHERE `downloads_info`.`added_file_cookie` = :added_file_cookie AND `downloads_info`.`is_delete` = 0";
		$params = [':added_file_cookie' => $addedFileCookie];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @param FileValueObject $fileValueObject
	 * @return int
	 */
	public function updateFile(FileValueObject $fileValueObject): int {
		$query = "SELECT `download_date` FROM `downloads_info` WHERE `file_id` = :file_id;";
		$params = [':file_id' => $fileValueObject->getId()];
		$downloadDate = $this->dataBase->select($query, $params, false)['download_date'];
		$query = "UPDATE `files` SET `description` = :description, 
				  `expire_time` = DATE_ADD(:download_date, INTERVAL :save_file_on_n_days DAY) WHERE id = :id";
		$params = [
			':download_date'		=> $downloadDate,
			':id'					=> $fileValueObject->getId(),
			':description'			=> $fileValueObject->getDescription(),
			':save_file_on_n_days'	=> $fileValueObject->getLifespanDays()
		];
		return $this->dataBase->update($query, $params);
	}

	/**
	 * @param string $fileName
	 * @return array|bool
	 */
	public function selectInfoForDownloadingAndShowingFileByName(string $fileName) {
		$query = "SELECT `files`.`id`, `files`.`path_to`, `files`.`original_name`, `files`.`original_extension`, `files`.`description`,`files`.`size`, 
					DATEDIFF(`files`.`expire_time`, `downloads_info`.`download_date`) AS `life_time`, `downloads_info`.`download_date`
					FROM `files` LEFT JOIN `downloads_info` ON `downloads_info`.`file_id` = `files`.`id` WHERE `files`.`name` = :name AND `downloads_info`.`is_delete` = 0;";
		$params = [':name' => $fileName];
		return $this->dataBase->select($query, $params, false);
	}

	/**
	 * @param int $userId
	 * @return array
	 */
	public function selectInfoForDownloadingAndShowingAllUsersFilesByUserId(int $userId): array {
		$query = "SELECT `files`.`id`, `files`.`path_to` AS `pathTo`, `files`.`original_name` AS `originalName`, `files`.`original_extension` as `originalExtension`, `files`.`description`,`files`.`size` AS `sizeKb`,
					DATEDIFF(`files`.`expire_time`, `downloads_info`.`download_date`) AS `lifeTime`, `downloads_info`.`download_date` AS `downloadDate`, `files`.`name`
					FROM `files` LEFT JOIN `downloads_info` ON `downloads_info`.`file_id` = `files`.`id` WHERE `downloads_info`.`user_id` = :user_id AND `downloads_info`.`is_delete` = 0;";
		$params = [':user_id' => $userId];
		return $this->dataBase->select($query, $params);
	}

	/**
	 * @param string $addedFileCookie
	 * @return array
	 */
	public function selectInfoForDownloadingAndShowingAllUsersFilesByAddedCookie(string $addedFileCookie): array {
		$query = "SELECT `files`.`id`, `files`.`path_to` AS `pathTo`, `files`.`original_name` AS `originalName`, `files`.`original_extension` as `originalExtension`, `files`.`description`,`files`.`size` AS `sizeKb`,
					DATEDIFF(`files`.`expire_time`, `downloads_info`.`download_date`) AS `lifeTime`, `downloads_info`.`download_date` AS `downloadDate`, `files`.`name`
					FROM `files` LEFT JOIN `downloads_info` ON `downloads_info`.`file_id` = `files`.`id` WHERE `downloads_info`.`added_file_cookie` = :added_file_cookie AND `downloads_info`.`is_delete` = 0;";
		$params = ['added_file_cookie' => $addedFileCookie];
		return $this->dataBase->select($query, $params);
	}
}