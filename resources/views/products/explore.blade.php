@extends('layouts.app', ['title' => 'Explore Products'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">Explore</h4>
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
            <form method="GET" action="/explore" class="custom-input-group mb-4">
                <input type="text" class="custom-input" name="search" value="{{ $search }}" placeholder="Search healthy snacks, drinks, or brands...">
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
            </form>

            <div class="section-header mt-0">
                <h5>Categories</h5>
            </div>

            <div class="category-grid">
                @foreach ($categories as $category)
                    <a
                        href="/explore?category={{ $category['key'] }}@if($search !== '')&search={{ urlencode($search) }}@endif"
                        class="category-item {{ $activeCategory === $category['key'] ? 'active' : '' }}"
                    >
                        <i class="bi {{ $category['icon'] }}"></i>
                        <span>{{ $category['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="section-header">
                <h5>{{ $activeCategory ? 'Filtered Products' : 'Trending Healthy Choices' }}</h5>
                @if ($activeCategory || $search !== '')
                    <a href="/explore" class="see-all">Reset</a>
                @endif
            </div>

            <div class="explore-card-grid" style="padding-bottom: 100px;">
                @forelse ($products as $product)
                    <div class="explore-product-card">
                        <a href="/products/{{ $product->id }}" class="explore-product-cover text-decoration-none">
                            <div class="explore-product-image-wrap">
                                <img src="{{ famshopProductImage($product->image_url) }}" alt="{{ $product->pr_name }}" class="explore-product-image">
                            </div>
                        </a>

                        <div class="explore-product-body">
                            <a href="/products/{{ $product->id }}" class="text-decoration-none explore-product-link">
                                <h6>{{ $product->pr_name }}</h6>
                                <p>{{ $product->brand ?: 'Healthy pick' }}</p>
                            </a>

                            <div class="explore-product-tags">
                                @foreach (array_slice($product->explore_categories ?? [], 0, 2) as $productCategory)
                                    @php
                                        $matchedCategory = collect($categories)->firstWhere('key', $productCategory);
                                    @endphp
                                    @if ($matchedCategory)
                                        <span class="explore-chip">
                                            <i class="bi {{ $matchedCategory['icon'] }}"></i>
                                            {{ $matchedCategory['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>

                            <div class="explore-product-footer">
                                <div class="explore-price-block">
                                    <span>Price</span>
                                    <strong>{{ number_format((float) $product->price, 2) }} SAR</strong>
                                </div>
                                <form method="POST" action="/cart">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button class="explore-cart-btn" type="submit" aria-label="Add {{ $product->pr_name }} to cart">
                                        <i class="bi bi-cart-plus-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alt-item">
                        <div class="alt-info">
                            <h6>No matching products</h6>
                            <p>Try another category or search term.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
