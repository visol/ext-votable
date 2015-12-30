<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['votable']);

if (FALSE === isset($configuration['autoload_typoscript']) || TRUE === (bool)$configuration['autoload_typoscript']) {

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		'votable',
		'constants',
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:votable/Configuration/TypoScript/constants.ts">'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		'votable',
		'setup',
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:votable/Configuration/TypoScript/setup.ts">'
	);
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.votable',
	'Pi1',
	array(
		'Voting' => 'list',

	),
	// non-cacheable actions
	array(
		'Voting' => 'list',

	)
);
