@php
	$posts ??= [];
	$totalPosts ??= 0;
@endphp
@if (!empty($posts) && $totalPosts > 0)
	@foreach($posts as $key => $post)
		@php
			// Get Package Info
			$premiumClass = '';
			$premiumBadge = '';
			if (data_get($post, 'featured') == 1) {
				if (!empty(data_get($post, 'latestPayment.package'))) {
					$premiumClass = ' premium-post';
					$premiumBadge = ' <span class="badge bg-dark float-end">' . data_get($post, 'latestPayment.package.short_name') . '</span>';
				}
			}
		@endphp
		
		<div class="item-list job-item{{ $premiumClass }}">
			<div class="row">
				<div class="col-md-1 col-sm-2 no-padding photobox">
					<div class="add-image">
						<a href="{{ \App\Helpers\UrlGen::post($post) }}">
							<img class="img-thumbnail no-margin" src="{{ data_get($post, 'logo_url.full') }}" alt="{{ data_get($post, 'company_name') }}">
						</a>
					</div>
				</div>
				
				<div class="col-md-11 col-sm-10 add-desc-box">
					<div class="add-details jobs-item">
						<h5 class="company-title">
							@if (!empty(data_get($post, 'company_id')))
								<a href="{{ \App\Helpers\UrlGen::company(null, data_get($post, 'company_id')) }}">
									{{ data_get($post, 'company_name') }}
								</a>
							@else
								{{ data_get($post, 'company_name') }}
							@endif
						</h5>
						<h4 class="job-title">
							<a href="{{ \App\Helpers\UrlGen::post($post) }}">{{ str(data_get($post, 'title'))->limit(70) }}</a>{!! $premiumBadge !!}
						</h4>
						<span class="info-row">
							@if (!config('settings.list.hide_dates'))
								<span class="date">
									<i class="far fa-clock"></i> {!! data_get($post, 'created_at_formatted') !!}
								</span>
							@endif
							<span class="category">
								<i class="bi bi-folder"></i>&nbsp;
								@if (!empty(data_get($post, 'category.parent')))
									<a href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category.parent'), null, $city ?? null) !!}">
										{{ data_get($post, 'category.parent.name') }}
									</a>&nbsp;&raquo;&nbsp;
								@endif
								<a href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category'), null, $city ?? null) !!}">
									{{ data_get($post, 'category.name') }}
								</a>
							</span>
							<span class="item-location">
								<i class="bi bi-geo-alt"></i>&nbsp;
								<a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city'), null, $cat ?? null) !!}">
									{{ data_get($post, 'city.name') }}
								</a> {{ (!empty(data_get($post, 'distance'))) ? '- ' . round(data_get($post, 'distance'), 2) . getDistanceUnit() : '' }}
							</span>
							<span class="post_type">
								<i class="bi bi-tag"></i> {{ data_get($post, 'postType.name') }}
							</span>
							<span class="salary">
								<i class="bi bi-cash-coin"></i>&nbsp;
								{!! data_get($post, 'salary_formatted') !!}
								@if (!empty(data_get($post, 'salaryType')))
									{{ t('per') }} {{ data_get($post, 'salaryType.name') }}
								@endif
							</span>
						</span>
	
						<div class="jobs-desc">
							{!! str(strCleaner(data_get($post, 'description')))->limit(180) !!}
						</div>
	
						{{-- Reactions and Views Section --}}
						<div class="job-engagement d-flex align-items-center mb-2">
							{{-- Views Count --}}
							<span class="views-count me-3" title="Views">
								<i class="far fa-eye"></i>
								<span class="count" id="views-{{ data_get($post, 'id') }}">{{ number_format(data_get($post, 'visits', 0)) }}</span>
							</span>

							{{-- Reactions Summary --}}
							<div class="reactions-summary me-3" id="reactions-summary-{{ data_get($post, 'id') }}">
								<span class="total-reactions">
									<span class="reaction-emojis"></span>
									<span class="reaction-count" data-post-id="{{ data_get($post, 'id') }}">0</span>
								</span>
							</div>

							{{-- Reaction Buttons --}}
							<div class="reaction-buttons" data-post-id="{{ data_get($post, 'id') }}">
								<button type="button" class="btn btn-sm btn-outline-secondary reaction-trigger" data-post-id="{{ data_get($post, 'id') }}">
									<span class="reaction-icon">👍</span>
									<span class="reaction-text">Like</span>
								</button>
								<div class="reaction-picker d-none" id="reaction-picker-{{ data_get($post, 'id') }}">
									<button type="button" class="reaction-option" data-reaction="like" data-post-id="{{ data_get($post, 'id') }}" title="Like">👍</button>
									<button type="button" class="reaction-option" data-reaction="love" data-post-id="{{ data_get($post, 'id') }}" title="Love">❤️</button>
									<button type="button" class="reaction-option" data-reaction="celebrate" data-post-id="{{ data_get($post, 'id') }}" title="Celebrate">🎉</button>
									<button type="button" class="reaction-option" data-reaction="insightful" data-post-id="{{ data_get($post, 'id') }}" title="Insightful">💡</button>
									<button type="button" class="reaction-option" data-reaction="curious" data-post-id="{{ data_get($post, 'id') }}" title="Curious">🤔</button>
								</div>
							</div>
						</div>

						<div class="job-actions">
							<ul class="list-unstyled list-inline">
								@if (!auth()->check())
									<li id="{{ data_get($post, 'id') }}">
										<a class="save-job" id="save-{{ data_get($post, 'id') }}" href="javascript:void(0)">
											<span class="far fa-bookmark"></span> {{ t('Save Job') }}
										</a>
									</li>
								@endif
								@if (auth()->check() && in_array(auth()->user()->user_type_id, [2]))
									@if (!empty(data_get($post, 'savedByLoggedUser')))
										<li class="saved-job" id="{{ data_get($post, 'id') }}">
											<a class="saved-job" id="saved-{{ data_get($post, 'id') }}" href="javascript:void(0)">
												<span class="fas fa-bookmark"></span> {{ t('Saved Job') }}
											</a>
										</li>
									@else
										<li id="{{ data_get($post, 'id') }}">
											<a class="save-job" id="save-{{ data_get($post, 'id') }}" href="javascript:void(0)">
												<span class="far fa-bookmark"></span> {{ t('Save Job') }}
											</a>
										</li>
									@endif
								@endif
								<li>
									<a class="email-job" data-bs-toggle="modal" data-id="{{ data_get($post, 'id') }}" href="#sendByEmail" id="email-{{ data_get($post, 'id') }}">
										<i class="far fa-envelope"></i>
										{{ t('Email Job') }}
									</a>
								</li>
							</ul>
						</div>
	
					</div>
				</div>
			</div>
		</div>

		{{-- In-Feed Ad Every 4 Job Listings --}}
		@if (!empty($infeedAdvertising) && ($loop->iteration % 4 == 0) && !$loop->last)
			<div class="item-list infeed-ad my-3">
				<div class="row">
					<div class="col-12 text-center py-3">
						{!! data_get($infeedAdvertising, 'tracking_code_large') !!}
					</div>
				</div>
			</div>
		@endif
	@endforeach
@else
	<div class="p-4" style="width: 100%;">
		@if (str_contains(\Route::currentRouteAction(), 'Search\CompanyController'))
			{{ t('No jobs were found for this company') }}
		@else
			{{ t('no_result_refine_your_search') }}
		@endif
	</div>
@endif

@section('modal_location')
	@parent
	@include('layouts.inc.modal.send-by-email')
@endsection

@push('after_scripts_stack')
	<script>
		/* Favorites Translation */
		var lang = {
			labelSavePostSave: "{!! t('Save Job') !!}",
			labelSavePostRemove: "{{ t('Saved Job') }}",
			loginToSavePost: "{!! t('Please log in to save the Ads') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search') !!}"
		};

		/* Reaction Types Configuration */
		var reactionTypes = {
			'like': { emoji: '👍', label: 'Like' },
			'love': { emoji: '❤️', label: 'Love' },
			'celebrate': { emoji: '🎉', label: 'Celebrate' },
			'insightful': { emoji: '💡', label: 'Insightful' },
			'curious': { emoji: '🤔', label: 'Curious' }
		};

		$(document).ready(function ()
		{
			/* Get Post ID */
			$('.email-job').click(function(){
				let postId = $(this).attr("data-id");
				$('input[type=hidden][name=post_id]').val(postId);
			});

			@if (isset($errors) && $errors->any())
				@if (old('sendByEmailForm')=='1')
					{{-- Re-open the modal if error occured --}}
					let sendByEmail = new bootstrap.Modal(document.getElementById('sendByEmail'), {});
					sendByEmail.show();
				@endif
			@endif

			/* Post Reactions Functionality */
			initReactions();
		});

		function initReactions() {
			// Collect all post IDs on the page
			var postIds = [];
			$('.reaction-buttons[data-post-id]').each(function() {
				postIds.push($(this).data('post-id'));
			});

			// Load reactions for all posts in batch
			if (postIds.length > 0) {
				loadReactionsBatch(postIds);
			}

			// Toggle reaction picker on hover/click
			$('.reaction-trigger').on('mouseenter', function() {
				var postId = $(this).data('post-id');
				showReactionPicker(postId);
			});

			$('.reaction-buttons').on('mouseleave', function() {
				var postId = $(this).data('post-id');
				hideReactionPicker(postId);
			});

			// Handle reaction click
			$(document).on('click', '.reaction-option', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var postId = $(this).data('post-id');
				var reactionType = $(this).data('reaction');
				toggleReaction(postId, reactionType);
			});

			// Handle main button click (quick like)
			$('.reaction-trigger').on('click', function(e) {
				e.preventDefault();
				var postId = $(this).data('post-id');
				var currentReaction = $(this).data('current-reaction') || 'like';
				toggleReaction(postId, currentReaction);
			});
		}

		function showReactionPicker(postId) {
			$('#reaction-picker-' + postId).removeClass('d-none');
		}

		function hideReactionPicker(postId) {
			$('#reaction-picker-' + postId).addClass('d-none');
		}

		function loadReactionsBatch(postIds) {
			$.ajax({
				url: '{{ url("ajax/reactions/batch") }}',
				method: 'POST',
				data: {
					post_ids: postIds,
					_token: '{{ csrf_token() }}'
				},
				success: function(response) {
					if (response.success && response.data) {
						$.each(response.data, function(postId, data) {
							updateReactionUI(postId, data);
						});
					}
				},
				error: function(xhr, status, error) {
					console.error('Failed to load reactions:', error);
				}
			});
		}

		function toggleReaction(postId, reactionType) {
			$.ajax({
				url: '{{ url("ajax/reactions/post") }}/' + postId + '/toggle',
				method: 'POST',
				data: {
					reaction_type: reactionType,
					_token: '{{ csrf_token() }}'
				},
				success: function(response) {
					if (response.success) {
						updateReactionUI(postId, {
							reaction_counts: response.reaction_counts,
							total_reactions: response.total_reactions,
							user_reaction: response.user_reaction
						});
						hideReactionPicker(postId);
					}
				},
				error: function(xhr, status, error) {
					console.error('Failed to toggle reaction:', error);
				}
			});
		}

		function updateReactionUI(postId, data) {
			var $summary = $('#reactions-summary-' + postId);
			var $trigger = $('.reaction-trigger[data-post-id="' + postId + '"]');
			var $countSpan = $summary.find('.reaction-count');
			var $emojisSpan = $summary.find('.reaction-emojis');

			// Update total count
			$countSpan.text(data.total_reactions > 0 ? data.total_reactions : '');

			// Update emoji display (show top 3 reactions)
			var topReactions = [];
			if (data.reaction_counts) {
				var sortedReactions = Object.entries(data.reaction_counts)
					.filter(function(entry) { return entry[1] > 0; })
					.sort(function(a, b) { return b[1] - a[1]; })
					.slice(0, 3);

				sortedReactions.forEach(function(entry) {
					if (reactionTypes[entry[0]]) {
						topReactions.push(reactionTypes[entry[0]].emoji);
					}
				});
			}
			$emojisSpan.text(topReactions.join(''));

			// Update trigger button based on user's reaction
			if (data.user_reaction && reactionTypes[data.user_reaction]) {
				$trigger.find('.reaction-icon').text(reactionTypes[data.user_reaction].emoji);
				$trigger.find('.reaction-text').text(reactionTypes[data.user_reaction].label);
				$trigger.data('current-reaction', data.user_reaction);
				$trigger.addClass('reacted');
			} else {
				$trigger.find('.reaction-icon').text('👍');
				$trigger.find('.reaction-text').text('Like');
				$trigger.data('current-reaction', 'like');
				$trigger.removeClass('reacted');
			}

			// Highlight user's selected reaction in picker
			$('#reaction-picker-' + postId + ' .reaction-option').removeClass('selected');
			if (data.user_reaction) {
				$('#reaction-picker-' + postId + ' .reaction-option[data-reaction="' + data.user_reaction + '"]').addClass('selected');
			}
		}
	</script>

	<style>
		/* Reactions Styles */
		.job-engagement {
			font-size: 0.9rem;
			color: #666;
			padding: 8px 0;
			border-top: 1px solid #eee;
			margin-top: 10px;
		}

		.views-count {
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}

		.views-count i {
			color: #888;
		}

		.reactions-summary {
			display: inline-flex;
			align-items: center;
		}

		.reactions-summary .total-reactions {
			display: inline-flex;
			align-items: center;
			gap: 4px;
		}

		.reactions-summary .reaction-emojis {
			font-size: 1rem;
		}

		.reactions-summary .reaction-count {
			color: #666;
			font-size: 0.85rem;
		}

		.reaction-buttons {
			position: relative;
			display: inline-block;
		}

		.reaction-trigger {
			border-radius: 20px;
			padding: 4px 12px;
			font-size: 0.85rem;
			transition: all 0.2s ease;
		}

		.reaction-trigger:hover {
			background-color: #f0f0f0;
		}

		.reaction-trigger.reacted {
			background-color: #e7f3ff;
			border-color: #0d6efd;
			color: #0d6efd;
		}

		.reaction-trigger .reaction-icon {
			margin-right: 4px;
		}

		.reaction-picker {
			position: absolute;
			bottom: 100%;
			left: 0;
			background: #fff;
			border-radius: 30px;
			box-shadow: 0 2px 12px rgba(0,0,0,0.15);
			padding: 6px 10px;
			display: flex;
			gap: 4px;
			z-index: 1000;
			margin-bottom: 5px;
			animation: fadeInUp 0.2s ease;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.reaction-option {
			background: none;
			border: none;
			font-size: 1.5rem;
			cursor: pointer;
			padding: 5px 8px;
			border-radius: 50%;
			transition: transform 0.15s ease, background-color 0.15s ease;
		}

		.reaction-option:hover {
			transform: scale(1.3);
			background-color: #f0f0f0;
		}

		.reaction-option.selected {
			background-color: #e7f3ff;
			transform: scale(1.1);
		}

		/* Mobile responsiveness */
		@media (max-width: 576px) {
			.job-engagement {
				flex-wrap: wrap;
				gap: 10px;
			}

			.reaction-picker {
				left: auto;
				right: 0;
			}
		}
	</style>
@endpush
