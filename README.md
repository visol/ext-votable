Votable
=======

Voting system and its API...


Data Model
==========


```

    ----------------------------------            ----------------------------------            ----------------------------------
    | tx_votable_domain_model_voting |            | tx_votable_domain_model_vote   |            | * tx_votable_vote_record_mm    |
    ----------------------------------            ---------------------------------- (limit 1)  ----------------------------------
    | * allowed_frequency (int)      | ---------- | * voting (int)                 | ---------  | * uid_local                    |
    | * closing_date (date)          |  1      n  | * item (int)                   |  1     1   | * uid_foreign                  |
    |                                |            | * user (int)                   |            | * tablenames                   |
    |                                |      |---  |                                |        --  | * fieldname                    |
    ----------------------------------      |  n  ----------------------------------        | n ---------------------------------
                                            |                                               |
                                            |                                               |
    ----------------------------------      |                                               |    ----------------------------------
    | fe_users                       |      |                                               |    | * (any content)                |
    ----------------------------------      |                                               | 1  ----------------------------------
    |                                | -----                                                |--  | * votes                        |
    |                                |  1                                                        | * rank                         |
    |                                |                                                           |                                |
    ----------------------------------                                                           ----------------------------------

```

Make Votable
============
    
Add an extra votable field to the table "tx_domain_domain_model_foo".

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


Widget
======

The extension provide an jQuery plugin for convenience sake which looks something like below and enables the User to vote.
 
 
!!!! Screenshot

To render the widget, you firstly need to load the jQuery file through the Page Renderer or by other means.

```

    page.includeJSFooterlibs {
    
        # Sourced version
        votableJs = EXT:votable/Resources/Public/JavaScript/votable.js
        
        # Minified version (alternative)
        votableJs = EXT:votable/Resources/Public/JavaScript/votable.min.js
    }
```

Alternatively, you can load the CSS file

```

    page.includeCSS {
    
        # Sourced version
        votableCss = EXT:votable/Resources/Public/StyleSheets/votable.css
    }
```


Then, you must generate everywhere you want the `votable` widget being displayed, this HTML:


```

    <div class="widget-votable" data-object="x" data-voting="y"/>

```

Finally in your JS, do something like:


```
    
    var options = {
    
        // x is the identifier of the voting, mandatory
        voting: x,
        
        // y is the identifier of the user, mandatory
        user: y,
        
        labels: {}
    };
    $('.widget-votable').votable(options)

```


API
===

The jQuery plugin is not mandatory as such. You can provide your own implementation (HTML). In this case you may want to know how to interact with the API so to cast new vote.
 
 

Build assets
============

Source is located at `Resources/Public/JavaScript/*.js`. Grunt will watch the files and generate as editing the build file. To start watching.

```
	npm install
	grunt watch
```

