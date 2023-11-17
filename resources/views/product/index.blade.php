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
            @foreach($allProducts as $product)
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <div class="card" style="width: 18rem;">
                        <img class="card-img-top" src="{{ $product->poster }}" alt="{{ $product->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->title }}</h5>
                            <form action="{{ route('basket.add' , ['product' => $product->id, 'quantity' => 1]) }}" method="post">
                                @csrf
                                <button class="btn btn-primary">add basket</button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
