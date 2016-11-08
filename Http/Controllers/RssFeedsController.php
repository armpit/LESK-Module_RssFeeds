<?php
/**
 * RSS Feeds Module
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

namespace App\Modules\RssFeeds\Http\Controllers;

use App\Modules\RssFeeds\Models\FeedsModel;
use App\Modules\RssFeeds\Utils\RssFeedsUtils;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuditRepository as Audit;
use Auth;
use App\Models\Setting;
use App\User;
use Illuminate\Contracts\Foundation\Application;

class RssFeedsController extends Controller
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;


    /**
     * Custom constructor to get a handle on the Application instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app, Audit $audit)
    {
        parent::__construct($app, $audit, "rssfeeds");
        $this->app = $app;
    }


    /**
     * Display feeds.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = trans('rssfeeds::general.page.index.title');
        $page_description = trans('rssfeeds::general.page.index.description');

        $feed_list = self::getFeeds();
        $feeds = $feed_list->toArray();

        $x = 0;
        foreach ($feeds as $feed) {
            // Only grab active feeds.
            if ($feed['feed_active'] == 1) {
                $data[$x] = RssFeedsUtils::getFeed($feed['feed_url']);

                // trim items
                //$data[$x]['items'] = array_slice($data[$x]['items'], $feed['feed_items'], count($data[$x]['items']));

                // Only increment our counter if the feed had items.
                if (count($data[$x]['items']) > 0)
                    $x++;
            }
        }

        return view('rssfeeds::index', compact('page_title', 'page_description', 'data'));
    }


    /**
     * Manage feeds.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function manage(Request $request)
    {
        $page_title = trans('rssfeeds::general.page.manage.title');
        $page_description = trans('rssfeeds::general.page.manage.description');

        $feed_list = self::getFeeds();
        $feeds = $feed_list->toArray();

        return view('rssfeeds::manage', compact('page_title', 'page_description', 'feeds'));
    }


    /**
     * Add a feed.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function add(Request $request)
    {
        $page_title = trans('rssfeeds::general.page.add.title');
        $page_description = trans('rssfeeds::general.page.add.description');

        return view('rssfeeds::add', compact('page_title', 'page_description'));
    }


    /**
     * Delete a feed.
     *
     * @param Request $request
     * @param Int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function delete(Request $request, $id)
    {
        $page_title = trans('rssfeeds::general.page.manage.title');
        $page_description = trans('rssfeeds::general.page.manage.description');

        $feed_list = self::getFeeds();
        $feeds = $feed_list->toArray();

        try {
            $model = new FeedsModel();
            $model->find($id);
            $model->delete();

            Flash::success(trans('rssfeeds::general.status.success-feed-deleted'));
            return view('rssfeeds::manage', compact('page_title', 'page_description', 'feeds'));

        } catch (Exception $ex) {
            Log::error('Exception deleting RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-deleting-feed'));
            return view('rssfeeds::manage', compact('page_title', 'page_description', 'feeds'));
        }
    }


    /**
     * Edit a feed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function edit()
    {
        $page_title = trans('rssfeeds::general.page.edit.title');
        $page_description = trans('rssfeeds::general.page.edit.description');

        return view('rssfeeds::edit', compact('page_title', 'page_description'));
    }


    /**
     * Process management actions.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function process(Request $request)
    {
        $query = $request->input();

        // ----- Add new feed
        if (isset($query['action']) && $query['action'] == 'add') {
            self::addFeed($query);

            $feed_list = self::getFeeds();
            $feeds = $feed_list->toArray();

            Flash::success(trans('rssfeeds::general.status.success-feed-added'));

            $page_title = trans('rssfeeds::general.page.manage.title');
            $page_description = trans('rssfeeds::general.page.manage.description');

            return view('rssfeeds::manage', compact('page_title', 'page_description', 'feeds'));
        }

    }


    /**
     * Add RSS feed to the database.
     *
     * @param $query
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function addFeed($query)
    {
        try {
            $model = new FeedsModel();

            $model->feed_name = $query['txtName'];
            $model->feed_url = $query['txtUrl'];
            $model->feed_active = $query['txtActive'];
            $model->feed_items = $query['txtItems'];
            $model->feed_interval = $query['txtInterval'];
            $model->feed_lastcheck = date_timestamp_get(date_create());
            $model->save();

            return;

        } catch (Exception $ex) {
            Log::error('Exception updating RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-adding-feed'));
            return view('rssfeeds::add', compact('page_title', 'page_description'));
        }
    }


    /**
     * Get all feeds from database.
     *
     * @return mixed
     */
    public static function getFeeds()
    {
        $feeds = FeedsModel::all();
        return $feeds;
    }

}
