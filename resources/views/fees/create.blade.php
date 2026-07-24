@extends('layouts.master')

@section('title', 'Add Fee Payment - Student Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold">Add Fee Payment</h2>
    <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 1rem;">
    <div class="row g-0">
        <!-- Manual Entry Section -->
        <div class="col-md-7 p-4 p-md-5 bg-white">
            <h5 class="fw-bold text-primary mb-4"><i class="bi bi-cash-stack me-2"></i>Manual Entry</h5>
                <form action="{{ route('fees.store') }}" method="POST">
                    @csrf

                    {{-- Student --}}
                    <div class="mb-3">
                        <label for="student_id" class="form-label fw-bold">Student <span class="text-danger">*</span></label>
                        <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    data-total-fee="{{ $student->total_fee }}"
                                    data-balance="{{ $student->balance }}"
                                    {{ (old('student_id') ?? request('student_id')) == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->course }}) - Bal: KES {{ number_format(max(0, $student->balance), 0) }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Term --}}
                    <div class="mb-3">
                        <label for="term" class="form-label fw-bold">Term <span class="text-danger">*</span></label>
                        <select name="term" id="term" class="form-select @error('term') is-invalid @enderror" required>
                            <option value="">Select Term</option>
                            <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                            <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                            <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Term Fee (expected amount) --}}
                    <div class="mb-3">
                        <label for="term_fee" class="form-label fw-bold">
                            Term Fee Due (KES)
                            <span class="text-muted fw-normal small">— how much is owed for this term</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="term_fee" id="term_fee"
                               class="form-control @error('term_fee') is-invalid @enderror"
                               value="{{ old('term_fee', 0) }}">
                        @error('term_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Amount Paid Now (KES) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                        <div id="balance_help" class="form-text text-primary mt-1" style="display: none; font-size: 0.85rem;"></div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select Method</option>
                            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Mpesa" {{ old('payment_method') == 'Mpesa' ? 'selected' : '' }}>Mpesa</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Receipt Number --}}
                    <div class="mb-3">
                        <label for="receipt_no" class="form-label fw-bold">Receipt Number / Transaction ID</label>
                        <input type="text" name="receipt_no" id="receipt_no" class="form-control @error('receipt_no') is-invalid @enderror" value="{{ old('receipt_no') }}">
                        @error('receipt_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Date --}}
                    <div class="mb-4">
                        <label for="payment_date" class="form-label fw-bold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Payment
                        </button>
                    </div>
                </form>
        </div>

        <!-- M-Pesa STK Push Section -->
        <div class="col-md-5 p-4 p-md-5 bg-light border-start">
            <h5 class="fw-bold text-success mb-2"><i class="bi bi-phone me-2"></i>M-Pesa STK Push</h5>
            <p class="text-muted small mb-4">Prompt the user's phone to enter M-Pesa PIN directly.</p>
                <form action="{{ route('mpesa.stkpush') }}" method="POST">
                    @csrf

                    {{-- Student Search --}}
                    <div class="mb-3 position-relative">
                        <label for="stk_student_search" class="form-label fw-bold">Search Student <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="stk_student_search" class="form-control @error('student_id') is-invalid @enderror" placeholder="Type student name..." autocomplete="off">
                        </div>
                        <input type="hidden" name="student_id" id="stk_student_id">
                        @error('student_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        {{-- Search Results Dropdown --}}
                        <div id="stk_search_results" class="list-group position-absolute w-100 shadow" style="display: none; z-index: 1050; max-height: 250px; overflow-y: auto; top: 100%;">
                            @foreach($students as $student)
                                <button type="button" class="list-group-item list-group-item-action stk-student-item" data-id="{{ $student->id }}" data-name="{{ $student->name }}">
                                    <div class="fw-semibold">{{ $student->name }}</div>
                                    <small class="text-muted">{{ $student->course ?? 'No Course' }}</small>
                                </button>
                            @endforeach
                            <div id="stk_no_results" class="list-group-item text-muted text-center" style="display: none;">
                                No students found
                            </div>
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">M-Pesa Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="e.g. 254712345678" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Term --}}
                    <div class="mb-3">
                        <label for="stk_term" class="form-label fw-bold">Term <span class="text-danger">*</span></label>
                        <select name="term" id="stk_term" class="form-select @error('term') is-invalid @enderror" required>
                            <option value="">Select Term</option>
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                            <option value="Term 3">Term 3</option>
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-4">
                        <label for="stk_amount" class="form-label fw-bold">Amount (KES) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1" name="amount" id="stk_amount" class="form-control @error('amount') is-invalid @enderror" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send me-1"></i> Send STK Push
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for live notifications --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_id');
        const termFeeInput  = document.getElementById('term_fee');

        studentSelect.addEventListener('change', function() {
            const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
            const totalFee    = parseFloat(selectedOpt.getAttribute('data-total-fee') || 0);
            const balance     = parseFloat(selectedOpt.getAttribute('data-balance') || 0);

            if (balance > 0) {
                termFeeInput.value = balance.toFixed(2);
            } else {
                termFeeInput.value = '0.00';
            }
            
            const amountInput = document.getElementById('amount');
            const balanceHelp = document.getElementById('balance_help');

            if (selectedOpt.value) {
                if (balance <= 0) {
                    amountInput.max = 0;
                    amountInput.disabled = true;
                    balanceHelp.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> This student has completely cleared their fee balance!';
                    balanceHelp.className = 'form-text mt-1 text-success fw-bold';
                } else {
                    amountInput.max = balance;
                    amountInput.disabled = false;
                    balanceHelp.innerHTML = `<i class="bi bi-info-circle-fill"></i> Maximum payable amount is <strong>KES ${balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>`;
                    balanceHelp.className = 'form-text mt-1 text-primary';
                }
                balanceHelp.style.display = 'block';
            } else {
                amountInput.removeAttribute('max');
                amountInput.disabled = false;
                balanceHelp.style.display = 'none';
            }
        });
        
        // Trigger change on load if an option is pre-selected
        if (studentSelect.value) {
            studentSelect.dispatchEvent(new Event('change'));
        }

        // M-Pesa STK Student Search Logic
        const stkSearchInput = document.getElementById('stk_student_search');
        const stkStudentId = document.getElementById('stk_student_id');
        const stkSearchResults = document.getElementById('stk_search_results');
        const stkStudentItems = document.querySelectorAll('.stk-student-item');
        const stkNoResults = document.getElementById('stk_no_results');

        if (stkSearchInput) {
            // Show all options when focused if empty
            stkSearchInput.addEventListener('focus', function() {
                if (this.value === '') {
                    stkSearchResults.style.display = 'block';
                    stkStudentItems.forEach(item => item.style.display = 'block');
                    stkNoResults.style.display = 'none';
                }
            });

            stkSearchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                let hasResults = false;
                
                stkSearchResults.style.display = 'block';

                stkStudentItems.forEach(item => {
                    const name = item.getAttribute('data-name').toLowerCase();
                    if (name.includes(filter)) {
                        item.style.display = 'block';
                        hasResults = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (!hasResults && filter.length > 0) {
                    stkNoResults.style.display = 'block';
                } else {
                    stkNoResults.style.display = 'none';
                }
                
                // Clear hidden ID if typing (forcing them to select again)
                stkStudentId.value = ''; 
            });

            // Handle selection
            stkStudentItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    stkSearchInput.value = this.getAttribute('data-name');
                    stkStudentId.value = this.getAttribute('data-id');
                    stkSearchResults.style.display = 'none';
                });
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!stkSearchInput.contains(e.target) && !stkSearchResults.contains(e.target)) {
                    stkSearchResults.style.display = 'none';
                }
            });
        }

        // M-Pesa Live Polling Logic
        @if(session('mpesa_checkout_id'))
            const checkoutId = @js(session('mpesa_checkout_id'));
            const studentName = @js(session('mpesa_student_name'));
            
            Swal.fire({
                title: 'Waiting for PIN...',
                text: `Please ask ${studentName} to check their phone and enter the M-Pesa PIN.`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const checkMpesaStatus = setInterval(() => {
                fetch(`/mpesa/status/${checkoutId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            clearInterval(checkMpesaStatus);
                            Swal.fire({
                                title: 'Payment Successful!',
                                text: `The fee for ${studentName} has been received and recorded.`,
                                icon: 'success',
                                confirmButtonText: 'Great!'
                            }).then(() => {
                                window.location.href = "{{ route('fees.index') }}?new_fee_id=" + (data.fee_id || '') + "#recent-payments";
                            });
                        } else if (data.status === 'failed') {
                            clearInterval(checkMpesaStatus);
                            Swal.fire({
                                title: 'Payment Failed',
                                text: `Payment for ${studentName} failed. Please ensure your phone is reachable and try again.`,
                                icon: 'error',
                                confirmButtonText: 'Try Again'
                            });
                        }
                        // if pending, do nothing, loop continues
                    })
                    .catch(err => {
                        console.error("Polling error:", err);
                    });
            }, 3000); // Check every 3 seconds
        @endif
    });
</script>
@endpush
