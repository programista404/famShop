@extends('layouts.admin', ['title' => 'Admin Products'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Products</h4>
                <p class="muted-note mb-0">Manage catalog data, ingredients, and product content from one table.</p>
            </div>
            <button class="support-ticket-cta" type="button" data-bs-toggle="modal" data-bs-target="#createProductModal">Add Product</button>
        </div>

        <div class="panel-card admin-table-card">
            <div class="table-responsive">
                <table id="adminProductsTable" class="table admin-table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Halal</th>
                        <th>Ingredients</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <div class="admin-table-thumb">
                                    <img src="{{ famshopProductImage($product->image_url) }}" alt="{{ $product->pr_name }}">
                                </div>
                            </td>
                            <td>{{ $product->pr_name }}</td>
                            <td>{{ $product->brand ?: '-' }}</td>
                            <td>{{ $product->barcode }}</td>
                            <td>{{ number_format((float) $product->price, 2) }} SAR</td>
                            <td><span class="history-status-badge is-safe">{{ ucfirst($product->halal_status) }}</span></td>
                            <td>{{ $product->ingredients->pluck('name')->take(2)->implode(', ') ?: '-' }}</td>
                            <td>
                                <div class="support-row-actions">
                                    <button class="mini-btn" type="button" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">Edit</button>
                                    <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteProductModal{{ $product->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $('#adminProductsTable').DataTable({
            pageLength: 10,
            order: [[1, 'asc']]
        });
    </script>
@endsection

@push('modal')
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content support-modal">
                <div class="modal-body">
                    <div class="support-modal-head">
                        <h5>Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="/admin/products">
                        @csrf
                        @include('admin.partials.product-form', ['product' => null, 'ingredients' => $ingredients])
                        <div class="support-modal-actions">
                            <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-main" type="submit">Create Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($products as $product)
        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content support-modal">
                    <div class="modal-body">
                        <div class="support-modal-head">
                            <h5>Edit Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/admin/products/{{ $product->id }}">
                            @csrf
                            @method('PUT')
                            @include('admin.partials.product-form', ['product' => $product, 'ingredients' => $ingredients])
                            <div class="support-modal-actions">
                                <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-main" type="submit">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content support-modal support-modal-small">
                    <div class="modal-body text-center">
                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                        <h5>Delete product?</h5>
                        <p>{{ $product->pr_name }} will be removed from the catalog.</p>
                        <form method="POST" action="/admin/products/{{ $product->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="support-modal-actions">
                                <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
