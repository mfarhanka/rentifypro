<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! class_exists('Locale')) {
	class Locale
	{
		private static string $default = 'en';

		public static function getDefault(): string
		{
			if (self::$default !== '') {
				return self::$default;
			}

			if (function_exists('locale_get_default')) {
				$locale = locale_get_default();

				if (is_string($locale) && $locale !== '') {
					self::$default = $locale;

					return $locale;
				}
			}

			$locale = ini_get('intl.default_locale');

			self::$default = is_string($locale) && $locale !== '' ? $locale : 'en';

			return self::$default;
		}

		public static function setDefault(string $locale): bool
		{
			if ($locale === '') {
				return false;
			}

			self::$default = $locale;

			return true;
		}
	}
}

if (! class_exists('NumberFormatter')) {
	class NumberFormatter
	{
		public const DECIMAL = 1;
		public const CURRENCY = 2;
		public const PERCENT = 3;
		public const SCIENTIFIC = 4;
		public const SPELLOUT = 5;
		public const ORDINAL = 6;
		public const DURATION = 7;
		public const FRACTION_DIGITS = 8;

		private int $type;

		private int $fractionDigits = 2;

		private int $errorCode = 0;

		private string $errorMessage = '';

		public function __construct(string $locale, int $type)
		{
			$this->type = $type;
		}

		public function setAttribute(int $attribute, float|int $value): bool
		{
			if ($attribute === self::FRACTION_DIGITS) {
				$this->fractionDigits = (int) $value;
			}

			return true;
		}

		public function setPattern(string $pattern): bool
		{
			$decimalPos = strrpos($pattern, '.');
			$this->fractionDigits = $decimalPos === false ? 0 : substr_count(substr($pattern, $decimalPos + 1), '#');

			return true;
		}

		public function format(float|int $value): string|false
		{
			return number_format((float) $value, $this->fractionDigits, '.', ',');
		}

		public function formatCurrency(float|int $value, string $currency): string|false
		{
			return $currency . ' ' . number_format((float) $value, $this->fractionDigits, '.', ',');
		}

		public function getErrorCode(): int
		{
			return $this->errorCode;
		}

		public function getErrorMessage(): string
		{
			return $this->errorMessage;
		}
	}
}

if (! function_exists('intl_is_failure')) {
	function intl_is_failure(int $errorCode): bool
	{
		return $errorCode !== 0;
	}
}
