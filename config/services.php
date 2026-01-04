<?php

return [
	
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */
	
	/*
	 * Mail providers
	 */
    'mailgun' => [
        'domain'   => null,
        'secret'   => null,
		'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'), // 'api.eu.mailgun.net' - If you are not using the United States Mailgun region
        'scheme'   => 'https',
    ],
	
	'postmark' => [
		'token' => env('POSTMARK_TOKEN', ''),
	],
	
    'ses' => [
        'key'    => null,
        'secret' => null,
        'region' => null,
		'token'  => null, // To utilize AWS temporary credentials via a session token
    ],
	
    'sparkpost' => [
        'secret' => null,
        'guzzle' => [
            'verify' => false,
        ],
    ],
	
    'resend' => [
	    'key' => env('RESEND_API_KEY'),
    ],
	
    'mailersend' => [
	    'api_key' => env('MAILERSEND_API_KEY'),
    ],
	
    'brevo' => [
	    'key' => env('BREVO_API_KEY'),
    ],
	
	/*
	 * Social Auth Providers
	 */
    'facebook' => [
        'client_id'     => null,
        'client_secret' => null,
        'redirect'      => env('APP_URL') . '/auth/facebook/callback',
    ],
    
    'linkedin-openid' => [
	    'client_id'     => null,
	    'client_secret' => null,
	    'redirect'      => env('APP_URL') . '/auth/linkedin/callback',
    ],
	
	// OAuth 2.0
    'twitter-oauth-2' => [
	    'client_id'       => null,
	    'client_secret'   => null,
	    'redirect'        => env('APP_URL') . '/auth/twitter-oauth-2/callback',
    ],
	
	// OAuth 1.0
    'twitter' => [
	    'client_id'       => null,
	    'client_secret'   => null,
	    'redirect'        => env('APP_URL') . '/auth/twitter/callback',
    ],
	
    'google' => [
        'client_id'     => null,
        'client_secret' => null,
        'redirect'      => env('APP_URL') . '/auth/google/callback',
    ],
	
	/*
	 * Payment gateways
	 * See payment plugins config files
	 */
	'paypal' => [
		'client_id' => env('PAYPAL_CLIENT_ID'),
		'client_secret' => env('PAYPAL_CLIENT_SECRET'),
		'mode' => env('PAYPAL_MODE', 'sandbox'), // 'sandbox' or 'live'
		'premium_plan_id' => env('PAYPAL_PREMIUM_PLAN_ID'), // Create via PayPal Developer Dashboard
	],
	
	/*
	 * Other
	 */
    'slack' => [
	    'notifications' => [
		    'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
		    'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
	    ],
    ],
	
	'googlemaps' => [
		'key' => null, //-> for Google Map JavaScript & Embeded
	],

	/*
	 * Job Board APIs
	 */
	'adzuna' => [
		'app_id' => env('ADZUNA_APP_ID'),
		'app_key' => env('ADZUNA_APP_KEY'),
		'base_url' => 'https://api.adzuna.com/v1/api/jobs',
		// Supported countries: gb, us, au, at, br, ca, de, fr, in, it, nl, nz, pl, ru, sg, za
		'countries' => ['gb', 'us', 'au', 'at', 'br', 'ca', 'de', 'fr', 'in', 'it', 'nl', 'nz', 'pl', 'sg', 'za'],
	],

	'jooble' => [
		'api_key' => env('JOOBLE_API_KEY'),
		'base_url' => 'https://jooble.org/api',
	],

	'careerjet' => [
		'affiliate_id' => env('CAREERJET_AFFILIATE_ID'),
		'base_url' => 'http://public.api.careerjet.net/search',
	],

];
