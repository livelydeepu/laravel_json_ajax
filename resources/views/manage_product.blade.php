@extends('welcome')

@section('content')
    <div class="row">
		<div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add Product</h3>
                    <div class="card-tools" style="align:right">
                        <a href="{{route('product')}}" class="btn btn-primary" type="button" title="Back"> Back</a>
                    </div>
                </div>
                <form id="productForm" method="POST" action="{{route('product.process')}}" enctype="multipart/form-data">
                @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter Product Name" value="{{$product_name}}">
                        </div>
                        @error('product_name')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                        <div class="form-group">
                            <label for="quantity_in_stock">Quantity in stock</label>
                            <input type="number" name="quantity_in_stock" id="quantity_in_stock" class="form-control" placeholder="Enter Quantity in stock in number" value="{{$quantity_in_stock}}">
                        </div>
                        @error('quantity_in_stock')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                        <div class="form-group">
                            <label for="price_per_item">Price per item</label>
                            <input type="number" step=".01" name="price_per_item" id="price_per_item" class="form-control" placeholder="Enter Price per item in number" value={{$price_per_item}}>
                        </div>
                        @error('price_per_item')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="id" name="id" value="{{$id}}">
                </form>
            </div>
        </div>
    </div>
@endsection