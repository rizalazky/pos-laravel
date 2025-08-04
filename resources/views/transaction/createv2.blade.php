<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        @if($type == 'out')   
            {{ __('TRANSAKSI PENJUALAN') }}
        @else
            {{ __('TRANSAKSI PEMBELIAN') }}
        @endif
    </x-slot>

    <input type="hidden" name="type" id="type" value="{{ $type }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <label for="date">Tanggal</label>
                                <input type="date" name="date" value="{{ date('Y-m-d')}}" id="date" class="form-control">
                            </div>
                            <div class="col-4">
                            @if($type == 'out')   
                                <label for="date">Customer</label>
                                <select name="" class="form-control" id="customer_id"></select> 
                            @else
                                <label for="date">Supplier</label>
                                <select name="" class="form-control" id="supplier_id"></select> 
                            @endif
                            </div>
                            <div class="col-4">
                                <label for="date">NO TRANSAKSI</label>
                                <input type="transaction_number" value="AUTO GENERATED" name="transaction_number" id="transaction_number" disabled class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                            <div class="col-6">
                                <label for="barcode-input">SCAN BARCODE</label>
                                <input type="text" name="barcode-input" placeholder="Scan Barcode" id="barcode-input" class="form-control">
                            </div>
                            
                            <div class="col-6">
                                <label for="product_id">CARI PRODUK</label>
                                <select id="product_id" name="product_id" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="d-flex p-2 overflow-x-auto mt-4">
                            <button class='btn btn-outline-info btn-sm btn-category mr-2' data-id='all'>All</button>
                            @foreach($product_categories as $category)
                                <button class='btn btn-outline-info btn-sm btn-category mr-2 text-nowrap' data-id='{{ $category->id }}'>{{ $category->name }}</button>
                            @endforeach
                            
                        </div>
                        <div class="row mt-4">
                            @foreach($products as $product)
                                <div class="col-3 item-product" 
                                    data-catid='{{ $product->category_id }}' 
                                    data-id='{{ $product->id }}' 
                                    data-name='{{ $product->name }}' 
                                    data-code='{{ $product->code }}' 
                                    data-stock='{{ $product->code }}' 
                                    data-priceid = '{{ $product->defaultProductPrice->id ?? 0 }}'
                                    data-price='{{ $type == "out" ? $product->defaultProductPrice->sell_price : $product->defaultProductPrice->buy_price }}'
                                    data-unit ='{{ $product->defaultProductPrice->productunit->name }}'
                                    data-productprices={{ json_encode($product->productprices) }}
                                    >
                                    <div class="card">
                                        <div class="card-body">
                                            <img src="{{ $product->image ? asset('storage/uploads/products/images/'.$product->image) : asset('images/no-image.png') }}" class="img-fluid" alt="{{ $product->name }}">
                                            <div class="text-left">
                                                <strong>{{ $product->code }}</strong>
                                                <br>
                                                <span class="text-muted">{{ $product->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
            </div>
            <div class="col-4 ">
                <div class="card h-100">
                    <div class="card-header flex justify-end">
                        <button disabled id='reset-cart' class='btn btn-sm btn-danger'>Reset</button>
                    </div>
                    <div class="card-body h-100 overflow-y-auto" id='cart'>
                        
                    </div>
                    <div class="card-footer">
                        <button class='btn btn-primary w-full' id='btn-checkout' disabled data-bs-toggle="modal" data-bs-target="#exampleModal">Checkout : <span><strong id='subTotal'>0</strong></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="sub_total" class="form-label">Sub Total</label>
                                <input type="text" name="sub_total_display" id="sub_total_display" disabled class="form-control currency-format">
                                <input type="hidden" name="sub_total" id="sub_total" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Diskon</label>
                                <input type="text" name="discount_display" value="0" id="discount_display" class="form-control currency-format">
                                <input type="hidden" name="discount" value="0" id="discount" class="form-control ">
                            </div>
                            
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="total" class="form-label">TOTAL</label>
                                <input type="text" name="total_display" id="total_display" disabled class="form-control currency-format">
                                <input type="hidden" name="total" id="total" disabled class="form-control ">
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="cash_paid" class="form-label">CASH</label>
                                <input type="text" name="cash_paid_display" id="cash_paid_display" class="form-control currency-format">
                                <input type="hidden" name="cash_paid" id="cash_paid" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="change" class="form-label">Kembali</label>
                                <input type="text" name="change_display" id="change_display" disabled class="form-control currency-format">
                                <input type="hidden" name="change" id="change" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">Catatan</label>
                               <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="btn-submit" class="btn btn-primary">Submit</button>
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

                    console.log('btnCategories', btnCategories)

                    for (let i = 0; i < btnCategories.length; i++) {
                        const btn = btnCategories[i];
                        btn.addEventListener('click',function(){
                            let id = this.dataset.id;
                            console.log('id', id)
                            for (let i = 0; i < items.length; i++) {
                                items[i].classList.remove('d-none');
                                if(id != 'all'){
                                    if(items[i].dataset.catid != id){
                                        items[i].classList.add('d-none');
                                    }
                                }
                            }
                        })
                    }


                    let productList = [];

                    btnResetCart.addEventListener('click',function(){
                        let confirm = window.confirm('Are you sure want to delete the cart items');
                        if(!confirm){
                            return false;
                        }
                        productList = [];
                        generateCart();
                    });


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
                                            ${price.productunit.name} - ${formatRupiah(price.sell_price)}
                                        </a>
                                    </li>`;
                                });
                            }
                            const productRow = `<div class="p-2 mb-2 shadow-sm">
                                <div class="d-flex relative justify-content-between align-items-center flex-wrap">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 fw-bold text-dark">${product.name}</h6>
                                        <div class="text-muted mb-2 flex items-center">
                                            ${formatRupiah(product.price)} / ${product.unit} 
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
                                        <div class="font-bold text-success mb-2 text-lg text-nowrap">${formatRupiah(product.total)}</div>
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
                    
                    

                    const setTransactionTotal = ()=>{
                        totalEl.value = (Number(subTotalModalEl.value) || 0) - (Number(discountEl.value) || 0);
                        let total = $('#total').val();
                        let cash_paid = $('#cash_paid').val();
                        
                        $('#total_display').val(formatRupiah(total));
                        $('#change').val(cash_paid - total);
                    }

                    const setTransactionSubTotal = ()=>{
                        let subTotal = productList.reduce((accumulator,currentValue) => Number(accumulator) + Number(currentValue.total), 0);

                        console.log(subTotal)
                        subTotalEl.innerText = formatRupiah(subTotal);
                        subTotalModalEl.value = subTotal;
                        $('#sub_total_display').val(formatRupiah(subTotal))

                        setTransactionTotal();
                    }

                    discountEl.addEventListener('input',()=>{
                        setTransactionSubTotal();
                    });

                    $('#discount_display').on('input',()=>{
                        discountEl.dispatchEvent(new Event('input'));
                    })
                    $('#cash_paid_display').on('input',()=>{
                        document.getElementById('cash_paid').dispatchEvent(new Event('input'));
                    })


                    $('#cash_paid, #total').on('input change',function(){
                        let total = $('#total').val();
                        let cash_paid = $('#cash_paid').val();

                        $('#change').val(cash_paid - total);
                        $('#change_display').val(formatRupiah(cash_paid - total));
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
                        let url = '/transaksi/save';

                        $.ajax({
                            url: url,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: 'application/json',
                            data: JSON.stringify(data),
                            success: function(response) {
                                console.log('result', response);
                                window.location.href = `/transaksi/detail/${response.transaction.id}`;
                                // if(!response.status){
                                //     alert(resp)
                                // }
                                // alert('Transaction saved successfully!');
                                // printReceipt(response.transaction.id);
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
                        
                        fetch(`/transaksi/pdf/preview/${transactionId}`)
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

                        // console.log('DATA', data);
                        // return false;


                        saveTransaction(data)
                    })
                });


        </script>
    @endpush
</x-app-layout>
