plugin.tx_votable {

    view {
        templateRootPath = {$plugin.tx_votable.view.templateRootPath}
        partialRootPath = {$plugin.tx_votable.view.partialRootPath}
        layoutRootPath = {$plugin.tx_votable.view.layoutRootPath}
    }

    settings {

    }
}


page_1451549782 = PAGE
page_1451549782 {
    typeNum = 1451549782
    config {
        xhtml_cleaning = 0
        admPanel = 0
        disableAllHeaderCode = 1
        disablePrefixComment = 1
        debug = 0
        additionalHeaders = Content-type:application/json
    }
    10 = USER_INT
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        extensionName = Votable
        pluginName = Pi1
        vendorName = Visol
        switchableControllerActions {
            Vote {
                1 = cast
            }
        }
    }
}