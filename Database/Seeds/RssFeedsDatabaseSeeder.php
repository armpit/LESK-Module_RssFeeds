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

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RssFeedsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('mod_rssfeeds')->insert([
            'feed_name'      => 'Undeadly',
            'feed_url'       => 'http://undeadly.org/cgi?action=rss',
            'feed_active'    => 'true',
            'feed_items'     => 5,
            'feed_interval'  => 360,
            'feed_lastcheck' => date_timestamp_get(date_create()),
        ]);
        DB::table('mod_rssfeeds')->insert([
            'feed_name'      => 'Laravel News',
            'feed_url'       => 'http://feed.laravel-news.com/',
            'feed_active'    => 'true',
            'feed_items'     => 5,
            'feed_interval'  => 360,
            'feed_lastcheck' => date_timestamp_get(date_create()),
        ]);

        Model::reguard();
    }
}