@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">order code</th>
                        <th scope="col">status</th>
                        <th scope="col">created_at</th>
                        <th scope="col">operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <th scope="row">{{ $loop->index + 1 }}</th>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                @if($order->status == 'unpaid' && $order->created_at > now()->setTimezone('Asia/Tehran')->subMinutes(config('app.reserved_order_time')))

                                    <form action="{{ route('payment.create', ['order' => $order->id]) }}" method="get">
                                        <button class="btn btn-success">Payment</button>
                                    </form>
                                @else
                                    <p>This order is no longer eligible for payment.</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
