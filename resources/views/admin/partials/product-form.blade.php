@php
    $selectedIngredientIds = $product ? $product->ingredients->pluck('id')->all() : [];
    $ingredientNotes = $product ? $product->ingredients->pluck('pivot.note', 'id')->all() : [];
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <input class="custom-input" type="text" name="pr_name" value="{{ old('pr_name', $product->pr_name ?? '') }}" placeholder="Product name">
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <input class="custom-input" type="text" name="brand" value="{{ old('brand', $product->brand ?? '') }}" placeholder="Brand">
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <input class="custom-input" type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" placeholder="Barcode">
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <input class="custom-input" type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" placeholder="Price">
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <input class="custom-input" type="url" name="image_url" value="{{ old('image_url', $product->image_url ?? '') }}" placeholder="Image URL">
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-input-group mb-0">
            <select class="custom-select" name="halal_status">
                <option value="unknown" {{ old('halal_status', $product->halal_status ?? 'unknown') === 'unknown' ? 'selected' : '' }}>Unknown</option>
                <option value="halal" {{ old('halal_status', $product->halal_status ?? '') === 'halal' ? 'selected' : '' }}>Halal</option>
                <option value="haram" {{ old('halal_status', $product->halal_status ?? '') === 'haram' ? 'selected' : '' }}>Haram</option>
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="custom-input-group mb-0">
            <textarea class="custom-textarea" name="raw_ingredients" placeholder="Ingredients">{{ old('raw_ingredients', $product->raw_ingredients ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="support-modal-block mt-3">
    <span>Linked Ingredients</span>
    <div class="admin-ingredient-grid">
        @foreach ($ingredients as $ingredient)
            @php
                $checked = in_array($ingredient->id, old('ingredient_ids', $selectedIngredientIds), true);
                $noteValue = old("ingredient_notes.{$ingredient->id}", $ingredientNotes[$ingredient->id] ?? '');
            @endphp
            <label class="admin-ingredient-card">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="ingredient_ids[]" value="{{ $ingredient->id }}" {{ $checked ? 'checked' : '' }}>
                    <span class="admin-ingredient-name">{{ $ingredient->name }}</span>
                </div>
                <input class="custom-input admin-ingredient-note" type="text" name="ingredient_notes[{{ $ingredient->id }}]" value="{{ $noteValue }}" placeholder="Optional note">
            </label>
        @endforeach
    </div>
</div>
