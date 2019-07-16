<?php
declare(strict_types=1);
namespace Bitmotion\CustomErrorPage\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Bitmotion GmbH <typo3-ext@bitmotion.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility
{
    protected $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public static function loadConfiguration(int $page = 404)
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['custom_error_page']);

        if (!isset($extConf['configFile']) || empty($extConf['configFile'])) {
            throw new \Exception('No custom_error_page configuration file configured', 1493046456);
        }

        $fileName = PATH_site . trim($extConf['configFile']);

        if (!file_exists($fileName)) {
            throw new \Exception('No configuration file found in ' . $fileName . '.', 1493046628);
        }

        return self::loadConfigurationFromYaml($fileName, $page);
    }

    private static function loadConfigurationFromYaml(string $fileName, int $page): array
    {
        $yamlParser = new Yaml();
        $configuration = $yamlParser->parse(file_get_contents($fileName));

        $configurationArray = [];
        $isFirst = true;
        $arrayKey = $page . 'Handling';

        foreach ($configuration[$page] as $pageConfiguration) {
            $domain = $pageConfiguration['domain'];
            $protocol = $domain['https'] === true ? 'https' : 'http';
            $pageUid = $domain['pages'][$page];
            $domainNames = [$domain['name']];

            if ($domain['additional-tlds'] !== false && is_array($domain['additional-tlds'])) {
                foreach ($domain['additional-tlds'] as $tld) {
                    $domainNames[] = $domain['name'] . $tld['tld'];
                }
            }

            foreach ($domainNames as $domainName) {
                $prependDefault = false;
                $baseUrl = $protocol . '://' . $domainName . '/index.php?id=' . $pageUid;
                $domainConfiguration = [
                    $arrayKey => [],
                ];

                // Forward the reason
                $domainConfiguration['forward-reason'] = $domain['forward-reason'] === true ?: false;

                foreach ($domain['language-pattern'] as $languageKey => $languageUid) {
                    if ($languageKey === 'default' && $languageUid == true) {
                        // Always prepend default configuration
                        $prependDefault = true;
                    } else {
                        $pattern = '|^/' . $languageKey . '/*|';
                        $domainConfiguration[$arrayKey][$pattern] = $baseUrl . '&L=' . $languageUid;
                    }
                }

                // Prepend default configuration
                if ($prependDefault === true) {
                    $domainConfiguration[$arrayKey]['|.*|'] = $baseUrl;
                }

                $configurationArray[$domainName] = $domainConfiguration;

                if ($isFirst === true) {
                    $configurationArray['_DEFAULT'] = $domainConfiguration;
                    $isFirst = false;
                }
            }
        }

        return $configurationArray;
    }
}
