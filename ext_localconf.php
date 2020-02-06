<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Bitmotion.CustomErrorPage',
            'Pi1',
            [
                'ErrorHandling' => 'show',
            ],
            // non-cacheable actions
            [
                'ErrorHandling' => 'show',
            ]
        );

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        pi1 {
                            iconIdentifier = custom_error_page-plugin-pi1
                            title = LLL:EXT:custom_error_page/Resources/Private/Language/locallang_db.xlf:tx_custom_error_page_pi1.name
                            description = LLL:EXT:custom_error_page/Resources/Private/Language/locallang_db.xlf:tx_custom_error_page_pi1.description
                            tt_content_defValues {
                                CType = list
                                list_type = customerrorpage_pi1
                            }
                        }
                    }
                    show = *
                }
            }'
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'custom_error_page-plugin-pi1',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:custom_error_page/Resources/Public/Icons/Extension.svg']
        );

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['realurl'] =
                'Bitmotion\\CustomErrorPage\\Utility\\Decoder\\UrlDecoder->decode';
        }

    }
);
