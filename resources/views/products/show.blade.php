@extends('layouts.app', ['title' => $product->pr_name])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/explore" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <a href="/cart" class="text-decoration-none text-reset cart-link">
                <span class="nav-icon-wrap">
                    <i class="bi bi-cart3"></i>
                    @if (($globalCartCount ?? 0) > 0)
                        <span class="cart-badge">{{ $globalCartCount }}</span>
                    @endif
                </span>
            </a>
        </div>

        <div class="content-block">
            <div class="product-card result-product-card">
                <div class="product-header">
                    <div class="result-product-media text-center">
                        <div class="result-product-image-wrap">
                            <img src="{{ famshopProductImage($product->image_url) }}" alt="{{ $product->pr_name }}" class="product-image result-product-image">
                        </div>
                        <div class="result-product-price">{{ number_format((float) $product->price, 2) }} SAR</div>
                    </div>
                    <div class="barcode-area result-barcode-area">
                        <i class="bi bi-upc-scan" style="font-size:4rem;color:#272424ed;"></i>
                    </div>
                </div>
                <div class="mt-4 result-product-copy">
                    <h4 class="fw-bold mb-1">{{ $product->pr_name }}</h4>
                    <p class="text-muted small mb-2">{{ $product->brand ?: 'Unknown brand' }}</p>
                    <div class="result-product-tags">
                        @foreach ($categories as $category)
                            @if (in_array($category['key'], $product->explore_categories, true))
                                <span class="badge-soft"><i class="bi {{ $category['icon'] }}"></i>{{ $category['label'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="panel-card mb-3">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Product details</h5>
                </div>
                <div class="support-modal-block mb-0">
                    <span>Ingredients</span>
                    <p>{{ $product->raw_ingredients ?: 'No ingredients information available.' }}</p>
                </div>
            </div>

            <div class="panel-card mb-3">
                <form method="POST" action="/cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn-main" type="submit">Add to Cart</button>
                </form>
            </div>

            <div class="section-header">
                <h5>Similar Products</h5>
            </div>
            <div class="similar-products-scroll">
                @forelse ($similarProducts as $similarProduct)
                    <a href="/products/{{ $similarProduct->id }}" class="similar-product-card text-decoration-none">
                        <div class="similar-product-image-wrap">
                            <img src="{{ famshopProductImage($similarProduct->image_url) }}" alt="{{ $similarProduct->pr_name }}" class="similar-product-image">
                        </div>
                        <h6>{{ $similarProduct->pr_name }}</h6>
                        <p>{{ $similarProduct->brand ?: 'Product' }}</p>
                        <strong>{{ number_format((float) $similarProduct->price, 2) }} SAR</strong>
                    </a>
                @empty
                    <div class="similar-product-card similar-product-empty">
                        <h6>No similar products</h6>
                        <p>More related products will appear here as your catalog grows.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
