@extends('layouts.master')

@section('content')
    <a href="{{ route('rssfeeds.home') }}" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.index') }}</a>
    @if(Auth::user())
        @if(Auth::user()->hasRole('rssfeeds-manager') || Auth::user()->hasRole('admins'))
            <a href="{{ route('rssfeeds.settings') }}" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.settings') }}</a>
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
                </div>
                <div class="box-body">
                    <div class="form-group">


                        {!! Form::open( array('route' => 'rssfeeds.process', 'id' => 'frmEdit') ) !!}
                        {!! Form::hidden('action', 'add', ['id' => 'action']) !!}

                        <b>Feed Name:</b><br />
                        {!! Form::text(
                            'txtName',
                            '',
                            ['style' => 'width:250px;', 'id' => 'txtName'])
                        !!}
                        <br />

                        <b>Feed URL:</b><br />
                        {!! Form::text(
                            'txtUrl',
                            '',
                            ['style' => 'width:250px;', 'id' => 'txtUrl'])
                        !!}
                        <br />

                        <b>Update Interval (minutes):</b><br />
                        {!! Form::text(
                            'txtInterval',
                            '3600',
                            ['style' => 'width:250px;', 'id' => 'txtInterval'])
                        !!}
                        <br />

                        <b>Number Of Articles To Retrieve:</b><br />
                        {!! Form::text(
                            'txtItems',
                            '5',
                            ['style' => 'width:250px;', 'id' => 'txtItems'])
                        !!}
                        <br />

                        <b>Feed Active:</b><br />
                        {!! Form::select(
                            'txtActive',
                            ['False', 'True'],
                            '',
                            ['style' => 'width:250px;', 'id' => 'txtActive'])
                        !!}
                        <br />

                        <b>Force Feed:</b><br />
                        {!! Form::select(
                            'txtForce',
                            ['False', 'True'],
                            '',
                            ['style' => 'width:250px;', 'id' => 'txtForce'])
                        !!}
                        <br />

                        @if(Auth::user()->hasRole('rssfeeds-manager') || Auth::user()->hasRole('admins'))
                            <b>Personal Feed:</b><br />
                            {!! Form::select(
                                'txtPersonal',
                                ['False', 'True'],
                                '',
                                ['style' => 'width:250px;', 'id' => 'txtPersonal'])
                            !!}
                            <br />
                        @else
                            {!! Form::hidden('txtPersonal', 1, ['id' => 'txtPersonal']) !!}
                        @endif

                        <br />
                        <a class="btn btn-default btn-sm fa fa-floppy-o" href="#" onclick="document.forms['frmEdit'].action = '{{ route('rssfeeds.process') }}';  document.forms['frmEdit'].submit(); return false;" title="{{ trans('rssfeeds::general.action.add') }}">
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
