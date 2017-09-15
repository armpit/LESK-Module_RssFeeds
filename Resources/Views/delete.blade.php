@extends('layouts.master')

@section('content')
    <a href="{{ route('rssfeeds.home') }}" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.index') }}</a>
    @if(Auth::user())
        @if(Auth::user()->hasRole('rssfeeds-manager') || Auth::user()->hasRole('admins'))
            <a href="{{ route('rssfeeds.settings') }}" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.settings') }}</a>
            <br />
        @endif
    @endif

    <br />

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.add.box-title') }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">

                    </div><!-- /.form-group -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
