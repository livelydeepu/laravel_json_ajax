@extends('welcome')

@section('styles')
    <!-- DataTables -->
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/dataTables.bootstrap5.min.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/responsive.bootstrap5.min.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/buttons.bootstrap5.min.css')}}" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                    <div class="card-tools" style="align:right">
                        <a href="{{route('product.manage', '')}}" class="btn btn-primary" type="button" title="Add"> Add Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    @csrf
                        <table id="datatable_product" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Datetime</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($products))
                                @foreach($products as $product)
                                <?php
                                    $total_value = (int)(json_decode($product->data)->quantity_in_stock)*(int)(json_decode($product->data)->price_per_item);
                                ?>
                                <tr>
                                    <td>{{$product['id']}}</td>
                                    <td>{{json_decode($product->data)->product_name}}</td>
                                    <td>{{json_decode($product->data)->quantity_in_stock}}</td>
                                    <td>{{json_decode($product->data)->price_per_item}}</td>
                                    <td>{{$product['created_at']}}</td>
                                    <td>{{$total_value}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Datetime</th>
                                    <th>Total Value</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
	<!-- jQuery -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script type="text/javascript" src="{{asset('bootstrap/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/dataTables.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/dataTables.responsive.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/responsive.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/buttons.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/jquery.tabledit.min.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
            $('#datatable_product').DataTable({
                order: [[4, 'desc']],

                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    $(api.column(6).footer()).html('Action');
        
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };
        
                    // Total over all pages
                    total = api
                        .column(5)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
        
                    // Total over this page
                    pageTotal = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
        
                    // Update footer
                    $(api.column(5).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
                },
            });

            $(function () {
                $.ajaxSetup({
                    headers:{
                    'X-CSRF-Token' : $("input[name=_token]").val()
                    }
                });

                $('#datatable_product').Tabledit({
                    editButton: true,
                    deleteButton: true,
                    url:'{{route("product.updateAjax")}}',
                    dataType:"json",
                    columns:{
                        identifier:[0, 'id'],
                        editable:[[1, 'product_name'], [2, 'quantity_in_stock'], [3, 'price_per_item']],
                    },
                    buttons: {
                        edit: {
                            class: 'btn btn-sm btn-primary',
                            html: '<span class="fas fa-pencil-alt"></span>',
                            action: 'edit'
                        },
                        delete: {
                            class: 'btn btn-sm btn-danger',
                            html: '<span class="fas fa-trash"></span>',
                            action: 'delete'
                        },
                        save: {
                            class: 'btn btn-sm btn-success',
                            html: 'Save'
                        },
                        restore: {
                            class: 'btn btn-sm btn-warning',
                            html: 'Restore',
                            action: 'restore'
                        },
                        confirm: {
                            class: 'btn btn-sm btn-danger',
                            html: 'Confirm'
                        }
                    },
                    restoreButton:false,
                    onSuccess:function(data, textStatus, jqXHR) {
                        if(data.action == 'edit') {
                            console.log(data);
                            var obj = JSON.parse(data);
                            console.log(obj.product_name);
                        }
                        if(data.action == 'delete') {
                            $('#'+data.id).remove();
                            $('#datatable-buttons').ajax.reload();
                        }
                    }
                });
            });

            $('#datatable_product thead tr th').each(function() {
                if ($(this).hasClass("tabledit-toolbar-column")) {
			        $(this).html('Action');
			    }
            });

            $('#datatable_product tfoot tr').each(function(index, tr) {
                $(tr).find('td').each(function(index, td) {
                    console.log(td)
                    $(this).html('<th class="tabledit-toolbar-column">Action</th>');
                });
            });
        });
	</script>
@endsection