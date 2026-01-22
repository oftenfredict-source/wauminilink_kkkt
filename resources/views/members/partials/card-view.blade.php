<!-- Card View for Members -->
<div class="row g-4">
    @php
        $permanentMembers = $members->where('membership_type', 'permanent');
        $temporaryMembers = $members->where('membership_type', 'temporary');
        $archivedMembers = $archivedMembers ?? collect();
    @endphp

    <!-- Permanent Members -->
    @if($permanentMembers->count() > 0)
        <div class="col-12">
            <h5 class="text-primary mb-3">
                <i class="fas fa-users me-2"></i>Permanent Members ({{ $permanentMembers->count() }})
            </h5>
        </div>
        @foreach($permanentMembers as $member)
            <div class="col-12 col-sm-6 col-lg-4 col-md-6 card-view-item" id="card-{{ $member->id }}">
                <div class="card h-100 border-0 shadow-lg member-card">
                    <!-- Member Header -->
                    <div class="card-header member-header" style="background: linear-gradient(90deg, #940000 0%, #667eea 50%, #764ba2 100%); border: none; padding: 0.75rem;">
                        <div class="text-center">
                            <div class="member-avatar mb-1">
                                @if($member->profile_picture)
                                    <img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); font-size: 1.2rem;">
                                        {{ substr($member->full_name ?? 'M', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <h6 class="mb-1 text-white fw-bold member-name" style="font-size: 0.95rem;">{{ $member->full_name ?? 'Unknown Member' }}</h6>
                            <span class="badge bg-success px-2 py-1 fw-semibold member-type" style="font-size: 0.7rem;">Permanent</span>
                        </div>
                    </div>
                    
                    <!-- Member Details -->
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="member-details">
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Member ID</div>
                                            <div class="detail-value">{{ $member->member_id ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Phone</div>
                                            <div class="detail-value">{{ $member->phone_number ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Email</div>
                                            <div class="detail-value">{{ $member->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-venus-mars"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Gender</div>
                                            <div class="detail-value">{{ ucfirst($member->gender ?? '—') }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Location</div>
                                            <div class="detail-value">{{ $member->region ?? '—' }}, {{ $member->district ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($member->children && $member->children->count() > 0)
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-child"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Children</div>
                                            <div class="detail-value">{{ $member->children->count() }} child(ren)</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer with Actions -->
                    <div class="card-footer member-footer">
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-outline-info btn-sm action-btn" onclick="window.viewDetails && window.viewDetails({{ $member->id }}) || console.error('viewDetails not available')" title="View Details">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            <button class="btn btn-outline-primary btn-sm action-btn" onclick="window.openEdit && window.openEdit({{ $member->id }}) || console.error('openEdit not available')" title="Edit Member">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            @if(auth()->user()->isAdmin())
                                <button class="btn btn-outline-success btn-sm action-btn" onclick="window.resetPassword && window.resetPassword({{ $member->id }}) || console.error('resetPassword not available')" title="Reset Password">
                                    <i class="fas fa-key me-1"></i>Reset
                                </button>
                            @endif
                            <button class="btn btn-outline-warning btn-sm action-btn" onclick="window.confirmDelete && window.confirmDelete({{ $member->id }}) || console.error('confirmDelete not available')" title="Archive Member">
                                <i class="fas fa-archive me-1"></i>Archive
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Temporary Members -->
    @if($temporaryMembers->count() > 0)
        <div class="col-12 mt-4">
            <h5 class="text-warning mb-3">
                <i class="fas fa-user-clock me-2"></i>Temporary Members ({{ $temporaryMembers->count() }})
            </h5>
        </div>
        @foreach($temporaryMembers as $member)
            <div class="col-12 col-sm-6 col-lg-4 col-md-6 card-view-item" id="card-{{ $member->id }}">
                <div class="card h-100 border-0 shadow-lg member-card">
                    <!-- Member Header -->
                    <div class="card-header member-header" style="background: linear-gradient(90deg, #940000 0%, #667eea 50%, #764ba2 100%); border: none; padding: 0.75rem;">
                        <div class="text-center">
                            <div class="member-avatar mb-1">
                                @if($member->profile_picture)
                                    <img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); font-size: 1.2rem;">
                                        {{ substr($member->full_name ?? 'M', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <h6 class="mb-1 text-white fw-bold member-name" style="font-size: 0.95rem;">{{ $member->full_name ?? 'Unknown Member' }}</h6>
                            <span class="badge bg-warning text-dark px-2 py-1 fw-semibold member-type" style="font-size: 0.7rem;">Temporary</span>
                        </div>
                    </div>
                    
                    <!-- Member Details -->
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="member-details">
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Member ID</div>
                                            <div class="detail-value">{{ $member->member_id ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Phone</div>
                                            <div class="detail-value">{{ $member->phone_number ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Guardian</div>
                                            <div class="detail-value">{{ $member->guardian_name ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-venus-mars"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Gender</div>
                                            <div class="detail-value">{{ ucfirst($member->gender ?? '—') }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Location</div>
                                            <div class="detail-value">{{ $member->region ?? '—' }}, {{ $member->district ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer with Actions -->
                    <div class="card-footer member-footer">
                        <div class="action-buttons-wrapper card-actions">
                            <button type="button" 
                                    class="action-btn action-btn-view" 
                                    onclick="if(window.viewDetails){window.viewDetails({{ $member->id }});}else{console.error('viewDetails not available');alert('View function not available. Please refresh the page.');}"
                                    title="View Details"
                                    data-member-id="{{ $member->id }}">
                                <i class="fas fa-eye"></i>
                                <span class="action-tooltip">View</span>
                            </button>
                            <button type="button" 
                                    class="action-btn action-btn-edit" 
                                    onclick="if(window.openEdit){window.openEdit({{ $member->id }});}else{console.error('openEdit not available');alert('Edit function not available. Please refresh the page.');}"
                                    title="Edit Member"
                                    data-member-id="{{ $member->id }}">
                                <i class="fas fa-edit"></i>
                                <span class="action-tooltip">Edit</span>
                            </button>
                            @if(auth()->user()->isAdmin())
                                <button type="button" 
                                        class="action-btn action-btn-reset" 
                                        onclick="if(window.resetPassword){window.resetPassword({{ $member->id }});}else{console.error('resetPassword not available');alert('Reset password function not available. Please refresh the page.');}"
                                        title="Reset Password"
                                        data-member-id="{{ $member->id }}">
                                    <i class="fas fa-key"></i>
                                    <span class="action-tooltip">Reset</span>
                                </button>
                            @endif
                            <button type="button" 
                                    class="action-btn action-btn-archive" 
                                    onclick="if(window.confirmDelete){window.confirmDelete({{ $member->id }});}else{console.error('confirmDelete not available');alert('Archive function not available. Please refresh the page.');}"
                                    title="Archive Member"
                                    data-member-id="{{ $member->id }}">
                                <i class="fas fa-archive"></i>
                                <span class="action-tooltip">Archive</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Archived Members -->
    @if($archivedMembers->count() > 0)
        <div class="col-12 mt-4">
            <h5 class="text-secondary mb-3">
                <i class="fas fa-archive me-2"></i>Archived Members ({{ $archivedMembers->count() }})
            </h5>
        </div>
        @foreach($archivedMembers as $member)
            @php $snap = $member->member_snapshot ?? []; @endphp
            <div class="col-12 col-sm-6 col-lg-4 col-md-6 card-view-item" id="card-{{ $member->member_id }}">
                <div class="card h-100 border-0 shadow-lg member-card archived-card">
                    <!-- Member Header -->
                    <div class="card-header member-header" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border: none; padding: 0.75rem;">
                        <div class="text-center">
                            <div class="member-avatar mb-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); font-size: 1.2rem;">
                                    {{ substr($snap['full_name'] ?? 'M', 0, 1) }}
                                </div>
                            </div>
                            <h6 class="mb-1 text-white fw-bold member-name" style="font-size: 0.95rem;">{{ $snap['full_name'] ?? 'Unknown Member' }}</h6>
                            <span class="badge bg-secondary px-2 py-1 fw-semibold member-type" style="font-size: 0.7rem;">Archived</span>
                        </div>
                    </div>
                    
                    <!-- Member Details -->
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="member-details">
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Member ID</div>
                                            <div class="detail-value">{{ $snap['member_id'] ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Phone</div>
                                            <div class="detail-value">{{ $snap['phone_number'] ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Archived Date</div>
                                            <div class="detail-value">{{ $member->deleted_at_actual ? \Carbon\Carbon::parse($member->deleted_at_actual)->format('d M Y') : '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="detail-label">Reason</div>
                                            <div class="detail-value">{{ $member->reason ?? 'Not specified' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer with Actions -->
                    <div class="card-footer member-footer">
                        <div class="action-buttons-wrapper card-actions">
                            <button type="button" 
                                    class="action-btn action-btn-view" 
                                    onclick="if(window.viewDetails){window.viewDetails({{ $member->member_id }});}else{console.error('viewDetails not available');alert('View function not available. Please refresh the page.');}"
                                    title="View Details"
                                    data-member-id="{{ $member->member_id }}">
                                <i class="fas fa-eye"></i>
                                <span class="action-tooltip">View</span>
                            </button>
                            <button type="button" 
                                    class="action-btn action-btn-restore" 
                                    onclick="if(window.restoreMember){window.restoreMember({{ $member->member_id }});}else{console.error('restoreMember not available');alert('Restore function not available. Please refresh the page.');}"
                                    title="Restore Member"
                                    data-member-id="{{ $member->member_id }}">
                                <i class="fas fa-undo"></i>
                                <span class="action-tooltip">Restore</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if($members->count() == 0 && $archivedMembers->count() == 0)
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No members found</h5>
                <p class="text-muted">Click "Add Member" to create your first member.</p>
            </div>
        </div>
    @endif
</div>

<style>
/* Member Card Styles */
.member-card {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e3e6f0;
    position: relative;
}

.member-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #940000 0%, #667eea 50%, #764ba2 100%);
    z-index: 1;
}

.member-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(148, 0, 0, 0.15), 0 8px 16px rgba(0,0,0,0.1) !important;
    border-color: #940000;
}

.archived-card::before {
    background: linear-gradient(90deg, #6c757d 0%, #495057 50%, #adb5bd 100%);
}

.archived-card:hover {
    border-color: #6c757d;
    box-shadow: 0 20px 40px rgba(108, 117, 125, 0.15), 0 8px 16px rgba(0,0,0,0.1) !important;
}

/* Member Header Styles */
.member-header {
    position: relative;
    border: none !important;
    padding: 0.75rem !important;
}

.member-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    line-height: 1.3;
    margin-bottom: 0.3rem;
}

.member-type {
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 12px;
    padding: 0.3rem 0.6rem !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Member Details Styles */
.member-details {
    background: #fafbfc;
    border-radius: 10px;
    padding: 1rem;
    margin: 0.75rem;
    border: 1px solid #e8ecf3;
}

.detail-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e8ecf3;
    transition: all 0.2s ease;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item:hover {
    background: rgba(31, 43, 108, 0.05);
    border-radius: 6px;
    padding: 0.5rem;
    margin: 0 -0.5rem;
}

.icon-wrapper {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 600;
    flex-shrink: 0;
}

.detail-item:nth-child(1) .icon-wrapper {
    background: linear-gradient(90deg, #940000, #667eea);
    color: white;
}

.detail-item:nth-child(2) .icon-wrapper {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.detail-item:nth-child(3) .icon-wrapper {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
}

.detail-item:nth-child(4) .icon-wrapper {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.detail-item:nth-child(5) .icon-wrapper {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: white;
}

.detail-item:nth-child(6) .icon-wrapper {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
}

.detail-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d3748;
    line-height: 1.3;
}

/* Card Footer Styles */
.member-footer {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: none !important;
    padding: 0.75rem 1rem !important;
    border-top: 1px solid #dee2e6;
}

/* Card Actions - Use same modern design as table */
.card-actions {
    justify-content: center;
    flex-wrap: wrap;
}

.card-actions .action-btn {
    flex: 0 0 auto;
}

/* View Toggle Styles */
.btn-group .btn.active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-group .btn:not(.active) {
    background-color: transparent;
    color: #6c757d;
}

.btn-group .btn:not(.active):hover {
    background-color: #e9ecef;
    border-color: #6c757d;
    color: #495057;
}
</style>
