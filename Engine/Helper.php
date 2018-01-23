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
}