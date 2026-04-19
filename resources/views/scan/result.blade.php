@extends('layouts.app', ['title' => 'Scan Result'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/scan" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
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
                    <p class="text-muted small mb-2">{{ $product->brand ?: 'Unknown brand' }} &middot; {{ $member->name_member }}</p>
                    <div class="result-product-tags">
                        <span class="badge-soft">{{ $product->brand ?: 'Product' }}</span>
                        <span class="badge-soft">{{ $budgetResult['within_budget'] ? 'Within budget' : 'Budget alert' }}</span>
                    </div>
                </div>
            </div>

            <div class="status-alert {{ $matchStatus === 'safe' ? 'safe' : '' }}">
                <div class="status-icon"><i class="bi {{ $matchStatus === 'safe' ? 'bi-check' : 'bi-x' }}"></i></div>
                <span>
                    @if ($matchStatus === 'safe')
                        Safe for {{ $member->name_member }}
                    @else
                        {{ ucfirst(str_replace('_', ' ', $matchStatus)) }} for {{ $member->name_member }}
                    @endif
                </span>
            </div>

            <div class="info-grid">
                <div class="info-tile warning">
                    <label>Safety Alert</label>
                    <value>{{ $allergyResult['safe'] ? 'No allergens detected' : implode(', ', $allergyResult['triggered_allergens']) }}</value>
                </div>
                <div class="info-tile success">
                    <label>Budget</label>
                    <value>{{ $budgetResult['within_budget'] ? 'Within budget' : 'Over ' . $budgetResult['exceeded_period'] }}</value>
                </div>
            </div>

            <div class="section-header">
                <h5>Healthy Alternatives</h5>
            </div>
            <div class="alternatives-list">
                @forelse ($alternatives as $alternative)
                    <div class="alt-item">
                        <div class="alt-img-wrapper">
                            <img src="{{ famshopProductImage($alternative->image_url) }}" alt="{{ $alternative->pr_name }}">
                        </div>
                        <div class="alt-info">
                            <h6>{{ $alternative->pr_name }}</h6>
                            <p>{{ $alternative->brand ?: 'Alternative product' }}</p>
                        </div>
                        <div class="alt-price">{{ number_format((float) $alternative->price, 2) }}</div>
                        <form method="POST" action="/cart">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $alternative->id }}">
                            <button class="alt-check border-0" type="submit"><i class="bi bi-plus"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="alt-item">
                        <div class="alt-info">
                            <h6>No saved alternatives</h6>
                            <p>Add curated safer products later in the catalog.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="section-header">
                <h5>Similar Products</h5>
            </div>
            <div class="similar-products-scroll">
                @forelse ($similarProducts as $similarProduct)
                    <div class="similar-product-card">
                        <a href="/products/{{ $similarProduct->id }}" class="similar-product-link text-decoration-none">
                            <div class="similar-product-image-wrap">
                                <img src="{{ famshopProductImage($similarProduct->image_url) }}" alt="{{ $similarProduct->pr_name }}" class="similar-product-image">
                            </div>
                            <h6>{{ $similarProduct->pr_name }}</h6>
                            <p>{{ $similarProduct->brand ?: 'Product' }}</p>
                            <strong>{{ number_format((float) $similarProduct->price, 2) }} SAR</strong>
                        </a>
                        <form method="POST" action="/cart" class="mt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $similarProduct->id }}">
                            <button class="mini-btn success w-100" type="submit">Add to Cart</button>
                        </form>
                    </div>
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

@section('scripts')
    <script>
        function speakResult(message) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(message);
                utterance.lang = 'en-US';
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            }
        }
        speakResult(@json($matchStatus === 'safe' ? 'This product is safe for ' . $member->name_member : 'Warning: this product may not be suitable for ' . $member->name_member));
    </script>
@endsection
