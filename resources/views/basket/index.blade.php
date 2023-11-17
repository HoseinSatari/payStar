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
                        <th scope="col">operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <th scope="row">{{ $loop->index + 1 }}</th>
                            <td>{{ $item['product']->title }}</td>
                            <td>{{ $item['product']->price }} <small class="text-danger">T</small></td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['product']->price * $item['quantity'] }} <small class="text-danger">T</small>
                            </td>
                            <td>
                                <form action="{{ route('basket.remove' , ['product' => $item['product']->id]) }}"
                                      method="post">
                                    @csrf
                                    <button class="btn btn-danger">remove</button>
                                </form>
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
                <form method="post" action="{{ route('order.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-success mt-2">Create Order</button>
                </form>
            </div>
        </div>
    </div>
@endsection
