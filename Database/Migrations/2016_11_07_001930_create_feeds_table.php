<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rssfeeds', function (Blueprint $table) {
            // columns
            $table->increments('id');
            $table->string('feed_name')->comment('The name of the feed.');
            $table->string('feed_url')->comment('The URL of the feed.');
            $table->int('feed_active')->comment('Is the feed active.');
            $table->int('feed_force')->comment('Force URL to be treated as feed.');
            $table->int('feed_items')->comment('Number of items to retrieve.');
            $table->int('feed_interval')->comment('Update interval.');
            $table->timestamp('feed_lastcheck')->comment('Timestamp of last time feed was checked.');
            //$table->timestamps();
            // indexes
            $table->primary('id');
            $table->unique('feed_name');
            $table->unique('feed_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rssfeeds');
    }
}
