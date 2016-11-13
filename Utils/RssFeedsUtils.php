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
        $pie->enable_cache(true);
        $pie->set_cache_duration(3600);
        $pie->set_cache_location(storage_path().'/app');
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
            $pie->set_feed_url($feed['feed_url']);
            $pie->set_cache_duration($feed['feed_interval']);
            $pie->init();
            $pie->handle_content_type();

            $data[$x]['meta'] = array(
                'image' => $pie->get_image_url(),
                'url' => $pie->get_permalink(),
                'title' => $pie->get_title(),
                'description' => $pie->get_description(),
            );

            $y = 0;
            for ($c = 0; $c < $feed['feed_items']; $c++) {
                $item = $pie->get_item($c);
                if($item) {
                    $data[$x]['items'][$y] = array(
                        'url' => $item->get_permalink(),
                        'title' => $item->get_title(),
                        'description' => $item->get_description(),
                        'pubdate' => $item->get_date('j F Y | g:i a'),
                    );
                }
                $y++;
            }
            $x++;
        }
        return $data;
    }


    /**
     * Add RSS feed to the database.
     *
     * @param $query
     * @return redirect
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
     * @return Redirect
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
            $feed->feed_lastcheck = date_timestamp_get(date_create());
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
     * Get all feeds from database.
     *
     * @return mixed
     */
    public static function getFeeds()
    {
        $feeds = FeedsModel::all();
        if (count($feeds) == 0)
            Flash::error(trans('rssfeeds::general.status.error-no-feeds'));
        return $feeds;
    }

}

