@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('rssfeeds::general.page.manage.box-title') }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
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
                            <th width="100">Updated</th>
                            <th width="80">Active</th>
                            <th  width="200"></th>
                        </tr>

                        @foreach($feeds as $feed)
                        <tr>
                            <td>{{ $feed['feed_name'] }}</td>
                            <td>{{ $feed['feed_url'] }}</td>
                            <td>{{ $feed['feed_items'] }}</td>
                            <td>{{ $feed['feed_interval'] }}</td>
                            <td>{{ date('m/d/Y H:i:s', $feed['feed_lastcheck']) }}</td>
                            <td>{{ $feed['feed_active'] }}</td>
                            <td>
                                <a href="edit/{{ $feed['id'] }}">EDIT</a>
                                &nbsp;&nbsp;
                                <a href="delete/{{ $feed['id'] }}">DELETE</a>
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
