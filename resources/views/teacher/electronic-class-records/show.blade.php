@extends('layouts.admin')

@section('title', 'Electronic Class Record - ' . $assignment->subject->name . ' Q' . $quarter)

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">{{ $assignment->subject->name }} - Quarter {{ $quarter }}</h4>
                    <p class="mb-1"><strong>Academic Year:</strong> {{ $assignment->academicYear->year }}</p>
                    <p class="mb-1"><strong>Strand:</strong> {{ $assignment->strand->name }}</p>
                    <p class="mb-0"><strong>Teacher:</strong> {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAssessmentModal">
                        <i class="fas fa-plus"></i> Add Assessment
                    </button>
                    <a href="{{ route('admin.electronic-class-records.export', ['assignment' => $assignment->id, 'quarter' => $quarter]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('admin.electronic-class-records.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Record Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                <table class="table table-bordered table-sm mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
                        <tr>
                            <th rowspan="3" class="align-middle text-center" style="min-width: 200px;">LEARNERS' NAMES</th>
                            <th colspan="{{ $writtenWorks->count() + 3 }}" class="text-center bg-info text-white">
                                WRITTEN WORK ({{ $assignment->written_works_percentage }}%)
                            </th>
                            <th colspan="{{ $performanceTasks->count() + 3 }}" class="text-center bg-warning">
                                PERFORMANCE TASKS ({{ $assignment->performance_tasks_percentage }}%)
                            </th>
                            <th colspan="{{ $quarterlyAssessments->count() + 3 }}" class="text-center bg-success text-white">
                                QUARTERLY ASSESSMENT ({{ $assignment->quarterly_assessment_percentage }}%)
                            </th>
                            <th rowspan="3" class="align-middle text-center bg-primary text-white" style="min-width: 80px;">
                                Initial Grade
                            </th>
                            <th rowspan="3" class="align-middle text-center bg-primary text-white" style="min-width: 80px;">
                                Quarterly Grade
                            </th>
                        </tr>
                        <tr>
                            <!-- Written Work Headers -->
                            @for($i = 1; $i <= $writtenWorks->count(); $i++)
                                <th class="text-center bg-info text-white" style="min-width: 50px;">{{ $i }}</th>
                            @endfor
                            <th class="text-center bg-info text-white" style="min-width: 60px;">Total</th>
                            <th class="text-center bg-info text-white" style="min-width: 60px;">PS</th>
                            <th class="text-center bg-info text-white" style="min-width: 60px;">WS</th>

                            <!-- Performance Task Headers -->
                            @for($i = 1; $i <= $performanceTasks->count(); $i++)
                                <th class="text-center bg-warning" style="min-width: 50px;">{{ $i }}</th>
                            @endfor
                            <th class="text-center bg-warning" style="min-width: 60px;">Total</th>
                            <th class="text-center bg-warning" style="min-width: 60px;">PS</th>
                            <th class="text-center bg-warning" style="min-width: 60px;">WS</th>

                            <!-- Quarterly Assessment Headers -->
                            @for($i = 1; $i <= $quarterlyAssessments->count(); $i++)
                                <th class="text-center bg-success text-white" style="min-width: 50px;">{{ $i }}</th>
                            @endfor
                            <th class="text-center bg-success text-white" style="min-width: 60px;">Total</th>
                            <th class="text-center bg-success text-white" style="min-width: 60px;">PS</th>
                            <th class="text-center bg-success text-white" style="min-width: 60px;">WS</th>
                        </tr>
                        <tr>
                            <!-- Written Work Max Scores -->
                            @foreach($writtenWorks as $ww)
                                <th class="text-center bg-info text-white" style="font-size: 0.75rem;">
                                    {{ $ww->max_score }}
                                </th>
                            @endforeach
                            <th class="text-center bg-info text-white">{{ $writtenWorks->sum('max_score') }}</th>
                            <th class="text-center bg-info text-white">100</th>
                            <th class="text-center bg-info text-white">{{ $assignment->written_works_percentage }}</th>

                            <!-- Performance Task Max Scores -->
                            @foreach($performanceTasks as $pt)
                                <th class="text-center bg-warning" style="font-size: 0.75rem;">
                                    {{ $pt->max_score }}
                                </th>
                            @endforeach
                            <th class="text-center bg-warning">{{ $performanceTasks->sum('max_score') }}</th>
                            <th class="text-center bg-warning">100</th>
                            <th class="text-center bg-warning">{{ $assignment->performance_tasks_percentage }}</th>

                            <!-- Quarterly Assessment Max Scores -->
                            @foreach($quarterlyAssessments as $qa)
                                <th class="text-center bg-success text-white" style="font-size: 0.75rem;">
                                    {{ $qa->max_score }}
                                </th>
                            @endforeach
                            <th class="text-center bg-success text-white">{{ $quarterlyAssessments->sum('max_score') }}</th>
                            <th class="text-center bg-success text-white">100</th>
                            <th class="text-center bg-success text-white">{{ $assignment->quarterly_assessment_percentage }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentGrades as $gradeData)
                        <tr>
                            <td class="px-2">
                                <strong>{{ $gradeData['student']->last_name }}, {{ $gradeData['student']->first_name }}</strong>
                            </td>

                            <!-- Written Work Scores -->
                            @foreach($gradeData['ww_scores'] as $index => $score)
                                <td class="text-center">
                                    <input type="number" 
                                           class="form-control form-control-sm text-center score-input" 
                                           style="width: 50px; padding: 2px; font-size: 0.8rem;"
                                           value="{{ $score }}"
                                           min="0"
                                           max="{{ $writtenWorks[$index]->max_score }}"
                                           step="0.01"
                                           data-student="{{ $gradeData['student']->id }}"
                                           data-record="{{ $writtenWorks[$index]->id }}">
                                </td>
                            @endforeach
                            <td class="text-center align-middle"><strong>{{ $gradeData['ww_total'] }}</strong></td>
                            <td class="text-center align-middle">{{ $gradeData['ww_ps'] }}</td>
                            <td class="text-center align-middle"><strong>{{ $gradeData['ww_ws'] }}</strong></td>

                            <!-- Performance Task Scores -->
                            @foreach($gradeData['pt_scores'] as $index => $score)
                                <td class="text-center">
                                    <input type="number" 
                                           class="form-control form-control-sm text-center score-input" 
                                           style="width: 50px; padding: 2px; font-size: 0.8rem;"
                                           value="{{ $score }}"
                                           min="0"
                                           max="{{ $performanceTasks[$index]->max_score }}"
                                           step="0.01"
                                           data-student="{{ $gradeData['student']->id }}"
                                           data-record="{{ $performanceTasks[$index]->id }}">
                                </td>
                            @endforeach
                            <td class="text-center align-middle"><strong>{{ $gradeData['pt_total'] }}</strong></td>
                            <td class="text-center align-middle">{{ $gradeData['pt_ps'] }}</td>
                            <td class="text-center align-middle"><strong>{{ $gradeData['pt_ws'] }}</strong></td>

                            <!-- Quarterly Assessment Scores -->
                            @foreach($gradeData['qa_scores'] as $index => $score)
                                <td class="text-center">
                                    <input type="number" 
                                           class="form-control form-control-sm text-center score-input" 
                                           style="width: 50px; padding: 2px; font-size: 0.8rem;"
                                           value="{{ $score }}"
                                           min="0"
                                           max="{{ $quarterlyAssessments[$index]->max_score }}"
                                           step="0.01"
                                           data-student="{{ $gradeData['student']->id }}"
                                           data-record="{{ $quarterlyAssessments[$index]->id }}">
                                </td>
                            @endforeach
                            <td class="text-center align-middle"><strong>{{ $gradeData['qa_total'] }}</strong></td>
                            <td class="text-center align-middle">{{ $gradeData['qa_ps'] }}</td>
                            <td class="text-center align-middle"><strong>{{ $gradeData['qa_ws'] }}</strong></td>

                            <!-- Final Grades -->
                            <td class="text-center align-middle bg-light">
                                <strong>{{ $gradeData['initial_grade'] }}</strong>
                            </td>
                            <td class="text-center align-middle bg-light">
                                <strong>{{ $gradeData['quarterly_grade'] }}</strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100" class="text-center text-muted py-4">
                                No students enrolled in this subject.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-primary" id="saveScoresBtn">
                <i class="fas fa-save"></i> Save All Scores
            </button>
            <span class="text-muted ms-3" id="saveStatus"></span>
        </div>
    </div>

    <!-- Assessment List -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Written Works</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($writtenWorks as $index => $ww)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $index + 1 }}.</strong> {{ $ww->name }}
                                <br>
                                <small class="text-muted">Max: {{ $ww->max_score }} | {{ $ww->date_given->format('M d, Y') }}</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">No written works yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">Performance Tasks</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($performanceTasks as $index => $pt)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $index + 1 }}.</strong> {{ $pt->name }}
                                <br>
                                <small class="text-muted">Max: {{ $pt->max_score }} | {{ $pt->date_given->format('M d, Y') }}</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">No performance tasks yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Quarterly Assessments</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($quarterlyAssessments as $index => $qa)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $index + 1 }}.</strong> {{ $qa->name }}
                                <br>
                                <small class="text-muted">Max: {{ $qa->max_score }} | {{ $qa->date_given->format('M d, Y') }}</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">No quarterly assessments yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Assessment Modal -->
<div class="modal fade" id="addAssessmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.electronic-class-records.store-record', $assignment->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Assessment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="quarter" value="{{ $quarter }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Assessment Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="Written Work">Written Work</option>
                            <option value="Performance Task">Performance Task</option>
                            <option value="Quarterly Assessment">Quarterly Assessment</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., Quiz 1, Project 1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Maximum Score</label>
                        <input type="number" name="max_score" class="form-control" required min="1" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Given</label>
                        <input type="date" name="date_given" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Assessment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('saveScoresBtn');
    const saveStatus = document.getElementById('saveStatus');
    const scoreInputs = document.querySelectorAll('.score-input');

    saveBtn.addEventListener('click', function() {
        const scores = [];
        
        scoreInputs.forEach(input => {
            if (input.value !== '') {
                scores.push({
                    student_id: input.dataset.student,
                    subject_record_id: input.dataset.record,
                    raw_score: input.value
                });
            }
        });

        saveStatus.textContent = 'Saving...';
        saveBtn.disabled = true;

        fetch('{{ route("admin.electronic-class-records.update-scores", ["assignment" => $assignment->id, "quarter" => $quarter]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ scores: scores })
        })
        .then(response => response.json())
        .then(data => {
            saveStatus.textContent = 'Saved successfully!';
            saveStatus.className = 'text-success ms-3';
            setTimeout(() => {
                location.reload();
            }, 1000);
        })
        .catch(error => {
            saveStatus.textContent = 'Error saving scores';
            saveStatus.className = 'text-danger ms-3';
            saveBtn.disabled = false;
        });
    });

    // Auto-save on blur
    scoreInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value !== '') {
                const data = {
                    scores: [{
                        student_id: this.dataset.student,
                        subject_record_id: this.dataset.record,
                        raw_score: this.value
                    }]
                };

                fetch('{{ route("admin.electronic-class-records.update-scores", ["assignment" => $assignment->id, "quarter" => $quarter]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
            }
        });
    });
});
</script>
@endpush
