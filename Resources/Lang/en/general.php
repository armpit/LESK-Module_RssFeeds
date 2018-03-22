<?php

return [
    'audit-log' => [
        'category'        => 'RssFeeds',
        'msg-index'       => 'Accessed index RssFeeds: :var.',
    ],

    'page'      => [
        'index'         => [
            'title'             => 'RSS Feeds',
            'description'       => 'Simple RSS Feed Reader.',
            'box-title'         => 'RSS Feeds',
        ],
        'mine'          => [
            'title'             => 'My Feeds',
            'description'       => 'Users personal RSS feeds.',
            'box-title'         => 'My Feeds',
        ],
        'add'           => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Add a new RSS feed.',
            'box-title'         => 'Add Feed',
        ],
        'manage'        => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Add/delete/modify RSS feeds.',
            'box-title'         => 'Manage Feeds',
        ],
        'settings'      => [
            'title'             => 'RSS Feeds Module Settings',
            'description'       => 'Modify module settings.',
            'box-title'         => 'Module Settings',
        ],
        'delete'        => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Delete an RSS feed.',
            'box-title'         => 'Delete Feed',
        ],
        'edit'          => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Edit an RSS feeds.',
            'box-title'         => 'Edit Feed',
        ],
    ],

    'status' => [
        'error-invalid-feed'        => 'Feed did not pass validation: ',
        'error-no-feeds'            => 'There are no feeds configured.',
        'error-no-such-user'        => 'User not found.',
        'error-no-user-feeds'       => 'User has no personal feeds.',

        'error-adding-feed'         => 'Could not add feed.',
        'success-feed-added'        => 'Feed added to database.',

        'error-updating-feed'       => 'Could not update feed.',
        'success-feed-updated'      => 'Feed updated.',

        'error-deleting-feed'       => 'Could not delete feed.',
        'success-feed-deleted'      => 'Feed deleted from database.',

        'error-activating-feed'     => 'Could not activate feed.',
        'success-feed-activated'    => 'Feed activated.',

        'error-deactivating-feed'   => 'Could not deactivate feed.',
        'success-feed-deactivated'  => 'Feed deactivated.',

        'error-forcing-feed'        => 'Feed forced.',
        'success-feed-forced'       => 'Feed forced.',

        'success-unforcing-feed'    => 'Error unforcing feed.',
        'success-feed-unforced'     => 'Feed unforced.',

        'error-updating-settings'   => "Could not update module settings.",
        'success-settings-updated'  => "Module settings updated.",
    ],

    'button' => [
        'cancel'    => 'Cancel',
        'back'      => 'Back',
        'edit'      => 'Edit',
        'delete'    => 'Delete',
        'save'      => 'Save',
        'manage'    => 'Manage Feeds',
        'add'       => 'Add Feed',
        'mine'      => 'My Feeds',
        'index'     => 'Feeds Home',
        'settings'  => 'Settings',
    ],

    'action' => [
        'add'       => 'Add new RSS feed.',
        'settings'  => 'Modify Settings',
        'update'    => 'Update feed details.',
        'save'      => 'Save',
        'cancel'    => 'Cancel',
    ],
];
