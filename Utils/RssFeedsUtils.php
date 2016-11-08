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


class RssFeedsUtils
{

    static public function getFeed($url)
    {
        $xml = self::doRequest($url);
        $feed = self::makeObjectTree($xml);

        $data = array(
            'image' =>
                self::safeUrl( (string) $feed->channel->image->url ),
            'link' =>
                self::safeUrl( (string) $feed->channel->link ),
            'title' => (string) $feed->channel->title,
            'description' => (string) $feed->channel->description,
            'language' => (string) $feed->channel->language,
            'generator' => (string) $feed->channel->generator,
        );

        $data['items'] = array();
        $x = 0;
        foreach ($feed->channel->item as $item) {
            $description = (string) $item->description;
            $data['items'][$x]['title'] = (string) $item->title;
            $data['items'][$x]['link'] = (string) $item->link;
            $data['items'][$x]['pubdate'] = (string) $item->pubDate;
            $data['items'][$x]['description'] = self::processDescription($description);
            $x++;
        }

        return $data;
    }


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
            return false;
        }

        return $xmlTree;
    }

    /**
     * @param $url
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

    static private function processDescription($text)
    {
        $clean = self::cleanText($text);
        $clean = nl2br($clean);
        return $clean;
    }


    static private function cleanText($text, $length = 0) {
        $html = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = strip_tags($html);
        if ($length > 0 && strlen($text) > $length) {
            $cut_point = strrpos(substr($text, 0, $length), ' ');
            $text = substr($text, 0, $cut_point) . '…';
        }
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }


    static private function safeUrl($raw_url) {
        $url_scheme = parse_url($raw_url, PHP_URL_SCHEME);
        if ($url_scheme == 'http' || $url_scheme == 'https') {
            return htmlspecialchars($raw_url, ENT_QUOTES, 'UTF-8', false);
        }
        // parse_url failed, or the scheme was not hypertext-based.
        return false;
    }

}
