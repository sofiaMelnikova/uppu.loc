<?php

namespace Engine;


class Helper {

	/**
	 * @param array $symbols
	 * @param int $length
	 * @return string
	 */
	public function getRandomString (array $symbols = [], int $length = 12): string {
		$symbols = $symbols ?: array_merge(range('A','Z'), range('a', 'z'), range('0', '9'));
		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$key = rand(0, count($symbols)-1);
			$result .= $symbols[$key];
		}

		return $result;
	}

	public function moveFile (string $actualPathToFile, string $moveTo, bool $sapi, string $renameFileTo = '') { // TODO!!!
		$errors = [];

//		$targetIsStream = strpos($moveTo, '://') > 0;

//		if (!$targetIsStream && !is_writable(dirname($moveTo))) {
//			// throw new InvalidArgumentException('Upload target path is not writable');
//		}
//
//		if ($targetIsStream) {
//			if (!copy($actualPathToFile, $moveTo)) {
//				// throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $moveTo));
//			}
//			if (!unlink($actualPathToFile)) {
//				// throw new RuntimeException(sprintf('Error removing uploaded file %1s', $this->name));
//			}
//		} elseif ($sapi) {
//			if (!is_uploaded_file($actualPathToFile)) {
//				// throw new RuntimeException(sprintf('%1s is not a valid uploaded file', $this->file));
//			}
//
//			if (!move_uploaded_file($actualPathToFile, $moveTo)) {
//				// throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $moveTo));
//			}
//		} else {
//			if ($renameFileTo) {
//				$renameRes = rename($actualPathToFile, $renameFileTo);
//			}
//
//			if (!$renameRes) {
//				// throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $moveTo));
//			}


//			if (!is_uploaded_file($actualPathToFile)) {
//				$errors['is_uploaded_file' => 'File uploaded'];
//			}
			is_uploaded_file($actualPathToFile);

			move_uploaded_file($actualPathToFile, $moveTo);

			if ($renameFileTo) {
				$renameRes = rename($actualPathToFile, $renameFileTo);
			}


//		}

		$this->moved = true;
	}
}