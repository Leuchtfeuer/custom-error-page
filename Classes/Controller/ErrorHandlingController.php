<?php
declare(strict_types = 1);

namespace Bitmotion\CustomErrorPage\Controller;

/***
 *
 * This file is part of the "Custom Error Pages" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Cyril Janody <cyril.janody@fsg.ulaval.ca>, FSG
 *
 ***/

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ErrorHandlingController extends ActionController
{
    /**
     * action show
     *
     * @return string
     */
    public function showAction(): string
    {
        switch (GeneralUtility::_GET('reason')) {
            case 'Page is not available in the requested language.':
            case 'Page is not available in the requested language (strict).':
            case 'Page is not available in the requested language (fallbacks did not apply).':
                $refererQueryArray = GeneralUtility::explodeUrl2Array(
                    parse_url(GeneralUtility::getIndpEnv('HTTP_REFERER'), PHP_URL_QUERY),
                    true
                );

                $refererTsfe = GeneralUtility::makeInstance(
                    TypoScriptFrontendController::class,
                    null,
                    $refererQueryArray['id'] ?: null,
                    $refererQueryArray['type'] ?: 0,
                    $refererQueryArray['no_cache'] ?: '',
                    $refererQueryArray['cHash'] ?: '',
                    null,
                    $refererQueryArray['MP'] ?: '',
                    $refererQueryArray['RDCT'] ?: ''
                );

                $refererTsfe->siteScript = ltrim(GeneralUtility::getIndpEnv('HTTP_REFERER'), '/');
                $refererTsfe->checkAlternativeIdMethods();

                $parameter = $refererTsfe->id;
                if ($refererTsfe->type && MathUtility::canBeInterpretedAsInteger($refererTsfe->type)) {
                    $parameter .= ',' . $refererTsfe->type;
                }

                $refererQueryArray  = array_merge($refererQueryArray, GeneralUtility::_GET());
                $refererQueryArray  = ArrayUtility::arrayDiffAssocRecursive(
                    $refererQueryArray,
                    ['id' => 0, 'L' => 0, 'reason' => 0]
                );
                $refererQueryParams = GeneralUtility::implodeArrayForUrl('', $refererQueryArray, '', false, true);

                $this->view->assignMultiple([
                                                'refererQueryParameter'        => $parameter,
                                                'refererQueryAdditionalParams' => $refererQueryParams,
                                                'contentElements'              => $this->settings['pageNotTranslated'],
                                            ]);

                // Set language to default
                $refererTsfe->sys_language_uid = ($refererTsfe->sys_language_content = 0);
                $refererTsfe->initLLvars();
                $refererTsfe->calculateLinkVars();

                // Override current language
                $GLOBALS['TSFE']->sys_language_uid = $refererTsfe->sys_language_uid;
                // Override current linkVars (Basically removes L parameter)
                $GLOBALS['TSFE']->linkVars = $refererTsfe->linkVars;

                $content = $this->view->render();

                // Reset language to current
                $GLOBALS['TSFE']->sys_language_uid =
                    ($GLOBALS['TSFE']->sys_language_content =
                        (int)$GLOBALS['TSFE']->config['config']['sys_language_uid']);
                $GLOBALS['TSFE']->initLLvars();
                $GLOBALS['TSFE']->calculateLinkVars();
                break;
            default:
                $content = $this->view->assign('contentElements', $this->settings['pageNotFound'])->render();
        }

        return $content;
    }
}
