<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Safe migration to sync production database with local
     * This migration adds all missing tables and columns without affecting existing data
     */
    public function up(): void
    {
        // ============================================
        // MISSING TABLES - Create if they don't exist
        // ============================================

        // candidate_scores
        if (!Schema::hasTable('candidate_scores')) {
            Schema::create('candidate_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('profile_completion_score', 5, 2)->default(0);
                $table->decimal('activity_score', 5, 2)->default(0);
                $table->decimal('verification_score', 5, 2)->default(0);
                $table->decimal('response_rate_score', 5, 2)->default(0);
                $table->decimal('success_rate_score', 5, 2)->default(0);
                $table->decimal('total_score', 5, 2)->default(0);
                $table->integer('profile_completion_percentage')->default(0);
                $table->integer('days_active_last_30')->default(0);
                $table->integer('applications_sent_last_30')->default(0);
                $table->integer('messages_responded_24h')->default(0);
                $table->integer('total_messages_received')->default(0);
                $table->integer('interviews_attended')->default(0);
                $table->integer('jobs_hired_for')->default(0);
                $table->boolean('email_verified')->default(false);
                $table->boolean('phone_verified')->default(false);
                $table->boolean('linkedin_verified')->default(false);
                $table->boolean('education_verified')->default(false);
                $table->boolean('employment_verified')->default(false);
                $table->json('score_history')->nullable();
                $table->timestamp('last_calculated_at')->nullable();
                $table->timestamps();
            });
        }

        // career_guides
        if (!Schema::hasTable('career_guides')) {
            Schema::create('career_guides', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->string('category')->nullable();
                $table->string('subcategory')->nullable();
                $table->string('difficulty_level')->nullable();
                $table->integer('estimated_read_time')->nullable();
                $table->string('featured_image')->nullable();
                $table->json('tags')->nullable();
                $table->json('career_paths')->nullable();
                $table->json('required_skills')->nullable();
                $table->json('salary_ranges')->nullable();
                $table->integer('view_count')->default(0);
                $table->decimal('rating', 3, 2)->default(0);
                $table->integer('rating_count')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('author_id')->nullable();
                $table->timestamps();
            });
        }

        // career_guide_reviews
        if (!Schema::hasTable('career_guide_reviews')) {
            Schema::create('career_guide_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('career_guide_id');
                $table->decimal('rating', 3, 2)->default(0);
                $table->text('review')->nullable();
                $table->boolean('is_helpful_vote')->default(false);
                $table->integer('helpful_votes')->default(0);
                $table->boolean('is_verified_professional')->default(false);
                $table->timestamps();
            });
        }

        // career_assessments
        if (!Schema::hasTable('career_assessments')) {
            Schema::create('career_assessments', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('assessment_type', ['personality', 'skills', 'interests', 'values', 'comprehensive'])->default('comprehensive');
                $table->json('questions')->nullable();
                $table->json('scoring_algorithm')->nullable();
                $table->json('result_categories')->nullable();
                $table->integer('estimated_duration')->nullable();
                $table->integer('total_questions')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('completion_count')->default(0);
                $table->decimal('average_rating', 3, 2)->default(0);
                $table->timestamps();
            });
        }

        // career_plans
        if (!Schema::hasTable('career_plans')) {
            Schema::create('career_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('plan_name');
                $table->text('description')->nullable();
                $table->enum('plan_type', ['5_year', 'career_pivot', 'skill_development', 'promotion'])->default('5_year');
                $table->json('current_situation')->nullable();
                $table->json('target_goals')->nullable();
                $table->json('milestones')->nullable();
                $table->json('action_items')->nullable();
                $table->json('skill_gaps')->nullable();
                $table->json('education_goals')->nullable();
                $table->json('financial_projections')->nullable();
                $table->date('target_completion_date')->nullable();
                $table->enum('status', ['draft', 'active', 'completed', 'paused'])->default('draft');
                $table->integer('progress_percentage')->default(0);
                $table->timestamp('last_reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        // career_plan_milestones
        if (!Schema::hasTable('career_plan_milestones')) {
            Schema::create('career_plan_milestones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('career_plan_id');
                $table->string('milestone_title');
                $table->text('description')->nullable();
                $table->date('target_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending');
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->json('success_criteria')->nullable();
                $table->text('notes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // compensation_calculations
        if (!Schema::hasTable('compensation_calculations')) {
            Schema::create('compensation_calculations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('calculation_type');
                $table->json('input_data')->nullable();
                $table->json('calculated_results')->nullable();
                $table->json('recommendations')->nullable();
                $table->json('market_comparisons')->nullable();
                $table->boolean('is_saved')->default(false);
                $table->string('calculation_name')->nullable();
                $table->timestamps();
            });
        }

        // cost_of_living
        if (!Schema::hasTable('cost_of_living')) {
            Schema::create('cost_of_living', function (Blueprint $table) {
                $table->id();
                $table->string('city');
                $table->string('country');
                $table->decimal('index_score', 5, 2)->default(0);
                $table->decimal('rent_index', 5, 2)->default(0);
                $table->decimal('groceries_index', 5, 2)->default(0);
                $table->decimal('restaurant_index', 5, 2)->default(0);
                $table->decimal('purchasing_power_index', 5, 2)->default(0);
                $table->date('last_updated')->nullable();
                $table->timestamps();
            });
        }

        // courses
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->text('objectives')->nullable();
                $table->string('thumbnail')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_free')->default(false);
                $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
                $table->integer('duration_hours')->default(0);
                $table->boolean('is_published')->default(false);
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->integer('enrollment_count')->default(0);
                $table->decimal('rating', 3, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // course_modules
        if (!Schema::hasTable('course_modules')) {
            Schema::create('course_modules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // course_lessons
        if (!Schema::hasTable('course_lessons')) {
            Schema::create('course_lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('module_id');
                $table->string('title');
                $table->text('content')->nullable();
                $table->enum('type', ['video', 'text', 'quiz', 'exercise', 'interactive'])->default('text');
                $table->string('video_url')->nullable();
                $table->integer('duration_minutes')->default(0);
                $table->integer('order')->default(0);
                $table->boolean('is_free_preview')->default(false);
                $table->json('interactive_config')->nullable();
                $table->timestamps();
            });
        }

        // course_enrollments
        if (!Schema::hasTable('course_enrollments')) {
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('course_id');
                $table->timestamp('enrolled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->integer('progress_percentage')->default(0);
                $table->timestamps();
            });
        }

        // course_certificates
        if (!Schema::hasTable('course_certificates')) {
            Schema::create('course_certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('enrollment_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('course_id');
                $table->string('certificate_number')->unique();
                $table->timestamp('issued_at')->nullable();
                $table->string('certificate_path')->nullable();
                $table->timestamps();
            });
        }

        // lesson_progress
        if (!Schema::hasTable('lesson_progress')) {
            Schema::create('lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('lesson_id');
                $table->unsignedBigInteger('enrollment_id');
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->integer('time_spent_minutes')->default(0);
                $table->timestamps();
            });
        }

        // lesson_exercises
        if (!Schema::hasTable('lesson_exercises')) {
            Schema::create('lesson_exercises', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lesson_id');
                $table->string('title');
                $table->text('question')->nullable();
                $table->text('code_template')->nullable();
                $table->text('solution')->nullable();
                $table->json('test_cases')->nullable();
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->integer('points')->default(0);
                $table->timestamps();
            });
        }

        // desktop_configs
        if (!Schema::hasTable('desktop_configs')) {
            Schema::create('desktop_configs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lesson_id');
                $table->json('desktop_icons')->nullable();
                $table->json('taskbar_apps')->nullable();
                $table->json('start_menu_apps')->nullable();
                $table->json('initial_windows')->nullable();
                $table->json('filesystem')->nullable();
                $table->string('wallpaper')->nullable();
                $table->enum('mode', ['guided', 'free', 'both'])->default('guided');
                $table->boolean('show_taskbar')->default(true);
                $table->boolean('show_start_menu')->default(true);
                $table->boolean('show_desktop_icons')->default(true);
                $table->boolean('allow_window_resize')->default(true);
                $table->boolean('allow_window_move')->default(true);
                $table->json('disabled_apps')->nullable();
                $table->json('hidden_elements')->nullable();
                $table->timestamps();
            });
        }

        // interactive_steps
        if (!Schema::hasTable('interactive_steps')) {
            Schema::create('interactive_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lesson_id');
                $table->integer('step_number')->default(1);
                $table->string('title');
                $table->text('instruction')->nullable();
                $table->enum('action_type', ['click', 'double_click', 'right_click', 'type', 'drag', 'open_app', 'close_window', 'minimize_window', 'maximize_window', 'navigate', 'create_file', 'create_folder', 'rename', 'delete', 'copy', 'paste', 'select'])->default('click');
                $table->string('target_element')->nullable();
                $table->json('action_data')->nullable();
                $table->json('validation_rules')->nullable();
                $table->string('hint')->nullable();
                $table->integer('points')->default(10);
                $table->boolean('is_required')->default(true);
                $table->integer('timeout_seconds')->nullable();
                $table->timestamps();
            });
        }

        // interactive_step_progress
        if (!Schema::hasTable('interactive_step_progress')) {
            Schema::create('interactive_step_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('lesson_id');
                $table->unsignedBigInteger('step_id');
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->integer('attempts')->default(0);
                $table->integer('time_spent_seconds')->default(0);
                $table->integer('points_earned')->default(0);
                $table->json('user_actions')->nullable();
                $table->timestamps();
            });
        }

        // job_alerts
        if (!Schema::hasTable('job_alerts')) {
            Schema::create('job_alerts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name', 100);
                $table->text('keywords')->nullable();
                $table->json('categories')->nullable();
                $table->json('locations')->nullable();
                $table->string('salary_min')->nullable();
                $table->string('salary_max')->nullable();
                $table->string('job_type')->nullable();
                $table->enum('frequency', ['instant', 'daily', 'weekly'])->default('daily');
                $table->boolean('active')->default(true);
                $table->timestamp('last_sent')->nullable();
                $table->integer('matches_count')->default(0);
                $table->timestamps();
            });
        }

        // job_attributes
        if (!Schema::hasTable('job_attributes')) {
            Schema::create('job_attributes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->enum('work_arrangement', ['remote', 'hybrid', 'onsite', 'flexible'])->nullable();
                $table->string('timezone_preference')->nullable();
                $table->decimal('equity_min', 8, 4)->nullable();
                $table->decimal('equity_max', 8, 4)->nullable();
                $table->enum('company_stage', ['seed', 'series_a', 'series_b', 'series_c', 'ipo', 'established'])->nullable();
                $table->json('impact_categories')->nullable();
                $table->string('clearance_level')->nullable();
                $table->integer('project_duration_months')->nullable();
                $table->decimal('hourly_rate_min', 8, 2)->nullable();
                $table->decimal('hourly_rate_max', 8, 2)->nullable();
                $table->enum('client_rating_required', ['none', 'basic', 'excellent'])->nullable();
                $table->timestamps();
            });
        }

        // job_specializations
        if (!Schema::hasTable('job_specializations')) {
            Schema::create('job_specializations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->json('required_fields')->nullable();
                $table->json('filter_options')->nullable();
                $table->json('metadata')->nullable();
                $table->boolean('is_remote_first')->default(false);
                $table->boolean('has_equity_info')->default(false);
                $table->boolean('has_impact_metrics')->default(false);
                $table->boolean('requires_clearance')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // post_reactions
        if (!Schema::hasTable('post_reactions')) {
            Schema::create('post_reactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('reaction_type', 20)->default('like');
                $table->timestamps();
                $table->unique(['post_id', 'user_id', 'reaction_type']);
            });
        }

        // post_specializations
        if (!Schema::hasTable('post_specializations')) {
            Schema::create('post_specializations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('specialization_id');
                $table->json('specialization_data')->nullable();
                $table->timestamps();
            });
        }

        // salary_data
        if (!Schema::hasTable('salary_data')) {
            Schema::create('salary_data', function (Blueprint $table) {
                $table->id();
                $table->string('job_title');
                $table->string('normalized_title')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('industry')->nullable();
                $table->string('company_size')->nullable();
                $table->string('location_country')->nullable();
                $table->string('location_state')->nullable();
                $table->string('location_city')->nullable();
                $table->decimal('cost_of_living_index', 5, 2)->nullable();
                $table->integer('years_experience_min')->nullable();
                $table->integer('years_experience_max')->nullable();
                $table->decimal('salary_min', 12, 2)->nullable();
                $table->decimal('salary_max', 12, 2)->nullable();
                $table->decimal('salary_median', 12, 2)->nullable();
                $table->string('currency', 3)->default('USD');
                $table->enum('salary_type', ['annual', 'monthly', 'hourly'])->default('annual');
                $table->decimal('bonus_average', 12, 2)->nullable();
                $table->decimal('equity_percentage', 8, 4)->nullable();
                $table->json('benefits_data')->nullable();
                $table->string('data_source')->nullable();
                $table->integer('sample_size')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->date('data_collected_at')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        // skill_resources
        if (!Schema::hasTable('skill_resources')) {
            Schema::create('skill_resources', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->enum('resource_type', ['article', 'video', 'course', 'tutorial', 'tool', 'book'])->default('article');
                $table->string('external_url')->nullable();
                $table->string('skill_category')->nullable();
                $table->string('skill_name')->nullable();
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
                $table->integer('estimated_duration')->nullable();
                $table->decimal('price', 8, 2)->default(0);
                $table->string('provider')->nullable();
                $table->json('prerequisites')->nullable();
                $table->json('learning_outcomes')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('view_count')->default(0);
                $table->decimal('rating', 3, 2)->default(0);
                $table->integer('rating_count')->default(0);
                $table->boolean('is_free')->default(true);
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        // subscription_tiers
        if (!Schema::hasTable('subscription_tiers')) {
            Schema::create('subscription_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->text('description')->nullable();
                $table->json('features')->nullable();
                $table->decimal('monthly_price', 10, 2)->default(0);
                $table->decimal('yearly_price', 10, 2)->default(0);
                $table->integer('job_posts_limit')->nullable();
                $table->integer('featured_posts_limit')->nullable();
                $table->integer('resume_views_limit')->nullable();
                $table->boolean('priority_support')->default(false);
                $table->boolean('analytics_access')->default(false);
                $table->boolean('api_access')->default(false);
                $table->boolean('white_label')->default(false);
                $table->boolean('active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // user_activity_logs
        if (!Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('activity_type');
                $table->json('activity_data')->nullable();
                $table->timestamp('activity_date')->nullable();
                $table->timestamps();
            });
        }

        // user_analytics
        if (!Schema::hasTable('user_analytics')) {
            Schema::create('user_analytics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('date');
                $table->string('event_type', 50);
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // user_assessment_results
        if (!Schema::hasTable('user_assessment_results')) {
            Schema::create('user_assessment_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('assessment_id');
                $table->json('answers')->nullable();
                $table->json('scores')->nullable();
                $table->json('primary_result')->nullable();
                $table->json('secondary_results')->nullable();
                $table->json('recommended_careers')->nullable();
                $table->json('skill_strengths')->nullable();
                $table->json('development_areas')->nullable();
                $table->text('detailed_analysis')->nullable();
                $table->integer('completion_time_minutes')->nullable();
                $table->decimal('user_rating', 3, 2)->nullable();
                $table->text('user_feedback')->nullable();
                $table->boolean('is_public')->default(false);
                $table->timestamps();
            });
        }

        // user_learning_progress
        if (!Schema::hasTable('user_learning_progress')) {
            Schema::create('user_learning_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('resource_id');
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'bookmarked'])->default('not_started');
                $table->integer('progress_percentage')->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->integer('time_spent_minutes')->default(0);
                $table->json('notes')->nullable();
                $table->decimal('user_rating', 3, 2)->nullable();
                $table->text('user_review')->nullable();
                $table->timestamps();
            });
        }

        // user_salary_submissions
        if (!Schema::hasTable('user_salary_submissions')) {
            Schema::create('user_salary_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('job_title');
                $table->string('company_name')->nullable();
                $table->string('location_city')->nullable();
                $table->string('location_country')->nullable();
                $table->integer('years_experience')->nullable();
                $table->decimal('annual_salary', 12, 2);
                $table->string('currency', 3)->default('USD');
                $table->decimal('bonus', 12, 2)->nullable();
                $table->text('additional_compensation')->nullable();
                $table->json('skills')->nullable();
                $table->boolean('is_anonymous')->default(true);
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        // ============================================
        // MISSING COLUMNS - Add to existing tables
        // ============================================

        // Add missing columns to packages table
        if (Schema::hasTable('packages')) {
            if (!Schema::hasColumn('packages', 'type')) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->enum('type', ['promotion', 'subscription'])->default('promotion')->after('id');
                });
            }
            if (!Schema::hasColumn('packages', 'interval')) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->enum('interval', ['week', 'month', 'year'])->nullable()->after('currency_code');
                });
            }
            if (!Schema::hasColumn('packages', 'listings_limit')) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->integer('listings_limit')->nullable()->after('interval');
                });
            }
            if (!Schema::hasColumn('packages', 'promotion_time')) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->integer('promotion_time')->nullable()->after('listings_limit');
                });
            }
            if (!Schema::hasColumn('packages', 'expiration_time')) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->unsignedInteger('expiration_time')->nullable()->after('promotion_time');
                });
            }
        }

        // Add missing columns to subscription_plans table
        if (Schema::hasTable('subscription_plans')) {
            if (!Schema::hasColumn('subscription_plans', 'monthly_price')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->decimal('monthly_price', 10, 2)->nullable()->after('price');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'yearly_price')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->decimal('yearly_price', 10, 2)->nullable()->after('monthly_price');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'job_posts_limit')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->integer('job_posts_limit')->nullable()->after('features');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'featured_posts_limit')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->integer('featured_posts_limit')->nullable()->after('job_posts_limit');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'resume_views_limit')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->integer('resume_views_limit')->nullable()->after('featured_posts_limit');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'priority_support')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->boolean('priority_support')->default(false)->after('resume_views_limit');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'analytics_access')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->boolean('analytics_access')->default(false)->after('priority_support');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'api_access')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->boolean('api_access')->default(false)->after('analytics_access');
                });
            }
            if (!Schema::hasColumn('subscription_plans', 'white_label')) {
                Schema::table('subscription_plans', function (Blueprint $table) {
                    $table->boolean('white_label')->default(false)->after('api_access');
                });
            }
        }

        // Add missing columns to user_subscriptions table
        if (Schema::hasTable('user_subscriptions')) {
            if (!Schema::hasColumn('user_subscriptions', 'payment_method')) {
                Schema::table('user_subscriptions', function (Blueprint $table) {
                    $table->string('payment_method')->nullable()->after('cancelled_at');
                });
            }
            if (!Schema::hasColumn('user_subscriptions', 'transaction_id')) {
                Schema::table('user_subscriptions', function (Blueprint $table) {
                    $table->string('transaction_id')->nullable()->after('payment_method');
                });
            }
            if (!Schema::hasColumn('user_subscriptions', 'amount_paid')) {
                Schema::table('user_subscriptions', function (Blueprint $table) {
                    $table->decimal('amount_paid', 10, 2)->nullable()->after('transaction_id');
                });
            }
        }

        // Add missing columns to payments table
        if (Schema::hasTable('payments')) {
            if (!Schema::hasColumn('payments', 'period_start')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->timestamp('period_start')->nullable()->after('active');
                });
            }
            if (!Schema::hasColumn('payments', 'period_end')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->timestamp('period_end')->nullable()->after('period_start');
                });
            }
            if (!Schema::hasColumn('payments', 'canceled_at')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->timestamp('canceled_at')->nullable()->after('period_end');
                });
            }
            if (!Schema::hasColumn('payments', 'refunded_at')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->timestamp('refunded_at')->nullable()->after('canceled_at');
                });
            }
        }

        // Add code column to languages table if missing
        if (Schema::hasTable('languages')) {
            if (!Schema::hasColumn('languages', 'code')) {
                Schema::table('languages', function (Blueprint $table) {
                    $table->string('code', 20)->nullable()->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order of creation
        Schema::dropIfExists('user_salary_submissions');
        Schema::dropIfExists('user_learning_progress');
        Schema::dropIfExists('user_assessment_results');
        Schema::dropIfExists('user_analytics');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('subscription_tiers');
        Schema::dropIfExists('skill_resources');
        Schema::dropIfExists('salary_data');
        Schema::dropIfExists('post_specializations');
        Schema::dropIfExists('post_reactions');
        Schema::dropIfExists('job_specializations');
        Schema::dropIfExists('job_attributes');
        Schema::dropIfExists('job_alerts');
        Schema::dropIfExists('interactive_step_progress');
        Schema::dropIfExists('interactive_steps');
        Schema::dropIfExists('desktop_configs');
        Schema::dropIfExists('lesson_exercises');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('course_certificates');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('cost_of_living');
        Schema::dropIfExists('compensation_calculations');
        Schema::dropIfExists('career_plan_milestones');
        Schema::dropIfExists('career_plans');
        Schema::dropIfExists('career_assessments');
        Schema::dropIfExists('career_guide_reviews');
        Schema::dropIfExists('career_guides');
        Schema::dropIfExists('candidate_scores');
    }
};
