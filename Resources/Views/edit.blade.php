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
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.edit.box-title') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">


                        {!! Form::open( array('route' => 'rssfeeds.process', 'id' => 'frmEdit') ) !!}
                        {!! Form::hidden('action', 'edit', ['id' => 'action']) !!}
                        {!! Form::hidden('id', $data['id'], ['id' => 'id']) !!}

                        <b>Feed Name:</b><br />
                        {!! Form::text(
                            'txtName',
                            $data['feed_name'],
                            ['style' => 'width:250px;', 'id' => 'txtName'])
                        !!}
                        <br />

                        <b>Feed URL:</b><br />
                        {!! Form::text(
                            'txtUrl',
                            $data['feed_url'],
                            ['style' => 'width:250px;', 'id' => 'txtUrl'])
                        !!}
                        <br />

                        <b>Update Interval (minutes):</b><br />
                        {!! Form::text(
                            'txtInterval',
                            $data['feed_interval'],
                            ['style' => 'width:250px;', 'id' => 'txtInterval'])
                        !!}
                        <br />

                        <b>Number Of Articles To Retrieve:</b><br />
                        {!! Form::text(
                            'txtItems',
                            $data['feed_items'],
                            ['style' => 'width:250px;', 'id' => 'txtItems'])
                        !!}
                        <br />

                        <b>Feed Active:</b><br />
                        {!! Form::select(
                            'txtActive',
                            ['False', 'True'],
                            $data['feed_active'],
                            ['style' => 'width:250px;', 'id' => 'txtActive'])
                        !!}
                        <br />

                        <br />
                        <a class="btn btn-default btn-sm fa fa-floppy-o" href="#" onclick="document.forms['frmEdit'].action = '{{ route('rssfeeds.process') }}';  document.forms['frmEdit'].submit(); return false;" title="{{ trans('rssfeeds::general.action.edit') }}">
                            {{ trans('rssfeeds::general.action.save') }}
                        </a>

                        &nbsp;&nbsp;

                        <a class="btn btn-default btn-sm fa fa-stop-o" href="#" onclick="window.history.back();" title="{{ trans('rssfeeds::general.action.cancel') }}">
                            {{ trans('rssfeeds::general.action.cancel') }}
                        </a>


                        {!! Form::close() !!}


                    </div><!-- /.form-group -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
