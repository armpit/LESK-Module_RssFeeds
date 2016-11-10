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
                    <!--<div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>-->
                </div>
                <div class="box-body">
                    <div class="form-group">

                        @foreach($data as $feed)
                            @if(isset($feed['image']))
                                <img src="{{ $feed['image'] }}" alt="{{ $feed['title'] }}" height="100px" />
                            @endif
                            <b>{{ $feed['title'] }}</b>
                            @if(isset($feed['description']))
                                - {{$feed['description'] }}
                            @endif

                            <br />

                            @foreach($feed['items'] as $item)
                                    <i class="fa fa-feed"></i>&nbsp;&nbsp;<a href="{{ $item['link'] }}">{{ $item['title'] }}</a><br />
                            @endforeach

                            <hr />
                        @endforeach

                    </div><!-- /.form-group -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
