<?php
	$classes = [
        'baseTables' => [
            CURRENT_WORKING_DIR . "/templates/inet/utils/data/baseTables.php"
        ],
        'userEventsStatistics' => [
            CURRENT_WORKING_DIR . "/templates/inet/utils/data/userEventsStatistics.php"
        ],

        // email interactions interfaces
        'Inet\Proxy\Mail\iMail' => [
            CURRENT_WORKING_DIR . '/templates/inet/utils/mail/iMail.php'
        ],
        'gunMail' => [
            CURRENT_WORKING_DIR . "/templates/inet/utils/mail/gunMail.php"
        ],
        'getResponseMail' => [
            CURRENT_WORKING_DIR . "/templates/inet/utils/mail/getResponseMail.php"
        ],
        'uMail' => [
            CURRENT_WORKING_DIR . "/templates/inet/utils/mail/uMail.php"
        ],
        'tMail' => [
            CURRENT_WORKING_DIR . "/templates/inet/traits/tMail.php"
        ],
        'tEventMailsNotification' => [
            CURRENT_WORKING_DIR . "/templates/inet/traits/tEventMailsNotification.php"
        ],

        // builders & views
        'IPageEntitiesViewBuilder' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/builder/IPageEntitiesViewBuilder.php"
        ],
        'AbstractEntitiesViewBuilder' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/builder/AbstractEntitiesViewBuilder.php"
        ],
        'ContentPageBuilder' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/builder/ContentPageBuilder.php"
        ],
        'ArticlePageBuilder' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/builder/ArticlePageBuilder.php"
        ],
        'ProductPageBuilder' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/builder/ProductPageBuilder.php"
        ],
        'IPageView' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/views/IPageView.php"
        ],
        'ArticlePageView' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/views/ArticlePageView.php"
        ],
        'DefaultPageView' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/views/DefaultPageView.php"
        ],
        'ExtendedPageView' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/views/ExtendedPageView.php"
        ],
        'ProductPageView' => [
            CURRENT_WORKING_DIR . "/templates/inet/templates/views/ProductPageView.php"
        ],
	];