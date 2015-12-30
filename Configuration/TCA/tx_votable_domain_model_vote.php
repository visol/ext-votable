<?php
return array(
    'ctrl' => [
        'title' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:tx_votable_domain_model_vote',
        'label' => 'user',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',

        'delete' => 'deleted',
        'searchFields' => '',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('votable') . 'Resources/Public/Images/tx_votable_domain_model_vote.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'user, voting, value, time, ip',
    ],
    'types' => [
        '1' => ['showitem' => 'user, voting, value, time, ip'],
    ],
    'columns' => [

        'user' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:user',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'voting' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:voting',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'value' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:ip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],

    ],
);