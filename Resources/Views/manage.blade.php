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
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.manage.box-title') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">

                        <a href="add" class="fa fa-bolt btn btn-primary"> {{ trans('rssfeeds::general.button.add') }}</a><br />
                        <br />

                        <table width=""90%" class="" cellpadding="6">
                        <tr style="background: #c5c5c5;">
                            <th width="150">Name</th>
                            <th width="300">URL</th>
                            <th width="80">Items</th>
                            <th width="80">Interval</th>
                            <th width="100">Owner</th>
                            <th width="80">Active</th>
                            <th  width="200"></th>
                        </tr>

                        @foreach($feeds as $feed)
                        <tr style="border-bottom: 1px #000 solid;">
                            <td>{{ $feed['feed_name'] }}</td>
                            <td>{{ $feed['feed_url'] }}</td>
                            <td>{{ $feed['feed_items'] }}</td>
                            <td>{{ $feed['feed_interval'] }}</td>
                            <td>{{ $feed['feed_owner'] }}</td>
                            <td>
                                @if($feed['feed_active'] == 1)
                                    <a href="deactivate/{{ $feed['id'] }}"><i class="fa fa-check-circle-o" aria-hidden="true" style="color: #00ff00;"></i></a>
                                @else
                                    <a href="activate/{{ $feed['id'] }}"><i class="fa fa-check-circle-o" aria-hidden="true" style="color: #ff0000;"></i></a>
                                @endif
                            </td>
                            <td>
                                <a href="edit/{{ $feed['id'] }}" class="fa fa-pencil"> {{ trans('rssfeeds::general.button.edit') }}</a>
                                |
                                <a href="delete/{{ $feed['id'] }}" class="fa fa-trash"> {{ trans('rssfeeds::general.button.delete') }}</a>
                            </td>
                        </tr>
                        @endforeach

                        </table>

                    </div><!-- /.form-group -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
