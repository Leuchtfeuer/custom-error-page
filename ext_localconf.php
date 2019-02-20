<?php
defined('TYPO3_MODE') || die;

if (!defined('TYPO3_COMPOSER_MODE') || !TYPO3_COMPOSER_MODE) {
    require \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('custom_error_page') . 'Libraries/vendor/autoload.php';
}