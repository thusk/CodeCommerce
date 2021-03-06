@extends('app')
@section('content')
    <div class="container">
            <div class="row">
                <h1>Images of {{ $product->name }}</h1>
                <hr>
                @forelse($product->images as $image)
                <div class="col-xs-6 col-md-3">
                    <a href="#" class="thumbnail">
                        <img src="{{ url('images/products')."/".$image->idExtension }}" style="width: 200px; height: 200px">
                        <a href="{{ route('products.image.delete' , ['id' => $image->id]) }}" class="btn btn-danger">Delete image</a><br><br>
                    </a>
                </div>
                @empty
                    <div class="col-xs-6 col-md-3">
                        <a class="thumbnail">
                            <img src="{{ url('images/products/not_found.jpg') }}" style="width: 200px; height: 200px" >
                            <h4>No image available , upload one.</h4>
                        </a>
                    </div>
                @endforelse

                </div>
                <div class="row">
                    <br><br>
                    <form method="POST" action="{{ route('products.image.store',['id' => $product->id]) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="file" name="image"><br>
                        <button type="submit" class="btn btn-success">Upload image</button> <a class="btn btn-danger" href="{{ route('products.edit' , ['id' => $product->id]) }}">Back to product</a>
                    </form>
                </div>
            </div>
    </div>
@endsection