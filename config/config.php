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

return [

    /*
     |--------------------------------------------------------------------------
     | Caching
     |--------------------------------------------------------------------------
     |
     | The cache_dir location is relative to: 'storage/app'
     |
     */
    'cache_enable'     => true,
    'cache_dir'        => 'rssfeeds_cache',
    'cache_ttl'        => '3600',

    /*
     |--------------------------------------------------------------------------
     | Personal Feeds
     |--------------------------------------------------------------------------
     */
    'personal_enable'  => false,

];
