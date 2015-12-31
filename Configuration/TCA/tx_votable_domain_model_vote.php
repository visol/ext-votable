<?php
return array(
    'ctrl' => [
        'title' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:tx_votable_domain_model_vote',
        'label' => 'user',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'delete' => 'deleted',
        'rootLevel' => -1,
        'searchFields' => 'ip',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('votable') . 'Resources/Public/Images/tx_votable_domain_model_vote.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'user, item, voting, value, time, ip',
    ],
    'types' => [
        '1' => ['showitem' => 'user, item, voting, value, time, ip'],
    ],
    'columns' => [

        'user' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:user',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'fe_users',
                'minitems' => 1,
                'maxitems' => 1,
                'readOnly' => true,
            ],
        ],
        'voting' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:voting',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_votable_domain_model_voting',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'item' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:item',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => '*',
                'MM' => 'tx_votable_vote_record_mm',
                'MM_oppositeUsage' => [],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'show_thumbs' => true,
                'readOnly' => true,
            ]
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
                'dbType' => 'datetime',
                'type' => 'input',
                'eval' => 'datetime',
                'checkbox' => 1,
                'default' => '0000-00-00 00:00:00',
                'readOnly' => true,
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