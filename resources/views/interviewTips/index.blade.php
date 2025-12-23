@extends('layouts.master')
<?php
	$addListingUrl = (isset($addListingUrl)) ? $addListingUrl : \App\Helpers\UrlGen::addPost();
	$addListingAttr = '';
	if (!auth()->check()) {
		if (config('settings.single.guests_can_post_listings') != '1') {
			$addListingUrl = '#quickLogin';
			$addListingAttr = ' data-bs-toggle="modal"';
		}
	}
?>
@section('content')
@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Recruitment Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=BAAv1oTL8XCRFUFGfT6cq35A9_jskMsvL8q_s9i0CcDGNikpl9vCClFIp6gGAt-e0cVHwa4BOv6H3DTpRc&components=hosted-buttons&disable-funding=venmo&currency=USD"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --background: #f8f9fa;
            --text: #2c3e50;
            --light-gray: #ecf0f1;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 4rem 2rem;
            text-align: center;
            color: var(--white);
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .service-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        .service-title {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .service-title i {
            color: var(--secondary);
        }

        .service-description {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 2rem;
        }

        .feature-list li {
            margin-bottom: 0.8rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .feature-list li::before {
            content: '→';
            color: var(--secondary);
            position: absolute;
            left: 0;
        }

        .price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .price span {
            font-size: 1rem;
            color: #666;
            font-weight: normal;
        }

        .popular-tag {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--accent);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }
        }

        /* PayPal Button Container Styling */
        [id^="paypal-container"] {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Professional Career Services</h1>
        <p>Elevate your professional presence with our comprehensive career development solutions</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <div class="service-title">
                    <i class="fas fa-file-alt"></i>
                    CV Creation & Updates
                </div>
                <div class="service-description">
                    Stand out from the crowd with a professionally crafted CV that highlights your unique value proposition.
                </div>
                <ul class="feature-list">
                    <li>ATS-optimized format</li>
                    <li>Industry-specific customization</li>
                    <li>Achievement-focused content</li>
                    <li>Modern, clean design</li>
                </ul>
                <div class="price">$50 <span>one-time</span></div>
                <div id="paypal-container-V7N7DLMQK4K3A"></div>
            </div>

            <div class="service-card">
                <div class="service-title">
                    <i class="fab fa-linkedin"></i>
                    LinkedIn Optimization
                </div>
                <div class="service-description">
                    Transform your LinkedIn profile into a powerful networking and career advancement tool.
                </div>
                <ul class="feature-list">
                    <li>SEO-optimized headline</li>
                    <li>Engaging profile summary</li>
                    <li>Strategic keyword placement</li>
                    <li>Network growth strategy</li>
                </ul>
                <div class="price">$40 <span>one-time</span></div>
                <div id="paypal-container-G2LPKK9SUDFH6"></div>
            </div>

            <div class="service-card">
                <div class="service-title">
                    <i class="fas fa-user-tie"></i>
                    Interview Mastery
                </div>
                <div class="service-description">
                    Gain the confidence and skills needed to ace your interviews and secure your dream role.
                </div>
                <ul class="feature-list">
                    <li>Interview strategy session</li>
                    <li>Role-specific preparation</li>
                    <li>Behavioral question coaching</li>
                    <li>Salary negotiation tips</li>
                </ul>
                <div class="price">$30 <span>per session</span></div>
                <div id="paypal-container-SU8F627BJA9PJ"></div>
            </div>

            <div class="service-card">
                <div class="popular-tag">Best Value</div>
                <div class="service-title">
                    <i class="fas fa-star"></i>
                    Complete Career Package
                </div>
                <div class="service-description">
                    Get the complete suite of career services at a special bundle price.
                </div>
                <ul class="feature-list">
                    <li>Professional CV Creation</li>
                    <li>LinkedIn Profile Optimization</li>
                    <li>Interview Preparation Session</li>
                    <li>Priority Support</li>
                </ul>
                <div class="price">$100 <span>save $20</span></div>
                <div id="paypal-container-ZDFUYELFSXYW2"></div>
            </div>
            Note: Once Payment is done, Send Paypment proof to: payments@nyasajob.com 
        </div>
    </div>

    <script>
        window.onload = function() {
            paypal.HostedButtons({
                hostedButtonId: "V7N7DLMQK4K3A",
            }).render("#paypal-container-V7N7DLMQK4K3A");

            paypal.HostedButtons({
                hostedButtonId: "G2LPKK9SUDFH6",
            }).render("#paypal-container-G2LPKK9SUDFH6");

            paypal.HostedButtons({
                hostedButtonId: "SU8F627BJA9PJ",
            }).render("#paypal-container-SU8F627BJA9PJ");

            paypal.HostedButtons({
                hostedButtonId: "ZDFUYELFSXYW2",
            }).render("#paypal-container-ZDFUYELFSXYW2");
        }
    </script>
</body>
</html>


@endsection
@section('after_styles')
@endsection
@section('after_scripts')
@endsection
