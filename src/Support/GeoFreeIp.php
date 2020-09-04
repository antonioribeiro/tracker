<?php

namespace PragmaRX\Tracker\Support;
use stdClass;

class GeoFreeIp
{
    private $geoIp;
    private $host = 'http://www.geoplugin.net/php.gp';


    /**
     * @var null
     */
    private $databasePath;

    public function __construct($databasePath = null)
    {
        $this->databasePath = $databasePath;
    }

    private function databaseExists()
    {
        return true;
    }

    private function getGeoIp()
    {
        if (! $this->geoIp && $this->databaseExists()) {
            $this->geoIp = new stdClass();
        }

        return $this->geoIp;
    }

    public function searchAddr($addr) {
		if ( function_exists('curl_init') ) {
			//use cURL to fetch data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->host . "?ip={$addr}&base_currency=BRL&lang=pt-BR");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.1');
			$response = curl_exec($ch);
			curl_close ($ch);
		} else if ( ini_get('allow_url_fopen') ) {
			//fall back to fopen()
			$response = $this->formatResponse(unserialize(file_get_contents($this->host, 'r')));
		} else {
			trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
			return;

        }
        return $this->formatResponse(unserialize($response));
    }

    /**
     * @return boolean
     */
    public function isEnabled() {
        return $this->getGeoIp()->isEnabled();
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        return $this->getGeoIp()->setEnabled($enabled);
    }

    public function isGeoIpAvailable() {
        return $this->getGeoIp()->isGeoIpAvailable();
    }

    public function formatResponse($response)
    {
        return [
            'latitude' => $response['geoplugin_latitude'],
            'longitude' => $response['geoplugin_longitude'],
            'country_code' => $response['geoplugin_countryCode'],
            'country_name' => $response['geoplugin_countryName'],
            'region' => $response['geoplugin_regionCode'],
            'city' => $response['geoplugin_city'],
            'area_code' => empty($response['geoplugin_areaCode']) ? null : $response['geoplugin_areaCode'],
            'dma_code' => empty($response['geoplugin_dmaCode']) ? null : $response['geoplugin_dmaCode'],
            'metro_code' => $response['geoplugin_locationAccuracyRadius'],
            'continent_code' => $response['geoplugin_continentCode']
        ];
    }
}