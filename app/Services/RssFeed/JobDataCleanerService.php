<?php

namespace App\Services\RssFeed;

use App\Models\JobFeedStagedItem;
use App\Models\Category;
use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JobDataCleanerService
{
    protected array $allowedTags = ['p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'h3', 'h4', 'h5'];

    /**
     * Category keywords mapping
     */
    protected array $categoryKeywords = [
        'IT / Software' => ['developer', 'software', 'engineer', 'programming', 'coding', 'web', 'frontend', 'backend', 'fullstack', 'devops', 'cloud', 'database', 'java', 'python', 'javascript', 'php', 'react', 'node', 'angular', 'vue', 'mobile', 'ios', 'android', 'data scientist', 'machine learning', 'ai', 'artificial intelligence'],
        'Marketing' => ['marketing', 'seo', 'digital marketing', 'social media', 'content', 'brand', 'advertising', 'ppc', 'sem', 'growth', 'campaign'],
        'Sales' => ['sales', 'account executive', 'business development', 'bdm', 'account manager', 'revenue', 'quota'],
        'Finance' => ['finance', 'accountant', 'accounting', 'financial', 'bookkeeper', 'cfo', 'controller', 'auditor', 'tax', 'treasury'],
        'Human Resources' => ['hr', 'human resources', 'recruiter', 'recruiting', 'talent', 'people operations', 'hiring', 'employee relations'],
        'Design' => ['designer', 'design', 'ui', 'ux', 'graphic', 'creative', 'visual', 'product designer', 'figma', 'sketch'],
        'Customer Service' => ['customer service', 'support', 'customer success', 'help desk', 'client services', 'call center'],
        'Operations' => ['operations', 'logistics', 'supply chain', 'warehouse', 'procurement', 'inventory'],
        'Healthcare' => ['nurse', 'doctor', 'medical', 'healthcare', 'hospital', 'clinical', 'pharmacy', 'therapist', 'health'],
        'Education' => ['teacher', 'professor', 'instructor', 'tutor', 'education', 'training', 'learning', 'academic'],
        'Legal' => ['lawyer', 'attorney', 'legal', 'paralegal', 'compliance', 'contract', 'litigation'],
        'Engineering' => ['mechanical engineer', 'civil engineer', 'electrical engineer', 'structural', 'construction', 'project engineer'],
        'Administrative' => ['administrative', 'admin', 'assistant', 'secretary', 'receptionist', 'office manager', 'executive assistant'],
    ];

    /**
     * Clean a staged item - process description, resolve location, infer category
     */
    public function cleanStagedItem(JobFeedStagedItem $item): JobFeedStagedItem
    {
        // Clean and format description
        $item->cleaned_description = $this->cleanDescription($item->raw_description, $item->title, $item->company_name);

        // Resolve location to city_id if not set
        if (!$item->city_id && $item->location_raw) {
            $city = $this->resolveLocation($item->location_raw, $item->country_code);
            if ($city) {
                $item->city_id = $city->id;
                $item->country_code = $city->country_code;
            }
        }

        // If still no city, get default city for country
        if (!$item->city_id && $item->country_code) {
            $city = $this->getDefaultCityForCountry($item->country_code);
            if ($city) {
                $item->city_id = $city->id;
            }
        }

        // Infer category if not set
        if (!$item->category_id) {
            $item->category_id = $this->inferCategory($item->title, $item->cleaned_description);
        }

        // Extract salary information
        $salaryData = $this->extractSalary($item->raw_description);
        if ($salaryData['min'] || $salaryData['max']) {
            $item->salary_min = $item->salary_min ?? $salaryData['min'];
            $item->salary_max = $item->salary_max ?? $salaryData['max'];
            $item->currency_code = $item->currency_code ?? $salaryData['currency'];
        }

        // Generate SEO-friendly tags
        if (!$item->tags) {
            $item->tags = $this->generateTags($item->title, $item->cleaned_description);
        }

        $item->save();

        return $item;
    }

    /**
     * Clean and format job description for SEO
     */
    public function cleanDescription(string $raw, ?string $title = null, ?string $company = null): string
    {
        // Remove script and style tags
        $clean = preg_replace('#<script[^>]*>.*?</script>#si', '', $raw);
        $clean = preg_replace('#<style[^>]*>.*?</style>#si', '', $clean);

        // Remove HTML comments
        $clean = preg_replace('/<!--.*?-->/s', '', $clean);

        // Remove embedded logo images at the start (WeWorkRemotely embeds these)
        $clean = preg_replace('#^\s*<img[^>]+>\s*#i', '', $clean);

        // Remove "To apply:" links at the end (often duplicated)
        $clean = preg_replace('#<p>\s*<strong>To apply:</strong>.*?</p>#si', '', $clean);

        // Remove footer links
        $clean = preg_replace('#<p>.*?weworkremotely\.com.*?</p>#si', '', $clean);

        // Remove "Headquarters:" and "URL:" prefix blocks (they clutter the description)
        $clean = preg_replace('#<p>\s*<strong>Headquarters:</strong>.*?</p>#si', '', $clean);

        // Decode HTML entities
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Strip disallowed HTML tags but keep allowed ones
        $clean = strip_tags($clean, '<' . implode('><', $this->allowedTags) . '>');

        // Clean up common cruft
        $clean = $this->removeDescriptionCruft($clean, $title, $company);

        // Convert line breaks to proper HTML
        $clean = $this->formatParagraphs($clean);

        // Remove excessive whitespace
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $clean);
        $clean = preg_replace('/(<p>\s*<\/p>\s*)+/', '', $clean);

        // Remove non-breaking spaces
        $clean = str_replace(['&nbsp;', "\xC2\xA0"], ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);

        // Ensure minimum content length
        $textLength = mb_strlen(strip_tags($clean));
        if ($textLength < 100) {
            $clean = $this->enhanceShortDescription($clean, $title, $company);
        }

        // Add SEO structure
        $clean = $this->addSeoStructure($clean, $title, $company);

        return trim($clean);
    }

    /**
     * Remove common cruft from descriptions
     */
    protected function removeDescriptionCruft(string $description, ?string $title, ?string $company): string
    {
        // Remove title if it appears at the start of description (duplication)
        if ($title) {
            $escapedTitle = preg_quote($title, '#');
            $description = preg_replace("#^\s*{$escapedTitle}\s*#i", '', $description);
            $description = preg_replace("#<strong>\s*{$escapedTitle}\s*</strong>#i", '', $description);
            $description = preg_replace("#<b>\s*{$escapedTitle}\s*</b>#i", '', $description);
            $description = preg_replace("#<h[1-6]>\s*{$escapedTitle}\s*</h[1-6]>#i", '', $description);
        }

        // Remove company name if it appears at the start (duplication)
        if ($company) {
            $escapedCompany = preg_quote($company, '#');
            $description = preg_replace("#^\s*{$escapedCompany}\s*[-:]\s*#i", '', $description);
        }

        // Remove common job board footer text
        $cruftPatterns = [
            '#LI-Remote#i',
            '#LI-Hybrid#i',
            '#LI-Onsite#i',
            '#LI-[A-Z]{2}[0-9]?#',
            'This job was posted by.*',
            'Apply now at.*',
            'Click here to apply.*',
        ];

        foreach ($cruftPatterns as $pattern) {
            $description = preg_replace("#{$pattern}#si", '', $description);
        }

        return trim($description);
    }

    /**
     * Format text into proper paragraphs
     */
    protected function formatParagraphs(string $text): string
    {
        // Convert double newlines to paragraph breaks
        $text = preg_replace('/\n\s*\n/', '</p><p>', $text);

        // Convert single newlines to br tags
        $text = nl2br($text);

        // Wrap in paragraph if not already wrapped
        if (!str_starts_with(trim($text), '<p') && !str_starts_with(trim($text), '<ul') && !str_starts_with(trim($text), '<ol')) {
            $text = '<p>' . $text . '</p>';
        }

        return $text;
    }

    /**
     * Enhance short descriptions with additional content
     */
    protected function enhanceShortDescription(string $description, ?string $title, ?string $company): string
    {
        $enhanced = $description;

        if ($title) {
            $enhanced = "<p><strong>Position:</strong> {$title}</p>" . $enhanced;
        }

        if ($company) {
            $enhanced .= "<p><strong>Company:</strong> {$company}</p>";
        }

        $enhanced .= "<p>For more details about this opportunity and to apply, please use the application link provided.</p>";

        return $enhanced;
    }

    /**
     * Add SEO-friendly structure to description
     */
    protected function addSeoStructure(string $description, ?string $title, ?string $company): string
    {
        $structured = '<div class="job-description" itemscope itemtype="https://schema.org/JobPosting">';

        // Add hidden structured data
        if ($title) {
            $structured .= '<meta itemprop="title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">';
        }
        if ($company) {
            $structured .= '<span itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">';
            $structured .= '<meta itemprop="name" content="' . htmlspecialchars($company, ENT_QUOTES) . '">';
            $structured .= '</span>';
        }

        $structured .= '<div itemprop="description">' . $description . '</div>';

        // Add source attribution
        $structured .= '<p class="job-source text-muted small mt-3">';
        $structured .= '<em>This job was aggregated from external career sources. ';
        $structured .= 'Posted by NyasaJob to help connect talent with opportunities.</em>';
        $structured .= '</p>';

        $structured .= '</div>';

        return $structured;
    }

    /**
     * Resolve location string to city
     */
    public function resolveLocation(string $locationString, ?string $countryCode = null): ?City
    {
        $cacheKey = 'location_resolve_' . md5($locationString . $countryCode);

        return Cache::remember($cacheKey, 3600, function () use ($locationString, $countryCode) {
            // Clean location string
            $location = trim($locationString);
            $location = preg_replace('/[,\-\/]+/', ' ', $location);
            $location = preg_replace('/\s+/', ' ', $location);

            // Extract city name (usually first part before comma)
            $parts = explode(' ', $location);
            $cityName = $parts[0] ?? $location;

            // Try exact match first
            $query = City::query();

            if ($countryCode) {
                $query->where('country_code', $countryCode);
            }

            // Try to find by name
            $city = $query->where('name', 'LIKE', $cityName . '%')
                ->orderByDesc('population')
                ->first();

            if ($city) {
                return $city;
            }

            // Try fuzzy match with full location string
            $city = City::query()
                ->when($countryCode, fn($q) => $q->where('country_code', $countryCode))
                ->where('name', 'LIKE', '%' . $location . '%')
                ->orderByDesc('population')
                ->first();

            return $city;
        });
    }

    /**
     * Get default (largest) city for a country
     */
    public function getDefaultCityForCountry(string $countryCode): ?City
    {
        $cacheKey = "default_city_{$countryCode}";

        return Cache::remember($cacheKey, 86400, function () use ($countryCode) {
            return City::where('country_code', $countryCode)
                ->orderByDesc('population')
                ->first();
        });
    }

    /**
     * Infer category from job title and description
     */
    public function inferCategory(string $title, ?string $description = null): ?int
    {
        $text = mb_strtolower($title . ' ' . ($description ?? ''));

        // Get all categories from database
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::where('parent_id', 0)
                ->orWhereNull('parent_id')
                ->pluck('name', 'id')
                ->toArray();
        });

        $bestMatch = null;
        $bestScore = 0;

        foreach ($this->categoryKeywords as $categoryName => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($text, mb_strtolower($keyword))) {
                    $score++;
                    // Title matches are worth more
                    if (str_contains(mb_strtolower($title), mb_strtolower($keyword))) {
                        $score += 2;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $categoryName;
            }
        }

        // Find category ID by name
        if ($bestMatch) {
            foreach ($categories as $id => $name) {
                if (str_contains(mb_strtolower($name), mb_strtolower($bestMatch)) ||
                    str_contains(mb_strtolower($bestMatch), mb_strtolower($name))) {
                    return $id;
                }
            }
        }

        // Return first category as fallback
        return array_key_first($categories);
    }

    /**
     * Extract salary information from text
     */
    public function extractSalary(string $text): array
    {
        $result = ['min' => null, 'max' => null, 'currency' => 'USD'];

        // Common salary patterns
        $patterns = [
            // $50,000 - $70,000 or $50k - $70k
            '/\$\s*(\d{1,3}(?:,\d{3})*(?:\.\d{2})?)\s*[k]?\s*[-–to]+\s*\$?\s*(\d{1,3}(?:,\d{3})*(?:\.\d{2})?)\s*[k]?/i',
            // USD 50000 - 70000
            '/(USD|EUR|GBP|MWK|ZAR)\s*(\d{1,3}(?:,\d{3})*)\s*[-–to]+\s*(\d{1,3}(?:,\d{3})*)/i',
            // 50,000 - 70,000 USD
            '/(\d{1,3}(?:,\d{3})*)\s*[-–to]+\s*(\d{1,3}(?:,\d{3})*)\s*(USD|EUR|GBP|MWK|ZAR)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                if (count($matches) >= 3) {
                    $val1 = $this->parseSalaryValue($matches[1] ?? $matches[2]);
                    $val2 = $this->parseSalaryValue($matches[2] ?? $matches[3]);

                    $result['min'] = min($val1, $val2);
                    $result['max'] = max($val1, $val2);

                    // Extract currency if present
                    if (isset($matches[3]) && preg_match('/USD|EUR|GBP|MWK|ZAR/i', $matches[3], $currMatch)) {
                        $result['currency'] = strtoupper($currMatch[0]);
                    } elseif (isset($matches[1]) && preg_match('/USD|EUR|GBP|MWK|ZAR/i', $matches[1], $currMatch)) {
                        $result['currency'] = strtoupper($currMatch[0]);
                    }

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Parse salary value string to number
     */
    protected function parseSalaryValue(string $value): ?float
    {
        // Remove currency symbols and commas
        $value = preg_replace('/[^0-9.k]/i', '', $value);

        if (empty($value)) {
            return null;
        }

        // Handle k suffix (thousands)
        if (str_contains(mb_strtolower($value), 'k')) {
            $value = str_replace(['k', 'K'], '', $value);
            return (float)$value * 1000;
        }

        return (float)$value;
    }

    /**
     * Generate SEO-friendly tags from title and description
     */
    public function generateTags(string $title, ?string $description = null): string
    {
        $text = mb_strtolower($title . ' ' . ($description ?? ''));

        // Common job-related keywords to extract
        $tagKeywords = [
            'remote', 'hybrid', 'onsite', 'full-time', 'part-time', 'contract', 'freelance',
            'senior', 'junior', 'lead', 'manager', 'director', 'intern', 'entry-level',
            'developer', 'engineer', 'designer', 'analyst', 'consultant', 'specialist',
            'marketing', 'sales', 'finance', 'hr', 'operations', 'support',
            'javascript', 'python', 'java', 'php', 'react', 'node', 'sql', 'aws',
        ];

        $foundTags = [];

        foreach ($tagKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                $foundTags[] = ucfirst($keyword);
            }
        }

        // Add words from title
        $titleWords = explode(' ', $title);
        foreach ($titleWords as $word) {
            $word = trim($word, '.,!?');
            if (mb_strlen($word) > 3 && !in_array(mb_strtolower($word), ['the', 'and', 'for', 'with', 'from'])) {
                $foundTags[] = ucfirst(mb_strtolower($word));
            }
        }

        // Limit to 10 unique tags
        $foundTags = array_unique($foundTags);
        $foundTags = array_slice($foundTags, 0, 10);

        return implode(',', $foundTags);
    }
}
