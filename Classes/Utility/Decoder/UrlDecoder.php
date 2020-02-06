<?php
declare(strict_types = 1);

namespace Bitmotion\CustomErrorPage\Utility\Decoder;

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

use DmitryDulepov\Realurl\Decoder\UrlDecoder as UrlDecoderBase;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * This class contains URL decoder for the RealURL. It is singleton because the
 * same instance must run in two different hooks.
 */
class UrlDecoder extends UrlDecoderBase implements SingletonInterface
{
    /**
     * Decodes the URL. This function is called from
     * \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::checkAlternativeIdMethods()
     *
     * @param array                        $params
     */
    public function decode(array $params)
    {
        if ($params['pObj'] instanceof \Bitmotion\CustomErrorPage\Controller\TypoScriptFrontendController) {
            $this->siteScript = $params['pObj']->siteScript;
        }
        parent::decodeUrl($params);
    }
}
