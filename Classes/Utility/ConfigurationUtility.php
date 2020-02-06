<?php
declare(strict_types = 1);
namespace Bitmotion\CustomErrorPage\Utility;

/***
 *
 * This file is part of the "Custom Error Pages" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
