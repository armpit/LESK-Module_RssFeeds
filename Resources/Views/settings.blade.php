@extends('layouts.master')

@section('content')
    @if(Auth::user())
        @if(Auth::user()->hasRole('rssfeeds-manager') || Auth::user()->hasRole('admins'))
            <a href="{{ route('rssfeeds.manage') }}" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.manage') }}</a>
        @endif
    @endif

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.settings.box-title') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">

                        {!! Form::open( array('route' => 'rssfeeds.process_settings', 'id' => 'frmSettings') ) !!}

                        @foreach($options as $option)
                        <b>{{ $option['name'] }}:</b><br />
                        @if($option['name'] == 'cache_enable' || $option['name'] == 'personal_enable')
                        {!! Form::select(
                            $option['name'],
                            ['False', 'True'],
                            $option['value'],
                            ['style' => 'width:250px;', 'id' => $option['name']])
                        !!}
                        @else
                        {!! Form::text(
                            $option['name'],
                            $option['value'],
                            ['style' => 'width:250px;', 'id' => $option['name']])
                        !!}
                        @endif
                        <br />
                        @endforeach

                        <br />
                        <a class="btn btn-default btn-sm fa fa-floppy-o" href="#" onclick="document.forms['frmSettings'].action = '{{ route('rssfeeds.process_settings') }}';  document.forms['frmSettings'].submit(); return false;" title="{{ trans('rssfeeds::general.action.settings') }}">
                            {{ trans('rssfeeds::general.action.save') }}
                        </a>

                        {!! Form::close() !!}

                    </div><!-- /.form-group -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
