Votable
=======

Versatile and pluggable Voting system. Tell what content type are votable, install the plugin on a page and your Frontend Users can start voting...

After the User has **authenticated** himself, he can see something like:

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Frontend-01.png)

Once voted:

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Frontend-02.png)


Data Model
==========


```

    ----------------------------------            ----------------------------------            ----------------------------------
    | fe_users                       |            | tx_votable_domain_model_vote   |            | * tx_votable_vote_record_mm    |
    ----------------------------------            ---------------------------------- (limit 1)  ----------------------------------
    |                                | ---------- | * voting (int)                 | ---------  | * uid_local                    |
    |                                |  1      n  | * item (int)                   |  1     1   | * uid_foreign                  |
    |                                |            | * user (int)                   |            | * tablenames                   |
    |                                |            |                                |        --  | * fieldname                    |
    ----------------------------------            ----------------------------------        | n ---------------------------------
                                                                                            |
                                                                                            |
                                                                                            |    ----------------------------------
                                                                                            |    | * (any content)                |
                                                                                            | 1  ----------------------------------
                                                                                            |--  | * votes                        |
                                                                                                 | * rank                         |
                                                                                                 |                                |
                                                                                                 ----------------------------------

```

Installation
============

1. Install the extension as normal in the Extension Manager. Make sure to declare what tables you wish to be "votable" there.

2. Next step is to create a content element of type votable that will configure the jQuery plugin (automatically loaded)

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Backend-01.png)

3. Then on the Frontend, everywhere you want the votable widget displayed, you must generate this kind of HTML where x is the current id of the votable object as declared in the plugin settings:  

```

    <div class="widget-votable" data-object="x"/>
```

4. Important: load and adjust the CSS in your plugin. (todo: the loading could be made more automatic but also configurable)


```

    page.includeCSS {
    
        # Sourced version
        votableCss = EXT:votable/Resources/Public/StyleSheets/votable.css
    }
```


Make Votable API
================
    
This code is automatically generated for you according the the settings in the Extension Manager. However, you can fiddle around and ajust it for your needs. It will add extra fields to the table "tx_domain_domain_model_foo", by default `votes` and `rank`. 

```

    \Visol\Votable\VotingUtility::makeVotable(
    
            # Mandatory extension name to have it seamlessly integrated when activating the extension in the EM. 
            'extension_key',
            
            # The table you want votable
            'tx_domain_domain_model_foo',
            
            # Default field name
            'votes',
            
            # Default sorting field name automatically computed
            'rank',
            
            # Configuration option
            array(
                    // Set a custom label
                    'label' => 'LLL:EXT:examples/Resources/Private/Language/locallang.xlf:additional_categories',
                    
                    // This field should not be an exclude-field
                    'exclude' => false,
                    
                    // Override generic configuration, e.g. sort by title rather than by sorting
                    'fieldConfiguration' => array(
                            ...
                    ),
            ),
    );
```

Signal Slot
===========

You can take advantage of a signal slot to perform some action such as cache flushing. Firstyl in some `ext_localconf.php`::

```
       
    // Votable Signal Slot to clear the candidate files cache.
    /** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    
    // Connect "afterVoteChange" signal slot with the "VotableAspects".
    $signalSlotDispatcher->connect(
        \Visol\Votable\Controller\VoteController::class,
        'beforeVoteChange',
        \Vender\MyExtension\VotableAspect\FlushCacheSlot::class,
        'flush',
        TRUE
    );
```

In your own implementation:

````

    namespace Vender\MyExtension\VotableAspect;
    
    use Visol\Votable\Domain\Model\Vote;
    
    /**
     * Class FlushCacheSlot
     */
    class FlushCacheSlot
    {
    
        /**
         * @param Vote $vote
         * @param string $addOrRemove
         * @return array
         */
        public function flush(Vote $vote, $addOrRemove)
        {
    
            // your code....
            return [$vote, $addOrRemove];
        }
        
    }

```

Hook
====

When updating a vote, the field rank is automatically computed to sort the voted object. In some circumstances, you may want to only apply this computing to a subset of records. There is a hook to add some clause when computing the rank field. To make used of it, add in your `ext_localconf.php` the following code.

```

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['votable']['rankCacheWhereClause'][] = \Vendor\MyExtension\Hook\RankCacheWhereClause::class;
```

Then in your own class:

```

    namespace Vendor\MyExtension\Hook;
    
    /**
     * Class RankCacheWhereClause
     */
    class RankCacheWhereClause implements Visol\Votable\Hook\RankCacheHookInterface
    {
    
        /**
         * @param $possibleWhereClause
         * @param \Visol\Votable\Domain\Model\Vote $vote
         * @return string
         */
        public function getPossibleWhereClause($possibleWhereClause, $vote)
        {
            // your logic comes here...
            return $possibleWhereClause;
        }
    
    }
```

Build assets
============

Source is located at `Resources/Public/JavaScript/*.js`. Grunt will watch the files and generate a min file as editing. To start watching:

```
	npm install
	grunt watch
```

