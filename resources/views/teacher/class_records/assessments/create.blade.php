@extends('teacher.components.template')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Create Assessment</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
n                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.class-records.assessments.store', $assignment) }}" method="post">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select">
                            <option value="11" {{ ($grade_level == '11') ? 'selected' : '' }}>11</option>
                            <option value="12" {{ ($grade_level == '12') ? 'selected' : '' }}>12</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select">
                            <option value="first-semester" {{ ($term == 'first-semester') ? 'selected' : '' }}>Midterm</option>
                            <option value="semester-final" {{ ($term == 'semester-final') ? 'selected' : '' }}>Finals</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="written_work">Written Work</option>
                            <option value="performance_task">Performance Task</option>
                            <option value="quarterly_assessment">Quarterly Assessment</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Date Given</label>
                        <input type="date" name="date_given" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Max Score</label>
                        <input type="number" name="max_score" class="form-control" min="1" required>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
