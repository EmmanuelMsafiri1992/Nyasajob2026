<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\User;

class FutureSkillsCourses3Seeder extends Seeder
{
    private $instructor;

    public function run(): void
    {
        $this->instructor = User::where('is_admin', 1)->first() ?? User::first();

        // Course 31: Mobile App Development Basics
        if (!Course::where('slug', 'mobile-app-development-basics')->exists()) {
            $this->createCourse31();
            $this->command->info("Course 31 created: Mobile App Development Basics");
        } else {
            $this->command->info("Course 31 skipped (already exists)");
        }

        // Course 32: Web Performance Optimization
        if (!Course::where('slug', 'web-performance-optimization')->exists()) {
            $this->createCourse32();
            $this->command->info("Course 32 created: Web Performance Optimization");
        } else {
            $this->command->info("Course 32 skipped (already exists)");
        }

        // Course 33: Network Security Essentials
        if (!Course::where('slug', 'network-security-essentials')->exists()) {
            $this->createCourse33();
            $this->command->info("Course 33 created: Network Security Essentials");
        } else {
            $this->command->info("Course 33 skipped (already exists)");
        }

        // Course 34: Cloud Security Fundamentals
        if (!Course::where('slug', 'cloud-security-fundamentals')->exists()) {
            $this->createCourse34();
            $this->command->info("Course 34 created: Cloud Security Fundamentals");
        } else {
            $this->command->info("Course 34 skipped (already exists)");
        }

        // Course 35: Kubernetes Essentials
        if (!Course::where('slug', 'kubernetes-essentials')->exists()) {
            $this->createCourse35();
            $this->command->info("Course 35 created: Kubernetes Essentials");
        } else {
            $this->command->info("Course 35 skipped (already exists)");
        }

        // Course 36: Infrastructure as Code
        if (!Course::where('slug', 'infrastructure-as-code')->exists()) {
            $this->createCourse36();
            $this->command->info("Course 36 created: Infrastructure as Code");
        } else {
            $this->command->info("Course 36 skipped (already exists)");
        }

        // Course 37: Serverless Computing
        if (!Course::where('slug', 'serverless-computing')->exists()) {
            $this->createCourse37();
            $this->command->info("Course 37 created: Serverless Computing");
        } else {
            $this->command->info("Course 37 skipped (already exists)");
        }

        // Course 38: Introduction to DevOps
        if (!Course::where('slug', 'introduction-to-devops')->exists()) {
            $this->createCourse38();
            $this->command->info("Course 38 created: Introduction to DevOps");
        } else {
            $this->command->info("Course 38 skipped (already exists)");
        }

        // Course 39: Natural Language Processing
        if (!Course::where('slug', 'natural-language-processing-essentials')->exists()) {
            $this->createCourse39();
            $this->command->info("Course 39 created: Natural Language Processing Essentials");
        } else {
            $this->command->info("Course 39 skipped (already exists)");
        }

        // Course 40: Computer Vision Basics
        if (!Course::where('slug', 'computer-vision-basics')->exists()) {
            $this->createCourse40();
            $this->command->info("Course 40 created: Computer Vision Basics");
        } else {
            $this->command->info("Course 40 skipped (already exists)");
        }
    }

    private function createCourse31()
    {
        $course = Course::create([
            'title' => 'Mobile App Development Basics',
            'slug' => 'mobile-app-development-basics',
            'description' => 'Learn the fundamentals of mobile app development for iOS and Android platforms. Explore cross-platform frameworks and build your first mobile application.',
            'objectives' => "- Understand mobile development landscape\n- Set up development environment\n- Build apps with React Native\n- Implement navigation and state management\n- Connect apps to APIs",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Introduction to Mobile Development',
            'description' => 'Overview of mobile development landscape',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Mobile Development Landscape',
            'content' => "<h2>Mobile Development Landscape</h2><p>The mobile development ecosystem offers multiple paths to creating apps.</p><h3>Native Development</h3><ul><li><strong>iOS:</strong> Swift, Objective-C with Xcode</li><li><strong>Android:</strong> Kotlin, Java with Android Studio</li><li>Best performance and platform integration</li></ul><h3>Cross-Platform Development</h3><ul><li><strong>React Native:</strong> JavaScript/TypeScript</li><li><strong>Flutter:</strong> Dart language</li><li><strong>Xamarin:</strong> C# and .NET</li><li>Single codebase for multiple platforms</li></ul><h3>Hybrid Apps</h3><ul><li><strong>Ionic:</strong> Web technologies in native wrapper</li><li><strong>Cordova/PhoneGap:</strong> HTML5 apps</li></ul><h3>Choosing Your Path</h3><ol><li>Consider target platforms</li><li>Evaluate team skills</li><li>Assess performance requirements</li><li>Review budget and timeline</li></ol>",
            'type' => 'text',
            'duration_minutes' => 20,
            'order' => 1,
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Setting Up Your Development Environment',
            'content' => "<h2>Development Environment Setup</h2><h3>For React Native</h3><pre><code># Install Node.js first\nnpm install -g react-native-cli\n\n# Create new project\nnpx react-native init MyFirstApp</code></pre><h3>For Flutter</h3><pre><code># Download Flutter SDK\n# Add to PATH\nflutter doctor  # Check setup\nflutter create my_app</code></pre><h3>Essential Tools</h3><ol><li><strong>Code Editor:</strong> VS Code with extensions</li><li><strong>Emulators:</strong> Android Studio emulator, iOS Simulator</li><li><strong>Device Testing:</strong> Physical devices for final testing</li></ol><h3>Android Setup</h3><ul><li>Install Android Studio</li><li>Configure Android SDK</li><li>Set up virtual devices</li></ul><h3>iOS Setup (Mac only)</h3><ul><li>Install Xcode from App Store</li><li>Accept licenses</li><li>Install iOS Simulator</li></ul>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'React Native Fundamentals',
            'description' => 'Core concepts of React Native development',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'React Native Components',
            'content' => "<h2>Core React Native Components</h2><h3>Basic Components</h3><pre><code>import { View, Text, Image, ScrollView } from 'react-native';\n\nconst MyComponent = () => (\n  &lt;View style={styles.container}&gt;\n    &lt;Text style={styles.title}&gt;Hello Mobile!&lt;/Text&gt;\n    &lt;Image source={{uri: 'https://example.com/image.png'}} /&gt;\n  &lt;/View&gt;\n);</code></pre><h3>Common Components</h3><ul><li><strong>View:</strong> Container component (like div)</li><li><strong>Text:</strong> Display text</li><li><strong>Image:</strong> Display images</li><li><strong>ScrollView:</strong> Scrollable container</li><li><strong>TextInput:</strong> User input field</li><li><strong>TouchableOpacity:</strong> Clickable element</li></ul><h3>Styling</h3><pre><code>const styles = StyleSheet.create({\n  container: {\n    flex: 1,\n    padding: 20,\n    backgroundColor: '#fff',\n  },\n  title: {\n    fontSize: 24,\n    fontWeight: 'bold',\n  },\n});</code></pre>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Navigation in React Native',
            'content' => "<h2>React Navigation</h2><h3>Installation</h3><pre><code>npm install @react-navigation/native\nnpm install @react-navigation/stack</code></pre><h3>Stack Navigator</h3><pre><code>import { NavigationContainer } from '@react-navigation/native';\nimport { createStackNavigator } from '@react-navigation/stack';\n\nconst Stack = createStackNavigator();\n\nfunction App() {\n  return (\n    &lt;NavigationContainer&gt;\n      &lt;Stack.Navigator&gt;\n        &lt;Stack.Screen name=\"Home\" component={HomeScreen} /&gt;\n        &lt;Stack.Screen name=\"Details\" component={DetailsScreen} /&gt;\n      &lt;/Stack.Navigator&gt;\n    &lt;/NavigationContainer&gt;\n  );\n}</code></pre><h3>Navigating Between Screens</h3><pre><code>// In HomeScreen\nnavigation.navigate('Details', { itemId: 42 });\n\n// In DetailsScreen\nconst { itemId } = route.params;</code></pre>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Building Your First App',
            'description' => 'Practical app development skills',
            'order' => 3,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'State Management',
            'content' => "<h2>Managing State in Mobile Apps</h2><h3>useState Hook</h3><pre><code>import { useState } from 'react';\n\nconst Counter = () => {\n  const [count, setCount] = useState(0);\n  \n  return (\n    &lt;View&gt;\n      &lt;Text&gt;Count: {count}&lt;/Text&gt;\n      &lt;Button title=\"+\" onPress={() => setCount(count + 1)} /&gt;\n    &lt;/View&gt;\n  );\n};</code></pre><h3>useEffect for Side Effects</h3><pre><code>useEffect(() => {\n  // Fetch data on mount\n  fetchData();\n  \n  return () => {\n    // Cleanup\n  };\n}, [dependencies]);</code></pre><h3>Global State Options</h3><ol><li><strong>Context API:</strong> Built-in React</li><li><strong>Redux:</strong> Complex state management</li><li><strong>MobX:</strong> Observable state</li><li><strong>Zustand:</strong> Lightweight alternative</li></ol>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'API Integration and Data Fetching',
            'content' => "<h2>Fetching Data in Mobile Apps</h2><h3>Using Fetch API</h3><pre><code>const fetchUsers = async () => {\n  try {\n    const response = await fetch('https://api.example.com/users');\n    const data = await response.json();\n    setUsers(data);\n  } catch (error) {\n    console.error('Error:', error);\n  }\n};</code></pre><h3>Display Data in List</h3><pre><code>import { FlatList } from 'react-native';\n\n&lt;FlatList\n  data={users}\n  keyExtractor={item => item.id.toString()}\n  renderItem={({ item }) => (\n    &lt;View style={styles.item}&gt;\n      &lt;Text&gt;{item.name}&lt;/Text&gt;\n    &lt;/View&gt;\n  )}\n/&gt;</code></pre><h3>Loading States</h3><pre><code>if (loading) {\n  return &lt;ActivityIndicator size=\"large\" /&gt;;\n}</code></pre><p>Always handle network errors gracefully with user feedback.</p>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse32()
    {
        $course = Course::create([
            'title' => 'Web Performance Optimization',
            'slug' => 'web-performance-optimization',
            'description' => 'Master techniques to make websites lightning fast. Learn about Core Web Vitals, caching strategies, image optimization, and performance monitoring.',
            'objectives' => "- Understand Core Web Vitals\n- Create performance budgets\n- Optimize images and assets\n- Implement caching strategies\n- Monitor and improve performance",
            'price' => 39.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Understanding Web Performance',
            'description' => 'Core concepts of web performance',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Core Web Vitals Explained',
            'content' => "<h2>Core Web Vitals</h2><p>Google's essential metrics for user experience.</p><h3>The Three Pillars</h3><h4>1. Largest Contentful Paint (LCP)</h4><ul><li>Measures loading performance</li><li>Target: &lt; 2.5 seconds</li><li>Marks when main content is visible</li></ul><h4>2. First Input Delay (FID)</h4><ul><li>Measures interactivity</li><li>Target: &lt; 100 milliseconds</li><li>Time until page responds to input</li></ul><h4>3. Cumulative Layout Shift (CLS)</h4><ul><li>Measures visual stability</li><li>Target: &lt; 0.1</li><li>Prevents unexpected layout shifts</li></ul><h3>Measuring Tools</h3><ol><li><strong>Lighthouse:</strong> Built into Chrome DevTools</li><li><strong>PageSpeed Insights:</strong> Online analysis</li><li><strong>Web Vitals Extension:</strong> Real-time monitoring</li><li><strong>Search Console:</strong> Field data from real users</li></ol>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Performance Budget',
            'content' => "<h2>Creating a Performance Budget</h2><h3>What is a Performance Budget?</h3><p>A set of limits on metrics that affect site performance.</p><h3>Types of Budgets</h3><h4>Quantity-Based</h4><ul><li>Maximum JavaScript: 300KB</li><li>Maximum images: 1MB total</li><li>Maximum requests: 50</li></ul><h4>Timing-Based</h4><ul><li>Time to Interactive: &lt; 3s</li><li>First Contentful Paint: &lt; 1.5s</li></ul><h4>Rule-Based</h4><ul><li>Lighthouse score: &gt; 90</li><li>No render-blocking resources</li></ul><h3>Implementation</h3><pre><code>// budget.json\n{\n  \"budgets\": [{\n    \"resourceTypes\": [\n      { \"type\": \"script\", \"budget\": 300 },\n      { \"type\": \"image\", \"budget\": 500 }\n    ],\n    \"resourceCounts\": [\n      { \"type\": \"total\", \"budget\": 50 }\n    ]\n  }]\n}</code></pre>",
            'duration_minutes' => 15,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Optimization Techniques',
            'description' => 'Practical optimization methods',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Image Optimization',
            'content' => "<h2>Image Optimization Strategies</h2><h3>Modern Image Formats</h3><ul><li><strong>WebP:</strong> 30% smaller than JPEG</li><li><strong>AVIF:</strong> Even better compression</li><li>Use with fallbacks for older browsers</li></ul><h3>Responsive Images</h3><pre><code>&lt;picture&gt;\n  &lt;source srcset=\"image.avif\" type=\"image/avif\"&gt;\n  &lt;source srcset=\"image.webp\" type=\"image/webp\"&gt;\n  &lt;img src=\"image.jpg\" alt=\"Description\"\n       loading=\"lazy\"\n       width=\"800\" height=\"600\"&gt;\n&lt;/picture&gt;</code></pre><h3>Lazy Loading</h3><pre><code>&lt;img src=\"image.jpg\" loading=\"lazy\" /&gt;</code></pre><h3>Compression Tools</h3><ol><li><strong>Squoosh:</strong> Online tool by Google</li><li><strong>ImageOptim:</strong> Mac application</li><li><strong>Sharp:</strong> Node.js library</li></ol><h3>CDN Optimization</h3><ul><li>Use image CDNs like Cloudinary</li><li>Automatic format conversion</li><li>On-the-fly resizing</li></ul>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'JavaScript Optimization',
            'content' => "<h2>JavaScript Performance</h2><h3>Code Splitting</h3><pre><code>// Dynamic imports\nconst module = await import('./heavyModule.js');\n\n// React lazy loading\nconst HeavyComponent = React.lazy(() => \n  import('./HeavyComponent')\n);</code></pre><h3>Tree Shaking</h3><p>Import only what you need:</p><pre><code>// Bad\nimport _ from 'lodash';\n\n// Good\nimport { debounce } from 'lodash-es';</code></pre><h3>Defer and Async</h3><pre><code>&lt;!-- Defer: Execute after DOM ready --&gt;\n&lt;script src=\"app.js\" defer&gt;&lt;/script&gt;\n\n&lt;!-- Async: Execute when loaded --&gt;\n&lt;script src=\"analytics.js\" async&gt;&lt;/script&gt;</code></pre><h3>Bundle Analysis</h3><pre><code># Webpack bundle analyzer\nnpx webpack-bundle-analyzer stats.json</code></pre><h3>Minification</h3><ul><li>Remove whitespace and comments</li><li>Shorten variable names</li><li>Use Terser for production builds</li></ul>",
            'duration_minutes' => 30,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Caching Strategies',
            'description' => 'Browser and CDN caching techniques',
            'order' => 3,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'Browser and CDN Caching',
            'content' => "<h2>Caching for Performance</h2><h3>Browser Cache Headers</h3><pre><code>Cache-Control: public, max-age=31536000\nCache-Control: no-cache\nCache-Control: private, no-store</code></pre><h3>Caching Strategy by Asset Type</h3><table><tr><th>Asset</th><th>Cache Duration</th><th>Strategy</th></tr><tr><td>HTML</td><td>Short/None</td><td>Revalidate</td></tr><tr><td>CSS/JS</td><td>Long</td><td>Fingerprint</td></tr><tr><td>Images</td><td>Long</td><td>Versioned URLs</td></tr><tr><td>Fonts</td><td>Long</td><td>Immutable</td></tr></table><h3>Service Worker Caching</h3><pre><code>self.addEventListener('fetch', event => {\n  event.respondWith(\n    caches.match(event.request)\n      .then(response => response || fetch(event.request))\n  );\n});</code></pre><h3>CDN Benefits</h3><ol><li>Geographic distribution</li><li>Edge caching</li><li>DDoS protection</li><li>Automatic optimization</li></ol>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse33()
    {
        $course = Course::create([
            'title' => 'Network Security Essentials',
            'slug' => 'network-security-essentials',
            'description' => 'Learn to protect networks from cyber threats. Understand firewalls, VPNs, intrusion detection, and secure network architecture.',
            'objectives' => "- Identify common network threats\n- Configure firewalls effectively\n- Implement VPN solutions\n- Set up intrusion detection systems\n- Design secure network architecture",
            'price' => 59.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Network Security Fundamentals',
            'description' => 'Core network security concepts',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Understanding Network Threats',
            'content' => "<h2>Network Security Threats</h2><h3>Common Attack Types</h3><h4>1. Man-in-the-Middle (MITM)</h4><ul><li>Attacker intercepts communication</li><li>Can read or modify data</li><li>Prevention: Encryption, HTTPS</li></ul><h4>2. DDoS Attacks</h4><ul><li>Overwhelming traffic floods</li><li>Service becomes unavailable</li><li>Prevention: CDNs, rate limiting</li></ul><h4>3. Port Scanning</h4><ul><li>Probing for open ports</li><li>Identifies vulnerabilities</li><li>Prevention: Firewalls, port management</li></ul><h4>4. Packet Sniffing</h4><ul><li>Capturing network traffic</li><li>Stealing credentials</li><li>Prevention: Encryption</li></ul><h3>The CIA Triad</h3><ul><li><strong>Confidentiality:</strong> Data privacy</li><li><strong>Integrity:</strong> Data accuracy</li><li><strong>Availability:</strong> System uptime</li></ul><h3>Defense in Depth</h3><p>Multiple layers of security controls.</p>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Network Protocols and Security',
            'content' => "<h2>Securing Network Protocols</h2><h3>Protocol Security Comparison</h3><table><tr><th>Protocol</th><th>Secure Version</th><th>Port</th></tr><tr><td>HTTP</td><td>HTTPS (TLS)</td><td>443</td></tr><tr><td>FTP</td><td>SFTP/FTPS</td><td>22/990</td></tr><tr><td>Telnet</td><td>SSH</td><td>22</td></tr><tr><td>SMTP</td><td>SMTPS</td><td>465</td></tr></table><h3>TLS/SSL Explained</h3><pre><code>1. Client Hello → Server\n2. Server Hello + Certificate ← Server\n3. Key Exchange → Server\n4. Encrypted Communication ↔</code></pre><h3>DNS Security</h3><ul><li><strong>DNSSEC:</strong> Authenticated DNS responses</li><li><strong>DNS over HTTPS (DoH):</strong> Encrypted queries</li><li><strong>DNS Filtering:</strong> Block malicious domains</li></ul><h3>Secure Network Design</h3><ol><li>Segment networks (VLANs)</li><li>Principle of least privilege</li><li>Regular security audits</li><li>Updated firmware/software</li></ol>",
            'duration_minutes' => 30,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Firewalls and VPNs',
            'description' => 'Network perimeter security',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Firewall Configuration',
            'content' => "<h2>Firewall Fundamentals</h2><h3>Firewall Types</h3><h4>Packet Filtering</h4><ul><li>Inspects individual packets</li><li>Based on IP, port, protocol</li><li>Fast but basic</li></ul><h4>Stateful Inspection</h4><ul><li>Tracks connection state</li><li>Context-aware decisions</li><li>More secure than packet filtering</li></ul><h4>Application Layer</h4><ul><li>Deep packet inspection</li><li>Understands application protocols</li><li>Can detect malware in traffic</li></ul><h3>Basic Rules</h3><pre><code># Allow established connections\niptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT\n\n# Allow SSH\niptables -A INPUT -p tcp --dport 22 -j ACCEPT\n\n# Allow HTTP/HTTPS\niptables -A INPUT -p tcp --dport 80 -j ACCEPT\niptables -A INPUT -p tcp --dport 443 -j ACCEPT\n\n# Drop all other inbound\niptables -A INPUT -j DROP</code></pre><h3>Best Practices</h3><ol><li>Default deny policy</li><li>Allow only necessary traffic</li><li>Log denied connections</li><li>Regular rule reviews</li></ol>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'VPN Technologies',
            'content' => "<h2>Virtual Private Networks</h2><h3>VPN Types</h3><h4>Site-to-Site VPN</h4><ul><li>Connects entire networks</li><li>Used between offices</li><li>Always-on connection</li></ul><h4>Remote Access VPN</h4><ul><li>Individual user connection</li><li>Work from anywhere</li><li>On-demand connection</li></ul><h3>VPN Protocols</h3><h4>OpenVPN</h4><ul><li>Open source</li><li>Highly configurable</li><li>SSL/TLS based</li></ul><h4>WireGuard</h4><ul><li>Modern, fast protocol</li><li>Simpler codebase</li><li>Better performance</li></ul><h4>IPSec</h4><ul><li>Industry standard</li><li>Complex configuration</li><li>Strong security</li></ul><h3>Setting Up VPN</h3><pre><code># WireGuard example\nwg genkey | tee privatekey | wg pubkey > publickey\n\n# Configuration\n[Interface]\nPrivateKey = &lt;private_key&gt;\nAddress = 10.0.0.1/24\n\n[Peer]\nPublicKey = &lt;peer_public_key&gt;\nAllowedIPs = 10.0.0.2/32</code></pre>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Intrusion Detection',
            'description' => 'Detecting and preventing intrusions',
            'order' => 3,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'IDS and IPS Systems',
            'content' => "<h2>Intrusion Detection &amp; Prevention</h2><h3>IDS vs IPS</h3><ul><li><strong>IDS:</strong> Detects and alerts</li><li><strong>IPS:</strong> Detects and blocks</li></ul><h3>Detection Methods</h3><h4>Signature-Based</h4><ul><li>Matches known attack patterns</li><li>Fast and accurate for known threats</li><li>Cannot detect zero-day attacks</li></ul><h4>Anomaly-Based</h4><ul><li>Learns normal behavior</li><li>Detects deviations</li><li>Can find unknown threats</li><li>May have false positives</li></ul><h3>Popular Tools</h3><h4>Snort</h4><pre><code># Example rule\nalert tcp any any -> any 80 \n  (msg:\"SQL Injection Attempt\"; \n   content:\"SELECT\"; nocase; \n   content:\"FROM\"; nocase;)</code></pre><h4>Suricata</h4><ul><li>Multi-threaded IDS/IPS</li><li>High performance</li><li>Compatible with Snort rules</li></ul><h3>Deployment Strategies</h3><ol><li>Network tap or span port</li><li>Inline for IPS mode</li><li>Multiple sensors for coverage</li><li>Centralized management</li></ol>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse34()
    {
        $course = Course::create([
            'title' => 'Cloud Security Fundamentals',
            'slug' => 'cloud-security-fundamentals',
            'description' => 'Secure your cloud infrastructure across AWS, Azure, and GCP. Learn identity management, encryption, compliance, and cloud-native security tools.',
            'objectives' => "- Understand shared responsibility model\n- Implement cloud IAM best practices\n- Configure data encryption\n- Secure cloud networks\n- Monitor for security threats",
            'price' => 59.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Cloud Security Foundations',
            'description' => 'Fundamental cloud security concepts',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Shared Responsibility Model',
            'content' => "<h2>Cloud Shared Responsibility Model</h2><h3>The Concept</h3><p>Security responsibility is shared between cloud provider and customer.</p><h3>Provider Responsibilities</h3><ul><li>Physical data center security</li><li>Network infrastructure</li><li>Hypervisor security</li><li>Hardware maintenance</li></ul><h3>Customer Responsibilities</h3><ul><li>Data encryption</li><li>Access management</li><li>Application security</li><li>OS patching (IaaS)</li><li>Configuration</li></ul><h3>By Service Model</h3><h4>IaaS (EC2, VMs)</h4><ul><li>Customer: Most responsibility</li><li>OS, applications, data, access</li></ul><h4>PaaS (App Engine, Azure App Service)</h4><ul><li>Customer: Application, data, access</li><li>Provider: Runtime, OS, infrastructure</li></ul><h4>SaaS (Office 365, Salesforce)</h4><ul><li>Customer: Data, access controls</li><li>Provider: Everything else</li></ul><h3>Key Takeaway</h3><p>\"You are always responsible for your data and access control.\"</p>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Identity and Access Management',
            'content' => "<h2>Cloud IAM Best Practices</h2><h3>Core Principles</h3><h4>Principle of Least Privilege</h4><p>Grant minimum permissions needed.</p><h4>Role-Based Access Control</h4><pre><code>// AWS IAM Policy\n{\n  \"Version\": \"2012-10-17\",\n  \"Statement\": [{\n    \"Effect\": \"Allow\",\n    \"Action\": [\n      \"s3:GetObject\",\n      \"s3:ListBucket\"\n    ],\n    \"Resource\": [\n      \"arn:aws:s3:::my-bucket/*\"\n    ]\n  }]\n}</code></pre><h3>Multi-Factor Authentication</h3><ul><li>Always enable for privileged accounts</li><li>Use hardware keys when possible</li><li>SMS is better than nothing</li></ul><h3>Service Accounts</h3><ul><li>Use for applications, not users</li><li>Rotate credentials regularly</li><li>Audit permissions frequently</li></ul><h3>Access Reviews</h3><ol><li>Quarterly permission audits</li><li>Remove unused accounts</li><li>Document exceptions</li><li>Automate where possible</li></ol>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Data Protection in the Cloud',
            'description' => 'Protecting data at rest and in transit',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Encryption Strategies',
            'content' => "<h2>Cloud Data Encryption</h2><h3>Encryption Types</h3><h4>At Rest</h4><p>Data stored on disk is encrypted.</p><pre><code># AWS S3 Server-Side Encryption\nimport boto3\n\ns3 = boto3.client('s3')\ns3.put_object(\n    Bucket='my-bucket',\n    Key='sensitive-data.txt',\n    Body=data,\n    ServerSideEncryption='AES256'\n)</code></pre><h4>In Transit</h4><p>Data moving between services.</p><ul><li>Always use HTTPS/TLS</li><li>Verify certificates</li><li>Use TLS 1.2 or higher</li></ul><h4>Client-Side</h4><p>Encrypt before uploading.</p><pre><code>from cryptography.fernet import Fernet\n\nkey = Fernet.generate_key()\ncipher = Fernet(key)\nencrypted = cipher.encrypt(data)</code></pre><h3>Key Management</h3><ul><li><strong>Cloud KMS:</strong> AWS KMS, Azure Key Vault, GCP Cloud KMS</li><li><strong>HSM:</strong> Hardware Security Modules for highest security</li><li><strong>BYOK:</strong> Bring Your Own Keys</li></ul><h3>Best Practices</h3><ol><li>Enable default encryption</li><li>Rotate keys regularly</li><li>Separate keys by environment</li><li>Audit key access</li></ol>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Network Security in Cloud',
            'content' => "<h2>Cloud Network Security</h2><h3>Virtual Private Clouds</h3><p>Isolate your resources in private networks.</p><h4>AWS VPC Configuration</h4><pre><code># Terraform example\nresource \"aws_vpc\" \"main\" {\n  cidr_block = \"10.0.0.0/16\"\n  \n  tags = {\n    Name = \"production-vpc\"\n  }\n}\n\nresource \"aws_subnet\" \"private\" {\n  vpc_id     = aws_vpc.main.id\n  cidr_block = \"10.0.1.0/24\"\n}</code></pre><h3>Security Groups</h3><p>Virtual firewalls for instances.</p><pre><code>resource \"aws_security_group\" \"web\" {\n  ingress {\n    from_port   = 443\n    to_port     = 443\n    protocol    = \"tcp\"\n    cidr_blocks = [\"0.0.0.0/0\"]\n  }\n}</code></pre><h3>Network Segmentation</h3><ol><li>Public subnet: Load balancers</li><li>Private subnet: Application servers</li><li>Data subnet: Databases</li></ol><h3>Additional Controls</h3><ul><li>Network ACLs</li><li>WAF (Web Application Firewall)</li><li>DDoS protection services</li></ul>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse35()
    {
        $course = Course::create([
            'title' => 'Kubernetes Essentials',
            'slug' => 'kubernetes-essentials',
            'description' => 'Master container orchestration with Kubernetes. Learn to deploy, scale, and manage containerized applications in production environments.',
            'objectives' => "- Understand Kubernetes architecture\n- Deploy applications with kubectl\n- Manage configurations and secrets\n- Configure networking and ingress\n- Implement scaling and monitoring",
            'price' => 69.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Kubernetes Architecture',
            'description' => 'Understanding K8s components',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Understanding Kubernetes Components',
            'content' => "<h2>Kubernetes Architecture</h2><h3>Control Plane Components</h3><h4>API Server</h4><ul><li>Front-end for Kubernetes</li><li>All communication goes through it</li><li>RESTful interface</li></ul><h4>etcd</h4><ul><li>Distributed key-value store</li><li>Stores cluster state</li><li>Source of truth</li></ul><h4>Scheduler</h4><ul><li>Assigns pods to nodes</li><li>Considers resources and constraints</li></ul><h4>Controller Manager</h4><ul><li>Runs controller processes</li><li>Node controller, replication controller, etc.</li></ul><h3>Node Components</h3><h4>Kubelet</h4><ul><li>Agent on each node</li><li>Ensures containers are running</li><li>Reports to control plane</li></ul><h4>Kube-proxy</h4><ul><li>Network proxy on each node</li><li>Handles service networking</li></ul><h4>Container Runtime</h4><ul><li>Docker, containerd, CRI-O</li><li>Actually runs containers</li></ul><h3>Cluster Diagram</h3><pre><code>[Control Plane]\n  ├── API Server\n  ├── etcd\n  ├── Scheduler\n  └── Controller Manager\n\n[Worker Nodes]\n  ├── Kubelet\n  ├── Kube-proxy\n  └── Container Runtime</code></pre>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Pods, Deployments, and Services',
            'content' => "<h2>Core Kubernetes Objects</h2><h3>Pods</h3><p>Smallest deployable unit.</p><pre><code>apiVersion: v1\nkind: Pod\nmetadata:\n  name: nginx-pod\nspec:\n  containers:\n  - name: nginx\n    image: nginx:latest\n    ports:\n    - containerPort: 80</code></pre><h3>Deployments</h3><p>Manages ReplicaSets and provides updates.</p><pre><code>apiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: nginx-deployment\nspec:\n  replicas: 3\n  selector:\n    matchLabels:\n      app: nginx\n  template:\n    metadata:\n      labels:\n        app: nginx\n    spec:\n      containers:\n      - name: nginx\n        image: nginx:1.21</code></pre><h3>Services</h3><p>Expose pods to network traffic.</p><pre><code>apiVersion: v1\nkind: Service\nmetadata:\n  name: nginx-service\nspec:\n  selector:\n    app: nginx\n  ports:\n  - port: 80\n    targetPort: 80\n  type: LoadBalancer</code></pre>",
            'duration_minutes' => 30,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Working with kubectl',
            'description' => 'Kubernetes command-line tool',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Essential kubectl Commands',
            'content' => "<h2>kubectl Command Reference</h2><h3>Cluster Information</h3><pre><code># View cluster info\nkubectl cluster-info\n\n# List nodes\nkubectl get nodes\n\n# View component status\nkubectl get componentstatuses</code></pre><h3>Working with Pods</h3><pre><code># List pods\nkubectl get pods\nkubectl get pods -A  # All namespaces\n\n# Describe pod details\nkubectl describe pod &lt;pod-name&gt;\n\n# View logs\nkubectl logs &lt;pod-name&gt;\nkubectl logs -f &lt;pod-name&gt;  # Follow\n\n# Execute command in pod\nkubectl exec -it &lt;pod-name&gt; -- /bin/bash</code></pre><h3>Deployments</h3><pre><code># Apply configuration\nkubectl apply -f deployment.yaml\n\n# Scale deployment\nkubectl scale deployment nginx --replicas=5\n\n# Update image\nkubectl set image deployment/nginx nginx=nginx:1.22\n\n# Rollback\nkubectl rollout undo deployment/nginx</code></pre><h3>Debugging</h3><pre><code># Get events\nkubectl get events --sort-by='.lastTimestamp'\n\n# Port forward\nkubectl port-forward pod/nginx 8080:80</code></pre>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Advanced Kubernetes',
            'description' => 'Configuration, secrets, and networking',
            'order' => 3,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'ConfigMaps and Secrets',
            'content' => "<h2>Configuration Management</h2><h3>ConfigMaps</h3><p>Store non-sensitive configuration.</p><pre><code>apiVersion: v1\nkind: ConfigMap\nmetadata:\n  name: app-config\ndata:\n  DATABASE_HOST: \"postgres-service\"\n  LOG_LEVEL: \"info\"</code></pre><h4>Using ConfigMaps</h4><pre><code>spec:\n  containers:\n  - name: app\n    envFrom:\n    - configMapRef:\n        name: app-config</code></pre><h3>Secrets</h3><p>Store sensitive data (base64 encoded).</p><pre><code>apiVersion: v1\nkind: Secret\nmetadata:\n  name: db-secret\ntype: Opaque\ndata:\n  password: cGFzc3dvcmQxMjM=  # base64 encoded</code></pre><h4>Create Secret from CLI</h4><pre><code>kubectl create secret generic db-secret \\\n  --from-literal=password=mypassword</code></pre><h4>Using Secrets</h4><pre><code>env:\n- name: DB_PASSWORD\n  valueFrom:\n    secretKeyRef:\n      name: db-secret\n      key: password</code></pre><h3>Best Practices</h3><ol><li>Never commit secrets to git</li><li>Use external secret management</li><li>Rotate secrets regularly</li></ol>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'Ingress and Load Balancing',
            'content' => "<h2>Kubernetes Networking</h2><h3>Ingress Controller</h3><p>Manages external access to services.</p><h4>Install NGINX Ingress</h4><pre><code>kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/main/deploy/static/provider/cloud/deploy.yaml</code></pre><h4>Ingress Resource</h4><pre><code>apiVersion: networking.k8s.io/v1\nkind: Ingress\nmetadata:\n  name: app-ingress\n  annotations:\n    nginx.ingress.kubernetes.io/rewrite-target: /\nspec:\n  ingressClassName: nginx\n  rules:\n  - host: app.example.com\n    http:\n      paths:\n      - path: /api\n        pathType: Prefix\n        backend:\n          service:\n            name: api-service\n            port:\n              number: 80\n      - path: /\n        pathType: Prefix\n        backend:\n          service:\n            name: web-service\n            port:\n              number: 80</code></pre><h3>TLS Termination</h3><pre><code>spec:\n  tls:\n  - hosts:\n    - app.example.com\n    secretName: tls-secret</code></pre><h3>Load Balancing Types</h3><ol><li>ClusterIP: Internal only</li><li>NodePort: Exposes on node IP</li><li>LoadBalancer: Cloud provider LB</li></ol>",
            'duration_minutes' => 30,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse36()
    {
        $course = Course::create([
            'title' => 'Infrastructure as Code',
            'slug' => 'infrastructure-as-code',
            'description' => 'Learn to manage infrastructure through code using Terraform and other IaC tools. Automate cloud resource provisioning and configuration management.',
            'objectives' => "- Understand IaC principles and benefits\n- Write Terraform configurations\n- Use variables and outputs\n- Create reusable modules\n- Implement IaC best practices",
            'price' => 59.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Introduction to IaC',
            'description' => 'Understanding Infrastructure as Code',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Why Infrastructure as Code?',
            'content' => "<h2>Infrastructure as Code Fundamentals</h2><h3>What is IaC?</h3><p>Managing infrastructure through machine-readable definition files.</p><h3>Benefits</h3><h4>1. Version Control</h4><ul><li>Track all changes in Git</li><li>Review infrastructure changes</li><li>Rollback when needed</li></ul><h4>2. Consistency</h4><ul><li>Same infrastructure every time</li><li>Eliminate manual errors</li><li>Environment parity</li></ul><h4>3. Documentation</h4><ul><li>Code IS the documentation</li><li>Always up to date</li><li>Self-documenting infrastructure</li></ul><h4>4. Automation</h4><ul><li>Rapid provisioning</li><li>CI/CD integration</li><li>Disaster recovery</li></ul><h3>IaC Tools Comparison</h3><table><tr><th>Tool</th><th>Type</th><th>Cloud</th></tr><tr><td>Terraform</td><td>Declarative</td><td>Multi-cloud</td></tr><tr><td>CloudFormation</td><td>Declarative</td><td>AWS</td></tr><tr><td>Pulumi</td><td>Imperative</td><td>Multi-cloud</td></tr><tr><td>Ansible</td><td>Configuration</td><td>Any</td></tr></table><h3>Declarative vs Imperative</h3><ul><li><strong>Declarative:</strong> Describe desired state</li><li><strong>Imperative:</strong> Describe steps to achieve state</li></ul>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Terraform Fundamentals',
            'description' => 'Getting started with Terraform',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Getting Started with Terraform',
            'content' => "<h2>Terraform Basics</h2><h3>Installation</h3><pre><code># macOS\nbrew install terraform\n\n# Verify\nterraform version</code></pre><h3>Basic Configuration</h3><pre><code># main.tf\nterraform {\n  required_providers {\n    aws = {\n      source  = \"hashicorp/aws\"\n      version = \"~> 5.0\"\n    }\n  }\n}\n\nprovider \"aws\" {\n  region = \"us-east-1\"\n}\n\nresource \"aws_instance\" \"web\" {\n  ami           = \"ami-0c55b159cbfafe1f0\"\n  instance_type = \"t2.micro\"\n\n  tags = {\n    Name = \"web-server\"\n  }\n}</code></pre><h3>Terraform Workflow</h3><pre><code># Initialize\nterraform init\n\n# Preview changes\nterraform plan\n\n# Apply changes\nterraform apply\n\n# Destroy resources\nterraform destroy</code></pre><h3>State Management</h3><ul><li>Terraform tracks resources in state file</li><li>Use remote state for teams</li><li>Never edit state manually</li></ul>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Variables and Outputs',
            'content' => "<h2>Terraform Variables and Outputs</h2><h3>Input Variables</h3><pre><code># variables.tf\nvariable \"instance_type\" {\n  description = \"EC2 instance type\"\n  type        = string\n  default     = \"t2.micro\"\n}\n\nvariable \"environment\" {\n  description = \"Environment name\"\n  type        = string\n}\n\nvariable \"allowed_ports\" {\n  type    = list(number)\n  default = [80, 443]\n}</code></pre><h3>Using Variables</h3><pre><code>resource \"aws_instance\" \"web\" {\n  instance_type = var.instance_type\n  \n  tags = {\n    Environment = var.environment\n  }\n}</code></pre><h3>Outputs</h3><pre><code># outputs.tf\noutput \"instance_ip\" {\n  description = \"Public IP of instance\"\n  value       = aws_instance.web.public_ip\n}\n\noutput \"instance_id\" {\n  value = aws_instance.web.id\n}</code></pre><h3>Setting Variables</h3><pre><code># Command line\nterraform apply -var=\"environment=prod\"\n\n# File (terraform.tfvars)\nenvironment = \"prod\"\ninstance_type = \"t2.small\"</code></pre>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Modules and Best Practices',
            'content' => "<h2>Terraform Modules</h2><h3>What are Modules?</h3><p>Reusable, self-contained packages of Terraform configurations.</p><h3>Module Structure</h3><pre><code>modules/\n└── vpc/\n    ├── main.tf\n    ├── variables.tf\n    ├── outputs.tf\n    └── README.md</code></pre><h3>Creating a Module</h3><pre><code># modules/vpc/main.tf\nresource \"aws_vpc\" \"main\" {\n  cidr_block = var.cidr_block\n  \n  tags = {\n    Name = var.name\n  }\n}\n\nresource \"aws_subnet\" \"public\" {\n  count      = length(var.public_subnets)\n  vpc_id     = aws_vpc.main.id\n  cidr_block = var.public_subnets[count.index]\n}</code></pre><h3>Using Modules</h3><pre><code>module \"vpc\" {\n  source = \"./modules/vpc\"\n  \n  name       = \"production-vpc\"\n  cidr_block = \"10.0.0.0/16\"\n  public_subnets = [\"10.0.1.0/24\", \"10.0.2.0/24\"]\n}\n\n# Access module outputs\noutput \"vpc_id\" {\n  value = module.vpc.vpc_id\n}</code></pre><h3>Best Practices</h3><ol><li>Use remote state with locking</li><li>Organize with workspaces or directories</li><li>Use consistent naming conventions</li><li>Pin provider versions</li></ol>",
            'duration_minutes' => 30,
            'order' => 3,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse37()
    {
        $course = Course::create([
            'title' => 'Serverless Computing',
            'slug' => 'serverless-computing',
            'description' => 'Build scalable applications without managing servers. Learn AWS Lambda, Azure Functions, event-driven architecture, and serverless best practices.',
            'objectives' => "- Understand serverless concepts\n- Create AWS Lambda functions\n- Integrate with API Gateway\n- Build event-driven architectures\n- Implement serverless best practices",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Serverless Concepts',
            'description' => 'Understanding serverless architecture',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Understanding Serverless',
            'content' => "<h2>Serverless Computing</h2><h3>What is Serverless?</h3><ul><li>No server management</li><li>Auto-scaling</li><li>Pay per execution</li><li>Event-driven</li></ul><h3>Serverless vs Traditional</h3><table><tr><th>Aspect</th><th>Traditional</th><th>Serverless</th></tr><tr><td>Scaling</td><td>Manual</td><td>Automatic</td></tr><tr><td>Cost</td><td>Always running</td><td>Pay per use</td></tr><tr><td>Management</td><td>High</td><td>Minimal</td></tr><tr><td>Cold starts</td><td>No</td><td>Yes</td></tr></table><h3>Use Cases</h3><h4>Perfect For</h4><ul><li>API backends</li><li>Data processing</li><li>Scheduled tasks</li><li>Event handling</li><li>Microservices</li></ul><h4>Not Ideal For</h4><ul><li>Long-running processes</li><li>Low-latency requirements</li><li>Stateful applications</li></ul><h3>Major Platforms</h3><ol><li><strong>AWS Lambda:</strong> Most mature</li><li><strong>Azure Functions:</strong> Microsoft ecosystem</li><li><strong>Google Cloud Functions:</strong> GCP integration</li><li><strong>Cloudflare Workers:</strong> Edge computing</li></ol><h3>Function as a Service (FaaS)</h3><p>Code runs in stateless containers triggered by events.</p>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AWS Lambda Deep Dive',
            'description' => 'Building with AWS Lambda',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Creating Lambda Functions',
            'content' => "<h2>AWS Lambda Basics</h2><h3>Simple Lambda Function</h3><pre><code>import json\n\ndef lambda_handler(event, context):\n    name = event.get('name', 'World')\n    \n    return {\n        'statusCode': 200,\n        'body': json.dumps({\n            'message': f'Hello, {name}!'\n        })\n    }</code></pre><h3>JavaScript/Node.js</h3><pre><code>exports.handler = async (event) =&gt; {\n    const name = event.name || 'World';\n    \n    return {\n        statusCode: 200,\n        body: JSON.stringify({\n            message: 'Hello, ' + name + '!'\n        })\n    };\n};</code></pre><h3>Event Sources</h3><ul><li><strong>API Gateway:</strong> HTTP requests</li><li><strong>S3:</strong> File uploads</li><li><strong>DynamoDB:</strong> Data changes</li><li><strong>SQS:</strong> Queue messages</li><li><strong>CloudWatch Events:</strong> Scheduled</li></ul><h3>Configuration</h3><ul><li>Memory: 128MB - 10GB</li><li>Timeout: Up to 15 minutes</li><li>Environment variables</li><li>VPC access</li></ul><h3>Deployment</h3><pre><code># Using AWS CLI\naws lambda create-function \\\n  --function-name my-function \\\n  --runtime python3.11 \\\n  --handler lambda_function.lambda_handler \\\n  --zip-file fileb://function.zip \\\n  --role arn:aws:iam::account:role/lambda-role</code></pre>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'API Gateway Integration',
            'content' => "<h2>Lambda with API Gateway</h2><h3>Creating REST API</h3><pre><code># serverless.yml\nservice: my-api\n\nprovider:\n  name: aws\n  runtime: python3.11\n\nfunctions:\n  hello:\n    handler: handler.hello\n    events:\n      - http:\n          path: hello\n          method: get\n  \n  createUser:\n    handler: handler.create_user\n    events:\n      - http:\n          path: users\n          method: post</code></pre><h3>Handler with API Gateway Event</h3><pre><code>import json\n\ndef create_user(event, context):\n    # Parse request body\n    body = json.loads(event['body'])\n    \n    # Get path/query parameters\n    path_params = event.get('pathParameters', {})\n    query_params = event.get('queryStringParameters', {})\n    \n    # Your logic here\n    user = create_user_in_db(body)\n    \n    return {\n        'statusCode': 201,\n        'headers': {\n            'Content-Type': 'application/json',\n            'Access-Control-Allow-Origin': '*'\n        },\n        'body': json.dumps(user)\n    }</code></pre><h3>CORS Configuration</h3><p>Enable CORS for browser access:</p><pre><code>events:\n  - http:\n      path: users\n      method: post\n      cors: true</code></pre>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse38()
    {
        $course = Course::create([
            'title' => 'Introduction to DevOps',
            'slug' => 'introduction-to-devops',
            'description' => 'Learn DevOps principles, practices, and tools. Understand CI/CD pipelines, automation, monitoring, and the culture of collaboration.',
            'objectives' => "- Understand DevOps principles and culture\n- Learn the DevOps toolchain\n- Build CI/CD pipelines\n- Implement automation practices\n- Monitor and improve processes",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'DevOps Fundamentals',
            'description' => 'Core DevOps concepts',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'What is DevOps?',
            'content' => "<h2>Understanding DevOps</h2><h3>Definition</h3><p>DevOps is a set of practices that combines software development (Dev) and IT operations (Ops).</p><h3>Core Principles</h3><h4>1. Culture</h4><ul><li>Break down silos</li><li>Shared responsibility</li><li>Blame-free environment</li><li>Continuous learning</li></ul><h4>2. Automation</h4><ul><li>Automate repetitive tasks</li><li>Infrastructure as Code</li><li>Automated testing</li><li>Deployment automation</li></ul><h4>3. Measurement</h4><ul><li>Monitor everything</li><li>Data-driven decisions</li><li>Continuous improvement</li></ul><h4>4. Sharing</h4><ul><li>Knowledge sharing</li><li>Collaboration tools</li><li>Transparent processes</li></ul><h3>DevOps Lifecycle</h3><pre><code>[Plan] → [Code] → [Build] → [Test] → [Release] → [Deploy] → [Operate] → [Monitor]\n   ↑                                                                           |\n   └───────────────────────── Feedback ────────────────────────────────────────┘</code></pre><h3>Benefits</h3><ul><li>Faster time to market</li><li>Improved collaboration</li><li>Higher quality releases</li><li>Faster incident recovery</li></ul>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'DevOps Tools Landscape',
            'content' => "<h2>DevOps Toolchain</h2><h3>Version Control</h3><ul><li><strong>Git:</strong> Distributed version control</li><li><strong>GitHub/GitLab:</strong> Hosting and collaboration</li></ul><h3>CI/CD</h3><ul><li><strong>Jenkins:</strong> Open-source automation server</li><li><strong>GitHub Actions:</strong> GitHub-integrated CI/CD</li><li><strong>GitLab CI:</strong> Built into GitLab</li><li><strong>CircleCI:</strong> Cloud-native CI/CD</li></ul><h3>Configuration Management</h3><ul><li><strong>Ansible:</strong> Agentless automation</li><li><strong>Chef:</strong> Ruby-based CM</li><li><strong>Puppet:</strong> Model-driven CM</li></ul><h3>Containerization</h3><ul><li><strong>Docker:</strong> Container runtime</li><li><strong>Kubernetes:</strong> Container orchestration</li></ul><h3>Infrastructure as Code</h3><ul><li><strong>Terraform:</strong> Multi-cloud IaC</li><li><strong>CloudFormation:</strong> AWS IaC</li></ul><h3>Monitoring &amp; Logging</h3><ul><li><strong>Prometheus:</strong> Metrics collection</li><li><strong>Grafana:</strong> Visualization</li><li><strong>ELK Stack:</strong> Logging</li><li><strong>Datadog:</strong> Full observability</li></ul><h3>Collaboration</h3><ul><li><strong>Slack/Teams:</strong> Communication</li><li><strong>Jira:</strong> Project tracking</li><li><strong>Confluence:</strong> Documentation</li></ul><h3>Choosing Tools</h3><ol><li>Start simple</li><li>Consider team skills</li><li>Integration capabilities</li><li>Community support</li></ol>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'CI/CD Pipelines',
            'description' => 'Building automated pipelines',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Building CI/CD Pipelines',
            'content' => "<h2>CI/CD Pipeline Design</h2><h3>Continuous Integration (CI)</h3><p>Automatically build and test on every commit.</p><h3>Continuous Delivery (CD)</h3><p>Automatically prepare releases for deployment.</p><h3>Continuous Deployment</h3><p>Automatically deploy to production.</p><h3>GitHub Actions Example</h3><pre><code>name: CI/CD Pipeline\n\non:\n  push:\n    branches: [main]\n  pull_request:\n    branches: [main]\n\njobs:\n  build:\n    runs-on: ubuntu-latest\n    steps:\n    - uses: actions/checkout@v4\n    \n    - name: Setup Node.js\n      uses: actions/setup-node@v4\n      with:\n        node-version: '20'\n    \n    - name: Install dependencies\n      run: npm ci\n    \n    - name: Run tests\n      run: npm test\n    \n    - name: Build\n      run: npm run build\n\n  deploy:\n    needs: build\n    runs-on: ubuntu-latest\n    if: github.ref == 'refs/heads/main'\n    steps:\n    - name: Deploy to production\n      run: ./deploy.sh</code></pre><h3>Pipeline Best Practices</h3><ol><li>Keep pipelines fast</li><li>Fail fast (run quick tests first)</li><li>Use caching</li><li>Parallelize where possible</li></ol>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse39()
    {
        $course = Course::create([
            'title' => 'Natural Language Processing Essentials',
            'slug' => 'natural-language-processing-essentials',
            'description' => 'Learn to build applications that understand human language. Explore text analysis, sentiment analysis, chatbots, and modern NLP techniques.',
            'objectives' => "- Understand NLP fundamentals\n- Process text with Python libraries\n- Implement sentiment analysis\n- Build text classification models\n- Work with modern NLP tools",
            'price' => 59.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'NLP Foundations',
            'description' => 'Introduction to NLP concepts',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Introduction to NLP',
            'content' => "<h2>Natural Language Processing</h2><h3>What is NLP?</h3><p>Enabling computers to understand, interpret, and generate human language.</p><h3>Applications</h3><ul><li><strong>Chatbots:</strong> Customer service, assistants</li><li><strong>Translation:</strong> Google Translate, DeepL</li><li><strong>Sentiment Analysis:</strong> Social media monitoring</li><li><strong>Search Engines:</strong> Understanding queries</li><li><strong>Summarization:</strong> Article condensation</li><li><strong>Voice Assistants:</strong> Siri, Alexa</li></ul><h3>NLP Pipeline</h3><pre><code>Raw Text → Tokenization → Normalization → Analysis → Output</code></pre><h3>Key Concepts</h3><h4>Tokenization</h4><p>Breaking text into words/sentences.</p><pre><code>text = \"Hello, how are you?\"\ntokens = [\"Hello\", \",\", \"how\", \"are\", \"you\", \"?\"]</code></pre><h4>Stemming &amp; Lemmatization</h4><ul><li>Stemming: running → runn</li><li>Lemmatization: running → run</li></ul><h4>Part-of-Speech Tagging</h4><pre><code>\"The cat sat\" → [(\"The\", DET), (\"cat\", NOUN), (\"sat\", VERB)]</code></pre><h4>Named Entity Recognition</h4><p>Identifying names, places, organizations.</p><h3>Modern NLP</h3><ul><li>Transformer models (BERT, GPT)</li><li>Large Language Models</li><li>Transfer learning</li></ul>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Practical NLP with Python',
            'description' => 'Hands-on NLP implementation',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Text Processing with NLTK',
            'content' => "<h2>NLTK for NLP</h2><h3>Installation</h3><pre><code>pip install nltk</code></pre><h3>Basic Text Processing</h3><pre><code>import nltk\nfrom nltk.tokenize import word_tokenize, sent_tokenize\nfrom nltk.corpus import stopwords\n\n# Download required data\nnltk.download('punkt')\nnltk.download('stopwords')\n\ntext = \"Natural language processing is fascinating. It helps computers understand humans.\"\n\n# Sentence tokenization\nsentences = sent_tokenize(text)\n# ['Natural language processing is fascinating.', 'It helps computers understand humans.']\n\n# Word tokenization\nwords = word_tokenize(text)\n# ['Natural', 'language', 'processing', 'is', ...]</code></pre><h3>Removing Stop Words</h3><pre><code>stop_words = set(stopwords.words('english'))\nfiltered = [w for w in words if w.lower() not in stop_words]\n# ['Natural', 'language', 'processing', 'fascinating', ...]</code></pre><h3>Stemming</h3><pre><code>from nltk.stem import PorterStemmer\n\nstemmer = PorterStemmer()\nwords = ['running', 'runs', 'ran']\nstems = [stemmer.stem(w) for w in words]\n# ['run', 'run', 'ran']</code></pre><h3>Lemmatization</h3><pre><code>from nltk.stem import WordNetLemmatizer\n\nlemmatizer = WordNetLemmatizer()\nlemmatizer.lemmatize('running', pos='v')  # 'run'</code></pre>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Sentiment Analysis',
            'content' => "<h2>Sentiment Analysis</h2><h3>What is Sentiment Analysis?</h3><p>Determining emotional tone of text.</p><h3>Using TextBlob</h3><pre><code>from textblob import TextBlob\n\ntext = \"I love this product! It's amazing.\"\nblob = TextBlob(text)\n\nprint(blob.sentiment)\n# Sentiment(polarity=0.625, subjectivity=0.6)\n\n# Polarity: -1 (negative) to 1 (positive)\n# Subjectivity: 0 (objective) to 1 (subjective)</code></pre><h3>Using Transformers</h3><pre><code>from transformers import pipeline\n\nsentiment = pipeline('sentiment-analysis')\n\nresults = sentiment([\n    \"I love this movie!\",\n    \"This was terrible.\"\n])\n# [{'label': 'POSITIVE', 'score': 0.99},\n#  {'label': 'NEGATIVE', 'score': 0.98}]</code></pre><h3>Building Custom Classifier</h3><pre><code>from sklearn.feature_extraction.text import TfidfVectorizer\nfrom sklearn.naive_bayes import MultinomialNB\n\n# Vectorize text\nvectorizer = TfidfVectorizer()\nX_train = vectorizer.fit_transform(train_texts)\n\n# Train classifier\nclassifier = MultinomialNB()\nclassifier.fit(X_train, train_labels)\n\n# Predict\nX_test = vectorizer.transform(test_texts)\npredictions = classifier.predict(X_test)</code></pre><h3>Applications</h3><ul><li>Social media monitoring</li><li>Product reviews analysis</li><li>Customer feedback processing</li></ul>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }

    private function createCourse40()
    {
        $course = Course::create([
            'title' => 'Computer Vision Basics',
            'slug' => 'computer-vision-basics',
            'description' => 'Learn the fundamentals of computer vision and image processing. Explore image recognition, object detection, and practical applications.',
            'objectives' => "- Understand computer vision concepts\n- Process images with OpenCV\n- Apply image filtering techniques\n- Implement edge detection\n- Build face detection applications",
            'price' => 59.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Computer Vision Fundamentals',
            'description' => 'Introduction to computer vision',
            'order' => 1,
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Introduction to Computer Vision',
            'content' => "<h2>Computer Vision Overview</h2><h3>What is Computer Vision?</h3><p>Teaching computers to interpret and understand visual information.</p><h3>Applications</h3><h4>Everyday Uses</h4><ul><li><strong>Face Recognition:</strong> Phone unlock, security</li><li><strong>Self-Driving Cars:</strong> Object detection</li><li><strong>Medical Imaging:</strong> Disease detection</li><li><strong>Quality Control:</strong> Manufacturing inspection</li><li><strong>Augmented Reality:</strong> Filters, games</li></ul><h3>Key Tasks</h3><h4>Image Classification</h4><p>What is in this image?</p><pre><code>Image → [Cat]</code></pre><h4>Object Detection</h4><p>What objects and where?</p><pre><code>Image → [Cat at (x,y), Dog at (x,y)]</code></pre><h4>Segmentation</h4><p>Pixel-level classification</p><pre><code>Image → Pixel map of objects</code></pre><h4>Pose Estimation</h4><p>Detecting human body positions</p><h3>How Images Work</h3><pre><code># Image = 3D array (height x width x channels)\nimage.shape  # (480, 640, 3) for RGB\n\n# Each pixel has RGB values 0-255\npixel = [255, 0, 0]  # Red</code></pre><h3>Popular Libraries</h3><ul><li>OpenCV: Image processing</li><li>TensorFlow/PyTorch: Deep learning</li><li>Pillow: Basic image handling</li></ul>",
            'duration_minutes' => 20,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => true,
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Image Processing with OpenCV',
            'description' => 'Practical image processing',
            'order' => 2,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'OpenCV Basics',
            'content' => "<h2>Getting Started with OpenCV</h2><h3>Installation</h3><pre><code>pip install opencv-python</code></pre><h3>Reading and Displaying Images</h3><pre><code>import cv2\nimport numpy as np\n\n# Read image\nimg = cv2.imread('photo.jpg')\n\n# Convert BGR to RGB (OpenCV uses BGR)\nimg_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)\n\n# Get image properties\nprint(img.shape)  # (height, width, channels)\n\n# Display image\ncv2.imshow('Image', img)\ncv2.waitKey(0)\ncv2.destroyAllWindows()</code></pre><h3>Basic Operations</h3><pre><code># Resize\nresized = cv2.resize(img, (300, 200))\n\n# Crop\ncropped = img[100:300, 150:400]\n\n# Rotate\nmatrix = cv2.getRotationMatrix2D((width/2, height/2), 45, 1)\nrotated = cv2.warpAffine(img, matrix, (width, height))\n\n# Flip\nflipped = cv2.flip(img, 1)  # Horizontal</code></pre><h3>Color Spaces</h3><pre><code># Convert to grayscale\ngray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)\n\n# Convert to HSV\nhsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)</code></pre><h3>Save Image</h3><pre><code>cv2.imwrite('output.jpg', img)</code></pre>",
            'duration_minutes' => 25,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Image Filtering and Edge Detection',
            'content' => "<h2>Image Filtering Techniques</h2><h3>Blurring</h3><pre><code># Gaussian Blur\nblurred = cv2.GaussianBlur(img, (5, 5), 0)\n\n# Median Blur (good for noise)\nmedian = cv2.medianBlur(img, 5)\n\n# Bilateral Filter (preserves edges)\nbilateral = cv2.bilateralFilter(img, 9, 75, 75)</code></pre><h3>Edge Detection</h3><pre><code># Convert to grayscale first\ngray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)\n\n# Canny Edge Detection\nedges = cv2.Canny(gray, 100, 200)\n\n# Parameters: image, threshold1, threshold2\n# Lower threshold = more edges</code></pre><h3>Thresholding</h3><pre><code># Simple threshold\nret, thresh = cv2.threshold(gray, 127, 255, cv2.THRESH_BINARY)\n\n# Adaptive threshold\nadaptive = cv2.adaptiveThreshold(\n    gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,\n    cv2.THRESH_BINARY, 11, 2\n)</code></pre><h3>Morphological Operations</h3><pre><code>kernel = np.ones((5, 5), np.uint8)\n\n# Erosion\neroded = cv2.erode(img, kernel, iterations=1)\n\n# Dilation\ndilated = cv2.dilate(img, kernel, iterations=1)\n\n# Opening (erosion then dilation)\nopening = cv2.morphologyEx(img, cv2.MORPH_OPEN, kernel)</code></pre>",
            'duration_minutes' => 25,
            'order' => 2,
            'type' => 'text',
            'is_free_preview' => false,
        ]);

        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Object Detection',
            'description' => 'Detecting objects in images',
            'order' => 3,
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'Face Detection',
            'content' => "<h2>Face Detection with OpenCV</h2><h3>Haar Cascade Classifier</h3><pre><code>import cv2\n\n# Load pre-trained classifier\nface_cascade = cv2.CascadeClassifier(\n    cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'\n)\n\n# Read image\nimg = cv2.imread('photo.jpg')\ngray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)\n\n# Detect faces\nfaces = face_cascade.detectMultiScale(\n    gray,\n    scaleFactor=1.1,\n    minNeighbors=5,\n    minSize=(30, 30)\n)\n\n# Draw rectangles\nfor (x, y, w, h) in faces:\n    cv2.rectangle(img, (x, y), (x+w, y+h), (0, 255, 0), 2)\n\nprint(f'Found {len(faces)} faces')</code></pre><h3>Real-time Face Detection</h3><pre><code>cap = cv2.VideoCapture(0)  # Webcam\n\nwhile True:\n    ret, frame = cap.read()\n    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)\n    \n    faces = face_cascade.detectMultiScale(gray, 1.1, 5)\n    \n    for (x, y, w, h) in faces:\n        cv2.rectangle(frame, (x, y), (x+w, y+h), (0, 255, 0), 2)\n    \n    cv2.imshow('Face Detection', frame)\n    \n    if cv2.waitKey(1) &amp; 0xFF == ord('q'):\n        break\n\ncap.release()\ncv2.destroyAllWindows()</code></pre><h3>Modern Approach: Deep Learning</h3><p>For better accuracy, use models like MTCNN, RetinaFace, or YOLO.</p>",
            'duration_minutes' => 30,
            'order' => 1,
            'type' => 'text',
            'is_free_preview' => false,
        ]);
    }
}
