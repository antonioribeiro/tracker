<?php

namespace PragmaRX\Tracker\Package\Support;

use Illuminate\Support\Collection;
use PragmaRX\Tracker\Package\Exceptions\MissingConfiguration;
use PragmaRX\Yaml\Package\Yaml;

class Config
{
    /**
     * The config loader.
     *
     * @var \PragmaRX\Yaml\Package\Yaml
     */
    protected $yaml;

    /**
     * The config file stub.
     *
     * @var string
     */
    protected $configFileStub;

    /**
     * The config file.
     *
     * @var string
     */
    protected $configFile;

    /**
     * Cache constructor.
     *
     * @param Yaml|null $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * Get config value.
     *
     * @param $string
     * @param mixed|null $default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function get($string, $default = null)
    {
        return config("version.{$string}", $default);
    }

    /**
     * Get config root.
     *
     * @return \Illuminate\Config\Repository|mixed
     *
     * @internal param $string
     */
    public function getRoot()
    {
        return config('version');
    }

    /**
     * Set the config file stub.
     *
     * @return string
     */
    public function getConfigFileStub()
    {
        return $this->configFileStub;
    }

    /**
     * Load YAML file to Laravel config.
     *
     * @param $path
     *
     * @return mixed
     */
    protected function loadToLaravelConfig($path)
    {
        return $this->yaml->loadToConfig($path, 'version');
    }

    /**
     * Set the config file stub.
     *
     * @param string $configFileStub
     */
    public function setConfigFileStub($configFileStub)
    {
        $this->configFileStub = $configFileStub;
    }

    /**
     * Load package YAML configuration.
     *
     * @param $path
     *
     * @return Collection
     */
    public function loadConfig($path = null)
    {
        return $this->loadToLaravelConfig(
            $this->setConfigFile($this->getConfigFile($path))
        );
    }

    /**
     * Get the config file path.
     *
     * @param string|null $file
     * @return string
     * @throws MissingConfiguration
     */
    public function getConfigFile($file = null)
    {
        if (!empty($file) && !file_exists($file)) {
            throw new MissingConfiguration("File {$file} does not exists.");
        }

        $file = $file ?: $this->configFile;

        return !empty($file)
            ? $file
            : $this->getConfigFileStub();
    }

    /**
     * Update the config file.
     *
     * @param $config
     */
    public function update($config)
    {
        config(['version' => $config]);

        $this->yaml->saveAsYaml($config, $this->configFile);
    }

    /**
     * Set the current config file.
     *
     * @param $file
     *
     * @return mixed
     */
    public function setConfigFile($file)
    {
        return $this->configFile = $file;
    }
}
