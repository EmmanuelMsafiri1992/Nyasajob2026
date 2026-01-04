@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @include('account.inc.sidebar')
                </div>

                <div class="col-md-9 page-content">
                    @include('flash::message')

                    <div class="inner-box">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="title-2 mb-0">
                                <strong><i class="fa-solid fa-user-tie"></i> Interview Preparation</strong>
                            </h2>
                            <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </a>
                        </div>

                        @if ($preferences && $preferences->desired_job_title)
                            <div class="alert alert-success">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                Tips tailored for <strong>{{ $preferences->desired_job_title }}</strong> positions
                            </div>
                        @endif

                        {{-- Pre-Interview Preparation --}}
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-clipboard-list me-2"></i> Before the Interview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fa-solid fa-building me-2 text-primary"></i> Research the Company</h6>
                                        <ul>
                                            <li>Visit their website and social media</li>
                                            <li>Understand their products/services</li>
                                            <li>Research recent news and achievements</li>
                                            <li>Know their mission and values</li>
                                            <li>Understand their industry position</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fa-solid fa-file-alt me-2 text-primary"></i> Prepare Your Documents</h6>
                                        <ul>
                                            <li>Bring multiple copies of your CV</li>
                                            <li>Prepare a portfolio if relevant</li>
                                            <li>Have references ready</li>
                                            <li>Bring a notepad and pen</li>
                                            <li>Prepare questions to ask</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Common Interview Questions --}}
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-comments me-2"></i> Common Questions & How to Answer</h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="questionsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                                "Tell me about yourself"
                                            </button>
                                        </h2>
                                        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <p class="text-muted">Use the Present-Past-Future formula:</p>
                                                <ol>
                                                    <li><strong>Present:</strong> What you're doing now</li>
                                                    <li><strong>Past:</strong> Relevant experience that led you here</li>
                                                    <li><strong>Future:</strong> Why this opportunity excites you</li>
                                                </ol>
                                                <div class="alert alert-light">
                                                    <strong>Example:</strong> "I'm currently a software developer at XYZ Company, where I focus on building web applications. Over the past 3 years, I've developed expertise in PHP and Laravel. I'm excited about this opportunity because I want to work on larger-scale projects that impact more users."
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                                "What are your strengths?"
                                            </button>
                                        </h2>
                                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Choose strengths relevant to the job</li>
                                                    <li>Provide specific examples</li>
                                                    <li>Quantify when possible</li>
                                                </ul>
                                                @if ($preferences && $preferences->key_skills)
                                                    <div class="alert alert-info">
                                                        <strong>Your key skills to highlight:</strong> {{ Str::limit($preferences->key_skills, 100) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                                "What is your greatest weakness?"
                                            </button>
                                        </h2>
                                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <p class="text-muted">Choose a genuine weakness and show how you're working on it:</p>
                                                <ul>
                                                    <li>Pick something real but not critical for the role</li>
                                                    <li>Show self-awareness</li>
                                                    <li>Explain what you're doing to improve</li>
                                                    <li>Never say "I'm a perfectionist" or "I work too hard"</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                                                "Why do you want to work here?"
                                            </button>
                                        </h2>
                                        <div id="q4" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Research the company beforehand</li>
                                                    <li>Connect their values to yours</li>
                                                    <li>Show enthusiasm for their products/services</li>
                                                    <li>Explain how you can contribute</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q5">
                                                "Where do you see yourself in 5 years?"
                                            </button>
                                        </h2>
                                        <div id="q5" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Show ambition aligned with realistic growth</li>
                                                    <li>Demonstrate commitment to the company</li>
                                                    <li>Focus on skills and responsibilities, not titles</li>
                                                </ul>
                                                @if ($preferences && $preferences->career_goals)
                                                    <div class="alert alert-info">
                                                        <strong>Your career goals:</strong> {{ Str::limit($preferences->career_goals, 150) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q6">
                                                "What are your salary expectations?"
                                            </button>
                                        </h2>
                                        <div id="q6" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Research market rates beforehand</li>
                                                    <li>Give a range rather than a specific number</li>
                                                    <li>Express flexibility</li>
                                                    <li>Emphasize total compensation (benefits, growth opportunities)</li>
                                                </ul>
                                                @if ($preferences && ($preferences->min_salary || $preferences->max_salary))
                                                    <div class="alert alert-info">
                                                        <strong>Your salary expectation:</strong>
                                                        @if ($preferences->min_salary && $preferences->max_salary)
                                                            {{ $preferences->salary_currency }} {{ number_format($preferences->min_salary) }} - {{ number_format($preferences->max_salary) }} per {{ $preferences->salary_period }}
                                                        @elseif ($preferences->min_salary)
                                                            Minimum {{ $preferences->salary_currency }} {{ number_format($preferences->min_salary) }} per {{ $preferences->salary_period }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STAR Method --}}
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-star me-2"></i> The STAR Method for Behavioral Questions</h5>
                            </div>
                            <div class="card-body">
                                <p>Use the STAR method to structure your answers to behavioral questions:</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 bg-light">
                                            <div class="card-body">
                                                <h6><span class="badge bg-primary me-2">S</span> Situation</h6>
                                                <p class="mb-0 small">Set the scene and provide context</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 bg-light">
                                            <div class="card-body">
                                                <h6><span class="badge bg-success me-2">T</span> Task</h6>
                                                <p class="mb-0 small">Describe your responsibility</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 bg-light">
                                            <div class="card-body">
                                                <h6><span class="badge bg-warning text-dark me-2">A</span> Action</h6>
                                                <p class="mb-0 small">Explain what you did</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 bg-light">
                                            <div class="card-body">
                                                <h6><span class="badge bg-danger me-2">R</span> Result</h6>
                                                <p class="mb-0 small">Share the outcome (quantify if possible)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Questions to Ask --}}
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fa-solid fa-question-circle me-2"></i> Questions to Ask the Interviewer</h5>
                            </div>
                            <div class="card-body">
                                <p>Always have questions ready. It shows interest and helps you evaluate the opportunity:</p>
                                <ul>
                                    <li>"What does a typical day look like in this role?"</li>
                                    <li>"What are the biggest challenges facing the team right now?"</li>
                                    <li>"How would you describe the company culture?"</li>
                                    <li>"What opportunities are there for professional development?"</li>
                                    <li>"What does success look like in this role after 90 days?"</li>
                                    <li>"What's the team structure like?"</li>
                                    <li>"What are the next steps in the interview process?"</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Interview Day Tips --}}
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-calendar-check me-2"></i> On Interview Day</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Do's</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Arrive 10-15 minutes early</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Dress professionally</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Maintain eye contact</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Use confident body language</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Listen actively</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Be enthusiastic</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Thank them for their time</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Don'ts</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Arrive late</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Speak negatively about past employers</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Interrupt the interviewer</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Check your phone</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Give one-word answers</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Lie or exaggerate</li>
                                            <li><i class="fa-solid fa-times text-danger me-2"></i> Focus only on salary/benefits</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- After the Interview --}}
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-envelope me-2"></i> After the Interview</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Send a thank-you email within 24 hours</li>
                                    <li>Reiterate your interest in the position</li>
                                    <li>Reference something specific from the conversation</li>
                                    <li>Keep it brief and professional</li>
                                    <li>Follow up if you haven't heard back within the expected timeframe</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('account.premium.job-matches') }}" class="btn btn-primary">
                                <i class="fa-solid fa-bullseye me-2"></i> Find Jobs to Apply
                            </a>
                            <a href="{{ route('account.premium.cv-tips') }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-file-alt me-2"></i> CV Tips
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
