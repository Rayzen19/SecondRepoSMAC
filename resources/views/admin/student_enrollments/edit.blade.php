@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-user-edit me-2"></i>
                    Edit Enrollment
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.student-enrollments.index') }}">Enrollments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-flex align-items-center"><i class="ti ti-arrow-back-up me-2"></i>Cancel</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.student-enrollments.update', $studentEnrollment) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select @error('student_id') is-invalid @enderror">
                            <option value="">-- Select Student --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" {{ old('student_id', $studentEnrollment->student_id) == $s->id ? 'selected' : '' }}>{{ $s->student_number }} — {{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror">
                            <option value="">-- Select Academic Year --</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $studentEnrollment->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->display_name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Strand (optional)</label>
                        <select name="strand_id" class="form-select @error('strand_id') is-invalid @enderror">
                            <option value="">-- Select Strand --</option>
                            @foreach($strands as $st)
                                <option value="{{ $st->id }}" {{ old('strand_id', $studentEnrollment->strand_id) == $st->id ? 'selected' : '' }}>{{ $st->code }} — {{ $st->name }}</option>
                            @endforeach
                        </select>
                        @error('strand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assigned Section</label>
                        <select name="academic_year_strand_section_id" class="form-select @error('academic_year_strand_section_id') is-invalid @enderror">
                            <option value="">-- Select AY-Strand-Section --</option>
                            @foreach($strandSections as $assn)
                                <option value="{{ $assn->id }}" {{ old('academic_year_strand_section_id', $studentEnrollment->academic_year_strand_section_id) == $assn->id ? 'selected' : '' }}>
                                    {{ $assn->academicYear->display_name }} — {{ $assn->strand?->code }} — {{ $assn->section?->grade }} {{ $assn->section?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_strand_section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['enrolled','dropped','completed'] as $st)
                                <option value="{{ $st }}" {{ old('status', $studentEnrollment->status) == $st ? 'selected' : '' }}>{{ ucwords($st) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary d-flex align-items-center"><i class="ti ti-device-floppy me-2"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const aySelect = document.querySelector('select[name="academic_year_id"]');
        const strandSelect = document.querySelector('select[name="strand_id"]');
        const sectionSelect = document.querySelector('select[name="academic_year_strand_section_id"]');

        function setStrandOptions(options) {
            const current = strandSelect.value;
            strandSelect.innerHTML = '<option value="">-- Select Strand --</option>';
            options.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = o.text;
                if (String(o.id) === String(current)) opt.selected = true;
                strandSelect.appendChild(opt);
            });
        }

        function setSectionOptions(options) {
            const selected = '{{ $studentEnrollment->academic_year_strand_section_id }}';
            sectionSelect.innerHTML = '<option value="">-- Select AY-Strand-Section --</option>';
            options.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = o.text;
                if (String(o.id) === String(selected)) opt.selected = true;
                sectionSelect.appendChild(opt);
            });
        }

        async function refreshSections() {
            const ay = aySelect.value;
            if (!ay) { setSectionOptions([]); return; }
            const strand = strandSelect.value || '';
            try {
                const url = new URL("{{ route('admin.student-enrollments.sections.options') }}", window.location.origin);
                url.searchParams.set('academic_year_id', ay);
                if (strand) url.searchParams.set('strand_id', strand);
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load sections');
                const json = await res.json();
                setSectionOptions(json.data || []);
            } catch (e) {
                console.error(e);
                setSectionOptions([]);
            }
        }

        async function refreshStrands() {
            const ay = aySelect.value;
            if (!ay) { setStrandOptions([]); return; }
            try {
                const url = new URL("{{ route('admin.student-enrollments.strands.options') }}", window.location.origin);
                url.searchParams.set('academic_year_id', ay);
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed to load strands');
                const json = await res.json();
                setStrandOptions(json.data || []);
            } catch (e) {
                console.error(e);
                setStrandOptions([]);
            }
        }

        aySelect && aySelect.addEventListener('change', async () => { await refreshStrands(); await refreshSections(); });
        strandSelect && strandSelect.addEventListener('change', refreshSections);

        // initial population for edit
        if (aySelect && aySelect.value) {
            (async () => { await refreshStrands(); await refreshSections(); })();
        }
    })();
</script>
@endpush
