<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        @if($data->type == 'out')   
            {{ __('TRANSAKSI PENJUALAN') }}
        @else
            {{ __('TRANSAKSI PEMBELIAN') }}
        @endif
    </x-slot>

    <input type="hidden" name="type" id="type" value="{{ $data->type }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header flex justify-content-end">
                        <button type="button" id="btn-new" class="btn btn-default mr-2">
                            <a href="/transaksi/penjualan">Transaksi Baru</a>
                        </button>
                        @if(($data->type == 'in' && Auth::user()->can('transaction-menu transaction-in update')) || ($data->type == 'out' && Auth::user()->can('transaction-menu transaction-out update')) )
                            <button type="button" id="btn-update" class="btn btn-primary mr-2">
                                <a href="/transaksi/edit/{{ $data->id }}" class='text-white'>Update</a>
                            </button>
                        @endif
                        <button type="button" id="btn-print-receipt" class="btn btn-info">Cetak Struk</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <label for="date">Tanggal</label>
                                <input type="date" name="date" disabled value="{{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}" id="date" class="form-control">
                            </div>
                            <div class="col-4">
                             @if($data->type == 'out')   
                                <label for="date">Customer</label>
                                <select name="" class="form-control" disabled id="customer_id">
                                    @if($data->customer)
                                        <option value="{{ $data->customer_id }}"  selected>{{ $data->customer->name }}</option>
                                    @endif
                                </select> 
                            @else
                                <label for="date">Supplier</label>
                                <select name="" class="form-control" disabled id="supplier_id">
                                    @if($data->supplier)
                                        <option value="{{ $data->supplier_id }}"  selected>{{ $data->supplier->name }}</option>
                                    @endif
                                </select> 
                            @endif
                            </div>
                            <div class="col-4">
                                <label for="date">NO TRANSAKSI</label>
                                <input type="transaction_number" value="{{ $data->transaction_number }}" disabled name="transaction_number" id="transaction_number" disabled class="form-control">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="sub_total" class="form-label">Sub Total</label>
                                <input type="text" name="sub_total" value="{{ number_format($data->sub_total) }}" id="sub_total" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Diskon</label>
                                <input type="text" name="discount" disabled value="{{ number_format($data->discount) }}" id="discount" class="form-control">
                            </div>
                            
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="total" class="form-label">TOTAL</label>
                                <input type="text" name="total" disabled id="total" value="{{ number_format($data->total) }}" disabled class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="cash_paid" class="form-label">CASH</label>
                                <input type="text" name="cash_paid" disabled id="cash_paid" value="{{ number_format($data->cash_paid) }}" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="change" class="form-label">Kembali</label>
                                <input type="text" name="change" id="change" disabled value="{{ number_format($data->change) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">Catatan</label>
                               <textarea name="notes" id="notes" disabled class="form-control" rows="4">{{ $data->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                
                
                
            </div>
            <div class="col-12 ">
                <div class="card h-100">
                    <div class="card-header flex">
                        <div class="card-title">Items</div>
                    </div>
                    <div class="card-body h-100 overflow-y-auto" id='cart'>
                        <table class='table table-bordered'>
                            <thead>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Amount</th>
                            </thead>
                            <tbody>
                                @foreach($data->transaction_details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->qty }}</td>
                                        <td>{{ $detail->productprice->productunit->name }}</td>
                                        <td>{{ number_format($detail->price) }}</td>
                                        <td>{{ number_format($detail->qty * $detail->price ) }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    @push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    @endpush
    @push('js')
        
        <script defer>
            
            
            
            $(document).ready(function() {
                    const items = document.getElementsByClassName('item-product');
                    const cart = document.getElementById('cart');
                    const totalEl = document.getElementById('total');
                    const subTotalEl = document.getElementById('subTotal');
                    const subTotalModalEl = document.getElementById('sub_total');
                    const discountEl = document.getElementById('discount');
                    const btnResetCart = document.getElementById('reset-cart');
                    const btnCategories = document.getElementsByClassName('btn-category');
                    const btnCheckout = document.querySelector('#btn-checkout');
                    let productList = []


                    


                    

                    

                    const setTransactionTotal = ()=>{
                        totalEl.value = (Number(subTotalModalEl.value) || 0) - (Number(discountEl.value) || 0);
                        // trigger total change event
                        $('#total').trigger('change');
                    }

                    const setTransactionSubTotal = ()=>{
                        let subTotal = productList.reduce((accumulator,currentValue) => Number(accumulator) + Number(currentValue.total), 0);

                        console.log(subTotal)
                        subTotalEl.innerText = subTotal;
                        subTotalModalEl.value = subTotal;

                        setTransactionTotal();
                    }


                    function generateCart(){
                        cart.innerHTML = '';
                        let html ='';

                        $('#reset-cart').prop('disabled', false);
                        $('#btn-checkout').prop('disabled', false);
                        if(productList.length == 0){
                            $('#reset-cart').prop('disabled', true);
                            $('#btn-checkout').prop('disabled', true);
                        }

                        productList.forEach((product) => {
                            let productPrices = '';
                            if(product.productPrices && product.productPrices.length > 0){
                                product.productPrices.forEach((price) => {
                                    productPrices += `<li>
                                        <a class="dropdown-item select-product-price" 
                                            href="#" 
                                            data-price="${price.sell_price}" 
                                            data-unit="${price.productunit.name}" 
                                            data-code="${product.code}"
                                            data-priceid="${price.id}"
                                            >
                                            ${price.productunit.name} - Rp ${price.sell_price}
                                        </a>
                                    </li>`;
                                });
                            }
                            const productRow = `<div class="p-2 mb-2 shadow-sm">
                                <div class="d-flex relative justify-content-between align-items-center flex-wrap">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 fw-bold text-dark">${product.name}</h6>
                                        <div class="text-muted mb-2 flex items-center">
                                            Rp ${product.price} / ${product.unit} 
                                            <div class="ml-2 dropdown">
                                                <button class="btn btn-xs btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </button>
                                                <ul class="dropdown-menu">        
                                                    ${productPrices}
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-xs btn-outline-warning btn-min-qty" data-code="${product.code}">
                                                <i class="fas fa-minus text-xs btn-min-qty" data-code="${product.code}"></i>
                                            </button>
                                            <span class=" font-semibold">${product.qty}</span>
                                            <button class="btn btn-xs btn-outline-info btn-plus-qty" data-code="${product.code}">
                                                <i class="fas fa-plus text-xs btn-plus-qty" data-code="${product.code}"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end flex justify-content-end align-items-center">
                                        <div class="font-bold text-success mb-2 text-lg text-nowrap">Rp ${product.total}</div>
                                    </div>
                                    <button class="absolute bottom-0 right-0 btn btn-sm btn-outline-danger btn-delete-item-detail" data-code="${product.code}">
                                        <i class="fas fa-trash text-xs btn-delete-item-detail" data-code="${product.code}"></i>
                                    </button>
                                </div>
                            </div>
                            `;
                            html += productRow;
                        });
                        cart.innerHTML =  html;
                        setTransactionSubTotal();
                    }

                    function initPage(){
                        const transactionDetails = @json($data->transaction_details);
                        for (let i = 0; i < transactionDetails.length; i++) {
                            const data = transactionDetails[i];
                            console.log('data', data);
                            const product = {
                                id : data.product.id,
                                code: data.product.code,
                                name: data.product.name,
                                price: data.price,
                                qty: data.qty,
                                total : Number(data.price) * Number(data.qty),
                                stock : data.product.stock,
                                unit : data.productprice.productunit.name,
                                productPrices : data.product.productprices,
                                product_price_id : data.product_price_id,
                                discount : data.discount
                            };
                            console.log('Product clicked:', product);
                            pushProductList(product);
                        }
                        generateCart();
                    }

                    // initPage();


                    cart.addEventListener('click',(e)=>{
                        const target = e.target;
                        if(target.classList.contains('btn-delete-item-detail')){
                            let code = target.dataset.code;
                            let confirm = window.confirm('Are you sure want to delete the cart item');
                            if(!confirm){
                                return false;
                            }
                            console.log('DELEE', code)
                            deleteCartItem(code);
                        }

                        if(target.classList.contains('btn-plus-qty')){
                            let code = target.dataset.code;
                            updateQtyCartItem(code, true);
                        }
                        if(target.classList.contains('btn-min-qty')){
                            let code = target.dataset.code;
                            updateQtyCartItem(code, false);
                        }

                        if(target.classList.contains('select-product-price')){
                            let data = target.dataset;
                            
                            console.log('CODE', data);
                            updatePriceCartItem(data);
                        }
                    })


                    for (let i = 0; i < items.length; i++) {
                        items[i].addEventListener('click', function() {
                            const data = this.dataset;
                            console.log('data', data);
                            const productId = data.id;
                            const productCode = data.code;
                            const productPrice = data.price;
                            const productName = data.name;
                            const stock = data.stock;
                            const unit = data.unit;
                            const product_price_id = data.priceid;
                            const productPrices = JSON.parse(data.productprices);
                            
                            const product = {
                                id : productId,
                                code: productCode,
                                name: productName,
                                price: productPrice,
                                total : productPrice,
                                qty: 1,
                                stock : stock,
                                unit : unit,
                                productPrices : productPrices,
                                product_price_id : product_price_id,
                                discount : 0
                            };
                            console.log('Product clicked:', product);
                            pushProductList(product);
                            
                        });
                    }

                    const deleteCartItem = (code)=>{
                        
                        const index = productList.findIndex(item => item.code == code);

                        if (index !== -1) {
                            productList.splice(index, 1);
                        }

                        generateCart();
                    }

                    const updateQtyCartItem = (code, isPlus)=>{
                        const product = productList.find(item => item.code == code);
                        if(product ){
                            if(isPlus){
                                product.qty = Number(product.qty) + 1;
                            }else{
                                if( product.qty > 1){
                                    product.qty = Number(product.qty) - 1;
                                }
                            }
                            product.total = Number(product.qty) * Number(product.price);

                            generateCart();
                        }
                    }

                    const updatePriceCartItem = (data)=>{
                        const product = productList.find(item => item.code == data.code);
                        if(product){
                            product.price = data.price;
                            product.unit = data.unit;
                            product.total = Number(product.qty) * Number(product.price);
                            product.product_price_id = data.priceid;

                            generateCart();
                        }
                    }
                    
                    

                    

                    discountEl.addEventListener('input',()=>{
                        setTransactionSubTotal();
                    })


                    $('#cash_paid, #total').on('input',function(){
                        let total = $('#total').val();
                        let cash_paid = $('#cash_paid').val();

                        $('#change').val(cash_paid - total);
                    });

                    $('#total').change(function(){
                        console.log('TOTAL CHANGE');
                        let total = $('#total').val();
                        let cash_paid = $('#cash_paid').val();

                        $('#change').val(cash_paid - total);
                    })

                   

                    async function findProduct(barcode){
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: `/produk/search?term=${barcode}`,
                                type: 'GET',
                                success: function (response) {
                                    resolve(response); // Resolve the promise with the response
                                },
                                error: function (err) {
                                    console.error('An error occurred:', err);
                                    reject(err); // Reject the promise if an error occurs
                                }
                            });
                        });
                    }

                    $('#barcode-input').on('input', async function () {
                        const barcode = $(this).val().trim();
                        const type = $('#type').val();

                        if (barcode.length > 0) {
                            let products= await findProduct(barcode);
                            // console.log('products', products)
                            let product = {
                                id : products[0].id,
                                code: products[0].code,
                                name: products[0].name,
                                price: type == 'out' ? products[0].default_product_price.sell_price : products[0].default_product_price.buy_price,
                                total : type == 'out' ? products[0].default_product_price.sell_price : products[0].default_product_price.buy_price,
                                qty: 1,
                                stock : products[0].stock,
                                unit : products[0].default_product_price.productunit.name,
                                productPrices : products[0].productprices,
                                product_price_id : products[0].default_product_price.id,
                                discount : 0
                            }
                            console.log('product', product);
                            pushProductList(product);
                        }

                        $('#barcode-input').val('');
                    });

                    function pushProductList(product){
                         // let productprices = data.productprices;
                        let checkIfExist = productList.find(list =>{return list.code == product.code});
                        if(checkIfExist){
                            alert('Item sudah ditambahkan');
                            return false;
                        }
                        if(product.stock == 0){
                            alert('Stok Kosong');
                            return false;
                        }
                        productList.push(product)
                        generateCart()
                    }

                    $('#product_id').on('select2:select', function (e) {
                        var params = e.params.data;
                        let data = params.item;
                        let type = $('#type').val();
                        let product = {
                            id : data.id,
                            code:data.code,
                            name:data.name,
                            price: type == 'out' ? data.default_product_price.sell_price : data.default_product_price.buy_price,
                            total : type == 'out' ? data.default_product_price.sell_price : data.default_product_price.buy_price,
                            qty: 1,
                            stock :data.stock,
                            unit :data.default_product_price.productunit.name,
                            productPrices :data.productprices,
                            product_price_id : data.default_product_price.id,
                            discount : 0
                        }
                        // console.log('data', data);
                        pushProductList(product)
                       
                        $("#product_id").empty().trigger('change')
                    });

                    $('#product_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/produk/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.code} - ${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search for a product',
                        allowClear: true
                    });

                    $('#customer_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/customer/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            // item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search Customer',
                        allowClear: true
                    });

                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/supplier/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            // item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search Supplier',
                        allowClear: true
                    });

                    


                    

                    async function saveTransaction(data){
                        // let url = 'http://localhost:8000/transaksi/save';
                        let crsfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        let url = '/transaksi/edit/{{ $data->id }}';

                        $.ajax({
                            url: url,
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: 'application/json',
                            data: JSON.stringify(data),
                            success: function(response) {
                                console.log('result', response);
                                // if(!response.status){
                                //     alert(resp)
                                // }
                                alert('Transaction saved successfully!');
                                // reload the page
                                // window.location.reload()
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status === 302) {
                                    window.location.href = xhr.getResponseHeader('Location');
                                } else {
                                    console.log('Error:', error);
                                    console.log('Response:', xhr.responseJSON);
                                    alert(xhr.responseJSON.message);
                                }
                            }
                        });
                    }

                    const printReceipt = (transactionId) => {
                        
                        fetch(`/transaksi/pdf/preview/{{ $data->id }}`)
                            .then(res => res.blob())
                            .then(blob => {
                                // Create a URL for the PDF blob
                                const url = URL.createObjectURL(blob);
                                console.log('blob', url);

                                // Embed the PDF in an iframe
                                const iframe = document.createElement('iframe');
                                iframe.style.display = 'none'; // Hide the iframe (optional)
                                document.body.appendChild(iframe);
                                iframe.src = url;

                                // Trigger the print dialog
                                iframe.onload = () => {
                                    console.log('on load iframe');
                                    iframe.contentWindow.print();

                                    // Clean up by removing the iframe and revoking the URL
                                    // iframe.contentWindow.onafterprint = () => {
                                    //     document.body.removeChild(iframe);
                                    //     URL.revokeObjectURL(url);
                                    // };
                                };
                            })
                            .catch(error => console.error('Error fetching PDF:', error));
                    }

                    $('#btn-print-receipt').click(function(){
                        printReceipt();

                    })

                    $('#btn-submit').on('click',function(e){
                        e.preventDefault();
                        let data = {
                            date : $('#date').val(),
                            type : $('#type').val(),
                            customer_id : $('#customer_id').val(),
                            supplier_id : $('#supplier_id').val(),
                            items : productList,
                            sub_total : $('#sub_total').val(),
                            discount : $('#discount').val(),
                            total : $('#total').val(),
                            cash_paid : $('#cash_paid').val(),
                            change : $('#change').val(),
                            notes : $('#notes').val(),
                        }

                        // console.log(data);
                        // return false;


                        saveTransaction(data)
                    });

                    
                });


        </script>
    @endpush
</x-app-layout>
