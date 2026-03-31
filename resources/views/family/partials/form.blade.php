@php
    $memberGender = old('gender', optional($member)->gender);
    $memberBudget = optional($member)->budget;
    $memberAllergyProfiles = optional($member)->allergyProfiles;
    $selectedAllergies = old('allergies', $memberAllergyProfiles ? $memberAllergyProfiles->pluck('allergy_type')->toArray() : []);
    $severityLevel = old('severity_level', optional($memberAllergyProfiles?->first())->severity_level ?? 'moderate');
@endphp

<div class="custom-input-group">
    <input class="custom-input" type="text" name="name_member" value="{{ old('name_member', optional($member)->name_member ?? '') }}" placeholder="Family member name">
</div>
<div class="row g-2">
    <div class="col-6">
        <input class="custom-input" type="number" name="age" value="{{ old('age', optional($member)->age ?? '') }}" placeholder="Age">
    </div>
    <div class="col-6">
        <select class="custom-select" name="gender">
            <option value="">Gender</option>
            <option value="female" {{ $memberGender === 'female' ? 'selected' : '' }}>Female</option>
            <option value="male" {{ $memberGender === 'male' ? 'selected' : '' }}>Male</option>
        </select>
    </div>
</div>
<div class="custom-input-group mt-3">
    <input class="custom-input" type="file" name="avatar" accept="image/*">
</div>
<div class="panel-card mt-3" style="box-shadow:none;">
    <h6 class="mb-3">Allergy profile</h6>
    <div class="row g-2">
        @foreach ($allergyOptions as $allergy)
            <div class="col-6">
                <label class="form-check">
                    <input class="form-check-input" type="checkbox" name="allergies[]" value="{{ $allergy }}" {{ in_array($allergy, $selectedAllergies) ? 'checked' : '' }}>
                    <span class="form-check-label text-capitalize">{{ $allergy }}</span>
                </label>
            </div>
        @endforeach
    </div>
</div>
<div class="custom-input-group mt-3">
    <select class="custom-select" name="severity_level">
        <option value="moderate" {{ $severityLevel === 'moderate' ? 'selected' : '' }}>Moderate severity</option>
        <option value="mild" {{ $severityLevel === 'mild' ? 'selected' : '' }}>Mild severity</option>
        <option value="severe" {{ $severityLevel === 'severe' ? 'selected' : '' }}>Severe severity</option>
    </select>
</div>
<div class="row g-2 mt-1">
    <div class="col-4">
        <input class="custom-input" type="number" step="0.01" name="daily_budget" value="{{ old('daily_budget', optional($memberBudget)->daily_budget ?? '') }}" placeholder="Daily">
    </div>
    <div class="col-4">
        <input class="custom-input" type="number" step="0.01" name="weekly_budget" value="{{ old('weekly_budget', optional($memberBudget)->weekly_budget ?? '') }}" placeholder="Weekly">
    </div>
    <div class="col-4">
        <input class="custom-input" type="number" step="0.01" name="monthly_budget" value="{{ old('monthly_budget', optional($memberBudget)->monthly_budget ?? '') }}" placeholder="Monthly">
    </div>
</div>
