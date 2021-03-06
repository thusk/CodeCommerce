@extends('store.layouts.default')
@if($category)
@section('title' , $category->name)
@else
@section('title' , 'categoria não encontrada')
@endif
@section('left-bar')
    <div class="col-sm-3">
        <div class="left-sidebar">
            <h2>Categorias</h2>
            <div class="panel-group category-products" id="accordian"><!--category-products-->
                @include('store.partial.list_category')
            </div><!--/category-products-->
        </div>
    </div>
@endsection
@section('content')
    @if($category)
    <div class="col-sm-9 padding-right">

        <div class="features_items"><!--features_items-->
            <h2 class="title text-center">{{ $category->name }}</h2>
            @include('store.partial.list_products' , ['products' => $categoryProducts])
        </div><!--features_items-->
    </div>
    @endif
@endsection