<?php

class AnalyticsService extends Google_AnalyticsService {

	private $site_ids = array();

	public function getAllSitesIds()
	{
		if(empty($site_ids))
		{
			if($this->management_webproperties->useObjects())
			{
				foreach($this->management_profiles->listManagementProfiles("~all", "~all")->getItems() as $site)
			    {
			        $this->site_ids[$site->websiteUrl] = 'ga:' . $site->id;
			    }
			}
			else
			{
				$sites = $this->management_profiles->listManagementProfiles("~all", "~all");
				foreach($sites['items'] as $site)
			    {
			        $this->site_ids[$site['websiteUrl']] = 'ga:' . $site['id'];
			    }
			}
		}

		return $this->site_ids;
	}

	public function getSiteIdByUrl($url)
	{
		if( ! isset($this->site_ids[$url]))
		{
			$this->getAllSitesIds();
		}

		if(isset($this->site_ids[$url]))
		{
			return $this->site_ids[$url];
		}

		throw new Exception("Site $url is not present in your Analytics account.");
	}
}