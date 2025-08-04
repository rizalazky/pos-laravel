<x-app-layout>
    <x-slot name="header">   
            {{ __('Manage Products') }}
    </x-slot>

    <div class="container-fluid">
        <form method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                {{ __('Add Product') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Kode Produk/Barcode</label>
                            <input type="text" class="form-control" disabled placeholder="AUTO GENERATED" id="code" name="code">
                            @error('code') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Kategori Produk</label>
                            <select name="category_id" class="form-control" id="role">
                                <option value="">-- Pilih Kategori Produk --</option>
                                @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="name" name="name">
                            @error('name') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Stok Awal</label>
                            <input type="number" class="form-control" id="stock" name="stock">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Satuan</label>
                            <select name="unit_id" class="form-control" id="">
                                <option value="">-- Pilih Satuan Produk --</option>
                                @foreach ($units as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Harga Beli</label>
                            <input type="text" class="form-control currency-format" id="buy_price_display" name="buy_price_display">
                            <input type="hidden" class="form-control" id="buy_price" name="buy_price">
                            @error('buy_price') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3 col-6">
                            <label for="name" class="form-label">Harga Jual</label>
                            <input type="text" class="form-control currency-format" id="sell_price_display" name="sell_price_display">
                            <input type="hidden" class="form-control" id="sell_price" name="sell_price">
                            @error('sell_price') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <input type="file" name="image" id="image" class="form-control-file mt-3">
                        @error('image') <p class="text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Description</label>
                        <textarea type="text" class="form-control" id="description" name="description"></textarea>
                    </div>
                    
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
