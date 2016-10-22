<?php

namespace App\Modules\RssFeeds\Utils;

use App\Models\Menu;
use App\Models\Permission;
use App\User;
use DB;
use Sroutier\LESKModules\Contracts\ModuleMaintenanceInterface;
use Sroutier\LESKModules\Traits\MaintenanceTrait;

class RssFeedsMaintenance implements ModuleMaintenanceInterface
{
    use MaintenanceTrait;

    /**
     * Initialize the module.
     * 
     */
    static public function initialize()
    {
        DB::transaction(function () {

            //----- Find some system permissions.
            $permBasicAuthenticated = Permission::where('name', 'basic-authenticated')->first();
            $permGuestOnly          = Permission::where('name', 'guest-only')->first();
            $permOpenToAll          = Permission::where('name', 'open-to-all')->first();
            $permAdminSettings      = Permission::where('name', 'admin-settings')->first();
            //----- Find home menu.
            $menuHome = Menu::where('name', 'home')->first();

            // ----- Create routes and associate some permission
            $routeHome = self::createRoute( 'rssfeeds.home',
                'rssfeeds',
                'App\Modules\RssFeeds\Http\Controllers\RssFeedsController@index',
                $permOpenToAll );

            // ----- Create menu structure
            // createMenu($name, $label, $position = 999, $icon = 'fa fa-file', $parent = 'root', $enabled = false,
            //            $route = null, $permission = null, $url = null, $separator = false)
            $menuRssFeeds = self::createMenu( 'rssfeeds', 'RSS Feeds', 20, 'fa fa-bolt', $menuHome, false,  $routeHome, $permOpenToAll );

        }); // End of DB::transaction(....)
    }

    /**
     * Uninitialize the module.
     * 
     */
    static public function unInitialize()
    {
        DB::transaction(function () {
            // ----- Delete menu structure
            self::destroyMenu('rssfeeds.home');
        }); // End of DB::transaction(....)
    }

    /**
     * Enable the module.
     * 
     */
    static public function enable()
    {
        DB::transaction(function () {
            // ----- Enable main menu items
            self::enableMenu('rssfeeds.home');
        }); // End of DB::transaction(....)
    }

    /**
     * Disable the module.
     * 
     */
    static public function disable()
    {
        DB::transaction(function () {
            // ----- Disable main menu items
            self::disableMenu('rssfeeds.home');
        }); // End of DB::transaction(....)
    }

}
