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
                        <th scope="col">title</th>
                        <th scope="col">price</th>
                        <th scope="col">quantity</th>
                        <th scope="col">total amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)

                        <tr>
                            <th scope="row">{{ $loop->index + 1 }}</th>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->pivot->amount }} <small class="text-danger">T</small></td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>{{ $product->pivot->amount * $product->pivot->quantity }} <small class="text-danger">T</small>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ $payment->order_amount }} <small class="text-danger">T</small></td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <div class="alert alert-info">This transaction can be paid in {{  $reserved_time }} minutes  </div>
            </div>
            <div class="col-12">
                <div class="alert alert-info">This transaction can only be paid with this card: <span
                        class="text-success"> {{  $payment->card_number }} </span> </div>
            </div>
            <div class="col-12">
                <div class="alert alert-info">The final amount of your payment is equal to: <span
                        class="text-success"> {{  $payment->final_amount }} </span> <small class="text-danger">T</small></div>
            </div>


            <div class="col-12">
                <form method="post" action="{{ route('payment.prepare' , [$order->id , $payment->id]) }}">
                    @csrf
                    <button type="submit" class="btn btn-success mt-2">Click To Redirect Payment Gateway Transaction</button>
                </form>
            </div>
        </div>
    </div>
@endsection
