<?php

namespace PragmaRX\Tracker\Support;
use stdClass;

class Ip2location
{
    private $host = 'https://api.ip2location.com/v2';
    private $key;

    private $validCredit = false;
    /**
     * @var null
     */
    private $databasePath;

    public function __construct($key, $databasePath = null)
    {
        $this->databasePath = $databasePath;

        $this->key = $key;
        $response = json_decode(file_get_contents($this->host . '/?key='.$this->key.'&check=1', 'r'));

        if ($response->response <= 0) {
            $this->validCredit = true;
        }
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

    public function searchAddr($addr)
    {
        if (isset($_SESSION['userLocalization']) && !empty($_SESSION['userLocalization'])) {
            return $_SESSION['userLocalization'];
        }

        if ($this->validCredit) {
            return false;
        }

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->host . '/?' . http_build_query([
                'ip'      => $addr,
                'key'     => $this->key,
                'package' => 'WS3',
            ]));

            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);

        } else if (ini_get('allow_url_fopen')) {
            $response = $this->formatResponseIp2(json_decode(file_get_contents($this->host, 'r'), true));
        } else {
            trigger_error('Ip2location class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
            return;
        }

        return  $_SESSION['userLocalization'] = $this->formatResponseIp2(json_decode($response, true));
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

    public function formatResponseIp2($response)
    {
        return [
            'country_code' => $response['country_code'],
            'country_name' => $response['country_name'],
            'region_name' => $response['region_name'],
            'region' => $this->getState($response['region_name']),
            'city' => $response['city_name'],
        ];
    }

    public function getState($state)
    {
        $states = array(
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapa',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceara',
            'DF' => 'Distrito Federal',
            'ES' => 'Espirito Santo',
            'GO' => 'Goias',
            'MA' => 'Maranhao',
            'MS' => 'Mato Grosso do Sul',
            'MT' => 'Mato Grosso',
            'MG' => 'Minas Gerais',
            'PA' => 'Para',
            'PB' => 'Paraiba',
            'PR' => 'Parana',
            'PE' => 'Pernambuco',
            'PI' => 'Piaui',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondonia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'Sao Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        );

        return array_search($state, $states);
    }
}
