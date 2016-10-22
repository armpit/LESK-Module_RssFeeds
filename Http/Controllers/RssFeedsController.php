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

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\AuditRepository as Audit;
use Auth;

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
     * @param Application $app
     */
//    public function __construct(Application $app, Audit $audit)
//    {
//        parent::__construct($app, $audit, "rssfeeds");
//        $this->app = $app;
//    }

    public function index()
    {
        $page_title = "RSS Feeds";
        $page_description = "Rss feed reader.";

        return view('rssfeeds::index', compact('page_title', 'page_description', 'page_message'));
    }

}
