<?php
defined('TYPO3_MODE') || die();

// customerrorpage_pi1
$pluginSignature = str_replace('_', '', 'custom_error_page') . '_pi1';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature]  = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:custom_error_page/Configuration/FlexForms/Pi1.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Bitmotion.CustomErrorPage',
    'Pi1',
    'Error message handling'
);
