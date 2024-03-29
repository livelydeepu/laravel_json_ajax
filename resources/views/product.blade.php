@extends('welcome')

@section('styles')
    <!-- DataTables -->
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/dataTables.bootstrap5.min.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/responsive.bootstrap5.min.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('bootstrap/css/buttons.bootstrap5.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
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
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Datetime</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody id="tablecontents">
                                @if(count($products))
                                @foreach($products as $product)
                                <?php
                                    $total_value = (int)(json_decode($product->data)->quantity_in_stock)*(int)(json_decode($product->data)->price_per_item);
                                ?>
                                <tr class="row1" data-id="{{$product['id']}}">
                                    <td>
                                        <div style="color:rgb(124,77,255); padding-left: 10px; float: left; font-size: 20px; cursor: pointer;" title="change display order">
                                            <i class="fa fa-ellipsis-v"></i>
                                            <i class="fa fa-ellipsis-v"></i>
                                        </div>
                                    </td>
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
                                    <th>#</th>
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
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="{{asset('bootstrap/js/jquery.tabledit.min.js')}}"></script>
    
	<script type="text/javascript">
		$(document).ready(function(){
            $('#datatable_product').DataTable({
                order: [[4, 'desc']],

                "createdRow": function( row, data, dataIndex ) {
                    if ( data[6] == "" ) {
                        $(row).hassClass( 'tabledit-toolbar-column' ).text() = "Action";
                    }
                },

                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
        
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

            $('#datatable_product thead tr th').each(function() {
                if ($(this).hasClass("tabledit-toolbar-column")) {
			        $(this).html('Action');
			    }
            });

            $('#datatable_product tfoot tr td').each(function() {
                $(this).replaceWith('<th class="tabledit-toolbar-column">' + $(this).text() + '</th>');
                $(this).html('Action');
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

            $(function () {
                $("#datatable_product").sortable({
                    items: "tr",
                    cursor: 'move',
                    opacity: 0.6,
                    update: function() {
                        sendProductToServer();
                    }
                });

                function sendProductToServer() {
                    var product = [];
                    $('tr.row1').each(function(index,element) {
                        product.push({
                            id: $(this).attr('data-id'),
                            position: index+1
                        });
                    });

                    $.ajax({
                            type: "POST", 
                            dataType: "json", 
                            url: "{{ url('product/updateProduct') }}",
                            data: {
                            product:product,
                            _token: '{{csrf_token()}}'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                console.log(response);
                            } else {
                                console.log(response);
                            }
                        }
                    });
                }
            });
        });
	</script>
@endsection