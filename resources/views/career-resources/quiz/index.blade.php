@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('career.index') }}">Career Resources</a></li>
                    <li class="breadcrumb-item active">Career Quiz</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    {{-- Quiz Intro --}}
                    <div id="quiz-intro" class="card border-0 shadow-sm text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fa-solid fa-compass fa-4x text-primary"></i>
                            </div>
                            <h1 class="h2 mb-3">Find Your Ideal Job</h1>
                            <p class="lead text-muted mb-4">
                                Answer a few quick questions to discover your career personality type and get personalized job recommendations.
                            </p>
                            <div class="row mb-4">
                                <div class="col-4">
                                    <div class="text-primary mb-2"><i class="fa-solid fa-clock fa-2x"></i></div>
                                    <small class="text-muted">2-3 minutes</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-success mb-2"><i class="fa-solid fa-list fa-2x"></i></div>
                                    <small class="text-muted">{{ $questions->count() }} questions</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-warning mb-2"><i class="fa-solid fa-bullseye fa-2x"></i></div>
                                    <small class="text-muted">Personalized results</small>
                                </div>
                            </div>
                            <button id="start-quiz" class="btn btn-primary btn-lg px-5">
                                <i class="fa-solid fa-play me-2"></i> Start Quiz
                            </button>
                        </div>
                    </div>

                    {{-- Quiz Questions --}}
                    <div id="quiz-container" class="d-none">
                        {{-- Progress Bar --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span id="progress-text">Question 1 of {{ $questions->count() }}</span>
                                    <span id="progress-percent">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div id="progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Questions --}}
                        @foreach($questions as $index => $question)
                        <div class="question-card card border-0 shadow-sm mb-4 {{ $index > 0 ? 'd-none' : '' }}" data-question="{{ $index }}">
                            <div class="card-body p-4 p-lg-5">
                                <h3 class="h4 mb-4">{{ $question->question }}</h3>
                                <div class="options-list">
                                    @foreach($question->options as $optIndex => $option)
                                    <div class="option-item mb-3">
                                        <input type="radio" class="btn-check" name="q{{ $question->id }}" id="q{{ $question->id }}_{{ $optIndex }}"
                                               value="{{ $optIndex }}" data-question-id="{{ $question->id }}">
                                        <label class="btn btn-outline-primary w-100 text-start p-3" for="q{{ $question->id }}_{{ $optIndex }}">
                                            <span class="option-letter me-2">{{ chr(65 + $optIndex) }}.</span>
                                            {{ $option['text'] }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer bg-white d-flex justify-content-between">
                                @if($index > 0)
                                <button class="btn btn-outline-secondary prev-btn">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Previous
                                </button>
                                @else
                                <div></div>
                                @endif
                                @if($index < $questions->count() - 1)
                                <button class="btn btn-primary next-btn" disabled>
                                    Next <i class="fa-solid fa-arrow-right ms-1"></i>
                                </button>
                                @else
                                <button class="btn btn-success submit-btn" disabled>
                                    Get Results <i class="fa-solid fa-check ms-1"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Results --}}
                    <div id="quiz-results" class="d-none">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 p-lg-5 text-center">
                                <div class="result-icon mb-4">
                                    <i id="result-icon" class="fa-solid fa-star fa-4x text-warning"></i>
                                </div>
                                <h2 id="result-title" class="h3 mb-3">Your Career Type</h2>
                                <p id="result-description" class="text-muted mb-4">Loading...</p>

                                <div id="result-traits" class="mb-4">
                                    <!-- Traits will be inserted here -->
                                </div>

                                <div class="d-grid gap-3">
                                    <a id="result-jobs-link" href="#" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-briefcase me-2"></i> View Recommended Jobs
                                    </a>
                                    <button id="retake-quiz" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-redo me-2"></i> Retake Quiz
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Share Results --}}
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body text-center">
                                <h5>Share Your Results</h5>
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <a href="#" id="share-facebook" class="btn btn-outline-primary">
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                    <a href="#" id="share-twitter" class="btn btn-outline-info">
                                        <i class="fa-brands fa-twitter"></i>
                                    </a>
                                    <a href="#" id="share-linkedin" class="btn btn-outline-primary">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" id="share-whatsapp" class="btn btn-outline-success">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div id="quiz-loading" class="d-none">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-5 text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h4>Analyzing your responses...</h4>
                                <p class="text-muted">Finding your ideal career path</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<style>
.option-item .btn-check:checked + .btn {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
.option-letter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: #f8f9fa;
    border-radius: 50%;
    font-weight: bold;
}
.btn-check:checked + .btn .option-letter {
    background: rgba(255,255,255,0.2);
}
.result-trait {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #e9ecef;
    border-radius: 20px;
    margin: 0.25rem;
}
</style>
@endsection

@section('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const intro = document.getElementById('quiz-intro');
    const container = document.getElementById('quiz-container');
    const results = document.getElementById('quiz-results');
    const loading = document.getElementById('quiz-loading');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const progressPercent = document.getElementById('progress-percent');

    const questions = document.querySelectorAll('.question-card');
    const totalQuestions = questions.length;
    let currentQuestion = 0;
    let answers = {};

    // Start Quiz
    document.getElementById('start-quiz').addEventListener('click', function() {
        intro.classList.add('d-none');
        container.classList.remove('d-none');
        updateProgress();
    });

    // Option Selection
    document.querySelectorAll('input[type="radio"]').forEach(input => {
        input.addEventListener('change', function() {
            const questionId = this.dataset.questionId;
            answers[questionId] = parseInt(this.value);

            // Enable next/submit button
            const card = this.closest('.question-card');
            const nextBtn = card.querySelector('.next-btn');
            const submitBtn = card.querySelector('.submit-btn');
            if (nextBtn) nextBtn.disabled = false;
            if (submitBtn) submitBtn.disabled = false;
        });
    });

    // Next Button
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentQuestion < totalQuestions - 1) {
                questions[currentQuestion].classList.add('d-none');
                currentQuestion++;
                questions[currentQuestion].classList.remove('d-none');
                updateProgress();
            }
        });
    });

    // Previous Button
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentQuestion > 0) {
                questions[currentQuestion].classList.add('d-none');
                currentQuestion--;
                questions[currentQuestion].classList.remove('d-none');
                updateProgress();
            }
        });
    });

    // Submit Quiz
    document.querySelectorAll('.submit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            container.classList.add('d-none');
            loading.classList.remove('d-none');

            fetch('{{ route("career.quiz.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ answers: answers })
            })
            .then(response => response.json())
            .then(data => {
                setTimeout(() => {
                    loading.classList.add('d-none');
                    displayResults(data);
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                loading.classList.add('d-none');
                container.classList.remove('d-none');
                alert('An error occurred. Please try again.');
            });
        });
    });

    // Retake Quiz
    document.getElementById('retake-quiz').addEventListener('click', function() {
        // Reset
        answers = {};
        currentQuestion = 0;
        document.querySelectorAll('input[type="radio"]').forEach(input => input.checked = false);
        document.querySelectorAll('.next-btn, .submit-btn').forEach(btn => btn.disabled = true);

        // Show first question
        questions.forEach((q, i) => {
            q.classList.toggle('d-none', i !== 0);
        });

        results.classList.add('d-none');
        container.classList.remove('d-none');
        updateProgress();
    });

    function updateProgress() {
        const percent = Math.round(((currentQuestion + 1) / totalQuestions) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = `Question ${currentQuestion + 1} of ${totalQuestions}`;
        progressPercent.textContent = percent + '%';
    }

    function displayResults(data) {
        if (!data.success || !data.result) {
            alert('Could not calculate results. Please try again.');
            return;
        }

        const result = data.result;

        document.getElementById('result-title').textContent = result.title;
        document.getElementById('result-description').textContent = result.description;
        document.getElementById('result-jobs-link').href = data.recommended_jobs_url || '{{ \App\Helpers\UrlGen::search() }}';

        // Icon
        const iconMap = {
            'fa-palette': 'fa-palette',
            'fa-chart-line': 'fa-chart-line',
            'fa-crown': 'fa-crown',
            'fa-heart': 'fa-heart',
            'fa-tasks': 'fa-tasks',
            'fa-laptop-code': 'fa-laptop-code'
        };
        const icon = document.getElementById('result-icon');
        icon.className = 'fa-solid ' + (iconMap[result.icon] || 'fa-star') + ' fa-4x text-primary';

        // Traits
        const traitsContainer = document.getElementById('result-traits');
        traitsContainer.innerHTML = '';
        if (result.traits && result.traits.length) {
            result.traits.forEach(trait => {
                const span = document.createElement('span');
                span.className = 'result-trait';
                span.textContent = trait;
                traitsContainer.appendChild(span);
            });
        }

        // Share links
        const shareUrl = encodeURIComponent(window.location.href);
        const shareText = encodeURIComponent('I am a "' + result.title + '" according to the career quiz! Find your career type at');
        document.getElementById('share-facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`;
        document.getElementById('share-twitter').href = `https://twitter.com/intent/tweet?url=${shareUrl}&text=${shareText}`;
        document.getElementById('share-linkedin').href = `https://www.linkedin.com/shareArticle?mini=true&url=${shareUrl}`;
        document.getElementById('share-whatsapp').href = `https://wa.me/?text=${shareText}%20${shareUrl}`;

        results.classList.remove('d-none');
    }
});
</script>
@endsection
