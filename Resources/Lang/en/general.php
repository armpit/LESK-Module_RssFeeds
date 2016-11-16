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
        'mine'         => [
            'title'             => 'My Feeds',
            'description'       => 'Users personal RSS feeds.',
            'box-title'         => 'My Feeds',
        ],
        'add'         => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Add a new RSS feed.',
            'box-title'         => 'Add Feed',
        ],
        'manage'         => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Add/delete/modify RSS feeds.',
            'box-title'         => 'Manage Feeds',
        ],
        'delete'         => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Delete an RSS feed.',
            'box-title'         => 'Delete Feed',
        ],
        'edit'         => [
            'title'             => 'RSS Feeds Management',
            'description'       => 'Edit an RSS feeds.',
            'box-title'         => 'Edit Feed',
        ],
    ],

    'status' => [
        'error-invalid-feed'        => 'Feed did not pass validation: ',
        'error-no-feeds'            => 'There are no feeds configured.',
        'error-adding-feed'         => 'Could not add feed.',
        'success-feed-added'        => 'Feed added to database.',
        'error-updating-feed'       => 'Could not update feed.',
        'success-feed-updated'      => 'Feed updated.',
        'error-deleting-feed'       => 'Could not delete feed.',
        'success-feed-deleted'      => 'Feed deleted from database.',
        'success-activating-feed'   => 'Could not activate feed.',
        'success-feed-activated'    => 'Feed activated.',
        'success-deactivating-feed' => 'Could not deactivate feed.',
        'success-feed-deactivated'  => 'Feed deactivated.',
        'error-no-such-user'        => 'User not found.',
        'error-no-user-feeds'       => 'User has no personal feeds.',
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
        'index'     => 'Feeds Home'
    ],

    'action' => [
        'add'       => 'Add new RSS feed.',
        'update'    => 'Update feed details.',
        'save'      => 'Save',
        'cancel'    => 'Cancel',
    ],
];
