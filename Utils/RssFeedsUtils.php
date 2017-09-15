<?php
/**
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
 */

namespace App\Modules\RssFeeds\Utils;

use App\Modules\RssFeeds\Models\FeedsModel;

use Flash;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

/**
 * Class RssFeedsUtils
 * @package App\Modules\RssFeeds\Utils
 */
class RssFeedsUtils
{

    /**
     * Initialize SimplePie object.
     * @return \SimplePie
     */
    public static function initPie()
    {
        $pie = new \SimplePie();

        if (config('rssfeeds.cache_enable')) {
            $pie->enable_cache(true);
            $cache_ttl = (config('rssfeeds.cache_ttl') == true) ? config('rssfeeds.cache_ttl') : 3600;
            $pie->set_cache_duration($cache_ttl);
            $storage = (config('rssfeeds.cache_dir') != '') ? config('rssfeeds.cache_dir') : 'rssfeeds_cache';
            $pie->set_cache_location(storage_path().'/app/'.$storage);
        }

        return $pie;
    }


    /**
     * @param \SimplePie $pie
     * @param array $feeds
     * @return mixed
     */
    public static function getFeedData(\SimplePie $pie, $feeds)
    {
        $x = 0;
        foreach ($feeds as $feed) {

            if ($feed['feed_active'] != 1)
                continue;

            $pie->set_feed_url($feed['feed_url']);
            $interval = (isset($feed['feed_interval'])) ? $feed['feed_interval'] : config('rssfeeds.cache_ttl');
            $pie->set_cache_duration($interval);

            $pie->init();
            $pie->handle_content_type();

            if ($pie->error != "") {
                $data[$x]['meta'] = array(
                    'image' => '',
                    'url' => $feed['feed_url'],
                    'title' => $feed['feed_name'],
                    'description' => $pie->error,
                );
            } else {
                $data[$x]['meta'] = array(
                    'image' => $pie->get_image_url(),
                    'url' => $pie->get_permalink(),
                    'title' => $pie->get_title(),
                    'description' => $pie->get_description(),
                );

                $y = 0;
                for ($c = 0; $c < $feed['feed_items']; $c++) {
                    $item = $pie->get_item($c);
                    if ($item) {
                        $data[$x]['items'][$y] = array(
                            'url' => $item->get_permalink(),
                            'title' => $item->get_title(),
                            'description' => $item->get_description(),
                            'pubdate' => $item->get_date('j F Y | g:i a'),
                        );
                    }
                    $y++;
                }
            }
            $x++;
        }
 
        return $data;
    }


    /**
     * Add RSS feed to the database.
     *
     * @param $query
     * @return Illuminate\Support\Facades\Redirect
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
            $model->feed_owner = ($query['txtPersonal'] == 1) ? \Auth::user()->id : '0';
            $model->save();
            Flash::success(trans('rssfeeds::general.status.success-feed-added'));
            return;
        }
        catch (Exception $ex) {
            Log::error('Exception adding RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-adding-feed'));
        }
 
        return redirect('rssfeeds/manage');
    }


    /**
     * Update feed in the database.
     *
     * @param $query
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function updateFeed($query)
    {
        try {
            $feed = FeedsModel::find($query['id']);
            $feed->feed_name = $query['txtName'];
            $feed->feed_url = $query['txtUrl'];
            $feed->feed_active = $query['txtActive'];
            $feed->feed_items = $query['txtItems'];
            $feed->feed_interval = $query['txtInterval'];
            $feed->save();
            Flash::success(trans('rssfeeds::general.status.success-feed-updated'));
        }
        catch (Exception $ex) {
            Log::error('Exception updating RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-updating-feed'));
        }

        return redirect('rssfeeds/manage');
    }


    /**
     * Update module settings.
     *
     * @param $query
     * @return Illuminate\Support\Facades\Redirect
     */
    public static function updateSettings($query)
    {
        try {
            $setting = new Setting();
            $setting->set('rssfeeds.cache_enable', $query['cache_enable']);
            $setting->set('rssfeeds.cache_dir', $query['cache_dir']);
            $setting->set('rssfeeds.cache_ttl', $query['cache_ttl']);
            $setting->set('rssfeeds.personal_enable', $query['personal_enable']);
            Flash::success(trans('rssfeeds::general.status.success-settings-updated'));
        }
        catch (Exception $ex) {
            Log::error('Exception updating module settings: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-updating-settings'));
        }

        return back()->withInput();
    }


    /**
     * Get feeds from database.
     *
     * @param integer $user
     * @return mixed
     */
    public static function getFeeds($user = null)
    {
        if (isset($user)) {
        	if ($user == 'all') {
        	    $feeds = FeedsModel::all();
		        if (count($feeds) == 0)
			        Flash::warning(trans('rssfeeds::general.status.error-no-feeds'));
	        } else {
		        $feeds = FeedsModel::where('feed_owner', $user)->get();
		        if (count($feeds) == 0)
			        Flash::warning(trans('rssfeeds::general.status.error-no-user-feeds'));
	        }
        } else {
            $feeds = FeedsModel::where('feed_owner', 0)->get();
            if (count($feeds) == 0)
                Flash::warning(trans('rssfeeds::general.status.error-no-feeds'));
        }

        return $feeds;
    }

}

