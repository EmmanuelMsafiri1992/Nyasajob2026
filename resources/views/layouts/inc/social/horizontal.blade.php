@if (isSocialSharesEnabled())
	<div class="social-media social-share text-center mt-4 mb-4 ms-0 me-0">
		<span class="text-secondary text-opacity-25" data-bs-toggle="tooltip" title="{{ t('share_on_social_media') }}">
			<i class="fas fa-share-alt"></i>
		</span>
		@if (isSocialSharesEnabled('facebook'))
			<a class="facebook" title="{{ t('share_on', ['media' => 'Facebook']) }}">
				<i class="fab fa-facebook-square"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('twitter'))
			<a class="x-twitter" title="{{ t('share_on', ['media' => 'X (Twitter)']) }}">
				<i class="fab fa-twitter-square"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('linkedin'))
			<a class="linkedin" title="{{ t('share_on', ['media' => 'LinkedIn']) }}">
				<i class="fab fa-linkedin"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('whatsapp'))
			<a class="whatsapp" title="{{ t('share_on', ['media' => 'WhatsApp']) }}">
				<i class="fab fa-whatsapp-square"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('telegram'))
			<a class="telegram" title="{{ t('share_on', ['media' => 'Telegram']) }}">
				<i class="fab fa-telegram"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('snapchat'))
			<a class="snapchat" title="{{ t('share_on', ['media' => 'Snapchat']) }}">
				<i class="fab fa-snapchat-square"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('messenger'))
			<a class="messenger"
			   data-fb-app-id="{{ config('settings.social_share.facebook_app_id') }}"
			   title="{{ t('share_on', ['media' => 'Facebook Messenger']) }}"
			>
				<i class="fab fa-facebook-messenger"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('pinterest'))
			<a class="pinterest" title="{{ t('share_on', ['media' => 'Pinterest']) }}">
				<i class="fab fa-pinterest-square"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('vk'))
			<a class="vk" title="{{ t('share_on', ['media' => 'VK (VKontakte)']) }}">
				<i class="fab fa-vk"></i>
			</a>
		@endif
		@if (isSocialSharesEnabled('tumblr'))
			<a class="tumblr" title="{{ t('share_on', ['media' => 'Tumblr']) }}">
				<i class="fab fa-tumblr-square"></i>
			</a>
		@endif
	</div>
@endif
