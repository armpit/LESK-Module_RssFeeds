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
 *
 */
class RssFeedsUtils
{

    /**
     * Grab the feed and return the data as an array. The default is to grab the data from
     * the cache file if it exists.
     *
     * @param string $url
     * @param bool $cache
     * @return array
     */
    static public function getFeed($url, $interval, $lastcheck, $cache = true)
    {
        $data = false;

        // set cache file name from url
        $cachefile = preg_replace('![^a-z0-9\s]+!', '_', strtolower($url));

        $now = date_timestamp_get(date_create());
        $diff = round(abs($lastcheck - $now) / 60);

        if ($cache == true) {
            if ($diff >= $interval) {
                if ($data = self::readCache($cachefile))
                    return $data;
            }
            Log::info("Cache file for " . $url . " invalidated. Refreshing feed data.");
        }

        // need to create a setting to enable/disable
//        if(!self::w3cValidator($url))
//            Log::warning('feed failed validation: '.$url);

        $xml = self::doRequest($url);
        if ($feed = self::makeObjectTree($xml, $url)) {
            if ($feed->channel->item)
                $data = self::parseRSS($feed);
            if ($feed->entry)
                $data = self::parseAtom($feed);
            self::writeCache($data, $cachefile);
        }
        return $data;
    }


    /**
     * Create XML object tree.
     *
     * @param string $xml
     * @return bool|\SimpleXMLElement
     */
    static private function makeObjectTree($xml, $url)
    {
        libxml_use_internal_errors(true);

        $sxe = simplexml_load_string($xml);
        if ($sxe == false) {
            Log::error('Error parsing feed: '.$url);
            foreach (libxml_get_errors() as $error) {
                Log::error('XML Error: '.$error->message);
            }
            return false;
        }

        try {
            $xmlTree = new \SimpleXMLElement($xml);
        } catch (Exception $e) {
            // Something went wrong.
/*            $error_message = 'SimpleXMLElement threw an exception.';
            foreach(libxml_get_errors() as $error_line) {
                $error_message .= "\t" . $error_line->message;
            }
            trigger_error($error_message);*/
            Log::error('Exception reading RSS feed: ' . $ex->getMessage());
            Log::error($ex->getTraceAsString());
            Flash::error(trans('rssfeeds::general.status.error-reading-feed'));
            return false;
        }
        return $xmlTree;
    }


    /**
     * Grab the url via curl.
     *
     * @param string $url
     * @return mixed
     */
    static private function doRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        curl_close($ch);
        return $xml;
    }


    /**
     * Parse atom feed.
     *
     * @param SimpleXMLElement $feed
     * @return array
     */
    private static function parseAtom($feed)
    {
        $feed = json_encode($feed);
        $feed = json_decode($feed, true);

        $data = array(
            'feed_type' => 'atom',
            'id' => (string) $feed['id'],
            'title' => (string) $feed['title'],
            'updated' => (string) $feed['updated'],
        );

        foreach($feed['link'] as $link) {
            if($link['@attributes']['type'] == 'text/html') {
                $data['rel'] = $link['@attributes']['href'];
            }
            if($link['@attributes']['type'] == 'application/atom+xml') {
                $data['self'] = $link['@attributes']['href'];
            }
        }

        $x = 0;
        foreach($feed['entry'] as $entry) {
            $data['items'][$x]['id'] = $entry['id'];
            $data['items'][$x]['pubdate'] = $entry['published'];
            $data['items'][$x]['updated'] = $entry['updated'];
            $data['items'][$x]['link']
                = $entry['link']['@attributes']['href'];
            $data['items'][$x]['title'] = $entry['title'];
            $data['items'][$x]['author'] = $entry['author']['name'];
            $data['items'][$x]['author_uri'] = $entry['author']['uri'];
            $data['items'][$x]['content'] = $entry['content'];
            $x++;
        }

        return $data;
    }


    /**
     * Parse RSS 2.0 feed.
     *
     * @param SimpleXMLElement $feed
     * @return array
     */
    private static function parseRSS($feed)
    {
        $data = array(
            'feed_type' => 'rss',
            'image' => self::safeUrl((string)$feed->channel->image->url),
            'link' => self::safeUrl((string)$feed->channel->link),
            'title' => (string)$feed->channel->title,
            'description' => (string)$feed->channel->description,
            'language' => (string)$feed->channel->language,
            'generator' => (string)$feed->channel->generator,
        );

        $x = 0;
        foreach ($feed->channel->item as $item) {
            $data['items'][$x]['title'] = (string)$item->title;
            $data['items'][$x]['link'] = (string)$item->link;
            $data['items'][$x]['pubdate'] = (string)$item->pubDate;
            $data['items'][$x]['content'] = self::processDescription((string)$item->description);
            $x++;
        }
        return $data;
    }


    /**
     * Validate feeds using the W3C online validator.
     *
     * @param $feed
     * @return bool
     */
    private static function w3cValidator($feed)
    {
        $validator = "http://validator.w3.org/feed/check.cgi?output=soap12&url={$feed}";
        $response = file_get_contents($validator);

        $xml = new \DOMDocument();
        $xml->loadXML($response);

        $validity = $xml->getElementsByTagName('validity');
        if ($validity->length && $validity->item(0)->nodeValue == 'true') {
            return true;
        }
        return false;
    }

    /**
     * Process the feed description removing anything that could be harmful.
     *
     * @param string $text
     * @return string
     */
    static private function processDescription($text)
    {
        // sanitize
        $clean = self::cleanText($text);
        // convert newlines
        //$clean = nl2br($clean);
        return $clean;
    }


    /**
     * Remove encoded tags and then re-encode the whole lot.
     *
     * @param string $text
     * @param int $length
     * @return string
     */
    static private function cleanText($text, $length = 0)
    {
        $html = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = strip_tags($html);
        if ($length > 0 && strlen($text) > $length) {
            $cut_point = strrpos(substr($text, 0, $length), ' ');
            $text = substr($text, 0, $cut_point) . '…';
        }
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }


    /**
     * Make sure that urls contain nothing harmful.
     *
     * @param string $raw_url
     * @return bool|string
     */
    static private function safeUrl($raw_url)
    {
        $url_scheme = parse_url($raw_url, PHP_URL_SCHEME);
        if ($url_scheme == 'http' || $url_scheme == 'https') {
            return htmlspecialchars($raw_url, ENT_QUOTES, 'UTF-8', false);
        }
        // parse_url failed, or the scheme was not hypertext-based.
        return false;
    }


    /**
     * Read feed data from cache file.
     *
     * @param $file
     * @return bool
     */
    static private function readCache($file)
    {
        $path = realpath(dirname(__FILE__));
        if (file_exists($path . '/../../../../storage/' . $file)) {
             $data = file_get_contents($path . '/../../../../storage/' . $file);
            $data = json_decode($data, true);
            return $data;
         }
        return false;
    }


    /**
     * Output feed data as json to cache file.
     *
     * @param array $data
     * @param string $cacheFile
     * @return bool
     */
    static private function writeCache($data, $cacheFile)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        try {
            $path = realpath(dirname(__FILE__));
            $fh = fopen($path . '/../../../../storage/' . $cacheFile, 'w');
            fwrite($fh, $json);
            fclose($fh);
            return true;
        } catch (Exception $ex) {
            return false;
        }
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

