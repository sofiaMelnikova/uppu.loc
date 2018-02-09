<?php

namespace App\TableDataGateway;


class FileTdg extends AbstractTableDataGateway {

	/**
	 * @param array $file
	 * @param array $downloadInfo
	 * @return int
	 * @throws \Exception
	 */
	public function addNewFileAnonym(array $file, array $downloadInfo):int {
		$this->dataBase->getConnection()->beginTransaction();

		try {
			try {
				$query = "INSERT INTO `files` (`original_name`, `original_extension`, `path_to`, `size`, `type_id`, `expire_time`, `updated_at`) 
							VALUES (:original_name, :original_extension, :path_to, :size, :type_id, :expire_time, :updated_at);";
				$params = [':original_name' => $file['originalName'],
					':original_extension' => $file['originalExtension'],
					':path_to' => $file['pathTo'],
					':size' => $file['size'],
					':type_id' => $file['typeId'],
					':expire_time' => $file['expireTime'],
					':updated_at' => $file['updatedAt']];
				$fileId = $this->dataBase->insert($query, $params, true);

				$query = "INSERT INTO `downloads_info` (`added_file_cookie`, `file_id`, `download_date`) 
							VALUES (:added_file_cookie, :file_id, :download_date);";
				$params = [':added_file_cookie' => $downloadInfo['addedFileCookie'],
					':file_id' => $fileId,
					':download_date' => $downloadInfo['downloadDate']];

				$countNewNotes = $this->dataBase->insert($query, $params);

			} catch (\Error $error) {
				$this->dataBase->getConnection()->rollBack();
				throw new \Exception("Message:\n" . $error->getMessage() ."\n" . "Trace:\n" . $error->getTraceAsString());
			}
		} catch (\Exception $exception) {
			$this->dataBase->getConnection()->rollBack();
			throw new \Exception("Message:\n" . $exception->getMessage() ."\n" . "Trace:\n" . $exception->getTraceAsString());
		}

		$this->dataBase->getConnection()->commit();
		return $countNewNotes;
	}

	/**
	 * @param string $typeName
	 * @return array|bool
	 */
	public function getTypeIdForFile(string $typeName) {
		$request = "SELECT `file_types`.`id` FROM `file_types` WHERE `file_types`.`type` = :type_name;";
		$params = [':type_name' => $typeName];
		return $this->dataBase->select($request, $params, false);
	}

	/**
	 * @return array|bool
	 */
	public function getAllFileTypes() {
		$request = "SELECT `file_types`.`type` FROM `file_types`;";
		return $this->dataBase->select($request);
	}

}