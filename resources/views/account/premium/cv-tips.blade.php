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
                                <strong><i class="fa-solid fa-file-alt"></i> CV Refinement Tips</strong>
                            </h2>
                            <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </a>
                        </div>

                        @if ($preferences && $preferences->cv_summary)
                            <div class="alert alert-success">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                Tips personalized based on your professional summary and preferences
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <a href="{{ route('account.premium.preferences') }}">Complete your profile</a> to get personalized CV tips
                            </div>
                        @endif

                        {{-- General CV Tips --}}
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-star me-2"></i> Essential CV Tips</h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="cvTipsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tip1">
                                                <i class="fa-solid fa-bullseye me-2 text-primary"></i> Tailor Your CV for Each Application
                                            </button>
                                        </h2>
                                        <div id="tip1" class="accordion-collapse collapse show" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Read the job description carefully and identify key requirements</li>
                                                    <li>Match your skills and experience to what the employer is looking for</li>
                                                    <li>Use keywords from the job posting in your CV</li>
                                                    <li>Highlight relevant achievements that demonstrate your capabilities</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tip2">
                                                <i class="fa-solid fa-align-left me-2 text-primary"></i> Professional Summary
                                            </button>
                                        </h2>
                                        <div id="tip2" class="accordion-collapse collapse" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Write 2-3 sentences summarizing your experience and goals</li>
                                                    <li>Include your years of experience and key areas of expertise</li>
                                                    <li>Mention your most impressive achievements</li>
                                                    <li>Avoid generic statements like "hardworking team player"</li>
                                                </ul>
                                                @if ($preferences && $preferences->cv_summary)
                                                    <div class="alert alert-info mt-3">
                                                        <strong>Your current summary:</strong><br>
                                                        {{ Str::limit($preferences->cv_summary, 200) }}
                                                        <a href="{{ route('account.premium.preferences') }}" class="d-block mt-2">Edit your summary</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tip3">
                                                <i class="fa-solid fa-trophy me-2 text-primary"></i> Quantify Your Achievements
                                            </button>
                                        </h2>
                                        <div id="tip3" class="accordion-collapse collapse" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <p>Numbers make your achievements more credible and impressive:</p>
                                                <ul>
                                                    <li><strong>Instead of:</strong> "Improved sales performance"</li>
                                                    <li><strong>Write:</strong> "Increased sales by 35% over 6 months"</li>
                                                </ul>
                                                <ul>
                                                    <li><strong>Instead of:</strong> "Managed a large team"</li>
                                                    <li><strong>Write:</strong> "Led a team of 15 developers across 3 projects"</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tip4">
                                                <i class="fa-solid fa-tools me-2 text-primary"></i> Skills Section
                                            </button>
                                        </h2>
                                        <div id="tip4" class="accordion-collapse collapse" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>List both technical and soft skills</li>
                                                    <li>Group related skills together</li>
                                                    <li>Be specific about tools and technologies</li>
                                                    <li>Only include skills you're comfortable discussing in an interview</li>
                                                </ul>
                                                @if ($preferences && $preferences->key_skills)
                                                    <div class="alert alert-info mt-3">
                                                        <strong>Your key skills:</strong><br>
                                                        {{ $preferences->key_skills }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tip5">
                                                <i class="fa-solid fa-spell-check me-2 text-primary"></i> Formatting & Proofreading
                                            </button>
                                        </h2>
                                        <div id="tip5" class="accordion-collapse collapse" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Use a clean, professional font (Arial, Calibri, or similar)</li>
                                                    <li>Keep font size between 10-12pt for body text</li>
                                                    <li>Use consistent formatting throughout</li>
                                                    <li>Keep your CV to 1-2 pages maximum</li>
                                                    <li>Proofread multiple times for spelling and grammar errors</li>
                                                    <li>Ask someone else to review it</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tip6">
                                                <i class="fa-solid fa-ban me-2 text-primary"></i> Common Mistakes to Avoid
                                            </button>
                                        </h2>
                                        <div id="tip6" class="accordion-collapse collapse" data-bs-parent="#cvTipsAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled">
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Including a photo (unless specifically requested)</li>
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Using an unprofessional email address</li>
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Including personal information (age, marital status, religion)</li>
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Using generic phrases like "References available upon request"</li>
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Lying or exaggerating experience</li>
                                                    <li><i class="fa-solid fa-times text-danger me-2"></i> Including irrelevant work experience</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Personalized Tips based on experience level --}}
                        @if ($preferences && $preferences->experience_level)
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fa-solid fa-user-graduate me-2"></i>
                                        Tips for {{ $preferences->experienceLabel }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @switch($preferences->experience_level)
                                        @case('entry')
                                            <ul>
                                                <li>Focus on education, internships, and relevant coursework</li>
                                                <li>Highlight transferable skills from part-time jobs or volunteer work</li>
                                                <li>Include academic projects that demonstrate relevant skills</li>
                                                <li>Emphasize your eagerness to learn and grow</li>
                                            </ul>
                                            @break
                                        @case('junior')
                                            <ul>
                                                <li>Highlight specific accomplishments from your early career</li>
                                                <li>Show progression in responsibilities</li>
                                                <li>Include any certifications or training completed</li>
                                                <li>Demonstrate your initiative and willingness to take on challenges</li>
                                            </ul>
                                            @break
                                        @case('mid')
                                            <ul>
                                                <li>Focus on leadership and project management experience</li>
                                                <li>Quantify the impact of your work on business outcomes</li>
                                                <li>Highlight cross-functional collaboration</li>
                                                <li>Show evidence of mentoring junior team members</li>
                                            </ul>
                                            @break
                                        @case('senior')
                                            <ul>
                                                <li>Emphasize strategic thinking and decision-making</li>
                                                <li>Highlight budget management and resource allocation</li>
                                                <li>Show your track record of building and leading teams</li>
                                                <li>Include industry recognition, speaking engagements, or publications</li>
                                            </ul>
                                            @break
                                        @case('executive')
                                            <ul>
                                                <li>Focus on business transformation and organizational impact</li>
                                                <li>Highlight board-level experience and stakeholder management</li>
                                                <li>Include P&L responsibility and major business achievements</li>
                                                <li>Demonstrate thought leadership in your industry</li>
                                            </ul>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        @endif

                        <div class="text-center">
                            <a href="{{ route('account.premium.job-matches') }}" class="btn btn-primary">
                                <i class="fa-solid fa-bullseye me-2"></i> Find Matching Jobs
                            </a>
                            <a href="{{ route('account.premium.interview-prep') }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-user-tie me-2"></i> Interview Preparation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
