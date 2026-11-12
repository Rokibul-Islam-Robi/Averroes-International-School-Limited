-- ====================================================================
-- Averroes International School CMS - Complete Unified Database Dump
-- Generated: 2026-11-12 08:13:21
-- Target Server: MySQL 5.7+ / MySQL 8.0+ / MariaDB / PlanetScale
-- To create a new database manually, you can run:
--   CREATE DATABASE IF NOT EXISTS `averroes_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   USE `averroes_db`;
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET NAMES utf8mb4;

-- --------------------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admins`
INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
('1', 'admin', 'admin@averroes.edu.bd', '$2y$10$tcWHLb.Mvtzvd3CaeifktO5VlsvOMlGtBZQ8uRYUAUmqpfqZbyxRe', '2026-11-08 17:30:39', '2026-11-08 17:30:39');

-- --------------------------------------------------------------------
-- Table structure for `admission_faqs`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `admission_faqs`;
CREATE TABLE `admission_faqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admission_faqs`
INSERT INTO `admission_faqs` (`id`, `category`, `question`, `answer`, `sort_order`, `is_active`) VALUES
('1', 'General', 'What is the admission procedure at Averroes International School?', 'The admission procedure consists of an online or physical query form submission, counseling appointment, comprehensive campus tour, diagnostic student assessment, result publication within 24 hours, session with the Academic Head, and fee settlement.', '1', '1'),
('2', 'Curriculum', 'What curriculum does Averroes follow?', 'Averroes seamlessly integrates the internationally acclaimed Pearson Edexcel and Cambridge Assessment curriculum frameworks with full-fledged Hifzul Quran, Islamic Studies, and Arabic immersion.', '2', '1'),
('3', 'Campuses', 'Where are Averroes campuses located in Dhaka?', 'Averroes currently operates modern campuses in Lalmatia (Mohammadpur - Main Administrative HQ) and Banasree (Eastern Dhaka Campus), equipped with specialized labs, synthetic turf, and aquatic facilities.', '3', '1'),
('4', 'Transport', 'Is safe school transport available across Dhaka?', 'Yes, Averroes operates a modern fleet of fully air-conditioned buses equipped with real-time GPS tracking, internal/external CCTV, speed governors, and trained female attendants.', '4', '1');

-- --------------------------------------------------------------------
-- Table structure for `admission_timeline`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `admission_timeline`;
CREATE TABLE `admission_timeline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `step_num` int(11) NOT NULL,
  `pill` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admission_timeline`
INSERT INTO `admission_timeline` (`id`, `step_num`, `pill`, `icon`, `title`, `description`, `sort_order`, `is_active`) VALUES
('1', '1', 'Step 1', 'form', 'Fill up the Web form above / Social Media Form', 'Submit the digital inquiry form on this page or collect the prescribed Admission Form directly from the campus Admission Office.', '1', '1'),
('2', '2', 'Step 2', 'calendar', 'Set an appointment to visit school through Phone Call, SMS, Email from Lead Management Team', 'Our admissions counselors will get in touch to confirm a dedicated time slot that fits your schedule.', '2', '1'),
('3', '3', 'Step 3', 'door', 'Visit School and attend session with Admission Team.', 'Tour our classrooms, Quranic learning halls, labs, turf, and semi-Olympic pool while consulting with senior educators.', '3', '1'),
('4', '4', 'Step 4', 'assessment', 'Form Purchase and Student Assessment', 'Complete form verification and appear for the age-appropriate diagnostic assessment (Oral for Early Years, Written for Class 1+).', '4', '1'),
('5', '5', 'Step 5', 'result', 'Result Published (Within Next Working Day)', 'Assessment outcomes and class placement feedback are published within 24 hours via SMS and parent portal.', '5', '1'),
('6', '6', 'Step 6', 'hod', 'Session with Academic Head of Department (HOD)', 'An insightful dialogue with the Academic Section Head and Principal to finalize curriculum streams and individualized goals.', '6', '1'),
('7', '7', 'Step 7', 'payment', 'Admission Fees Payment', 'Settle the admission and session fees through the Accounts Counter or online banking gateway to secure enrollment.', '7', '1');

-- --------------------------------------------------------------------
-- Table structure for `campuses`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `campuses`;
CREATE TABLE `campuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) NOT NULL,
  `footer_heading` varchar(190) NOT NULL,
  `phone` text NOT NULL,
  `email` varchar(190) NOT NULL,
  `address` text NOT NULL,
  `map_url` varchar(500) NOT NULL DEFAULT '#',
  `card_link` varchar(500) NOT NULL DEFAULT '#',
  `image` varchar(255) DEFAULT NULL,
  `image_alt` varchar(190) DEFAULT NULL,
  `show_in_grid` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `campuses`
INSERT INTO `campuses` (`id`, `name`, `footer_heading`, `phone`, `email`, `address`, `map_url`, `card_link`, `image`, `image_alt`, `show_in_grid`, `sort_order`, `is_active`) VALUES
('1', 'Averroes International School, Lalmatia', 'Averroes International School, Lalmatia', '+880 1949-000 555\n+880 1714 622 211', 'admin@aisl.edu.bd', 'House No – 7/16, Block – B, Lalmatia, Mohammadpur, Dhaka - 1207', 'https://maps.google.com/?q=House+No+7/16+Block+B+Lalmatia+Mohammadpur+Dhaka+1207', '#', 'assets/img/Campus-1.jpg', 'Lalmatia campus', '1', '1', '1'),
('2', 'Averroes International School, Banasree', 'Averroes International School, Banasree', '+88 01901-048844', '', 'Plot 4, Road 13, Block A , Banasree, Dhaka-1230', 'https://maps.app.goo.gl/v5cDG3CMC3WdMtQZ6?g_st=aw', '#', 'assets/img/Campus-1.jpg', 'Banasree campus', '1', '2', '1');

-- --------------------------------------------------------------------
-- Table structure for `contact_inquiries`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `contact_inquiries`;
CREATE TABLE `contact_inquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `campus` varchar(50) NOT NULL DEFAULT 'general',
  `grade` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `reply_message` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` varchar(100) DEFAULT NULL,
  `status` enum('new','contacted','enrolled','archived') NOT NULL DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inq_status` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `contact_inquiries`
INSERT INTO `contact_inquiries` (`id`, `name`, `email`, `phone`, `campus`, `grade`, `subject`, `message`, `reply_message`, `replied_at`, `replied_by`, `status`, `ip_address`, `created_at`) VALUES
('1', 'Engr. Tariqul Islam', 'tariqul.parent@example.com', '+880 1711-987654', 'lalmatia', 'Standard 4', 'Admission Query (Lalmatia) — Grade: Standard 4 [Tour Date: 2026-11-20]', 'We are interested in British curriculum with Hifzul Quran track for our son.', NULL, NULL, NULL, 'new', '127.0.0.1', '2026-11-12 12:29:19'),
('2', 'Dr. Nusrat Jahan', 'nusrat.jahan@example.com', '+880 1912-345678', 'banasree', 'KG-II', 'Website Contact Inquiry (Banasree) — Grade: KG-II', 'Please share details regarding school transport service from Rampura to Banasree campus.', NULL, NULL, NULL, 'new', '127.0.0.1', '2026-11-12 12:29:19'),
('3', 'Website Visitor (Chat Widget)', 'chat-inquiry@averroes.edu.bd', 'Website Live Chat', 'central', NULL, 'Chat Query: What are the admission deadlines for O Level Pearson Edexcel?', 'What are the admission deadlines for O Level Pearson Edexcel intake for upcoming session?', NULL, NULL, NULL, 'new', '127.0.0.1', '2026-11-12 12:29:19');

-- --------------------------------------------------------------------
-- Table structure for `nav_items`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `nav_items`;
CREATE TABLE `nav_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location` enum('header','header_btn','footer','legal') NOT NULL DEFAULT 'header',
  `label` varchar(120) NOT NULL,
  `url` varchar(500) NOT NULL DEFAULT '#',
  `css_class` varchar(50) DEFAULT NULL,
  `has_dropdown` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_nav_loc_active` (`location`,`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `nav_items`
INSERT INTO `nav_items` (`id`, `location`, `label`, `url`, `css_class`, `has_dropdown`, `sort_order`, `is_active`) VALUES
('1', 'header', 'First in Bangladesh', '#', NULL, '1', '1', '1'),
('2', 'header', 'Why Averroes', '#why', NULL, '0', '2', '1'),
('3', 'header', 'Life at Averroes', '#', NULL, '0', '3', '1'),
('4', 'header', 'Admissions', '#', NULL, '0', '4', '1'),
('5', 'header', 'Contact Us', '#footer', NULL, '0', '5', '1'),
('6', 'header_btn', 'Apply Now', '#', 'pri', '0', '1', '1'),
('7', 'header_btn', 'Student Portal', '#', 'out', '0', '2', '1'),
('8', 'footer', 'Home', '#', NULL, '0', '1', '1'),
('9', 'footer', 'Why Averroes', '#why', NULL, '0', '2', '1'),
('10', 'footer', 'Apply at Averroes Lalmatia', '#', NULL, '0', '3', '1'),
('11', 'footer', 'Averroes Portal', '#', NULL, '0', '4', '1'),
('12', 'footer', 'Contact Us', '#', NULL, '0', '5', '1'),
('13', 'footer', 'Life at Averroes', '#', NULL, '0', '6', '1'),
('14', 'footer', 'Apply at Averroes Banasree', '#', NULL, '0', '7', '1'),
('15', 'legal', 'Terms & Conditions', '#', NULL, '0', '1', '1'),
('16', 'legal', 'Privacy Policy', '#', NULL, '0', '2', '1');

-- --------------------------------------------------------------------
-- Table structure for `newsletter_subscribers`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subscribers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for `partners`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `partners`;
CREATE TABLE `partners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `css_class` varchar(50) NOT NULL,
  `label_html` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `partners`
INSERT INTO `partners` (`id`, `name`, `css_class`, `label_html`, `sort_order`, `is_active`) VALUES
('1', 'Ministry of Education', 'p-moe', '<img src=\"assets/img/partners/moe.svg\" alt=\"Ministry of Education Bangladesh\" class=\"partner-img\">', '1', '1'),
('2', 'Cambridge Assessment', 'p-cam', '<img src=\"assets/img/partners/cambridge.svg\" alt=\"Cambridge Assessment International Education\" class=\"partner-img\">', '2', '1'),
('3', 'Pearson Edexcel', 'p-edx', '<img src=\"assets/img/partners/edexcel.svg\" alt=\"Pearson Edexcel\" class=\"partner-img\">', '3', '1'),
('4', 'British Council', 'p-bc', '<img src=\"assets/img/partners/british-council.svg\" alt=\"British Council\" class=\"partner-img\">', '4', '1'),
('5', 'Dhaka Education Board', 'p-deb', '<img src=\"assets/img/partners/dhaka-board.svg\" alt=\"Dhaka Education Board DSHE\" class=\"partner-img\">', '5', '1'),
('6', 'Monash University', 'p-monash', '<img src=\"assets/img/partners/monash.svg\" alt=\"Monash University Pathway\" class=\"partner-img\">', '6', '1');

-- --------------------------------------------------------------------
-- Table structure for `programs`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `highlights_json` text DEFAULT NULL,
  `stats_json` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `icon` varchar(255) NOT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `image_row1` varchar(255) DEFAULT NULL,
  `image_row2` varchar(255) DEFAULT NULL,
  `link_url` varchar(500) NOT NULL DEFAULT '#',
  `link_text` varchar(100) NOT NULL DEFAULT 'Learn More',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `programs`
INSERT INTO `programs` (`id`, `title`, `slug`, `subtitle`, `summary`, `body_html`, `highlights_json`, `stats_json`, `meta_description`, `icon`, `banner_image`, `image_row1`, `image_row2`, `link_url`, `link_text`, `sort_order`, `is_active`) VALUES
('1', 'Hifzul Qur\'an with British Curriculum', 'hifzul-quran-with-british-curriculum', 'Dual Excellence: Islamic Memorization & Cambridge Academic Rigor', 'Memorising the Holy Qur\'an while continuing the British Curriculum — nurturing strong Islamic values alongside academic excellence.', '<p>Averroes International School is recognized by the Bangladesh Ministry of Education and authorized by the British Council and Pearson Edexcel. One of our hallmark contributions to the educational landscape of Bangladesh is the seamless integration of a structured <strong>Hifzul Qur\'an</strong> program alongside standard British curriculum schooling.</p><p>Students are guided by certified Huffaz, Islamic scholars, and Cambridge-trained faculty. Our methodology ensures that learners master Tajweed rules, correct phonetic Makharij, and systematic Qur\'an memorization without compromising the rigorous study of Mathematics, English, Sciences, and Modern Computing.</p><p>Daily sessions incorporate <em>Halaqatul Qur\'an</em> (recitation and character reflection circles), <em>Nazera</em>, and <em>Deeniyat</em>, instilling timeless Islamic ethics, empathy, and personal discipline that stay with learners throughout their lives.</p>', '[\"Authorized by British Council and Pearson Edexcel (Primary & Secondary)\",\"Systematic full and integrated Hifz curriculum options\",\"Daily Tajweed, Makharij training, and character-building Halaqas\",\"Seamless academic progression into O Level and A Level certifications\"]', '{\"Grade Levels\":\"Grade 1 through O Levels\",\"Campus\":\"Lalmatia & Banasree Campuses\",\"Instruction\":\"Certified Huffaz & British Curriculum Teachers\",\"Timetable\":\"Integrated Academic & Memorization Schedule\"}', 'Explore Averroes International School\'s pioneering Hifzul Qur\'an program integrated with the British curriculum in Dhaka, Bangladesh.', 'assets/img/quran.svg', 'assets/img/Campus-1.jpg', 'assets/img/subpages/aisl-hifz-classroom-hd.jpg', 'assets/img/subpages/aisl-hifz-boy-full.jpg', 'program.php?slug=hifzul-quran-with-british-curriculum', 'Learn More', '1', '1'),
('2', 'Qira\'at Competition', 'qiraat-competition', 'Excellence in Recitation, Tajweed & Stage Presentation', 'Inspiring students to improve Tajweed, pronunciation and confidence through meaningful and engaging Qur\'an recitation competitions.', '<p>The annual Averroes <strong>Qira\'at Competition</strong> is a cornerstone spiritual and co-curricular tradition. Open to students across all grade levels, this event inspires learners to perfect their Tajweed, cultivate melodious recitation, and build immense public confidence.</p><p>Students perform before an esteemed jury of nationally and internationally recognized Qaris and Islamic scholars. Through successive campus rounds leading up to the Grand Inter-Campus Finals, participants hone their stage composure, vocal modulation, and reverent connection to the Holy Qur\'an.</p><p>Outstanding reciters receive special certificates, commemorative crests, and academic scholarships, motivating the entire school community toward lifelong engagement with the Divine Book.</p>', '[\"Multi-stage competition: Class Heats, Campus Semi-Finals & Grand Finals\",\"Judged by renowned Qaris, scholars, and language experts\",\"Stringent evaluation across Tajweed, Voice Modulation, and Makharij\",\"Prestigious awards, crests, and academic recognitions for top reciters\"]', '{\"Frequency\":\"Annual Flagship Championship\",\"Eligibility\":\"Playgroup to A Levels\",\"Campuses\":\"Lalmatia & Banasree Campuses\",\"Jury\":\"Renowned National & International Qaris\"}', 'Discover the annual Averroes International School Qira\'at Competition celebrating beautiful recitation and Tajweed mastery.', 'assets/img/qirat.svg', 'assets/img/Campus-2.jpg', 'assets/img/subpages/aisl-qiraat-students-group.jpg', 'assets/img/subpages/aisl-qiraat-stage-reciter.jpg', 'program.php?slug=qiraat-competition', 'Learn More', '2', '1'),
('3', 'Seerah Exhibition', 'seerah-exhibition', 'Exploring the Noble Life & Timeless Character of Prophet Muhammad ﷺ', 'A creative and educational initiative helping students explore the life, character, and noble values of Prophet Muhammad ﷺ through interactive displays.', '<p>The Averroes <strong>Seerah Exhibition</strong> is an immersive educational initiative that brings the blessed life and character of Prophet Muhammad ﷺ to vibrant life. Conceived, researched, and built by our students under the mentorship of Islamic studies and history faculty, the exhibition is a testament to experiential learning.</p><p>Visitors walk through chronological thematic galleries featuring handmade 3D architectural models of historic Makkah and Madinah, authentic map routes of the Hijrah, interactive multimedia timelines, and student-led oral presentations. The exhibition emphasizes universal moral virtues such as honesty, compassion, social justice, and resilience.</p><p>This signature event welcomes hundreds of parents, educational dignitaries, and community guests, fostering deep pride in Islamic heritage and ethical leadership.</p>', '[\"Student-built 3D architectural models of historic Islamic landmarks\",\"Interactive chronological timelines spanning Prophetic biography\",\"Focus on character building, research methodology, and public speaking\",\"Community-wide event welcoming parents and educational leaders\"]', '{\"Format\":\"3-Day Immersive Campus Exhibition\",\"Exhibits\":\"50+ Student-built physical & digital models\",\"Core Theme\":\"Prophetic Character, Ethics & Leadership\",\"Participants\":\"Primary, Middle & High School Students\"}', 'Learn about the inspiring Averroes International School Seerah Exhibition showcasing the life of Prophet Muhammad ﷺ through interactive displays.', 'assets/img/seerah.svg', 'assets/img/Campus-1.jpg', 'assets/img/subpages/aisl-seerah-sealed-nectar.jpg', 'assets/img/subpages/aisl-seerah-arabic-conversation.jpg', 'program.php?slug=seerah-exhibition', 'Learn More', '3', '1'),
('4', 'Averroes Swimming Club', 'averroes-swimming-club', 'Aquatic Excellence, Water Safety & Physical Fitness', 'A structured aquatic program helping students develop essential swimming skills, water safety awareness, stamina, and physical fitness.', '<p>Physical fitness and water confidence are fundamental components of holistic student development. Inspired by the Sunnah emphasis on learning swimming, the <strong>Averroes Swimming Club</strong> offers professional aquatic training in a hygienic, temperature-controlled environment.</p><p>Sessions are strictly structured with dedicated, separate time slots for male and female students, supervised at all times by certified instructors and on-deck lifeguards. Our comprehensive curriculum covers water acclimation for early learners, four competitive strokes (Freestyle, Breaststroke, Backstroke, Butterfly), and international life-saving water safety techniques.</p><p>Beyond physical endurance, swimming instills mental resilience, goal-setting discipline, and healthy sportsmanship among participants.</p>', '[\"Separate, dedicated sessions for male and female students\",\"Instruction by certified professional swim trainers and lifeguards\",\"Comprehensive curriculum from water safety to competitive strokes\",\"Temperature-controlled, hygienically maintained aquatic facility\"]', '{\"Facility\":\"Modern, Certified Semi-Olympic Aquatic Pool\",\"Supervision\":\"100% Lifeguard & CPR-certified Staff\",\"Skill Tiers\":\"Beginner, Intermediate & Advanced Squads\",\"Schedules\":\"Separate Boys & Girls Timetable\"}', 'Discover the Averroes Swimming Club offering certified aquatic training, water safety, and physical conditioning in Dhaka.', 'assets/img/swimming.svg', 'assets/img/Campus-2.jpg', 'assets/img/subpages/aisl-swimming-pool-students.jpg', 'assets/img/subpages/aisl-swimming-club-splash.jpg', 'program.php?slug=averroes-swimming-club', 'Learn More', '4', '1'),
('5', 'Averroes Turf', 'averroes-turf', 'High-Performance Campus Sports & Rooftop Athletics Arena', 'A dedicated modern sports facility providing a safe and engaging space for physical exercise, football, athletics, and teamwork.', '<p><strong>Averroes Turf</strong> is an innovative, high-standard sports arena designed to overcome urban space limitations and provide students with dynamic daily athletic opportunities. Built with shock-absorbing, FIFA-standard synthetic turf, the arena supports football, cricket, track events, and structured physical education drills.</p><p>Enclosed with heavy-duty protective perimeter netting and equipped with modern LED floodlights, the turf provides a secure, weatherproof environment where students can play, train, and compete year-round. Dedicated physical education instructors lead conditioning sessions, intramural tournaments, and athletic skill camps.</p><p>Through regular sports on the turf, students develop agility, cardiovascular fitness, strategic teamwork, and leadership qualities essential for lifelong health.</p>', '[\"Shock-absorbing FIFA-grade artificial turf surface\",\"Full perimeter safety netting and advanced LED lighting\",\"Multi-sport versatility: Football, Cricket, Badminton & Athletics\",\"Daily scheduled physical education and intramural tournaments\"]', '{\"Surface Type\":\"All-Weather Synthetic Astro-Turf\",\"Sports\":\"Football, Cricket, Athletics & Fitness\",\"Campuses\":\"Lalmatia & Banasree Arenas\",\"Safety\":\"Fully Enclosed Protective Perimeter Netting\"}', 'Explore Averroes Turf, the state-of-the-art rooftop and campus sports facility for football, cricket, and athletic excellence.', 'assets/img/turf.svg', 'assets/img/Campus-1.jpg', 'assets/img/subpages/aisl-turf-camp-squad.jpg', 'assets/img/subpages/aisl-turf-green-arena.jpg', 'program.php?slug=averroes-turf', 'Learn More', '5', '1'),
('6', 'National & International University Progression', 'national-international-university-progression', 'Global Pathways to Top Tier Higher Education & Scholarships', 'Comprehensive academic guidance and counseling preparing students for successful transitions to leading universities in Bangladesh and abroad.', '<p>The <strong>National & International University Progression</strong> cell at Averroes International School bridges the bridge between secondary education and world-class tertiary institutions. Starting as early as Grade 9, students receive structured career counseling to align their subject choices with long-term aspirations.</p><p>Our dedicated college advisors guide students through standard admission pathways, including UCAS applications for the United Kingdom, the Common Application for the United States, and direct entry systems for universities in Canada, Australia, Malaysia, and Europe. We also assist candidates pursuing medical and engineering entrances in Bangladesh.</p><p>With dedicated SAT, IELTS, and personal statement workshops, our graduates have achieved stellar admission results and prestigious merit scholarships worldwide.</p>', '[\"Personalized 1-on-1 career and university advisory sessions\",\"Comprehensive support for UCAS, Common App, and regional admissions\",\"In-house preparation modules for SAT, IELTS, and university entrance\",\"Annual University Fair hosting visiting global admissions officers\"]', '{\"Target Groups\":\"Grade 9 to A Level Graduates\",\"Destinations\":\"UK, USA, Canada, Australia, Malaysia, BD\",\"Counseling\":\"Full-time Certified University Placement Advisors\",\"Track Record\":\"Global Admissions & Substantial Merit Scholarships\"}', 'Learn how Averroes International School guides students to top universities globally through personalized career and college counseling.', 'assets/img/university.svg', 'assets/img/Campus-2.jpg', 'assets/img/subpages/aisl-progression-auditorium.jpg', 'assets/img/subpages/aisl-university-progression-demo.jpg', 'program.php?slug=national-international-university-progression', 'Learn More', '6', '1'),
('7', 'Summer Camp', 'summer-camp', 'Creative Exploration, STEM Workshops & Islamic Life Skills', 'An exciting blend of Islamic learning, creativity, sports, teamwork and fun beyond the regular classroom.', '<p>The Averroes <strong>Summer Camp</strong> is an enriching seasonal immersion that transforms holiday downtime into a dynamic journey of discovery. Designed for children ages 4 to 15, the camp combines hands-on STEM projects with creative arts and Islamic character-building activities.</p><p>Campers engage in hands-on robotics, coding, Arabic calligraphy, public speaking, self-defense martial arts, culinary basics, and scientific experimentation. Every module is tailored to spark curiosity, nurture independent problem-solving, and cultivate lasting friendships outside the typical exam-focused environment.</p><p>The program culminates in a community open house and talent exhibition where campers proudly demonstrate their new skills and projects to family and friends.</p>', '[\"Hands-on STEM challenges, coding, and introductory robotics\",\"Traditional Islamic arts, Arabic calligraphy, and moral stories\",\"Martial arts, fitness workouts, and outdoor team-building games\",\"Culminating project showcase and certificate of achievement\"]', '{\"Duration\":\"2-3 Weeks Intensive Seasonal Program\",\"Age Eligibility\":\"Ages 4 to 15 Years Old\",\"Locations\":\"Lalmatia & Banasree Campuses\",\"Activities\":\"Robotics, Arts, Calligraphy, Sports & Public Speaking\"}', 'Discover the vibrant Averroes International School Summer Camp blending STEM, Islamic values, robotics, and sports.', 'assets/img/summer-camp.svg', 'assets/img/Campus-1.jpg', 'assets/img/subpages/aisl-summer-camp-banner-squad.jpg', 'assets/img/subpages/aisl-summer-camp-coaching-lineup.jpg', 'program.php?slug=summer-camp', 'Learn More', '7', '1'),
('8', 'Guardians’ Waiting Lounge', 'guardians-waiting-lounge', 'Hospitality, Dignity & Comfort for Our Valued School Community', 'A comfortable and welcoming waiting space designed especially for the convenience of our valued parents and guardians.', '<p>At Averroes International School, we recognize parents as invaluable partners in the educational journey. In pioneer spirit, we established the <strong>Guardians\' Waiting Lounge</strong>—a dedicated, fully air-conditioned facility created exclusively for the comfort of parents during arrival, dismissal, and campus appointments.</p><p>The lounge features ergonomic seating, high-speed campus Wi-Fi, digital informational displays, clean restroom facilities, and complimentary refreshments. A dedicated front-desk liaison officer is stationed on-site to assist with administrative questions, teacher meeting appointments, and transport schedules.</p><p>This initiative eliminates the discomfort of waiting outdoors and reflects our deep institutional culture of respect, dignity, and hospitality toward every family.</p>', '[\"Fully air-conditioned lounge equipped with ergonomic seating\",\"High-speed complimentary Wi-Fi and designated workspaces\",\"On-site parent liaison officers for swift administrative queries\",\"Digital updates on school activities, notices, and bus departures\"]', '{\"Operating Hours\":\"7:30 AM to 4:30 PM (School Days)\",\"Access\":\"Free for All Registered Parents & Guardians\",\"Amenities\":\"Air-conditioned, Wi-Fi, Water\\/Tea Station\",\"Staffing\":\"Dedicated Parent Relations Officers\"}', 'Explore the dedicated, air-conditioned Guardians\' Waiting Lounge at Averroes International School providing comfort and hospitality to parents.', 'assets/img/lounge.svg', 'assets/img/Campus-2.jpg', 'assets/img/subpages/aisl-lounge-interior-lighting.jpg', 'assets/img/subpages/aisl-lounge-parents-meeting.jpg', 'program.php?slug=guardians-waiting-lounge', 'Learn More', '8', '1'),
('9', 'School Bus Service', 'school-bus-service', 'Safe, Air-Conditioned & GPS-Monitored Student Transportation', 'Safe, organized and convenient transportation designed to make students’ daily journey to and from school easier.', '<p>Student safety during transit is of paramount concern. Averroes International School operates a modern, reliable <strong>School Bus Service</strong> serving vital residential neighborhoods across Dhaka. Our fleet consists of purpose-built, fully air-conditioned buses adhering to rigorous mechanical and safety standards.</p><p>Every vehicle is outfitted with real-time GPS tracking accessible to our central transport control desk, internal and external CCTV cameras, first-aid kits, and automated speed limiters. Trained female attendants supervise students during every leg of the commute, ensuring orderly seating, safety belt compliance, and safe handovers at designated drop-off points.</p><p>Our transport service affords parents peace of mind and gives students a pleasant, timely, and social start to each school day.</p>', '[\"Modern fleet of fully air-conditioned, well-maintained school buses\",\"Real-time GPS tracking and CCTV cameras monitored by central desk\",\"Trained female transport attendants on board every journey\",\"Strict safety compliance, speed limiters, and first-aid kits\"]', '{\"Coverage\":\"Dhanmondi, Mohammadpur, Banasree, Rampura, Uttara & Mirpur\",\"Fleet Standards\":\"100% Air-Conditioned, Speed-Governed Buses\",\"Safety Tech\":\"Live GPS Tracking & High-Definition CCTV\",\"Supervision\":\"Vetted Drivers & Full-time Female Attendants\"}', 'Learn about Averroes International School\'s safe, air-conditioned school bus fleet with real-time GPS tracking and female attendants.', 'assets/img/bus.svg', 'assets/img/Campus-1.jpg', 'assets/img/subpages/aisl-bus-ribbon-cutting.jpg', 'assets/img/subpages/aisl-bus-service-launched.jpg', 'program.php?slug=school-bus-service', 'Learn More', '9', '1'),
('10', 'Averroes Book Shop', 'averroes-book-shop', 'Authorized Curriculum Textbooks, Learning Resources & Uniforms', 'Convenient access to books, educational materials and essential learning resources for the entire Averroes community.', '<p>The on-campus <strong>Averroes Book Shop</strong> simplifies academic preparation by providing guaranteed access to authentic, publisher-certified textbooks and educational resources under one roof. We stock original Pearson Edexcel, Cambridge, and Oxford publications alongside proprietary Islamic studies workbooks and Tajweed guides.</p><p>In addition to academic literature, the store provides official Averroes school uniforms, sports kits, lab equipment, and high-quality stationery. At the beginning of each academic session, the bookshop offers pre-packaged, grade-specific bundles so parents can obtain everything their child needs effortlessly without dealing with multiple suppliers.</p><p>All items are sold at standardized, fair institutional rates, reinforcing our commitment to educational convenience and integrity.</p>', '[\"Original, authentic Pearson Edexcel & Cambridge textbooks\",\"Official tailored school uniforms, sports attire, and school badges\",\"Comprehensive stationery, science lab kits, and art materials\",\"Pre-packaged grade bundles for seamless session startup\"]', '{\"Location\":\"Dedicated on-campus facility at both branches\",\"Inventory\":\"Textbooks, Uniforms, Stationery & Islamic Books\",\"Pricing\":\"Publisher-Standardized Fair Institutional Rates\",\"Services\":\"Grade-Wise Pre-Packaged Academic Bundles\"}', 'Visit the Averroes Book Shop for authorized Pearson Edexcel textbooks, official school uniforms, and educational stationery in Dhaka.', 'assets/img/bookshop.svg', 'assets/img/Campus-2.jpg', 'assets/img/subpages/aisl-bookshop-main-hall.jpg', 'assets/img/subpages/aisl-bookshop-aisle-shelves.jpg', 'program.php?slug=averroes-book-shop', 'Learn More', '10', '1');

-- --------------------------------------------------------------------
-- Table structure for `sections`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(50) NOT NULL,
  `heading_plain` varchar(150) NOT NULL,
  `heading_em` varchar(150) NOT NULL,
  `icon_image` varchar(255) DEFAULT 'assets/img/cap.svg',
  `body` text DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_url` varchar(500) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `sections`
INSERT INTO `sections` (`id`, `section_key`, `heading_plain`, `heading_em`, `icon_image`, `body`, `button_text`, `button_url`, `image`, `image_alt`) VALUES
('1', 'why', 'Why ', 'Averroes?', 'assets/img/cap.svg', 'Averroes International Schools are educational initiatives of the STS Group. Established with a vision of providing 21st-century education, Averroes is founded on the philosophy of the “School of Life,” where education goes beyond academics to prepare students for the opportunities and challenges of the future.', 'Read More', '#', 'assets/img/rocket.svg', 'Pencil rocket illustration'),
('2', 'first', 'First Time In', 'Bangladesh', 'assets/img/cap.svg', NULL, NULL, NULL, NULL, NULL),
('3', 'campuses', 'Discover Our', 'Campuses', 'assets/img/cap.svg', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------------------
-- Table structure for `settings`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `label` varchar(150) NOT NULL DEFAULT '',
  `group_name` varchar(50) NOT NULL DEFAULT 'general',
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`setting_key`, `setting_value`, `label`, `group_name`) VALUES
('chat_text', 'Chat with Us', 'Chat Button Label', 'social'),
('chat_url', '#', 'Chat With Us Link', 'social'),
('copyright_text', '© 2026 Averroes International School. All Rights Reserved.', 'Copyright Text', 'footer'),
('favicon', 'assets/img/logo.jpeg', 'Favicon Image', 'media'),
('footer_logo', 'assets/img/footer-logo-banner.jpg', 'Footer Logo', 'media'),
('hero_video_url', 'https://youtu.be/lWlHTIiGMvk', 'Hero Vimeo Video Embed URL', 'media'),
('logo', 'assets/img/logo-1.webp', 'Header Logo', 'media'),
('logo_link', '#', 'Logo Link URL', 'general'),
('meta_description', 'Averroes International Schools are educational initiatives of the STS Group providing 21st-century education based on the philosophy of School of Life.', 'Meta Description', 'general'),
('site_title', 'Averroes International School | School of Life', 'Site Title', 'general'),
('whatsapp_url', 'https://wa.me/8801954123123', 'WhatsApp URL / Number', 'social');

-- --------------------------------------------------------------------
-- Table structure for `stats`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_value` varchar(50) NOT NULL,
  `count_to` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `stats`
INSERT INTO `stats` (`id`, `display_value`, `count_to`, `suffix`, `label`, `sort_order`, `is_active`) VALUES
('1', '275+', '275', '+', 'Teachers', '1', '1'),
('2', '99.9%', '99.9', '%', 'Average Passing Score', '2', '1'),
('3', '12', '12', '', 'World Toppers Since 2015', '3', '1'),
('4', '1:10', NULL, '', 'Student-Teacher Ratio', '4', '1');

-- --------------------------------------------------------------------
-- Table structure for `subpage_contents`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `subpage_contents`;
CREATE TABLE `subpage_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `eyebrow` varchar(255) DEFAULT NULL,
  `title_base` varchar(255) DEFAULT NULL,
  `title_highlight` varchar(255) DEFAULT NULL,
  `lead_text` text DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `doodle_type` varchar(50) DEFAULT NULL,
  `layout_mode` varchar(50) DEFAULT 'normal',
  `type` varchar(50) DEFAULT 'bullets',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_page_section` (`page_key`,`section_key`),
  KEY `idx_page_sort` (`page_key`,`sort_order`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `subpage_contents`
INSERT INTO `subpage_contents` (`id`, `page_key`, `section_key`, `eyebrow`, `title_base`, `title_highlight`, `lead_text`, `body_html`, `image`, `image_alt`, `doodle_type`, `layout_mode`, `type`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'why_averroes', 'hero', 'WHY CHOOSE AVERROES', 'Give your Child the Averroes Edge', NULL, 'At Averroes, we go beyond traditional boundaries of education. Guided by our motto — \"One School Serving the Purposes of Here & Hereafter\" — we provide an unmatched synthesis of internationally recognized British curricula, advanced STEM education, and deep-rooted Islamic values (Tarbiyah). We nurture enlightened minds, moral leadership, and visionary scholars prepared for global distinction.', NULL, 'assets/img/subpages/aisl-hifz-boy-full.jpg', 'Averroes International School Student holding Holy Quran', NULL, 'normal', 'bullets', '1', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('2', 'why_averroes', 'research', NULL, 'Holistic Education Rooted in ', 'Cambridge & Tarbiyah', 'We believe that rigorous academic exploration must be harmonized with moral enlightenment. Our dual curriculum blends the internationally acclaimed Pearson Edexcel and Cambridge Assessment frameworks with comprehensive Islamic Studies, Tajweed, and Arabic language immersion. Guided by university professors and seasoned curriculum directors, our students develop analytical mastery, scientific inquiry, and unshakeable ethical character.', NULL, 'assets/img/subpages/aisl-hifz-classroom-hd.jpg', 'Averroes Modern Academic Classrooms', 'science', 'reverse', 'bullets', '2', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('3', 'why_averroes', 'pedagogy', NULL, 'Innovative Teaching ', 'Methods & Dual-Track Hifz', 'Our pedagogical model ensures that every child receives customized attention, active engagement, and the tools to excel both spiritually and academically. Through systematic dual-track scheduling, students can memorize the entire Quran while securing top percentiles in Cambridge IGCSE examinations.', NULL, 'assets/img/subpages/aisl-qiraat-stage-reciter.jpg', 'Averroes Student Reciting at Quran Competition', 'books', 'normal', 'features', '3', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('4', 'why_averroes', 'merit', NULL, 'Rewarding Merit, Inspiring ', 'Brilliance', 'Excellence deserves recognition. Averroes maintains a proud tradition of celebrating scholastic triumph, leadership potential, and spiritual devotion through generous institutional scholarships and national awards.', NULL, 'assets/img/subpages/aisl-hifz-student-felicitation.jpg', 'Averroes Lalmatia Student Award Felicitation', 'merit', 'reverse', 'bullets', '4', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('5', 'why_averroes', 'universities', NULL, 'Targeting the World\'s Top ', 'Universities', 'From early secondary years, our dedicated University Guidance and Career Counseling Cell mentors students to curate competitive profiles, master standardized tests, and secure admissions with scholarships at elite institutions worldwide.', NULL, 'assets/img/subpages/aisl-progression-auditorium.jpg', 'Averroes International University Progression Convocation', 'globe', 'normal', 'bullets', '5', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('6', 'why_averroes', 'cocurricular', NULL, 'Co-Curricular & Sports ', 'Excellence', 'True education extends far beyond textbooks. At Averroes, athletic vigor, creative expression, and teamwork are essential pillars of student development. Our campus infrastructure provides specialized professional facilities rarely matched by other institutions in Bangladesh.', NULL, 'assets/img/subpages/aisl-swimming-pool-students.jpg', 'Averroes Swimming Club Students', 'sports', 'reverse', 'bullets', '6', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('7', 'life_at_averroes', 'hero', 'STUDENT LIFE AT AVERROES', 'Every Day Inspires New Ideas, Passion, and Character', NULL, 'At Averroes International School, education is not confined to textbooks—it is an invigorating daily adventure of intellectual discovery, spiritual grounding, and vibrant friendship. Rooted in our philosophy of nurturing the whole child for \"Here & Hereafter\", every morning opens new horizons for our students to lead with confidence, learn with joy, and live with purposeful conviction.', NULL, 'assets/img/subpages/aisl-student-life-hero.jpg', 'Averroes Student Life Explorer', NULL, 'normal', 'bullets', '1', '1', '2026-11-11 23:58:09', '2026-11-12 11:07:00'),
('8', 'life_at_averroes', 'learning', NULL, 'A Day of Purposeful ', 'Learning', 'Students engage in 8 structured, vibrant periods each day, seamlessly balancing the world-renowned Pearson Edexcel and Cambridge international curricula with Quran memorization, Arabic language proficiency, and Islamic studies.', NULL, 'assets/img/subpages/aisl-hifz-classroom-hd.jpg', 'Averroes Students Engaged in Purposeful Classroom Learning', 'aura', 'normal', 'bullets', '2', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('9', 'life_at_averroes', 'movement', NULL, 'Movement, Expression & Creative ', 'Exploration', 'Physical vitality, creative expression, and teamwork are vital cornerstones of life at Averroes. We nurture disciplined athletes, eloquent speakers, and inspired artists through exceptional sporting and cultural avenues.', NULL, 'assets/img/subpages/aisl-swimming-pool-students.jpg', 'Averroes Swimming Club & Sports', 'motion', 'reverse', 'bullets', '3', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('10', 'life_at_averroes', 'beyond', NULL, 'Beyond the ', 'Classroom', 'Some of the most powerful lessons are learned beyond campus walls. Through educational field trips, environmental excursions, community welfare drives, and leadership camps, Averroes students apply their knowledge in the real world.', NULL, 'assets/img/subpages/aisl-bus-service-launched.jpg', 'Averroes School Bus Fleet and Beyond the Classroom', 'bus', 'normal', 'bullets', '4', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('11', 'life_at_averroes', 'achievement', NULL, 'Achievement Through ', 'Challenge', 'We encourage our students to test their abilities, step outside their comfort zones, and strive for peak distinction in national and international arenas.', NULL, 'assets/img/subpages/aisl-hifz-student-felicitation.jpg', 'Averroes Student Award Presentation & Trophies', 'trophy', 'reverse', 'bullets', '5', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('12', 'life_at_averroes', 'pastoral', NULL, 'Personal Growth and Pastoral ', 'Care', 'Every child\'s emotional, moral, and physical well-being is held sacred. Through our comprehensive pastoral care framework, house mentorship system, and nurturing Tarbiyah guidance, students grow up in a safe, compassionate, and uplifting atmosphere.', NULL, 'assets/img/subpages/aisl-lounge-interior-lighting.jpg', 'Averroes Pastoral Care & Guardians Lounge', 'jigsaw', 'normal', 'bullets', '6', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('13', 'contact', 'hero', 'CONNECT WITH AVERROES INTERNATIONAL SCHOOL', 'We\'d Love to ', 'Hear From You', 'Whether you are a prospective parent seeking admission guidance for your child, a current member of our school community, or simply curious to learn more about our distinctive synthesis of Cambridge/Edexcel curricula and Islamic Tarbiyah, we are here to assist. Our dedicated admissions counselors, academic leads, and administrative coordinators are eager to welcome you. Reach out through the form below, call our helpline, or visit our campuses in person — we look forward to connecting with your family!', NULL, NULL, NULL, NULL, 'normal', 'bullets', '1', '1', '2026-11-11 23:58:09', '2026-11-11 23:58:09'),
('14', 'admission', 'hero', 'ADMISSIONS OPEN FOR SESSION 2026–2027', 'Open the Doors to a ', 'Brighter Future', 'Thank you for considering Averroes International School for your child\'s educational journey. Guided by our foundational motto — \"One School Serving the Purposes of Here & Hereafter\" — we deliver an unmatched synthesis of internationally recognized British curricula (Cambridge & Pearson Edexcel), advanced STEM learning, and deep-rooted Islamic values (Tarbiyah).', NULL, NULL, NULL, NULL, 'normal', 'bullets', '1', '1', '2026-11-12 11:35:26', '2026-11-12 11:35:26'),
('15', 'admission', 'assessment', NULL, 'Structured ', 'Assessment', 'At Averroes, assessment is designed to identify and celebrate each child\'s unique learning style and foundational preparedness in a supportive, stress-free atmosphere.', 'The selection of candidates is purely merit-based according to assessment performance and vacancy availability. Results are strictly confidential and governed by the Academic Board.', NULL, NULL, NULL, 'normal', 'bullets', '2', '1', '2026-11-12 12:01:25', '2026-11-12 12:01:25'),
('16', 'admission', 'session_docs', NULL, 'Academic Session & Required ', 'Documents', 'Ensure all required photocopies and attested documents are submitted with your application dossier.', NULL, NULL, NULL, NULL, 'normal', 'bullets', '3', '1', '2026-11-12 12:01:25', '2026-11-12 12:01:25');

-- --------------------------------------------------------------------
-- Table structure for `subpage_items`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `subpage_items`;
CREATE TABLE `subpage_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  `page_key` varchar(50) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `item_type` varchar(50) NOT NULL DEFAULT 'bullet',
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `val` varchar(100) DEFAULT NULL,
  `lbl` varchar(150) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_item_content` (`content_id`),
  KEY `idx_item_page_sec` (`page_key`,`section_key`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `subpage_items`
INSERT INTO `subpage_items` (`id`, `content_id`, `page_key`, `section_key`, `item_type`, `title`, `subtitle`, `description`, `val`, `lbl`, `icon`, `link`, `sort_order`, `is_active`) VALUES
('1', '1', 'why_averroes', 'hero', 'metric', NULL, NULL, NULL, '100%', 'Cambridge & Edexcel Pass Rate', NULL, NULL, '1', '1'),
('2', '1', 'why_averroes', 'hero', 'metric', NULL, NULL, NULL, '5.5h', 'Daily Hifzul Quran Balanced Track', NULL, NULL, '2', '1'),
('3', '1', 'why_averroes', 'hero', 'metric', NULL, NULL, NULL, '100+', 'Global & National Placements', NULL, NULL, '3', '1'),
('4', '2', 'why_averroes', 'research', 'bullet', 'Dual International & National Recognition', NULL, 'Approved by the Ministry of Education Bangladesh and recognized by the British Council as a premier examination center for Pearson Edexcel and Cambridge IGCSE / GCE A-Levels.', NULL, NULL, NULL, NULL, '1', '1'),
('5', '2', 'why_averroes', 'research', 'bullet', 'Advanced STEM & Robotics Laboratories', NULL, 'State-of-the-art physics, chemistry, biology, and computer science labs fostering inquiry-based scientific exploration, robotics, and coding.', NULL, NULL, NULL, NULL, '2', '1'),
('6', '2', 'why_averroes', 'research', 'bullet', 'Curriculum Advisory Council', NULL, 'Academic curricula continuously enriched by an advisory council of experienced university professors, foreign lecturers, and senior English-medium pedagogues.', NULL, NULL, NULL, NULL, '3', '1'),
('7', '3', 'why_averroes', 'pedagogy', 'bullet', 'Visible Learning & Formative Assessment', NULL, 'Continuous diagnostic feedback, benchmark testing, and personalized learning plans ensure zero academic blind spots from primary through A-Levels.', NULL, NULL, NULL, NULL, '1', '1'),
('8', '3', 'why_averroes', 'pedagogy', 'bullet', '5.5-Hour Daily Hifzul Quran Integration', NULL, 'A structured daily regimen (3.5 hours morning recitation + 2 hours evening revision) with dedicated Junior, Mid, and Senior Hifz wings, ensuring balanced academic progression.', NULL, NULL, NULL, NULL, '2', '1'),
('9', '3', 'why_averroes', 'pedagogy', 'bullet', 'Faculty from Al-Azhar & Madinah University', NULL, 'Our Islamic Studies and Arabic Foreign Language departments are steered by distinguished scholars holding degrees from Al-Azhar University (Cairo) and the Islamic University of Madinah.', NULL, NULL, NULL, NULL, '3', '1'),
('10', '3', 'why_averroes', 'pedagogy', 'bullet', 'Gender-Appropriate Learning Environments', NULL, 'Thoughtfully separated classroom wings and campus sections for boys and girls ensure utmost modesty, personal comfort, and focused academic concentration.', NULL, NULL, NULL, NULL, '4', '1'),
('11', '4', 'why_averroes', 'merit', 'bullet', 'Cambridge & Edexcel High Achievers Awards', NULL, 'Honoring students who achieve Country Highest and World Highest percentile scores in Pearson Edexcel and Cambridge board examinations.', NULL, NULL, NULL, NULL, '1', '1'),
('12', '4', 'why_averroes', 'merit', 'bullet', 'The Daily Star National High Achievers Recognition', NULL, 'Regular felicitations at national platforms honoring O and A Level straight A/A* scholars.', NULL, NULL, NULL, NULL, '2', '1'),
('13', '4', 'why_averroes', 'merit', 'bullet', 'Hafiz-e-Quran Distinction Scholarships', NULL, 'Full and partial tuition waivers granted to students who successfully complete complete memorization of the Holy Quran.', NULL, NULL, NULL, NULL, '3', '1'),
('14', '4', 'why_averroes', 'merit', 'bullet', 'Academic & Talent Merit Waivers', NULL, 'Comprehensive financial aid and merit scholarships for deserving scholars demonstrating intellectual distinction and exemplary moral character.', NULL, NULL, NULL, NULL, '4', '1'),
('15', '5', 'why_averroes', 'universities', 'bullet', 'Averroes Student Development Program (ASDP)', NULL, 'Tailored profile building, research projects, Model United Nations (MUN), and international leadership retreats.', NULL, NULL, NULL, NULL, '1', '1'),
('16', '5', 'why_averroes', 'universities', 'bullet', 'Global Higher Education Pathways', NULL, 'Direct admissions into leading institutions across the UK (Oxford, Cambridge, Imperial, UCL), USA, Canada (Toronto, UBC), and Australia (Monash University, Melbourne).', NULL, NULL, NULL, NULL, '2', '1'),
('17', '5', 'why_averroes', 'universities', 'bullet', 'National Elite University Success', NULL, 'Exemplary entry records into BUET, IBA (Dhaka University), and government medical colleges.', NULL, NULL, NULL, NULL, '3', '1'),
('18', '5', 'why_averroes', 'universities', 'bullet', 'Standardized Exam & Profile Masterclass', NULL, 'In-house preparation for IELTS, SAT, SOP writing, and university interview coaching.', NULL, NULL, NULL, NULL, '4', '1'),
('19', '6', 'why_averroes', 'cocurricular', 'bullet', 'Averroes Swimming Club', NULL, 'Professional semi-Olympic standard pool with certified male and female coaches, separate timing, and safety lifeguards.', NULL, NULL, NULL, NULL, '1', '1'),
('20', '6', 'why_averroes', 'cocurricular', 'bullet', 'Averroes Turf', NULL, 'FIFA-standard artificial green turf arena for inter-school football tournaments, cricket, and athletic training.', NULL, NULL, NULL, NULL, '2', '1'),
('21', '6', 'why_averroes', 'cocurricular', 'bullet', 'Annual Sports & Summer Camps', NULL, 'City-wide tournaments, seasonal athletics bootcamps, and martial arts training.', NULL, NULL, NULL, NULL, '3', '1'),
('22', '6', 'why_averroes', 'cocurricular', 'bullet', 'Student Leadership & Specialized Clubs', NULL, 'Active Debate Society, Quran & Husnut Tilawah Club, Robotics Club, Seerah Exhibition, and the Averroes Students\' Representative Council.', NULL, NULL, NULL, NULL, '4', '1'),
('23', '7', 'life_at_averroes', 'hero', 'metric', NULL, NULL, NULL, '8', 'Dynamic Daily Periods', NULL, NULL, '1', '1'),
('24', '7', 'life_at_averroes', 'hero', 'metric', NULL, NULL, NULL, '15+', 'Active Clubs & Societies', NULL, NULL, '2', '1'),
('25', '7', 'life_at_averroes', 'hero', 'metric', NULL, NULL, NULL, '100%', 'Daily Congregational Salah', NULL, NULL, '3', '1'),
('26', '8', 'life_at_averroes', 'learning', 'bullet', 'Cambridge & Edexcel Core Academics', NULL, 'Advanced English Literature, Mathematics, Pure Sciences, and Global Perspectives taught with interactive pedagogy.', NULL, NULL, NULL, NULL, '1', '1'),
('27', '8', 'life_at_averroes', 'learning', 'bullet', 'Bangla & National Heritage Studies', NULL, 'Deep pride in national history, cultural heritage, and Bengali literature.', NULL, NULL, NULL, NULL, '2', '1'),
('28', '8', 'life_at_averroes', 'learning', 'bullet', '5.5-Hour Hifzul Quran & Halaqah Sessions', NULL, 'Structured daily memorization, Tajweed, and recitation with zero academic lag.', NULL, NULL, NULL, NULL, '3', '1'),
('29', '8', 'life_at_averroes', 'learning', 'bullet', 'Quranic Arabic & Foreign Language Program', NULL, 'Systematic Arabic language immersion led by Al-Azhar and Madinah University scholars.', NULL, NULL, NULL, NULL, '4', '1'),
('30', '9', 'life_at_averroes', 'movement', 'bullet', 'Averroes Swimming Club & Karate Club', NULL, 'Certified professional coaches providing Olympic-standard swim training, self-defense, and martial arts discipline with dedicated separate sessions.', NULL, NULL, NULL, NULL, '1', '1'),
('31', '9', 'life_at_averroes', 'movement', 'bullet', 'Averroes Turf Football & Cricket Arena', NULL, 'FIFA-standard artificial green turf hosting regular house matches and inter-school tournaments.', NULL, NULL, NULL, NULL, '2', '1'),
('32', '9', 'life_at_averroes', 'movement', 'bullet', 'Cultural Forum & Writers Guild', NULL, 'Islamic nasheed, poetry recitations, creative storytelling, calligraphy, and annual student publications.', NULL, NULL, NULL, NULL, '3', '1'),
('33', '9', 'life_at_averroes', 'movement', 'bullet', 'English & Bangla Debating Society', NULL, 'Public speaking masterclasses, Model United Nations (MUN), and parliamentary debate training.', NULL, NULL, NULL, NULL, '4', '1'),
('34', '10', 'life_at_averroes', 'beyond', 'bullet', 'Fleet of GPS-Monitored School Buses', NULL, 'Safe, comfortable daily transportation across Dhaka equipped with real-time GPS tracking and dedicated attendants.', NULL, NULL, NULL, NULL, '1', '1'),
('35', '10', 'life_at_averroes', 'beyond', 'bullet', 'Social Welfare & Community Service Club', NULL, 'Food distribution, humanitarian relief campaigns, and orphan support programs teaching empathy and social responsibility.', NULL, NULL, NULL, NULL, '2', '1'),
('36', '10', 'life_at_averroes', 'beyond', 'bullet', 'Science Fairs & Heritage Excursions', NULL, 'Hands-on botanical visits, planetarium excursions, and national museum heritage tours.', NULL, NULL, NULL, NULL, '3', '1'),
('37', '10', 'life_at_averroes', 'beyond', 'bullet', 'Annual Seerah Exhibition', NULL, 'City-wide interactive cultural exhibitions bringing the prophetic biography to life.', NULL, NULL, NULL, NULL, '4', '1'),
('38', '11', 'life_at_averroes', 'achievement', 'bullet', 'Husnut Tilawah & Qira\'at Competitions', NULL, 'Annual inter-campus Quran recitation contests judged by world-renowned Qaris.', NULL, NULL, NULL, NULL, '1', '1'),
('39', '11', 'life_at_averroes', 'achievement', 'bullet', 'Pearson Edexcel & Cambridge High Achievers', NULL, 'Consistent World Highest and Country Highest distinctions in O and A Levels.', NULL, NULL, NULL, NULL, '2', '1'),
('40', '11', 'life_at_averroes', 'achievement', 'bullet', 'National Olympiads & Robotics Battles', NULL, 'High honors in Mathematics Olympiads, Bangladesh Junior Science Olympiads, and inter-school robotics hackathons.', NULL, NULL, NULL, NULL, '3', '1'),
('41', '11', 'life_at_averroes', 'achievement', 'bullet', 'Inter-School Sports Championships', NULL, 'Trophies in regional football tournaments, cricket leagues, and swimming galas.', NULL, NULL, NULL, NULL, '4', '1'),
('42', '12', 'life_at_averroes', 'pastoral', 'bullet', 'Structured Tarbiyah & Moral Mentorship', NULL, 'Daily ethical discussions instilling integrity, modesty, humility, and leadership.', NULL, NULL, NULL, NULL, '1', '1'),
('43', '12', 'life_at_averroes', 'pastoral', 'bullet', 'Dedicated Student Counseling Cell', NULL, 'Professional guidance counselors supporting mental well-being, peer collaboration, and academic confidence.', NULL, NULL, NULL, NULL, '2', '1'),
('44', '12', 'life_at_averroes', 'pastoral', 'bullet', 'Gender-Segregated Learning Environments', NULL, 'Purposefully separate classrooms and facilities for boys and girls, ensuring comfort, respect, and focused development.', NULL, NULL, NULL, NULL, '3', '1'),
('45', '12', 'life_at_averroes', 'pastoral', 'bullet', 'Guardians\' Partnership & Waiting Lounge', NULL, 'Modern dedicated lounge for parents with regular parent-teacher dialogues and pastoral conferences.', NULL, NULL, NULL, NULL, '4', '1'),
('46', '13', 'contact', 'hero', 'quick_contact', NULL, NULL, NULL, '+880 1954-123 123', 'Direct Helpline', 'phone', 'tel:+8801954123123', '1', '1'),
('47', '13', 'contact', 'hero', 'quick_contact', NULL, NULL, NULL, '+880 1954-123 123', 'WhatsApp Instant', 'whatsapp', 'https://wa.me/8801954123123', '2', '1'),
('48', '13', 'contact', 'hero', 'quick_contact', NULL, NULL, NULL, 'info@aisl.edu.bd', 'Official Desk', 'email', 'mailto:info@aisl.edu.bd', '3', '1'),
('49', '15', 'admission', 'assessment', 'card', 'Play Group and Nursery', NULL, 'No written tests are conducted for Play Group and Nursery. A gentle, friendly oral interaction with the child and the parents is conducted. Attendance of both the father and the mother is mandatory to ensure unified pastoral partnership.', NULL, 'Early Years', NULL, NULL, '1', '1'),
('50', '15', 'admission', 'assessment', 'card', 'KG-I & KG-II (Kindergarten)', NULL, 'Children participate in interactive oral and visual cognitive assessments. The review focuses on foundational phonics, number familiarity (1–50), color and shape recognition, active listening, and social responsiveness.', NULL, 'Kindergarten', NULL, NULL, '2', '1');
INSERT INTO `subpage_items` (`id`, `content_id`, `page_key`, `section_key`, `item_type`, `title`, `subtitle`, `description`, `val`, `lbl`, `icon`, `link`, `sort_order`, `is_active`) VALUES
('51', '15', 'admission', 'assessment', 'card', 'Class 1 to Class 8 (Primary & Middle School)', NULL, 'Candidates appear for written diagnostic assessments in English Language, Mathematics, and Bengali. Only candidates who successfully meet the benchmark qualify for the subsequent oral session with both parents.', NULL, 'Standard 1–8', NULL, NULL, '3', '1'),
('52', '15', 'admission', 'assessment', 'card', 'Class 9 & 10 (Pearson Edexcel & Cambridge O Level)', NULL, 'Admission into Class IX and X is subject to selective seat availability, as the formal 3-year O-Level curriculum starts from Class 8. Prior academic transcripts, conduct certificates, and stream selection interviews are required.', NULL, 'O Level Track', NULL, NULL, '4', '1'),
('53', '15', 'admission', 'assessment', 'card', 'Class 11 & 12 (Cambridge & Edexcel International A Level)', NULL, 'Admissions occur in two distinct annual cycles: Spring Stream (classes commencing in January/February) and Autumn Stream (classes commencing in July/August). Admission requires verified O-Level transcripts with high grades.', NULL, 'A Level Dual Intake', NULL, NULL, '5', '1'),
('54', '16', 'admission', 'academic_sessions', 'session', 'Playgroup to Class 8', 'July to June Annual Session (Main Academic Cycle)', NULL, NULL, NULL, NULL, NULL, '1', '1'),
('55', '16', 'admission', 'academic_sessions', 'session', 'O Level (Class 9 & 10)', '3-Year Integrated Cambridge/Edexcel Board Cycle', NULL, NULL, NULL, NULL, NULL, '2', '1'),
('56', '16', 'admission', 'academic_sessions', 'session', 'A Level Stream A', 'January / February Intake (Accelerated & Standard)', NULL, NULL, NULL, NULL, NULL, '3', '1'),
('57', '16', 'admission', 'academic_sessions', 'session', 'A Level Stream B', 'July / August Intake (Standard Full 2-Year Program)', NULL, NULL, NULL, NULL, NULL, '4', '1'),
('58', '16', 'admission', 'document_checklist', 'doc', '4 recent passport-size color photographs of the student.', NULL, NULL, NULL, NULL, NULL, NULL, '1', '1'),
('59', '16', 'admission', 'document_checklist', 'doc', '2 recent passport-size color photographs of each parent / guardian.', NULL, NULL, NULL, NULL, NULL, NULL, '2', '1'),
('60', '16', 'admission', 'document_checklist', 'doc', 'Attested photocopy of Digital Birth Certificate (English / Bilingual format).', NULL, NULL, NULL, NULL, NULL, NULL, '3', '1'),
('61', '16', 'admission', 'document_checklist', 'doc', 'Photocopies of previous two years\' official academic report cards.', NULL, NULL, NULL, NULL, NULL, NULL, '4', '1'),
('62', '16', 'admission', 'document_checklist', 'doc', 'Transfer Certificate (TC) / Clearance letter from the last attended institution.', NULL, NULL, NULL, NULL, NULL, NULL, '5', '1'),
('63', '16', 'admission', 'document_checklist', 'doc', 'Photocopies of parents\' National Identity (NID) Cards or Passports.', NULL, NULL, NULL, NULL, NULL, NULL, '6', '1'),
('64', '16', 'admission', 'document_checklist', 'doc', 'Photocopy of parents\' Tax Return Acknowledgment / TIN Certificate.', NULL, NULL, NULL, NULL, NULL, NULL, '7', '1');

-- --------------------------------------------------------------------
-- Table structure for `topbar_items`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `topbar_items`;
CREATE TABLE `topbar_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `side` enum('left','right') NOT NULL DEFAULT 'left',
  `text` varchar(255) NOT NULL DEFAULT '',
  `has_icon` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `topbar_items`
INSERT INTO `topbar_items` (`id`, `side`, `text`, `has_icon`, `sort_order`, `is_active`) VALUES
('1', 'left', 'Lalmatia: admin@aisl.edu.bd', '0', '1', '1'),
('3', 'right', 'Lalmatia : +880 1954-123 123', '1', '1', '1'),
('4', 'right', 'Banasree : +88 01901-048844', '1', '2', '1');

SET FOREIGN_KEY_CHECKS = 1;
