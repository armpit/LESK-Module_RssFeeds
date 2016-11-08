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


use League\Flysystem\Exception;

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
    static public function getFeed($url, $cache = true)
    {
        $data = array('items' => array());

        // set cache file name from url
        $cf = preg_replace('![^a-z0-9\s]+!', '_', strtolower($url));

        // Try reading from cache file or fall through and fetch from web.
        if ($cache == true) {
            $data = self::readCache($cf);
           return $data;
        }

        // fetch feed and parse
        $xml = self::doRequest($url);
        $feed = self::makeObjectTree($xml);

        // generate data array
        $data = array(
            'image' => self::safeUrl((string)$feed->channel->image->url),
            'link' => self::safeUrl((string)$feed->channel->link),
            'title' => (string)$feed->channel->title,
            'description' => (string)$feed->channel->description,
            'language' => (string)$feed->channel->language,
            'generator' => (string)$feed->channel->generator,
        );

        // insert items into data array
        $x = 0;
        foreach ($feed->channel->item as $item) {
            $data['items'][$x]['title'] = (string)$item->title;
            $data['items'][$x]['link'] = (string)$item->link;
            $data['items'][$x]['pubdate'] = (string)$item->pubDate;
            $data['items'][$x]['description'] = self::processDescription((string)$item->description);
            $x++;
        }

        // write cache file and return feed data
        self::writeCache($data, $cf);
        return $data;
    }


    /**
     * Create XML object tree.
     *
     * @param string $xml
     * @return bool|\SimpleXMLElement
     */
    static private function makeObjectTree($xml)
    {
        libxml_use_internal_errors(true);
        try {
            $xmlTree = new \SimpleXMLElement($xml);
        } catch (Exception $e) {
            // Something went wrong.
            $error_message = 'SimpleXMLElement threw an exception.';
            foreach(libxml_get_errors() as $error_line) {
                $error_message .= "\t" . $error_line->message;
            }
            trigger_error($error_message);
            dd($xml);
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

}

