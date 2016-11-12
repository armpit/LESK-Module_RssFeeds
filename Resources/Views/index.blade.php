@extends('layouts.master')

@section('content')

    @if(Auth::user() && (Auth::user()->hasRole('rssfeeds-manager') || Auth::user()->hasRole('admins')))
        <a href="rssfeeds/manage" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.manage') }}</a>
    @endif

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.index.box-title') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">

                        @foreach($data as $feed)
                            @if(isset($feed['meta']['image']) && $feed['meta']['image'] != '')
                                <img src="{{ $feed['meta']['image'] }}" alt="{{ $feed['meta']['title'] }}" width="144px" />
                            @endif
                            <b>{{ $feed['meta']['title'] }}</b>
                            @if(isset($feed['meta']['description']) && $feed['meta']['description'] != '')
                                - {{$feed['meta']['description'] }}
                            @endif

                            <br />

                            @foreach($feed['items'] as $item)
                                <div class="feed_container">
                                    <i class="fa fa-feed"></i>&nbsp;&nbsp;<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                    &nbsp;&#64;&nbsp;{{ $item['pubdate'] }}
                                    <p class="feed_expand">View Item</p>
                                    <div class="feed_content">
                                        {!! $item['description'] !!}
                                    </div>
                                </div>
                            @endforeach

                            <hr />
                        @endforeach

                    </div><!-- /.form-group -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
@section('body_bottom')
<style>
    .feed_expand {
        margin: 1px;
        /*color: #fff;*/
        padding: 3px 10px;
        cursor: pointer;
        position: relative;
        font-size: 0.6em;
        /*background-color:#c30;*/
    }
    .feed_content {
        padding: 5px 10px;
        background-color:#fafafa;
    }
    p { padding: 5px 0; }
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".feed_content").hide();
        //toggle the componenet with class msg_body
        jQuery(".feed_expand").click(function()
        {
            jQuery(this).next(".feed_content").slideToggle(500);
        });
    });
</script>
@endsection
