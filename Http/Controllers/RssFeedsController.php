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
use App\Modules\RssFeeds\Http\Requests\AddFeed;
use App\Modules\RssFeeds\Http\Requests\EditSettings;

use App\Repositories\AuditRepository as Audit;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\User;
use Auth;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class RssFeedsController extends Controller
{

    /**
     * The application instance.
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * Accessor for simple pie instance.
     * @var \SimplePie
     */
    public $pie;

    /**
     * Custom constructor to get a handle on the Application instance.
     *
     * @param Application $app
     * @param Audit $audit
     */
    public function __construct(Application $app, Audit $audit)
    {
        parent::__construct($app, $audit, "rssfeeds");
        $this->app = $app;
        if (! $this->pie)
            $this->pie = RssFeedsUtils::initPie();
    }


    /**
     * Display feeds.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = array();
        $page_title = trans('rssfeeds::general.page.index.title');
        $page_description = trans('rssfeeds::general.page.index.description');

        $feeds = RssFeedsUtils::getFeeds();
        if(!$feeds->isEmpty())
            $data = RssFeedsUtils::getFeedData($this->pie, $feeds->toArray());

        return view('rssfeeds::index', compact('page_title', 'page_description', 'data'));
    }


    /**
     * Show users personal feeds.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function mine(Request $request)
    {
        $data = array();
        $page_title = trans('rssfeeds::general.page.mine.title');
        $page_description = trans('rssfeeds::general.page.mine.description');

        $feeds = RssFeedsUtils::getFeeds(Auth::user()->id);
        if (!$feeds->isEmpty())
            $data = RssFeedsUtils::getFeedData($this->pie, $feeds->toArray());

        return view('rssfeeds::mine', compact('page_title', 'page_description', 'data'));
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

        $feeds = RssFeedsUtils::getFeeds('all')->toArray();

        for ($i = 0; $i < count($feeds); $i++) {
            if ($feeds[$i]['feed_owner'] == 0) {
                $feeds[$i]['feed_owner'] = 'Public';
            } else {
                $feeds[$i]['feed_owner'] = Auth::getUser($feeds[$i]['feed_owner'])->username;
            }
        }

        return view('rssfeeds::manage', compact('page_title', 'page_description', 'feeds'));
    }


    /**
     * Edit module settings.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function settings(Request $request)
    {
        $_settings = new Setting();

        $page_title = trans('rssfeeds::general.page.settings.title');
        $page_description = trans('rssfeeds::general.page.settings.description');

        $settings = array('cache_enable', 'cache_dir', 'cache_ttl', 'personal_enable');
        foreach ($settings as $setting) {
            if ( $_settings->get('rssfeeds.'.$setting) ) {
                $options[] = array('name' => $setting, 'value' => $_settings->get('rssfeeds.'.$setting));
            } else {
                $options[] = array("name" => $setting, "value" => config("rssfeeds.".$setting));
            }
        }

        return view('rssfeeds::settings', compact('page_title', 'page_description', 'options' ));
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
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function delete(Request $request, $id)
    {
        try {
            FeedsModel::destroy($id);
            Flash::success(trans('rssfeeds::general.status.success-feed-deleted'));
        }
        catch (Exception $ex) {
            Log::error('Exception deleting RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-deleting-feed'));
        }
        return redirect()->route('rssfeeds.manage');
    }


    /**
     * Edit a feed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function edit($id)
    {
        $data = FeedsModel::find($id)->toArray();
        $page_title = trans('rssfeeds::general.page.edit.title');
        $page_description = trans('rssfeeds::general.page.edit.description');
        return view('rssfeeds::edit', compact('page_title', 'page_description', 'data'));
    }


    /**
     * Process management actions.
     *
     * @param AddFeed $request
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function process(AddFeed $request)
    {
        $query = $request->input();
        if (isset($query['action'])) {
            if ($query['action'] == 'add') {
                RssFeedsUtils::addFeed($query);
            }
            if ($query['action'] == 'edit') {
                RssFeedsUtils::updateFeed($query);
            }
            if ($query['action'] == 'update_settings') {
                RssFeedsUtils::updateSettings($query);
            }
        }
        return redirect()->route('rssfeeds.manage');
    }


    /**
     * Process management actions.
     *
     * @param EditSettings $request
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function process_settings(EditSettings $request)
    {
        $query = $request->input();
        RssFeedsUtils::updateSettings($query);
        return redirect()->route('rssfeeds.settings');
    }


    /**
     * Activate a feed.
     *
     * @param $id
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function activate($id)
    {
        try {
            $model = FeedsModel::find($id);
            $model->feed_active = 1;
            $model->save();
            Flash::success(trans('rssfeeds::general.status.success-feed-activated'));
        }
        catch (Exception $ex) {
            Log::error('Exception deleting RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-activating-feed'));
        }
        return redirect()->route('rssfeeds.manage');
    }


    /**
     * Deactivate a feed.
     *
     * @param $id
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function deactivate($id)
    {
        try {
            $model = FeedsModel::find($id);
            $model->feed_active = 0;
            $model->save();
            Flash::success(trans('rssfeeds::general.status.success-feed-deactivated'));
        }
        catch (Exception $ex) {
            Log::error('Exception deleting RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-deactivating-feed'));
        }
        return redirect()->route('rssfeeds.manage');
    }

}
