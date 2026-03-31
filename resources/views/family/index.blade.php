@extends('layouts.app', ['title' => 'Family Members'])

@section('content')
    <div class="screen-shell screen-top family-screen">
        <div class="top-nav family-top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">Family Members</h4>
            <button type="button" class="family-add-button" onclick="openModal('addMemberModal')">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>

        <div class="content-block">
            <div class="custom-input-group mb-4">
                <input
                    type="text"
                    class="custom-input"
                    placeholder="Search family..."
                    onkeyup="filterFamilyMembers(this.value)"
                >
            </div>

            <div class="stack-list" id="memberList">
                @forelse ($members as $member)
                    <div class="family-profile-card family-design-card" data-member-name="{{ strtolower($member->name_member) }}">
                        <div class="family-profile-top mb-0">
                            <div class="family-avatar-wrap">
                                @if ($member->avatar)
                                    <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name_member }}" class="family-avatar">
                                @else
                                    <div class="family-avatar family-avatar-fallback">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="item-main">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h3 class="family-name">{{ $member->name_member }}</h3>
                                    @if ($activeMemberId == $member->id)
                                        <span class="family-tag">ACTIVE</span>
                                    @endif
                                </div>
                                <p class="muted-note">{{ ucfirst($member->gender ?: 'Unspecified') }} • {{ $member->age ?? 'No age' }} years old</p>
                            </div>
                        </div>

                        <div class="family-card-footer">
                            <div class="family-allergies compact">
                                @forelse ($member->allergyProfiles as $profile)
                                    <span class="allergy-chip severity-{{ $profile->severity_level }}">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        {{ ucfirst($profile->allergy_type) }}
                                    </span>
                                @empty
                                    <span class="allergy-chip severity-low">
                                        <i class="bi bi-check-circle-fill"></i>
                                        No allergies
                                    </span>
                                @endforelse
                            </div>

                            <div class="family-icon-actions">
                                <button type="button" class="family-icon-btn" onclick="openModal('viewMemberModal-{{ $member->id }}')">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button type="button" class="family-icon-btn success" onclick="openModal('budgetMemberModal-{{ $member->id }}')">
                                    <i class="bi bi-cash-stack"></i>
                                </button>
                                <button type="button" class="family-icon-btn" onclick="openModal('editMemberModal-{{ $member->id }}')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="family-icon-btn danger" onclick="openModal('deleteMemberModal-{{ $member->id }}')">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="family-profile-card">
                        <div class="item-main">
                            <div class="family-name">No family members yet</div>
                            <div class="muted-note">Create the first family profile with the add button.</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addMemberModal" class="family-modal-overlay">
        <div class="family-modal-container family-modal-sheet">
            <div class="family-modal-handle"></div>
            <div class="family-modal-header">
                <h2>New Profile</h2>
                <button type="button" class="family-close-btn" onclick="closeModal('addMemberModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="/family" enctype="multipart/form-data">
                @csrf
                @include('family.partials.form', ['member' => null, 'allergyOptions' => $allergyOptions])
                <button class="btn btn-main mt-4" type="submit">Create Profile</button>
            </form>
        </div>
    </div>

    @foreach ($members as $member)
        <div id="editMemberModal-{{ $member->id }}" class="family-modal-overlay">
            <div class="family-modal-container family-modal-sheet">
                <div class="family-modal-handle"></div>
                <div class="family-modal-header">
                    <h2>Edit Profile</h2>
                    <button type="button" class="family-close-btn" onclick="closeModal('editMemberModal-{{ $member->id }}')"><i class="bi bi-x-lg"></i></button>
                </div>
                <form method="POST" action="/family/{{ $member->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('family.partials.form', ['member' => $member, 'allergyOptions' => $allergyOptions])
                    <button class="btn btn-main mt-4" type="submit">Update Changes</button>
                </form>
            </div>
        </div>

        <div id="viewMemberModal-{{ $member->id }}" class="family-modal-overlay">
            <div class="family-modal-container family-modal-sheet">
                <div class="family-modal-handle"></div>
                <div class="text-center mb-4">
                    @if ($member->avatar)
                        <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name_member }}" class="family-view-avatar">
                    @else
                        <div class="family-view-avatar family-avatar-fallback mx-auto">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    @endif
                    <h2 class="family-view-name">{{ $member->name_member }}</h2>
                    <p class="muted-note">Family Member ID: #{{ $member->id }}</p>
                </div>

                <div class="family-view-grid">
                    <div class="family-view-stat">
                        <p>Status</p>
                        <strong>{{ $activeMemberId == $member->id ? 'Active Member' : 'Family Profile' }}</strong>
                    </div>
                    <div class="family-view-stat">
                        <p>Budget</p>
                        <strong>{{ number_format(optional($member->budget)->monthly_budget ?? 0, 2) }} SAR</strong>
                    </div>
                </div>

                <h4 class="family-view-section">Restricted Ingredients</h4>
                <div class="stack-list">
                    @forelse ($member->allergyProfiles as $profile)
                        <div class="family-view-allergy severity-{{ $profile->severity_level }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="family-view-allergy-icon">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <span>{{ ucfirst($profile->allergy_type) }}</span>
                            </div>
                            <span class="family-view-level">{{ strtoupper($profile->severity_level) }}</span>
                        </div>
                    @empty
                        <div class="family-view-allergy severity-low">
                            <div class="d-flex align-items-center gap-3">
                                <div class="family-view-allergy-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <span>No registered allergies</span>
                            </div>
                            <span class="family-view-level">SAFE</span>
                        </div>
                    @endforelse
                </div>

                <button type="button" class="btn btn-alt mt-4" onclick="closeModal('viewMemberModal-{{ $member->id }}')">Close Profile</button>
            </div>
        </div>

        <div id="deleteMemberModal-{{ $member->id }}" class="family-modal-overlay family-modal-center">
            <div class="family-modal-container family-delete-box">
                <div class="family-delete-icon">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h3>Delete Profile?</h3>
                <p>This action cannot be undone. All scan history for this member will be lost.</p>
                <div class="d-flex flex-column gap-2">
                    <form method="POST" action="/family/{{ $member->id }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger w-100" type="submit">Delete Permanently</button>
                    </form>
                    <button type="button" class="btn btn-alt" onclick="closeModal('deleteMemberModal-{{ $member->id }}')">Cancel</button>
                </div>
            </div>
        </div>

        <div id="budgetMemberModal-{{ $member->id }}" class="family-modal-overlay">
            <div class="family-modal-container family-modal-sheet">
                <div class="family-modal-handle"></div>
                <div class="family-modal-header">
                    <h2>Update Budget</h2>
                    <button type="button" class="family-close-btn" onclick="closeModal('budgetMemberModal-{{ $member->id }}')"><i class="bi bi-x-lg"></i></button>
                </div>

                <div class="family-view-grid mb-4">
                    <div class="family-view-stat">
                        <p>Daily Left</p>
                        <strong>{{ number_format((optional($member->budget)->daily_budget ?? 0) - (optional($member->budget)->daily_spent ?? 0), 2) }} SAR</strong>
                    </div>
                    <div class="family-view-stat">
                        <p>Monthly Left</p>
                        <strong>{{ number_format((optional($member->budget)->monthly_budget ?? 0) - (optional($member->budget)->monthly_spent ?? 0), 2) }} SAR</strong>
                    </div>
                </div>

                <form method="POST" action="/budget/{{ $member->id }}">
                    @csrf
                    @method('PUT')
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="daily_budget" value="{{ old('daily_budget', optional($member->budget)->daily_budget ?? 0) }}" placeholder="Daily budget">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="weekly_budget" value="{{ old('weekly_budget', optional($member->budget)->weekly_budget ?? 0) }}" placeholder="Weekly budget">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="monthly_budget" value="{{ old('monthly_budget', optional($member->budget)->monthly_budget ?? 0) }}" placeholder="Monthly budget">
                    </div>
                    <button class="btn btn-main" type="submit">Save Budget</button>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script>
        function filterFamilyMembers(value) {
            const query = (value || '').toLowerCase().trim();
            document.querySelectorAll('#memberList [data-member-name]').forEach((card) => {
                card.style.display = card.dataset.memberName.includes(query) ? '' : 'none';
            });
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.style.display = 'flex';
            requestAnimationFrame(() => modal.classList.add('active'));
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 250);
            document.body.style.overflow = '';
        }

        window.addEventListener('click', function (event) {
            if (event.target.classList.contains('family-modal-overlay')) {
                closeModal(event.target.id);
            }
        });
    </script>
@endsection
