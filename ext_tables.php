<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Backend integration
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Visol.votable',
	'Pi1',
	'Votable: configuration'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['votable_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	'votable_pi1',
	sprintf('FILE:EXT:votable/Configuration/FlexForm/Votable.xml')
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['votable_pi1'] = 'layout, select_key, pages, recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['votable_pi1'] = 'pi_flexform';

// @todo check namespace
$GLOBALS['TBE_MODULES_EXT']["xMOD_db_new_content_el"]['addElClasses']['Visol\Votable\Backend\Wizard'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('votable') . 'Classes/Backend/Wizard.php';


// Vidi configuration
/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
$configurationUtility = $objectManager->get(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility::class);
$configuration = $configurationUtility->getCurrentConfiguration('votable');

// Possible Static TS loading
if (TRUE === isset($configuration['autoload_typoscript']['value']) && FALSE === (bool)$configuration['autoload_typoscript']['value']) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('votable', 'Configuration/TypoScript', 'Variety of forms - effortless!');
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('vidi')) {

//	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
//	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_votable_domain_model_voting');
//
//	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
//	$moduleLoader->setIcon('EXT:votable/Resources/Public/Images/tx_votable_domain_model_voting.png')
//		->setModuleLanguageFile('LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf')
//		->setDefaultPid($configuration['default_pid']['value']) // configurable
//		->register();

	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_votable_domain_model_vote');

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader->setIcon('EXT:votable/Resources/Public/Images/tx_votable_domain_model_vote.png')
		->setModuleLanguageFile('LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf')
		->addJavaScriptFile('EXT:votable/Resources/Public/Backend/tx_votable_domain_model_vote.js')
		->register();
}

# Allowed on every page.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_votable_domain_model_vote');
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_votable_domain_model_voting');


\Visol\Votable\VotingUtility::makeVotable('votable', 'tx_easyvotesmartvote_domain_model_candidate');