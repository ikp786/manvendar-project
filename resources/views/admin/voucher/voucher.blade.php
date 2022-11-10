@if(Auth::user()->role->id ==1)
    @extends('admin.layouts.templatetable')
    @section('title','Account Monthly Salary')
    @section('content')
        <script>
        function addCategory() {
            var x = document.getElementById("category_div");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
        function addBrand() {
            var x = document.getElementById("brand_div");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
		function addProductGUID() {
            var x = document.getElementById("product_guid_div");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
        function addPrice() {
            var x = document.getElementById("price_div");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
          function saveCategory()
          {
            category_name = $("#category_name").val().trim();
            var name_pattern = /^[A-Za-z ]+$/;
            if(category_name == '')
            {
                alert("Please Enter Category Name");
                return false;
            }
            /* else if(!category_name.match(name_pattern))
				{
					alert('Special Character not allowed');
					$('#category_name').focus();
					return false;
				} */
             var dataString = 'category_name=' + category_name ;
             var url="{{url('set-voucher-category')}}";
             $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({

                        type: "POST",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                            $("#category_btn").text('Processing...');
                        },
                        success: function (msg) {
                            alert(msg.message);
                            $("#category_btn").text('Save');
                            $("#category_name").val('');
                        }
                        
                    });
            }
          function saveBrand()
          {
            brand_name = $("#brand_name").val().trim();
          //  var name_pattern = /^[A-Za-z!- ]+$/;
            if(brand_name == '')
            {
                alert("Please Enter Brand Name");
                return false;
            }
            /* else if(!brand_name.match(name_pattern))
				{
					alert('Special Character not allowed');
					$('#brand_name').focus();
					return false;
				} */
            category_id = $("#category").val();
             var dataString = 'brand_name=' + brand_name +'&category_id='+category_id ;
             var url="{{url('set-voucher-brand')}}";
             $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({

                        type: "POST",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                            $("#brand_btn").text('Processing...');
                        },
                        success: function (msg) {
                            alert(msg.message);
                            $("#brand_btn").text('Save');
                            $("#brand_name").val('');
                            categoryChange();

                            
                        }
                        
                    });
            }
			function saveProductGUID()
          {
            price_table_id = $("#price").val().trim();
            product_guid = $("#product_guid").val().trim();
          //  var name_pattern = /^[A-Za-z!- ]+$/;
				if(price_table_id == '')
				{
					alert("Please Select Price");
					return false;
				}
				else if(product_guid == '')
				{
					alert("Please Enter Product GUID");
					return false;
				}
            
             var dataString = 'price_table_id=' + price_table_id +'&product_guid='+product_guid;
             var url="{{url('set-product-guid')}}";
             $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({

                        type: "POST",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                            $("#brand_btn").text('Processing...');
                        },
                        success: function (msg) {
                            alert(msg.message);
                            $("#brand_btn").text('Save');
                            $("#brand_name").val('');
                           // categoryChange();

                            
                        }
                        
                    });
            }
			function getProductGUID()
          {
            price_table_id = $("#price").val().trim();
           
             var dataString = 'price_table_id=' + price_table_id;
             var url="{{url('get-product-guid')}}";
             $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({

                        type: "get",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                            $("#brand_btn").text('Processing...');
                        },
                        success: function (msg) {
                
                            $("#product_guid").val(msg.product_guid);
                           // categoryChange();

                            
                        }
                        
                    });
            }
            function savePrice()
          {
            price = $("#brand_price").val();
            var price_pattern = /^[0-9.]+$/;
            if(price == '')
            {
                alert("Please Enter Price");
                return false;
            }
            else if(!price.match(price_pattern))
            {
                alert('Please Enter valid Price');
                $('#price').focus();
                return false;
            }
            category_id = $("#category").val();
            brand_id = $("#brand").val();
             var dataString = 'brand_id=' + brand_id +'&category_id='+category_id +'&price='+price ;
             var url="{{url('set-voucher-price')}}";
             $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({

                        type: "POST",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                            $("#price_btn").text('Processing...');
                        },
                        success: function (msg) {
                            alert(msg.message);
                            $("#price_btn").text('Save');
                            $("#price").val('');
                            BrandChange();
                        }
                        
                    });
            }
            function categoryChange()
            {
                category_id = $("#category").val();
               if(category_id == '')
               {
                    $("#main_brand_div").hide();
                    $("#main_price_div").hide();
                    return false;
               }
                var url="{{url('get-voucher-brand')}}";
                var dataString = 'category_id=' + category_id ;
                $.ajax({

                        type: "get",
                        url: url,
                        data: dataString,
                        dataType:"json",
                        success: function (msg) {
                            $("#main_brand_div").show();
                            $("#main_price_div").hide();
                            // if(msg.status == 1)
                            // {
                                var combo = $("<select class = 'form-control' id = 'brand' onChange='BrandChange()')></select>");
                                combo.append("<option selected = selected value=''>-- Select Voucher Brand --</option>");
                                $.each(msg.brands, function (i, el) {
                                combo.append("<option value="+ i +" >" + el + "</option>");
                                });
                                $("#brand_select_div").html(combo);
                        //     }
                        //    else
                        //     alert(msg.message)
                        }
                        
                    });
            }
            function BrandChange()
            {
                brand_id = $("#brand").val();
                if(brand_id == '')
               {
                    $("#main_price_div").hide();
                    return false;
               }
                var url="{{url('get-voucher-price')}}";
                var dataString = 'brand_id=' + brand_id ;
                $.ajax({

                        type: "get",
                        url: url,
                        data: dataString,
                        dataType:"json",
                        success: function (msg) {
                            $("#main_price_div").show();
                            // if(msg.status == 1)
                            // {
                                var combo = $("<select class = 'form-control' id ='price' onChange='getProductGUID()'></select>");
                                combo.append("<option selected = selected value=''>-- Select Price --</option>");
                                $.each(msg.brandsprices, function (i, el) {
                                combo.append("<option value="+ i +" >" + el + "</option>");
                                });
                                $("#price_select_div").html(combo);
                            // }
                          
                            //  else
                            // alert(msg.message)
                        }
                        
                    });
            }
            function refreshCatgory()
            {

            }
          
            </script>



        <!-- Page-Title -->
       
        

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
				
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
                                
                                    <div class="form-group col-md-3">
                                        {{ Form::select('category', $categories, old('category'), array('class' => 'form-control','id' => 'category','placeholder'=>'-- Select Voucher Category--','onChange'=>'categoryChange()')) }}
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button onClick="addCategory()" class="btn btn-primary">Add Category</button>
                                    
                                    </div>
                                    <div id="category_div" style="display:none">
                                        <div class="form-group col-md-4">
                                            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Voucher Category" value="">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button onClick="saveCategory()" class="btn btn-success" id="category_btn">Save</button>
                                        
                                        </div>
                                    </div>
							</div>
						
                        <div class="col-md-12" id="main_brand_div" style="display:none">
                               
                                    <div class="form-group col-md-3" id="brand_select_div">
                                        
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button onClick="addBrand()" class="btn btn-primary">Add Brand</button>
                                    
                                    </div>
									
                                    <div id="brand_div" style="display:none">
                                        <div class="form-group col-md-3">
                                            <input type="text" class="form-control" id="brand_name" name="brand_name" placeholder="Enter Voucher Brand" value="">
                                        </div>
                                        <div class="form-group col-md-1">
                                            <button onClick="saveBrand()" class="btn btn-success" id="brand_btn">Save</button>
                                        
                                        </div>
                                    </div>
									
                                    
                                </div>
                       
						<div class="col-md-12" id="main_price_div" style="display:none">
                               
                                    <div class="form-group col-md-3" id="price_select_div">
                                       
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button onClick="addPrice()" class="btn btn-primary">Add Price</button>
                                    
                                    </div>
                                    <div id="price_div" style="display:none">
                                        <div class="form-group col-md-3">
                                            <input type="text" class="form-control" id="brand_price" name="brand_price" placeholder="Enter Brand Price" value="">
                                        </div>
                                        <div class="form-group col-md-1">
                                            <button onClick="savePrice()" class="btn btn-success" id="price_btn">Save</button>
                                        
                                        </div>
                                    </div>
									<div class="form-group col-md-2">
                                        <button onClick="addProductGUID()" class="btn btn-primary">Add Prod GUID</button>
                                    
                                    </div>
									<div id="product_guid_div" style="display:none">
                                        <div class="form-group col-md-4">
                                            <input type="text" class="form-control" id="product_guid" name="product_guid" placeholder="Enter Prod GUID" value="">
                                        </div>
                                        <div class="form-group col-md-1">
                                            <button onClick="saveProductGUID()" class="btn btn-success" id="brand_btn">Save</button>
                                        
                                    </div>
									</div>
									
                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</div>
        <meta name="_token" content="{!! csrf_token() !!}"/>
    @endsection
    @endif