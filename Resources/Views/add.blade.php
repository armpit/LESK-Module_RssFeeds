@extends('layouts.master')

@section('content')
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
                            '180',
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
                            ['True', 'False'],
                            '',
                            ['style' => 'width:250px;', 'id' => 'txtActive'])
                        !!}
                        <br />

                        <br />
                        <a class="btn btn-default btn-sm fa fa-floppy-o" href="#" onclick="document.forms['frmEdit'].action = '{{ route('rssfeeds.process') }}';  document.forms['frmEdit'].submit(); return false;" title="{{ trans('rssfeeds::general.action.add') }}">
                            Save
                        </a>

                        {!! Form::close() !!}


                    </div><!-- /.form-group -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
