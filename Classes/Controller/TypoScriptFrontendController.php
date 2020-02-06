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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController as TyposcriptFrontendControllerBase;

/**
 * Class for the built TypoScript based frontend.
 */
class TypoScriptFrontendController extends TyposcriptFrontendControllerBase
{
    /**
     * Calculates and sets the internal linkVars based upon the current
     * $_GET parameters and the setting "config.linkVars".
     * Remove language parameter if any
     */
    public function calculateLinkVars()
    {
        parent::calculateLinkVars();
        preg_replace('/&?L=[0-9]+/', '', $this->linkVars);
    }

    /**
     * Provides ways to bypass the '?id=[xxx]&type=[xx]' format, using either PATH_INFO or virtual HTML-documents
     * (using Apache mod_rewrite)
     *
     * Two options:
     * 1) Use PATH_INFO (also Apache) to extract id and type from that var. Does not require any special modules
     * compiled with apache. (less typical)
     * 2) Using hook which enables features like those provided from "realurl" extension (AKA "Speaking URLs")
     */
    public function checkAlternativeIdMethods()
    {
        $this->siteScript = $this->siteScript ?: GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT');
        // Call post processing function for custom URL methods.
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc'])) {
            $_params = ['pObj' => &$this];
            $postProc = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc'];
            foreach ($postProc as $_funcRef) {
                GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
        }
    }
}
