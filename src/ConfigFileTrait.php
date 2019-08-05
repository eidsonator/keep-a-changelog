<?php
/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Compose this trait for any command that needs access to the configuration file.
 */
trait ConfigFileTrait
{
    /**
     * Path where global config is kept.
     *
     * This property exists solely for testing. When set, the value will be used
     * instead of getenv('HOME').
     */
    private $globalPath;

    /**
     * Path where global config is kept.
     *
     * This property exists solely for testing. When set, the value will be used
     * instead of realpath(getcwd())
     */
    private $localPath;

    private function getConfigFile(InputInterface $input) : string
    {
        $useGlobal  = $input->getOption('global') ?: false;
        $globalPath = $this->globalPath ?: getenv('HOME');
        $localPath  = $this->localPath ?: realpath(getcwd());

        return $useGlobal
            ? sprintf('%s/.keep-a-changelog/config.ini', $globalPath)
            : sprintf('%s/.keep-a-changelog.ini', $localPath);
    }

    private function getLocalConfigFile() : string
    {
        $localPath  = $this->localPath ?: realpath(getcwd());
        return sprintf('%s/.keep-a-changelog.ini', $localPath);
    }

    private function getGlobalConfigFile() : string
    {
        $globalPath = $this->globalPath ?: getenv('HOME');
        return sprintf('%s/.keep-a-changelog/config.ini', $globalPath);
    }

    private function getConfig(InputInterface $input) : Config
    {
        $configFile = $this->getConfigFile($input);
        return is_readable($this->getLocalConfigFile()) || is_readable($this->getGlobalConfigFile())
            ? $this->createConfigFromFiles($this->getLocalConfigFile(), $this->getGlobalConfigFile())
            : $this->createNewConfig();
    }

    private function saveConfigFile(string $filename, Config $config) : bool
    {
        $ini = '';
        foreach ($config->getArrayCopy() as $key => $value) {
            $ini .= sprintf('%s = %s%s', $key, $value, PHP_EOL);
        }
        return file_put_contents($filename, $ini) !== false;
    }

    /**
     * Create a new Config instance.
     *
     * If the config file does not exist, this creates empty configuration,
     * optionally using an existing tokenfile ($HOME/.keep-a-changelog/token)
     * if it exists.
     */
    private function createNewConfig() : Config
    {
        $globalPath = $this->globalPath ?: getenv('HOME');
        $tokenFile  = sprintf('%s/.keep-a-changelog/token', $globalPath);
        $token = is_readable($tokenFile)
            ? trim(file_get_contents($tokenFile))
            : '';
        return new Config($token);
    }

    /**
     * Parses the config file and returns a populated Config instance.
     */
    private function createConfigFromFiles(string $localConfigFile, string $globalConfigFile) : Config
    {
        $localIni = is_readable($localConfigFile) ? parse_ini_file($localConfigFile) : [];
        $globalIni = is_readable($globalConfigFile) ? parse_ini_file($globalConfigFile) : [];

        $ini = array_merge($globalIni, $localIni);

        return new Config($ini['token'] ?? '', $ini['provider'] ?? Config::PROVIDER_GITHUB, $ini['enterpriseUrl'] ?? '', $ini['package'] ?? '');
    }
}
