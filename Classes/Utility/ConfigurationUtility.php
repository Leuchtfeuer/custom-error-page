<?php

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

/**
 * Class ConfigurationUtility
 * @package Bitmotion\CustomErrorPage\Utility
 */
class ConfigurationUtility
{
    /**
     * @param int $page
     * @throws \Exception
     *
     * @return array
     */
    public static function loadConfiguration($page = 404)
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['custom_error_page']);

        if (!isset($extConf['configFile']) || empty($extConf['configFile'])) {
            throw new \Exception('No custom_error_page configuration File configured', 1493046456);
        }

        $fileName = PATH_site . trim($extConf['configFile']);

        if (!file_exists($fileName)) {
            throw new \Exception('No configuration file found in ' . $fileName . '.', 1493046628);
        }

        return self::loadConfigurationFromYaml($fileName, $page);
    }

    /**
     * @param string $fileName
     * @param int $page
     *
     * @return array
     */
    private static function loadConfigurationFromYaml($fileName, $page)
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
                $baseUrl = $protocol . '://' . $domainName . '/index.php?id=' . $pageUid;
                $defaultConfiguration = [
                    $arrayKey => [],
                ];

                foreach ($domain['language-pattern'] as $languageKey => $languageUid) {
                    if ($languageKey === 'default' && $languageKey == true) {
                        $defaultConfiguration[$arrayKey]['|.*|'] = $baseUrl;
                    } else {
                        $pattern = '|^/' . $languageKey . '/*|';
                        $defaultConfiguration[$arrayKey][$pattern] = $baseUrl . '&L=' . $languageUid;
                    }
                }
                $configurationArray[$domainName] = $defaultConfiguration;

                if ($isFirst === true) {
                    $configuration['_DEFAULT'] = $defaultConfiguration;
                    $isFirst = false;
                }
            }

        }
        return $configurationArray;
    }
}