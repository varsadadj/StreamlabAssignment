@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Subscription List</div>

                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach ($subscriptions as $subscription)
                                <li class="list-group-item clearfix">
                                    <div class="pull-left">
                                        <h4>{{ $subscription->name }}</h4>
                                    </div>

                                    <a href="{{ url('/subscription/cancel', $subscription->id) }}" class="btn btn-default pull-right">Cancel Subscription</a>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection