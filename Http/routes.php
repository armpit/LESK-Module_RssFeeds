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

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::group(['prefix' => 'rssfeeds'], function() {
    Route::get( '/',  ['as' => 'rssfeeds.home', 'uses' => 'RssFeedsController@index']);
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
//Route::group(['prefix' => 'rssfeeds', 'middleware' => ['web']], function () {
//	//
//});

// Routes in this group must be authorized.
Route::group(['middleware' => 'authorize'], function () {

    // rssfeeds routes
    Route::group(['prefix' => 'rssfeeds'], function () {
        Route::get( 'mine',             ['as' => 'rssfeeds.mine',        'uses' => 'RssFeedsController@mine']);
        Route::get( '/manage',          ['as' => 'rssfeeds.manage',      'uses' => 'RssFeedsController@manage']);
        Route::get( '/settings',        ['as' => 'rssfeeds.settings',    'uses' => 'RssFeedsController@settings']);
        Route::get( '/add',             ['as' => 'rssfeeds.add',         'uses' => 'RssFeedsController@add']);
        Route::get( '/add_personal',    ['as' => 'rssfeeds.add_personal','uses' => 'RssFeedsController@add']);
        Route::get( '/activate/{id}',   ['as' => 'rssfeeds.activate',    'uses' => 'RssFeedsController@activate']);
        Route::get( '/deactivate/{id}', ['as' => 'rssfeeds.deactivate',  'uses' => 'RssFeedsController@deactivate']);
        Route::get( '/force/{id}',      ['as' => 'rssfeeds.force',       'uses' => 'RssFeedsController@force']);
        Route::get( '/unforce/{id}',    ['as' => 'rssfeeds.unforce',     'uses' => 'RssFeedsController@unforce']);
        Route::get( '/delete/{id}',     ['as' => 'rssfeeds.delete',      'uses' => 'RssFeedsController@delete']);
        Route::get( '/edit/{id}',       ['as' => 'rssfeeds.edit',        'uses' => 'RssFeedsController@edit']);
        Route::post( '/process',        ['as' => 'rssfeeds.process',     'uses' => 'RssFeedsController@process']);
        Route::post( '/process_settings', ['as' => 'rssfeeds.process_settings', 'uses' => 'RssFeedsController@process_settings']);
    });

});
