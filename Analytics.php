<?php

class Analytics {

	private static $analytics = NULL;

	public static function init($analytics)
	{
		if(is_null(self::$analytics)) self::$analytics = $analytics;
	}

	public static function query($id, $start_date, $end_date, $metrics, $others = array())
	{
		return self::$analytics->data_ga->get($id, $start_date, $end_date, $metrics, $others);
	}

	public static function segments()
	{
		return self::$analytics->management_segments;
	}

	public static function accounts()
	{
		return self::$analytics->management_accounts;
	}

	public static function goals()
	{
		return self::$analytics->management_goals;
	}

	public static function profiles()
	{
		return self::$analytics->management_profiles;
	}

	public static function webproperties()
	{
		return self::$analytics->management_webproperties;
	}

	/**
	 * @return String ga:xxxxxxx
	 */
	public static function getAllSitesIds()
	{
		return self::$analytics->getAllSitesIds();
	}

	/**
	 * @param $url String
	 * @return array(array('url' => 'id'))
	 */
	public static function getSiteIdByUrl($url)
	{
		return self::$analytics->getSiteIdByUrl($url);
	}
}