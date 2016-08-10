<?php

namespace PragmaRX\Tracker\Support;

use Jenssegers\Agent\Agent;

class LanguageDetect extends Agent {

	/**
	 * Detect preference and language-range
	 *
	 * @return array
	 */
	public function detectLanguage()
	{
		return [
					'preference' => $this->getLanguagePreference(),
					'language-range' => $this->getLanguageRange(),
				];
	}

	/**
	 * Get language prefernece.
	 *
	 * @return string
	 */
	public function getLanguagePreference()
	{
		$languages = $this->languages();
		return count($languages) ? array_keys($languages, max($languages))[0] : 'en';
	}

	/**
	 * Get languages ranges
	 *
	 * @return string
	 */
	public function getLanguageRange()
	{
		return http_build_query($this->languages(),'','');
	}

}
