<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\User;

class FutureSkillsCourses4Seeder extends Seeder
{
    private $instructor;

    public function run(): void
    {
        $this->instructor = User::where('is_admin', 1)->first() ?? User::first();

        $courses = [
            ['slug' => 'time-management-productivity', 'method' => 'createCourse41', 'name' => 'Time Management & Productivity'],
            ['slug' => 'ethical-hacking-basics', 'method' => 'createCourse42', 'name' => 'Ethical Hacking Basics'],
            ['slug' => 'blockchain-cryptocurrency-basics', 'method' => 'createCourse43', 'name' => 'Blockchain & Cryptocurrency Basics'],
            ['slug' => 'internet-of-things-fundamentals', 'method' => 'createCourse44', 'name' => 'Internet of Things Fundamentals'],
            ['slug' => 'introduction-to-robotics', 'method' => 'createCourse45', 'name' => 'Introduction to Robotics'],
            ['slug' => 'augmented-virtual-reality-basics', 'method' => 'createCourse46', 'name' => 'Augmented & Virtual Reality Basics'],
            ['slug' => 'quantum-computing-introduction', 'method' => 'createCourse47', 'name' => 'Quantum Computing Introduction'],
            ['slug' => 'green-technology-sustainability', 'method' => 'createCourse48', 'name' => 'Green Technology & Sustainability'],
            ['slug' => 'generative-ai-for-creatives', 'method' => 'createCourse49', 'name' => 'Generative AI for Creatives'],
            ['slug' => 'ai-ethics-responsible-ai', 'method' => 'createCourse50', 'name' => 'AI Ethics and Responsible AI'],
        ];

        foreach ($courses as $index => $c) {
            $num = 41 + $index;
            if (!Course::where('slug', $c['slug'])->exists()) {
                $this->{$c['method']}();
                $this->command->info("Course {$num} created: {$c['name']}");
            } else {
                $this->command->info("Course {$num} skipped (already exists)");
            }
        }
    }

    private function createCourse41()
    {
        $course = Course::create([
            'title' => 'Time Management & Productivity',
            'slug' => 'time-management-productivity',
            'description' => 'Master your time and boost productivity. Learn proven techniques for prioritization, focus, eliminating distractions, and achieving work-life balance.',
            'objectives' => "- Identify time wasters and eliminate them\n- Apply proven productivity techniques\n- Set and achieve meaningful goals\n- Create sustainable work habits\n- Achieve better work-life balance",
            'price' => 29.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Understanding Time Management',
            'description' => 'Core concepts of effective time management',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'Why Time Management Matters', 'content' => '<h2>The Value of Time</h2><p>Time is your most valuable, non-renewable resource. Unlike money, you cannot earn more time.</p><h3>Benefits of Good Time Management</h3><ul><li>Reduced stress and anxiety</li><li>Higher quality work</li><li>Better work-life balance</li><li>Increased career success</li><li>More time for what matters</li></ul><h3>The Cost of Poor Time Management</h3><ul><li>Missed deadlines</li><li>Constant firefighting</li><li>Burnout</li><li>Strained relationships</li></ul>'],
            ['title' => 'Common Time Wasters', 'content' => '<h2>Where Does Your Time Go?</h2><h3>Digital Distractions</h3><ul><li>Social media scrolling</li><li>Unnecessary emails</li><li>Notifications</li><li>Multitasking</li></ul><h3>Workplace Time Wasters</h3><ul><li>Unproductive meetings</li><li>Unclear priorities</li><li>Poor delegation</li><li>Perfectionism</li></ul><h3>Exercise: Time Audit</h3><p>Track your time for one week. Record what you do in 30-minute blocks. You will be surprised.</p>'],
            ['title' => 'Setting Goals That Stick', 'content' => '<h2>SMART Goals Framework</h2><ul><li><strong>Specific:</strong> Clear and well-defined</li><li><strong>Measurable:</strong> Quantifiable progress</li><li><strong>Achievable:</strong> Realistic but challenging</li><li><strong>Relevant:</strong> Aligned with values</li><li><strong>Time-bound:</strong> Has a deadline</li></ul><h3>Example</h3><p>Bad: "Get healthier"</p><p>Good: "Walk 10,000 steps daily for 30 days"</p>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Productivity Techniques',
            'description' => 'Proven methods to get more done',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'The Pomodoro Technique', 'content' => '<h2>Work in Focused Sprints</h2><h3>How It Works</h3><ol><li>Choose a task</li><li>Set timer for 25 minutes</li><li>Work with full focus</li><li>Take a 5-minute break</li><li>After 4 pomodoros, take a longer break (15-30 min)</li></ol><h3>Why It Works</h3><ul><li>Creates urgency</li><li>Fights perfectionism</li><li>Reduces mental fatigue</li><li>Makes progress visible</li></ul>'],
            ['title' => 'Time Blocking', 'content' => '<h2>Schedule Your Time Like Money</h2><p>Time blocking means assigning specific tasks to specific time blocks in your calendar.</p><h3>Implementation</h3><ol><li>List all tasks and commitments</li><li>Estimate time needed</li><li>Block time on calendar</li><li>Protect your blocks</li></ol><h3>Types of Blocks</h3><ul><li>Deep work blocks (2-4 hours)</li><li>Admin blocks (email, calls)</li><li>Meeting blocks</li><li>Buffer blocks (unexpected tasks)</li></ul>'],
            ['title' => 'The Eisenhower Matrix', 'content' => '<h2>Prioritize What Matters</h2><h3>The Four Quadrants</h3><table><tr><th></th><th>Urgent</th><th>Not Urgent</th></tr><tr><td>Important</td><td>DO First</td><td>SCHEDULE</td></tr><tr><td>Not Important</td><td>DELEGATE</td><td>ELIMINATE</td></tr></table><h3>Key Insight</h3><p>Most people spend too much time on urgent-but-not-important tasks (Quadrant 3) instead of important-but-not-urgent tasks (Quadrant 2).</p>'],
        ]);
    }

    private function createCourse42()
    {
        $course = Course::create([
            'title' => 'Ethical Hacking Basics',
            'slug' => 'ethical-hacking-basics',
            'description' => 'Learn the fundamentals of ethical hacking and penetration testing. Understand how hackers think and how to protect systems from attacks.',
            'objectives' => "- Understand the hacker mindset\n- Learn common attack techniques\n- Use basic penetration testing tools\n- Identify vulnerabilities in systems\n- Apply ethical hacking principles",
            'price' => 69.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Introduction to Ethical Hacking',
            'description' => 'Fundamentals and legal considerations',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'What is Ethical Hacking?', 'content' => '<h2>White Hat vs Black Hat</h2><h3>Types of Hackers</h3><ul><li><strong>White Hat:</strong> Ethical hackers who help secure systems</li><li><strong>Black Hat:</strong> Malicious hackers who exploit systems</li><li><strong>Grey Hat:</strong> Somewhere in between</li></ul><h3>What Ethical Hackers Do</h3><ul><li>Find vulnerabilities before criminals do</li><li>Test security defenses</li><li>Report findings responsibly</li><li>Help organizations improve security</li></ul><h3>Legal Considerations</h3><p>Always get written permission before testing. Unauthorized hacking is illegal, even with good intentions.</p>'],
            ['title' => 'The Penetration Testing Process', 'content' => '<h2>The Five Phases</h2><h3>1. Reconnaissance</h3><p>Gather information about the target.</p><h3>2. Scanning</h3><p>Identify live hosts, open ports, services.</p><h3>3. Gaining Access</h3><p>Exploit vulnerabilities to gain entry.</p><h3>4. Maintaining Access</h3><p>Establish persistence (in authorized tests only).</p><h3>5. Covering Tracks</h3><p>Understand how attackers hide their presence.</p><h3>Reporting</h3><p>Document findings clearly with remediation steps.</p>'],
            ['title' => 'Setting Up Your Lab', 'content' => '<h2>Practice Safely</h2><h3>Essential Tools</h3><ul><li><strong>Kali Linux:</strong> Security-focused Linux distribution</li><li><strong>VirtualBox/VMware:</strong> Run virtual machines</li><li><strong>Vulnerable VMs:</strong> Metasploitable, DVWA, HackTheBox</li></ul><h3>Lab Setup Steps</h3><ol><li>Install virtualization software</li><li>Download Kali Linux ISO</li><li>Create Kali VM</li><li>Download vulnerable VMs for practice</li><li>Create isolated network</li></ol><h3>Never test on:</h3><ul><li>Systems you do not own</li><li>Systems without written permission</li><li>Production systems (unless authorized)</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Common Attack Techniques',
            'description' => 'Understanding how attacks work',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Information Gathering', 'content' => '<h2>Reconnaissance Techniques</h2><h3>Passive Reconnaissance</h3><ul><li>WHOIS lookups</li><li>DNS enumeration</li><li>Google dorking</li><li>Social media research</li></ul><h3>Active Reconnaissance</h3><ul><li>Port scanning with Nmap</li><li>Service enumeration</li><li>Vulnerability scanning</li></ul><h3>Nmap Basics</h3><pre><code># Basic scan\nnmap target.com\n\n# Service version detection\nnmap -sV target.com\n\n# OS detection\nnmap -O target.com\n\n# Comprehensive scan\nnmap -A target.com</code></pre>'],
            ['title' => 'Web Application Attacks', 'content' => '<h2>Common Web Vulnerabilities</h2><h3>SQL Injection</h3><p>Inserting SQL code into inputs to manipulate databases.</p><pre><code>SELECT * FROM users WHERE id = 1 OR 1=1</code></pre><h3>Cross-Site Scripting (XSS)</h3><p>Injecting malicious scripts into web pages.</p><pre><code>&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code></pre><h3>CSRF (Cross-Site Request Forgery)</h3><p>Tricking users into making unwanted requests.</p><h3>Prevention</h3><ul><li>Input validation</li><li>Parameterized queries</li><li>Output encoding</li><li>Security headers</li></ul>'],
        ]);
    }

    private function createCourse43()
    {
        $course = Course::create([
            'title' => 'Blockchain & Cryptocurrency Basics',
            'slug' => 'blockchain-cryptocurrency-basics',
            'description' => 'Understand blockchain technology and cryptocurrencies. Learn how distributed ledgers work, Bitcoin fundamentals, and the future of decentralized finance.',
            'objectives' => "- Understand blockchain technology\n- Learn how cryptocurrencies work\n- Explore Bitcoin and Ethereum\n- Understand smart contracts\n- Evaluate blockchain use cases",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Understanding Blockchain',
            'description' => 'Core blockchain concepts',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'What is Blockchain?', 'content' => '<h2>A Distributed Ledger</h2><p>Blockchain is a decentralized, immutable record of transactions shared across a network.</p><h3>Key Characteristics</h3><ul><li><strong>Decentralized:</strong> No single point of control</li><li><strong>Transparent:</strong> Anyone can verify transactions</li><li><strong>Immutable:</strong> Cannot be changed once recorded</li><li><strong>Secure:</strong> Cryptographically protected</li></ul><h3>How It Works</h3><ol><li>Transaction is requested</li><li>Transaction broadcast to network</li><li>Network validates transaction</li><li>Transaction added to a block</li><li>Block added to the chain</li></ol>'],
            ['title' => 'Cryptographic Foundations', 'content' => '<h2>The Crypto in Cryptocurrency</h2><h3>Hash Functions</h3><p>Convert any input into a fixed-size output (hash).</p><ul><li>One-way: Cannot reverse</li><li>Deterministic: Same input = same output</li><li>Collision-resistant: Hard to find two inputs with same hash</li></ul><h3>Public Key Cryptography</h3><ul><li><strong>Private Key:</strong> Secret, used to sign transactions</li><li><strong>Public Key:</strong> Shared, used to receive funds</li><li><strong>Digital Signature:</strong> Proves ownership without revealing private key</li></ul>'],
            ['title' => 'Consensus Mechanisms', 'content' => '<h2>How Networks Agree</h2><h3>Proof of Work (PoW)</h3><ul><li>Miners solve complex puzzles</li><li>First to solve adds the block</li><li>Energy-intensive</li><li>Used by Bitcoin</li></ul><h3>Proof of Stake (PoS)</h3><ul><li>Validators stake tokens as collateral</li><li>Selected based on stake amount</li><li>More energy-efficient</li><li>Used by Ethereum 2.0</li></ul><h3>Other Mechanisms</h3><ul><li>Delegated Proof of Stake (DPoS)</li><li>Proof of Authority (PoA)</li><li>Proof of History (PoH)</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Cryptocurrencies',
            'description' => 'Bitcoin, Ethereum, and beyond',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Bitcoin Fundamentals', 'content' => '<h2>The First Cryptocurrency</h2><h3>Key Facts</h3><ul><li>Created in 2009 by Satoshi Nakamoto</li><li>Maximum supply: 21 million BTC</li><li>New block every ~10 minutes</li><li>Halving every 210,000 blocks</li></ul><h3>Bitcoin Wallets</h3><ul><li><strong>Hot Wallets:</strong> Connected to internet (convenient)</li><li><strong>Cold Wallets:</strong> Offline storage (secure)</li><li><strong>Hardware Wallets:</strong> Physical devices (most secure)</li></ul><h3>Bitcoin Transactions</h3><p>Transactions are broadcast to the network, validated by miners, and recorded on the blockchain.</p>'],
            ['title' => 'Ethereum and Smart Contracts', 'content' => '<h2>Programmable Blockchain</h2><h3>What Makes Ethereum Different</h3><p>Ethereum is a platform for decentralized applications, not just a currency.</p><h3>Smart Contracts</h3><p>Self-executing code that runs on the blockchain.</p><pre><code>// Simple Solidity contract\ncontract SimpleStorage {\n    uint256 storedValue;\n    \n    function set(uint256 x) public {\n        storedValue = x;\n    }\n    \n    function get() public view returns (uint256) {\n        return storedValue;\n    }\n}</code></pre><h3>Use Cases</h3><ul><li>Decentralized Finance (DeFi)</li><li>NFTs (Non-Fungible Tokens)</li><li>DAOs (Decentralized Organizations)</li><li>Supply chain tracking</li></ul>'],
        ]);
    }

    private function createCourse44()
    {
        $course = Course::create([
            'title' => 'Internet of Things Fundamentals',
            'slug' => 'internet-of-things-fundamentals',
            'description' => 'Explore the world of connected devices. Learn IoT architecture, sensors, protocols, and how to build smart systems.',
            'objectives' => "- Understand IoT architecture\n- Learn about sensors and actuators\n- Explore IoT communication protocols\n- Build simple IoT projects\n- Address IoT security concerns",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Introduction to IoT',
            'description' => 'Understanding connected devices',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'What is IoT?', 'content' => '<h2>The Internet of Things</h2><p>IoT connects everyday objects to the internet, enabling them to send and receive data.</p><h3>Examples</h3><ul><li>Smart home devices (thermostats, lights)</li><li>Wearables (fitness trackers, smartwatches)</li><li>Industrial sensors (manufacturing, agriculture)</li><li>Smart cities (traffic, utilities)</li><li>Healthcare devices (monitors, implants)</li></ul><h3>IoT Statistics</h3><ul><li>Billions of connected devices worldwide</li><li>Market growing rapidly</li><li>Present in every industry</li></ul>'],
            ['title' => 'IoT Architecture', 'content' => '<h2>Four-Layer Architecture</h2><h3>1. Perception Layer (Devices)</h3><ul><li>Sensors collect data</li><li>Actuators perform actions</li><li>Examples: Temperature sensors, motors</li></ul><h3>2. Network Layer (Connectivity)</h3><ul><li>Data transmission</li><li>Protocols: WiFi, Bluetooth, Zigbee, LoRa</li></ul><h3>3. Processing Layer (Edge/Cloud)</h3><ul><li>Data processing and storage</li><li>Edge computing for low latency</li><li>Cloud for heavy processing</li></ul><h3>4. Application Layer</h3><ul><li>User interfaces</li><li>Business logic</li><li>Analytics and visualization</li></ul>'],
            ['title' => 'Sensors and Actuators', 'content' => '<h2>IoT Building Blocks</h2><h3>Common Sensors</h3><ul><li><strong>Temperature:</strong> DHT11, DHT22, DS18B20</li><li><strong>Motion:</strong> PIR, accelerometer</li><li><strong>Light:</strong> Photoresistor, LDR</li><li><strong>Distance:</strong> Ultrasonic, infrared</li><li><strong>Humidity:</strong> DHT sensors</li></ul><h3>Common Actuators</h3><ul><li><strong>Motors:</strong> DC, servo, stepper</li><li><strong>Relays:</strong> Control high-power devices</li><li><strong>LEDs:</strong> Status indicators</li><li><strong>Displays:</strong> LCD, OLED</li></ul><h3>Popular Platforms</h3><ul><li>Arduino</li><li>Raspberry Pi</li><li>ESP32/ESP8266</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'IoT Protocols',
            'description' => 'Communication in IoT',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'IoT Communication Protocols', 'content' => '<h2>How IoT Devices Talk</h2><h3>Short Range</h3><ul><li><strong>Bluetooth:</strong> Wearables, audio</li><li><strong>WiFi:</strong> High bandwidth, smart home</li><li><strong>Zigbee:</strong> Low power mesh networks</li><li><strong>Z-Wave:</strong> Home automation</li></ul><h3>Long Range</h3><ul><li><strong>LoRaWAN:</strong> Low power, long range</li><li><strong>Cellular (4G/5G):</strong> Mobile IoT</li><li><strong>Satellite:</strong> Remote locations</li></ul><h3>Application Protocols</h3><ul><li><strong>MQTT:</strong> Lightweight publish/subscribe</li><li><strong>CoAP:</strong> Constrained devices</li><li><strong>HTTP/REST:</strong> Standard web APIs</li></ul>'],
            ['title' => 'MQTT Deep Dive', 'content' => '<h2>The IoT Messaging Protocol</h2><h3>MQTT Concepts</h3><ul><li><strong>Broker:</strong> Central message server</li><li><strong>Publisher:</strong> Sends messages to topics</li><li><strong>Subscriber:</strong> Receives messages from topics</li><li><strong>Topic:</strong> Message category (e.g., home/temperature)</li></ul><h3>QoS Levels</h3><ul><li><strong>QoS 0:</strong> At most once (fire and forget)</li><li><strong>QoS 1:</strong> At least once (acknowledged)</li><li><strong>QoS 2:</strong> Exactly once (guaranteed)</li></ul><h3>Example</h3><pre><code># Python MQTT client\nimport paho.mqtt.client as mqtt\n\nclient = mqtt.Client()\nclient.connect("broker.example.com", 1883)\nclient.publish("home/temperature", "22.5")</code></pre>'],
        ]);
    }

    private function createCourse45()
    {
        $course = Course::create([
            'title' => 'Introduction to Robotics',
            'slug' => 'introduction-to-robotics',
            'description' => 'Discover the world of robotics. Learn about robot types, components, programming basics, and applications across industries.',
            'objectives' => "- Understand robotics fundamentals\n- Learn about robot components\n- Explore robot kinematics basics\n- Understand robot programming concepts\n- Identify robotics applications",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Robotics Fundamentals',
            'description' => 'Core robotics concepts',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'What is Robotics?', 'content' => '<h2>The Science of Robots</h2><p>Robotics combines mechanical engineering, electrical engineering, and computer science to create intelligent machines.</p><h3>Types of Robots</h3><ul><li><strong>Industrial Robots:</strong> Manufacturing, assembly</li><li><strong>Service Robots:</strong> Healthcare, cleaning</li><li><strong>Mobile Robots:</strong> Drones, autonomous vehicles</li><li><strong>Humanoid Robots:</strong> Human-like form</li><li><strong>Collaborative Robots (Cobots):</strong> Work alongside humans</li></ul><h3>Key Components</h3><ul><li>Sensors (perception)</li><li>Actuators (action)</li><li>Controllers (brain)</li><li>Power system</li></ul>'],
            ['title' => 'Robot Components', 'content' => '<h2>Building Blocks of Robots</h2><h3>Sensors</h3><ul><li>Cameras (vision)</li><li>LIDAR (distance mapping)</li><li>Force/torque sensors</li><li>Encoders (position)</li><li>IMU (orientation)</li></ul><h3>Actuators</h3><ul><li><strong>Motors:</strong> DC, servo, stepper</li><li><strong>Pneumatics:</strong> Air-powered</li><li><strong>Hydraulics:</strong> Fluid-powered</li></ul><h3>Controllers</h3><ul><li>Microcontrollers (Arduino)</li><li>Single-board computers (Raspberry Pi)</li><li>PLCs (industrial)</li><li>Robot controllers (specialized)</li></ul>'],
            ['title' => 'Robot Applications', 'content' => '<h2>Where Robots Work</h2><h3>Manufacturing</h3><ul><li>Welding</li><li>Painting</li><li>Assembly</li><li>Quality inspection</li></ul><h3>Healthcare</h3><ul><li>Surgical robots</li><li>Rehabilitation</li><li>Pharmacy automation</li><li>Disinfection robots</li></ul><h3>Logistics</h3><ul><li>Warehouse automation</li><li>Last-mile delivery</li><li>Sorting systems</li></ul><h3>Other Areas</h3><ul><li>Agriculture</li><li>Construction</li><li>Space exploration</li><li>Entertainment</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Robot Programming',
            'description' => 'Making robots move and think',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Robot Programming Basics', 'content' => '<h2>How to Program Robots</h2><h3>Programming Methods</h3><ul><li><strong>Teach Pendant:</strong> Manual positioning</li><li><strong>Offline Programming:</strong> Simulation-based</li><li><strong>Text-based:</strong> Code in languages like Python</li><li><strong>Visual Programming:</strong> Block-based (Scratch)</li></ul><h3>ROS (Robot Operating System)</h3><p>Popular framework for robot software development.</p><ul><li>Nodes communicate via topics</li><li>Publisher/subscriber model</li><li>Large ecosystem of packages</li></ul><h3>Simple Robot Control</h3><pre><code># Move forward for 2 seconds\nrobot.move_forward(speed=0.5)\ntime.sleep(2)\nrobot.stop()</code></pre>'],
            ['title' => 'Introduction to ROS', 'content' => '<h2>Robot Operating System</h2><h3>What is ROS?</h3><p>Not an OS, but a framework for writing robot software.</p><h3>Key Concepts</h3><ul><li><strong>Nodes:</strong> Individual processes</li><li><strong>Topics:</strong> Message channels</li><li><strong>Services:</strong> Request/response</li><li><strong>Actions:</strong> Long-running tasks</li></ul><h3>ROS Commands</h3><pre><code># List running nodes\nrosnode list\n\n# List topics\nrostopic list\n\n# Echo messages on a topic\nrostopic echo /camera/image\n\n# Launch a node\nrosrun package_name node_name</code></pre><h3>Getting Started</h3><ol><li>Install ROS on Ubuntu</li><li>Create a catkin workspace</li><li>Write your first node</li><li>Use simulation (Gazebo)</li></ol>'],
        ]);
    }

    private function createCourse46()
    {
        $course = Course::create([
            'title' => 'Augmented & Virtual Reality Basics',
            'slug' => 'augmented-virtual-reality-basics',
            'description' => 'Explore immersive technologies. Learn the differences between AR, VR, and MR, and discover how these technologies are transforming industries.',
            'objectives' => "- Understand AR, VR, and MR concepts\n- Learn about XR hardware\n- Explore development platforms\n- Discover industry applications\n- Evaluate future trends",
            'price' => 39.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Understanding XR Technologies',
            'description' => 'AR, VR, MR explained',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'AR vs VR vs MR', 'content' => '<h2>The Reality Spectrum</h2><h3>Virtual Reality (VR)</h3><ul><li>Fully immersive digital environment</li><li>Blocks out physical world</li><li>Examples: Oculus Quest, PlayStation VR</li></ul><h3>Augmented Reality (AR)</h3><ul><li>Digital overlays on real world</li><li>See through to physical world</li><li>Examples: Pokemon Go, Snapchat filters</li></ul><h3>Mixed Reality (MR)</h3><ul><li>Digital objects interact with real world</li><li>More advanced than AR</li><li>Examples: Microsoft HoloLens</li></ul><h3>Extended Reality (XR)</h3><p>Umbrella term covering all immersive technologies.</p>'],
            ['title' => 'XR Hardware', 'content' => '<h2>Devices and Equipment</h2><h3>VR Headsets</h3><ul><li><strong>Standalone:</strong> Quest 3, Pico</li><li><strong>PC-tethered:</strong> Valve Index, HP Reverb</li><li><strong>Console:</strong> PlayStation VR2</li></ul><h3>AR Devices</h3><ul><li><strong>Smartphones:</strong> ARKit (iOS), ARCore (Android)</li><li><strong>Smart Glasses:</strong> Ray-Ban Meta, Nreal</li><li><strong>Enterprise:</strong> HoloLens, Magic Leap</li></ul><h3>Key Specs</h3><ul><li><strong>Resolution:</strong> Higher = clearer image</li><li><strong>Field of View:</strong> Wider = more immersive</li><li><strong>Refresh Rate:</strong> Higher = smoother motion</li><li><strong>Tracking:</strong> Inside-out vs outside-in</li></ul>'],
            ['title' => 'XR Applications', 'content' => '<h2>How Industries Use XR</h2><h3>Gaming & Entertainment</h3><ul><li>Immersive games</li><li>Virtual concerts</li><li>360 video experiences</li></ul><h3>Education & Training</h3><ul><li>Medical training simulations</li><li>Virtual labs</li><li>Historical recreations</li></ul><h3>Healthcare</h3><ul><li>Surgical planning</li><li>Physical therapy</li><li>Pain management</li></ul><h3>Real Estate & Architecture</h3><ul><li>Virtual property tours</li><li>Design visualization</li></ul><h3>Manufacturing</h3><ul><li>Assembly guidance</li><li>Remote assistance</li><li>Training</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'XR Development',
            'description' => 'Creating immersive experiences',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Development Platforms', 'content' => '<h2>Tools for XR Development</h2><h3>Game Engines</h3><ul><li><strong>Unity:</strong> Most popular for XR, C#</li><li><strong>Unreal Engine:</strong> High-quality graphics, C++/Blueprints</li></ul><h3>AR Frameworks</h3><ul><li><strong>ARKit:</strong> Apple iOS</li><li><strong>ARCore:</strong> Google Android</li><li><strong>Vuforia:</strong> Cross-platform</li></ul><h3>Web XR</h3><ul><li>A-Frame: HTML-like 3D</li><li>Three.js: WebGL library</li><li>WebXR API: Browser standard</li></ul><h3>Choosing a Platform</h3><ol><li>Target device(s)</li><li>Team skills</li><li>Project requirements</li><li>Budget</li></ol>'],
            ['title' => 'Creating Your First AR Experience', 'content' => '<h2>Simple AR with Unity</h2><h3>Setup Steps</h3><ol><li>Install Unity Hub</li><li>Create new 3D project</li><li>Import AR Foundation package</li><li>Configure for iOS/Android</li><li>Add AR Session and Camera</li></ol><h3>Basic AR Script</h3><pre><code>// Place object on detected plane\nusing UnityEngine.XR.ARFoundation;\n\npublic class PlaceObject : MonoBehaviour\n{\n    public GameObject objectToPlace;\n    private ARRaycastManager raycastManager;\n    \n    void Update()\n    {\n        if (Input.touchCount &gt; 0)\n        {\n            // Raycast from touch\n            // Instantiate object at hit point\n        }\n    }\n}</code></pre><h3>Next Steps</h3><ul><li>Add 3D models</li><li>Implement interactions</li><li>Test on device</li></ul>'],
        ]);
    }

    private function createCourse47()
    {
        $course = Course::create([
            'title' => 'Quantum Computing Introduction',
            'slug' => 'quantum-computing-introduction',
            'description' => 'Understand the basics of quantum computing. Learn about qubits, quantum gates, and how quantum computers differ from classical computers.',
            'objectives' => "- Understand quantum computing basics\n- Learn about qubits and superposition\n- Explore quantum algorithms\n- Discover quantum applications\n- Understand current limitations",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Quantum Computing Basics',
            'description' => 'Fundamental quantum concepts',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'Classical vs Quantum Computing', 'content' => '<h2>A New Computing Paradigm</h2><h3>Classical Computers</h3><ul><li>Use bits: 0 or 1</li><li>Sequential processing</li><li>Deterministic operations</li></ul><h3>Quantum Computers</h3><ul><li>Use qubits: 0, 1, or both (superposition)</li><li>Parallel processing of possibilities</li><li>Probabilistic results</li></ul><h3>What Quantum Excels At</h3><ul><li>Optimization problems</li><li>Cryptography</li><li>Drug discovery simulations</li><li>Machine learning (potentially)</li></ul><h3>What Quantum is NOT</h3><ul><li>Not faster for all tasks</li><li>Not a replacement for classical computers</li><li>Not fully practical yet</li></ul>'],
            ['title' => 'Qubits and Superposition', 'content' => '<h2>The Quantum Bit</h2><h3>What is a Qubit?</h3><p>A qubit can exist in a superposition of 0 and 1 simultaneously.</p><h3>Superposition</h3><p>Like a coin spinning in the air - not heads or tails until measured.</p><h3>Mathematical Representation</h3><pre><code>|ψ⟩ = α|0⟩ + β|1⟩\n\nwhere |α|² + |β|² = 1</code></pre><h3>Measurement</h3><p>When measured, qubit collapses to either 0 or 1 with probabilities |α|² and |β|².</p><h3>Entanglement</h3><p>Two qubits can be correlated so measuring one instantly affects the other, regardless of distance.</p>'],
            ['title' => 'Quantum Gates', 'content' => '<h2>Operating on Qubits</h2><h3>Common Gates</h3><ul><li><strong>Hadamard (H):</strong> Creates superposition</li><li><strong>Pauli-X:</strong> Quantum NOT gate</li><li><strong>CNOT:</strong> Controlled NOT, creates entanglement</li><li><strong>T Gate:</strong> Phase rotation</li></ul><h3>Quantum Circuits</h3><pre><code># Using Qiskit (IBM)\nfrom qiskit import QuantumCircuit\n\nqc = QuantumCircuit(2)\nqc.h(0)      # Hadamard on qubit 0\nqc.cx(0, 1)  # CNOT: control=0, target=1\nqc.measure_all()</code></pre><h3>Key Difference</h3><p>Unlike classical gates, quantum gates are reversible (except measurement).</p>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Quantum Applications',
            'description' => 'Real-world quantum computing uses',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Quantum Algorithms', 'content' => '<h2>Famous Quantum Algorithms</h2><h3>Shor\'s Algorithm</h3><ul><li>Factors large numbers exponentially faster</li><li>Threatens RSA encryption</li><li>Requires fault-tolerant quantum computer</li></ul><h3>Grover\'s Algorithm</h3><ul><li>Searches unsorted databases faster</li><li>Quadratic speedup (square root)</li><li>More near-term applicable</li></ul><h3>Variational Quantum Eigensolver (VQE)</h3><ul><li>Finds molecular ground states</li><li>Useful for drug discovery</li><li>Works on near-term quantum computers</li></ul><h3>Quantum Machine Learning</h3><ul><li>Quantum kernel methods</li><li>Quantum neural networks</li><li>Active research area</li></ul>'],
            ['title' => 'The Future of Quantum', 'content' => '<h2>Where Quantum is Heading</h2><h3>Current State</h3><ul><li>NISQ era (Noisy Intermediate-Scale Quantum)</li><li>50-100+ qubit systems</li><li>High error rates</li><li>Limited practical applications</li></ul><h3>Challenges</h3><ul><li><strong>Decoherence:</strong> Qubits lose quantum state</li><li><strong>Error correction:</strong> Requires many physical qubits per logical qubit</li><li><strong>Scaling:</strong> More qubits = more noise</li></ul><h3>Timeline Estimates</h3><ul><li>Near-term: Specialized applications</li><li>5-10 years: More practical quantum advantage</li><li>15+ years: Fault-tolerant quantum computers</li></ul><h3>Learning Resources</h3><ul><li>IBM Quantum Experience</li><li>Google Cirq</li><li>Amazon Braket</li></ul>'],
        ]);
    }

    private function createCourse48()
    {
        $course = Course::create([
            'title' => 'Green Technology & Sustainability',
            'slug' => 'green-technology-sustainability',
            'description' => 'Learn about sustainable technologies and practices. Explore renewable energy, circular economy, carbon footprint reduction, and green business practices.',
            'objectives' => "- Understand sustainability concepts\n- Learn about renewable energy\n- Explore circular economy principles\n- Calculate and reduce carbon footprint\n- Apply green practices at work",
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Sustainability Fundamentals',
            'description' => 'Core sustainability concepts',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'What is Sustainability?', 'content' => '<h2>Meeting Present Needs Without Compromising the Future</h2><h3>The Three Pillars</h3><ul><li><strong>Environmental:</strong> Protecting natural resources</li><li><strong>Social:</strong> Ensuring equity and wellbeing</li><li><strong>Economic:</strong> Enabling prosperity</li></ul><h3>Why It Matters</h3><ul><li>Climate change is accelerating</li><li>Resources are finite</li><li>Consumer demand for sustainability</li><li>Regulatory pressure increasing</li><li>Business opportunity</li></ul><h3>Key Metrics</h3><ul><li>Carbon footprint</li><li>Water usage</li><li>Waste generation</li><li>Energy consumption</li></ul>'],
            ['title' => 'Climate Change Basics', 'content' => '<h2>Understanding Our Impact</h2><h3>The Greenhouse Effect</h3><p>Certain gases trap heat in the atmosphere:</p><ul><li>Carbon dioxide (CO2)</li><li>Methane (CH4)</li><li>Nitrous oxide (N2O)</li></ul><h3>Human Activities</h3><ul><li>Burning fossil fuels</li><li>Deforestation</li><li>Agriculture</li><li>Industrial processes</li></ul><h3>Impacts</h3><ul><li>Rising temperatures</li><li>Extreme weather events</li><li>Sea level rise</li><li>Ecosystem disruption</li></ul><h3>The Goal</h3><p>Limit warming to 1.5°C above pre-industrial levels (Paris Agreement).</p>'],
            ['title' => 'Carbon Footprint', 'content' => '<h2>Measuring Your Impact</h2><h3>What is a Carbon Footprint?</h3><p>Total greenhouse gases emitted by an individual, organization, or product.</p><h3>Major Contributors</h3><ul><li><strong>Transportation:</strong> 28%</li><li><strong>Buildings:</strong> 27%</li><li><strong>Industry:</strong> 22%</li><li><strong>Agriculture:</strong> 10%</li></ul><h3>Calculating Your Footprint</h3><ul><li>Energy bills</li><li>Travel distance and mode</li><li>Diet choices</li><li>Consumption habits</li></ul><h3>Reduction Strategies</h3><ul><li>Switch to renewable energy</li><li>Use public transport/EV</li><li>Reduce, reuse, recycle</li><li>Eat less meat</li><li>Buy local products</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Green Technologies',
            'description' => 'Sustainable technology solutions',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Renewable Energy', 'content' => '<h2>Clean Power Sources</h2><h3>Solar Energy</h3><ul><li>Photovoltaic panels</li><li>Cost dropping rapidly</li><li>Works in most locations</li></ul><h3>Wind Energy</h3><ul><li>Onshore and offshore</li><li>Large-scale deployment</li><li>Variable but predictable</li></ul><h3>Other Sources</h3><ul><li><strong>Hydropower:</strong> Dams and run-of-river</li><li><strong>Geothermal:</strong> Earth heat</li><li><strong>Biomass:</strong> Organic materials</li></ul><h3>Energy Storage</h3><ul><li>Batteries (lithium-ion)</li><li>Pumped hydro</li><li>Green hydrogen</li></ul><h3>The Grid of the Future</h3><p>Smart grids that balance supply and demand from diverse renewable sources.</p>'],
            ['title' => 'Circular Economy', 'content' => '<h2>Beyond Take-Make-Waste</h2><h3>Linear vs Circular</h3><p><strong>Linear:</strong> Extract → Make → Use → Dispose</p><p><strong>Circular:</strong> Design for longevity, reuse, and recycling</p><h3>Principles</h3><ul><li>Design out waste and pollution</li><li>Keep products and materials in use</li><li>Regenerate natural systems</li></ul><h3>Strategies</h3><ul><li><strong>Product-as-a-Service:</strong> Lease instead of sell</li><li><strong>Sharing Platforms:</strong> Maximize utilization</li><li><strong>Refurbishment:</strong> Extend product life</li><li><strong>Recycling:</strong> Recover materials</li></ul><h3>Business Benefits</h3><ul><li>Reduced material costs</li><li>New revenue streams</li><li>Customer loyalty</li><li>Regulatory compliance</li></ul>'],
        ]);
    }

    private function createCourse49()
    {
        $course = Course::create([
            'title' => 'Generative AI for Creatives',
            'slug' => 'generative-ai-for-creatives',
            'description' => 'Harness AI for creative work. Learn to use AI tools for art, writing, music, and content creation to enhance your creative process.',
            'objectives' => "- Use AI image generation tools\n- Create content with AI writing assistants\n- Explore AI music generation\n- Develop effective creative workflows\n- Understand AI art ethics",
            'price' => 39.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AI Image Generation',
            'description' => 'Creating visual art with AI',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'Introduction to AI Art', 'content' => '<h2>The AI Art Revolution</h2><h3>Popular Tools</h3><ul><li><strong>Midjourney:</strong> Artistic, stylized images</li><li><strong>DALL-E:</strong> OpenAI versatile generator</li><li><strong>Stable Diffusion:</strong> Open source, customizable</li><li><strong>Adobe Firefly:</strong> Integrated into Creative Cloud</li></ul><h3>How They Work</h3><p>These models learn patterns from millions of images to generate new ones from text descriptions.</p><h3>Use Cases</h3><ul><li>Concept art and ideation</li><li>Marketing visuals</li><li>Social media content</li><li>Product mockups</li><li>Book covers and illustrations</li></ul>'],
            ['title' => 'Prompt Crafting for Images', 'content' => '<h2>Getting Great Results</h2><h3>Prompt Structure</h3><p>[Subject] + [Style] + [Details] + [Modifiers]</p><h3>Example Prompts</h3><p><strong>Basic:</strong> "A cat sitting on a windowsill"</p><p><strong>Better:</strong> "A fluffy orange cat sitting on a windowsill at sunset, warm lighting, cozy atmosphere, detailed fur, photorealistic"</p><h3>Style Keywords</h3><ul><li>Photorealistic, cinematic, 4K</li><li>Watercolor, oil painting, sketch</li><li>Studio Ghibli, Art Nouveau, Cyberpunk</li><li>Dramatic lighting, soft focus, bokeh</li></ul><h3>Negative Prompts</h3><p>Specify what you do NOT want to avoid common issues.</p>'],
            ['title' => 'Advanced Image Techniques', 'content' => '<h2>Beyond Basic Generation</h2><h3>Image-to-Image</h3><ul><li>Start with existing image</li><li>AI transforms based on prompt</li><li>Control strength of transformation</li></ul><h3>Inpainting</h3><ul><li>Edit specific parts of image</li><li>Add or remove elements</li><li>Fix imperfections</li></ul><h3>Outpainting</h3><ul><li>Extend images beyond borders</li><li>Create larger compositions</li></ul><h3>ControlNet</h3><ul><li>Guide generation with poses</li><li>Use edge maps for structure</li><li>Maintain composition control</li></ul><h3>Workflows</h3><ol><li>Generate multiple variations</li><li>Select best candidates</li><li>Refine with editing tools</li><li>Post-process in Photoshop</li></ol>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AI for Writing and Content',
            'description' => 'Creating text content with AI',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'AI Writing Assistants', 'content' => '<h2>Enhancing Your Writing</h2><h3>Tools</h3><ul><li><strong>ChatGPT:</strong> Versatile conversation AI</li><li><strong>Claude:</strong> Long-form, analytical writing</li><li><strong>Jasper:</strong> Marketing-focused</li><li><strong>Copy.ai:</strong> Copywriting templates</li></ul><h3>What AI Can Help With</h3><ul><li>Brainstorming ideas</li><li>Writing first drafts</li><li>Editing and proofreading</li><li>Changing tone or style</li><li>Translating content</li></ul><h3>Best Practices</h3><ul><li>Provide clear context</li><li>Always review and edit output</li><li>Use AI as assistant, not replacement</li><li>Maintain your unique voice</li></ul>'],
            ['title' => 'Content Creation Workflows', 'content' => '<h2>Efficient AI-Powered Content</h2><h3>Blog Post Workflow</h3><ol><li>Generate topic ideas with AI</li><li>Create outline together</li><li>Draft sections with AI assistance</li><li>Add personal insights and examples</li><li>Edit for accuracy and voice</li><li>Generate social media snippets</li></ol><h3>Marketing Content</h3><ul><li>Ad copy variations</li><li>Email subject line testing</li><li>Product descriptions</li><li>Landing page copy</li></ul><h3>Tips for Quality</h3><ul><li>Fact-check everything</li><li>Add original research</li><li>Include personal experience</li><li>Ensure brand consistency</li></ul>'],
        ]);
    }

    private function createCourse50()
    {
        $course = Course::create([
            'title' => 'AI Ethics and Responsible AI',
            'slug' => 'ai-ethics-responsible-ai',
            'description' => 'Understand the ethical implications of AI. Learn about bias, fairness, privacy, transparency, and how to develop AI responsibly.',
            'objectives' => "- Identify ethical AI concerns\n- Understand AI bias and fairness\n- Learn privacy best practices\n- Apply responsible AI principles\n- Navigate AI governance frameworks",
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AI Ethics Fundamentals',
            'description' => 'Core ethical considerations',
            'order' => 1,
        ]);

        $this->createLessons($module1, [
            ['title' => 'Why AI Ethics Matters', 'content' => '<h2>The Stakes Are High</h2><h3>AI Decisions Impact Lives</h3><ul><li>Who gets a loan or job</li><li>Medical diagnoses and treatment</li><li>Criminal justice decisions</li><li>Content recommendations</li></ul><h3>Key Concerns</h3><ul><li><strong>Bias:</strong> AI can perpetuate discrimination</li><li><strong>Privacy:</strong> AI needs data, lots of it</li><li><strong>Transparency:</strong> Black box decisions</li><li><strong>Accountability:</strong> Who is responsible?</li><li><strong>Safety:</strong> Unintended consequences</li></ul><h3>Real-World Failures</h3><ul><li>Biased hiring algorithms</li><li>Facial recognition errors</li><li>Discriminatory credit scoring</li><li>Harmful content generation</li></ul>'],
            ['title' => 'Bias in AI Systems', 'content' => '<h2>When AI Discriminates</h2><h3>Sources of Bias</h3><ul><li><strong>Training Data:</strong> Historical inequities</li><li><strong>Sampling:</strong> Unrepresentative data</li><li><strong>Feature Selection:</strong> Proxies for protected attributes</li><li><strong>Human Labelers:</strong> Subjective judgments</li></ul><h3>Types of Bias</h3><ul><li><strong>Selection Bias:</strong> Who is in the data?</li><li><strong>Measurement Bias:</strong> How variables are captured</li><li><strong>Confirmation Bias:</strong> Reinforcing existing patterns</li></ul><h3>Mitigation Strategies</h3><ul><li>Diverse, representative training data</li><li>Bias testing across demographics</li><li>Regular auditing</li><li>Diverse development teams</li><li>Clear documentation</li></ul>'],
            ['title' => 'AI and Privacy', 'content' => '<h2>Protecting Personal Information</h2><h3>Privacy Concerns</h3><ul><li>Data collection scope</li><li>Consent and transparency</li><li>Data retention</li><li>Purpose limitation</li></ul><h3>Key Regulations</h3><ul><li><strong>GDPR:</strong> EU data protection</li><li><strong>CCPA:</strong> California privacy law</li><li><strong>AI Act:</strong> EU AI regulation</li></ul><h3>Privacy-Preserving Techniques</h3><ul><li><strong>Differential Privacy:</strong> Add noise to protect individuals</li><li><strong>Federated Learning:</strong> Train without centralizing data</li><li><strong>Data Anonymization:</strong> Remove identifying info</li><li><strong>Synthetic Data:</strong> Generate artificial training data</li></ul><h3>Best Practices</h3><ul><li>Minimize data collection</li><li>Clear privacy policies</li><li>User control over data</li><li>Regular security audits</li></ul>'],
        ]);

        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Responsible AI Practices',
            'description' => 'Building AI responsibly',
            'order' => 2,
        ]);

        $this->createLessons($module2, [
            ['title' => 'Principles of Responsible AI', 'content' => '<h2>Framework for Ethical AI</h2><h3>Core Principles</h3><ul><li><strong>Fairness:</strong> Equitable treatment across groups</li><li><strong>Transparency:</strong> Explainable decisions</li><li><strong>Accountability:</strong> Clear responsibility</li><li><strong>Privacy:</strong> Data protection</li><li><strong>Safety:</strong> Robust and secure systems</li><li><strong>Human Control:</strong> Meaningful oversight</li></ul><h3>Implementation Steps</h3><ol><li>Establish AI ethics board</li><li>Define organizational principles</li><li>Create review processes</li><li>Train teams on ethics</li><li>Monitor deployed systems</li></ol>'],
            ['title' => 'AI Governance and Compliance', 'content' => '<h2>Managing AI Responsibly</h2><h3>Governance Framework</h3><ul><li>Policies and standards</li><li>Risk assessment processes</li><li>Documentation requirements</li><li>Audit mechanisms</li></ul><h3>EU AI Act Categories</h3><ul><li><strong>Unacceptable Risk:</strong> Banned</li><li><strong>High Risk:</strong> Strict requirements</li><li><strong>Limited Risk:</strong> Transparency obligations</li><li><strong>Minimal Risk:</strong> No restrictions</li></ul><h3>High-Risk AI Requirements</h3><ul><li>Risk management system</li><li>Data governance</li><li>Technical documentation</li><li>Human oversight</li><li>Accuracy and robustness</li></ul><h3>Organizational Actions</h3><ul><li>Inventory all AI systems</li><li>Classify by risk level</li><li>Implement required controls</li><li>Maintain documentation</li><li>Train employees</li></ul>'],
        ]);
    }

    private function createLessons(CourseModule $module, array $lessons): void
    {
        foreach ($lessons as $index => $lesson) {
            CourseLesson::create([
                'module_id' => $module->id,
                'title' => $lesson['title'],
                'content' => $lesson['content'],
                'type' => 'text',
                'order' => $index + 1,
                'duration_minutes' => 10,
                'is_free_preview' => $index === 0,
            ]);
        }
    }
}
