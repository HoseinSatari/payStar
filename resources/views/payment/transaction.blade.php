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
                        <td>{{ $totalPrice }} <small class="text-danger">T</small></td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div class="col-12">
                <div class="alert alert-info">The amount of your order is equal to: <span
                        class="text-success"> {{  $totalPrice }} </span> <small class="text-danger">T</small></div>
            </div>

            <div class="col-12">
                <form method="post" action="{{ route('payment.store' , $order->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="Card">Payment Gateway : </label>
                        <select name="payment_gateway" class="form-control">
                            <option value="" >select</option>
                            @foreach($allGateway as $gateway)
                                <option value="{{ $gateway }}">{{ $gateway }}</option>
                            @endforeach
                        </select>
                        @error('payment_gateway') <small class="text-danger">{{ $message }}</small> <br> @enderror

                    </div>
                    <div class="form-group">
                        <label for="Card">The card you want to pay with : </label>
                        <input type="text" class="form-control" name="card_number" id="Card"
                               placeholder="5022291098321685">
                        @error('card_number') <small class="text-danger">{{ $message }}</small> <br> @enderror
                        <small id="cardhelp" class="form-text text-muted">Please enter the card correctly, otherwise the
                            order will not be completed</small>
                    </div>

                    <button type="submit" class="btn btn-success mt-2">Create Transaction</button>
                </form>
            </div>
        </div>
    </div>
@endsection
