<?php
/**
 * Rss Feeds Module
 *
 * Copyright (c) 2016, armpit <armpit@rumpigs.net>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 */

namespace App\Modules\RssFeeds\Utils;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Setting;
use App\User;
use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Schema;

use Sroutier\LESKModules\Contracts\ModuleMaintenanceInterface;
use Sroutier\LESKModules\Traits\MaintenanceTrait;

class RssFeedsMaintenance implements ModuleMaintenanceInterface
{
    use MaintenanceTrait;

    /**
     * Initialize the module.
     */
    static public function initialize()
    {
        Log::info('Initializing Rss Feeds module.');

        DB::transaction(function () {
            //----- Build database or run migration.
            self::buildDB();

            $permAuthed = Permission::where('name', 'basic-authenticated')->first();
            $permOpenToAll = Permission::where('name', 'open-to-all')->first();
            $menuHome = Menu::where('name', 'home')->first();

            // -----  Create permissions and roles for the module
            $permManage = self::createPermission( 'rssfeeds-manage-feeds',
                'RSS Feed Management',
                'Edit/delete/modify RSS feeds.'
            );

            self::createRole( 'rssfeeds-manager',
                'RSS Feeds Manager',
                'Allowed to manage RSS feeds.',
                [$permManage->id]
            );

            // ----- Create routes and associate permissions
            $routeHome = self::createRoute( 'rssfeeds.home',
                'rssfeeds',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@home',
                $permOpenToAll );

            $routeMine = self::createRoute( 'rssfeeds.mine',
                'rssfeeds/mine',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@mine',
                $permAuthed );

            $routeManage = self::createRoute( 'rssfeeds.manage',
                'rssfeeds/manage',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@manage',
                $permManage );

            $routeAdd = self::createRoute( 'rssfeeds.add',
                'rssfeeds/add',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@add',
                $permManage );

            $routeDelete = self::createRoute( 'rssfeeds.delete',
                'rssfeeds/delete/{id}',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@delete',
                $permManage );

            $routeEdit = self::createRoute( 'rssfeeds.edit',
                'rssfeeds/edit/{id}',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@edit',
                $permManage );

            $routeProcess = self::createRoute( 'rssfeeds.process',
                'rssfeeds/process',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@process',
                $permManage );

            $routeActivate = self::createRoute( 'rssfeeds.activate',
                'rssfeeds/activate/{id}',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@activate',
                $permManage );

            $routeDeactivate = self::createRoute( 'rssfeeds.deactivate',
                'rssfeeds/deactivate/{id}',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@deactivate',
                $permManage );

            // ----- Create menu items
            $menuRssFeeds  = self::createMenu( 'rssfeeds.root', 'RSS Feeds', 20, 'fa fa-feed', $menuHome, false,  $routeHome, $permOpenToAll );
            $menuRssView   = self::createMenu( 'rssfeeds.home', 'View Feeds', 1, 'fa fa-bolt', $menuRssFeeds, false,  $routeHome, $permOpenToAll );
            $menuRssManage = self::createMenu( 'rssfeeds.manage', 'Manage Feeds', 2, 'fa fa-bolt', $menuRssFeeds, false,  $routeManage, $permManage );
            $menuRssAdd    = self::createMenu( 'rssfeeds.add', 'Add Feeds', 3, 'fa fa-bolt', $menuRssFeeds, false,  $routeAdd, $permManage );
            $menuRssEdit   = self::createMenu( 'rssfeeds.edit', 'Edit Feeds', 4, 'fa fa-bolt', $menuRssFeeds, false,  $routeEdit, $permManage );

            // ----- Create cache directory
            Storage::makeDirectory('rssfeeds_cache');
        }); // End of DB::transaction(....)
    }


    /**
     * Uninitialize the module.
     */
    static public function unInitialize()
    {
        Log::info('Uninitializing Rss Feeds module.');

        DB::transaction(function () {
            // ----- Delete menu structure
            self::destroyMenu('rssfeeds.home');
            self::destroyMenu('rssfeeds.manage');
            self::destroyMenu('rssfeeds.add');
            self::destroyMenu('rssfeeds.edit');
            self::destroyMenu('rssfeeds.root');

            // ----- Destroy permissions
            self::destroyPermission('rssfeeds-management');

            // ----- Destroy roles
            self::destroyRole('rssfeeds-manager');

            // ----- Destroy routes
            self::destroyRoute('rssfeeds.home');
            self::destroyRoute('rssfeeds.manage');
            self::destroyRoute('rssfeeds.add');
            self::destroyRoute('rssfeeds.delete');
            self::destroyRoute('rssfeeds.edit');
            self::destroyRoute('rssfeeds.process');
            self::destroyRoute('rssfeeds.activate');
            self::destroyRoute('rssfeeds.deactivate');

            // ----- Destroy database or rollback migration.
            self::destroyDB();

            // ----- Delete cache directory
            Storage::deleteDirectory('rssfeeds_cache');
        }); // End of DB::transaction(....)
    }


    /**
     * Enable the module.
     */
    static public function enable()
    {
        Log::info('Enabling Rss Feeds module.');

        DB::transaction(function () {
            // ----- Enable main menu items
            self::enableMenu('rssfeeds.root');
            self::enableMenu('rssfeeds.home');
            self::enableMenu('rssfeeds.manage');
        }); // End of DB::transaction(....)
    }


    /**
     * Disable the module.
     */
    static public function disable()
    {
        Log::info('Disabling Rss Feeds module.');

        DB::transaction(function () {
            // ----- Disable main menu items
            self::disableMenu('rssfeeds.home');
            self::disableMenu('rssfeeds.manage');
            self::disableMenu('rssfeeds.root');
        }); // End of DB::transaction(....)
    }


    /**
     * Build database.
     */
    static public function buildDB()
    {
        Log::info('Creating database for Rss Feeds module.');

        Schema::create('mod_rssfeeds', function (Blueprint $table) {
            // columns
            $table->increments('id');
            $table->string('feed_name')->comment('The name of the feed.')->unique();
            $table->string('feed_url')->comment('The URL of the feed.')->unique();
            $table->boolean('feed_active')->comment('Is the feed active.')->default('true');
            $table->integer('feed_items')->comment('Number of items to retrieve.')->default(5);
            $table->integer('feed_interval')->comment('Update interval.')->default(3600);
            $table->integer('feed_owner')->comment('The user that owns the feed.')->default(0);
            //$table->timestamps();
        });
    }


    /**
     * Drop database.
     */
    static public function destroyDB()
    {
        Log::info('Destroying database for Rss Feeds module.');
        Schema::dropIfExists('mod_rssfeeds');
    }

}
