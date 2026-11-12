<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Content Repository (Sections, Stats, Programs, Campuses, Partners)
 */

if (!function_exists('get_section')) {
    /**
     * Get section information
     */
    function get_section(?PDO $pdo, string $key): array {
        $fallback = [
            'why' => [
                'heading_plain' => 'Why ',
                'heading_em'    => 'Averroes?',
                'icon_image'    => 'assets/img/cap.svg',
                'body'          => 'Averroes International School (AISL) is a globally recognized, award-winning English-medium educational institution approved by the Ministry of Education, Bangladesh, and accredited by Cambridge Assessment International Education and Pearson Edexcel. Founded with the vision of harmonizing academic excellence with deep-rooted Islamic values and modern science, Averroes provides an integrated bicultural education from Playgroup through A-Levels. We foster intellectual curiosity, moral discipline, and 21st-century technological readiness—equipping students to excel both in this world and the Hereafter.',
                'button_text'   => 'Read More',
                'button_url'    => 'why-averroes.php',
                'image'         => 'assets/img/rocket.svg',
                'image_alt'     => 'Pencil rocket illustration',
            ],
            'first' => [
                'heading_plain' => 'First Time In ',
                'heading_em'    => 'Bangladesh',
                'icon_image'    => 'assets/img/cap.svg',
                'body'          => '',
                'button_text'   => '',
                'button_url'    => '',
                'image'         => '',
                'image_alt'     => '',
            ],
            'campuses' => [
                'heading_plain' => 'Discover Our ',
                'heading_em'    => 'Campuses',
                'icon_image'    => 'assets/img/cap.svg',
                'body'          => '',
                'button_text'   => '',
                'button_url'    => '',
                'image'         => '',
                'image_alt'     => '',
            ],
        ];

        if ($pdo === null) {
            return $fallback[$key] ?? [];
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM sections WHERE section_key = :k LIMIT 1");
            $stmt->execute(['k' => $key]);
            $row = $stmt->fetch();
            if ($row) {
                if ($key === 'why' && (empty($row['button_url']) || $row['button_url'] === '#' || $row['button_url'] === '#why')) {
                    $row['button_url'] = 'why-averroes.php';
                }
                return $row;
            }
            return $fallback[$key] ?? [];
        } catch (Throwable $e) {
            error_log('[get_section] ' . $e->getMessage());
            return $fallback[$key] ?? [];
        }
    }
}

if (!function_exists('get_stats')) {
    /**
     * Get stats items
     */
    function get_stats(?PDO $pdo): array {
        $fallback = [
            ['display_value' => '275+',  'count_to' => '275',  'suffix' => '+', 'label' => 'Teachers'],
            ['display_value' => '99.9%', 'count_to' => '99.9', 'suffix' => '%', 'label' => 'Average Passing Score'],
            ['display_value' => '12',    'count_to' => '12',   'suffix' => '',  'label' => 'World Toppers Since 2015'],
            ['display_value' => '1:10',  'count_to' => null,   'suffix' => '',  'label' => 'Student-Teacher Ratio'],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->query("SELECT * FROM stats WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
            $rows = $stmt->fetchAll();
            return !empty($rows) ? $rows : $fallback;
        } catch (Throwable $e) {
            error_log('[get_stats] ' . $e->getMessage());
            return $fallback;
        }
    }
}

if (!function_exists('get_programs')) {
    /**
     * Get programs for slider
     */
    function get_programs(?PDO $pdo): array {
        $fallback = [
            [
                'id' => 1,
                'title' => "Hifzul Qur'an with British Curriculum",
                'slug' => 'hifzul-quran-with-british-curriculum',
                'subtitle' => 'Dual Excellence: Cambridge Academic Rigor & Tahfeezul Qur\'an',
                'summary' => "Memorising the Holy Qur'an while continuing the British Curriculum — nurturing strong Islamic values alongside academic excellence.",
                'lead_text' => "Averroes International School is recognized by the Ministry of Education and authorized by the British Council and Pearson Edexcel. As a pioneering initiative in an Islamic English Medium school in Bangladesh, our full-time and integrated Hifzul Qur'an program empowers students to memorize the 30 paras of the Holy Qur'an while seamlessly progressing through their mainstream British curriculum without academic compromise.",
                'body_html' => "<p>Under the continuous guidance of certified Huffaz and Cambridge-trained educators, learners master correct Tajweed phonetics, accurate Makharij, and systematic daily revision. Our balanced timetable ensures that spiritual devotion and Quranic memorization strengthen cognitive concentration, photographic memory, and linguistic mastery in English and Arabic.</p><p>Students participate in daily <em>Halaqatul Qur'an</em> and <em>Deeniyat</em> character circles, absorbing timeless moral ethics such as honesty, discipline, and empathy. The curriculum guarantees that upon completion, students transition effortlessly into senior Edexcel O Level and A Level studies, ready to excel in top global universities as moral leaders.</p>",
                'icon' => 'assets/img/quran.svg',
                'banner_image' => 'assets/img/subpages/aisl-hifz-convocation-banner.jpg',
                'image_row1' => 'assets/img/subpages/aisl-hifz-convocation-banner.jpg',
                'image_row2' => 'assets/img/subpages/aisl-hifz-student-felicitation.jpg',
                'link_url' => 'program.php?slug=hifzul-quran-with-british-curriculum',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 2,
                'title' => "Qira'at Competition",
                'slug' => 'qiraat-competition',
                'subtitle' => 'Annual Husnut Tilawah National Championship & Tajweed Mastery',
                'summary' => "Inspiring students to improve Tajweed, pronunciation and confidence through meaningful and engaging Qur'an recitation competitions.",
                'lead_text' => "The annual Averroes Qira'at Competition is a celebrated institutional tradition that inspires students from Playgroup through A Levels to beautify their voices with the words of Allah, master classical Tajweed rules, and build immense public stage composure before distinguished international Qaris and scholars.",
                'body_html' => "<p>Reciting the Holy Qur'an with reverent melody and flawless phonetic articulation is both a sacred discipline and an art form. Through multi-tier classroom heats, campus semi-finals, and the Grand Inter-Campus Championship, students learn vocal modulation, breath control, and classical Maqamat under certified Arabic phonetics masters.</p><p>Finalists perform in an auditorium equipped with international-standard acoustics, evaluated by an independent jury of globally recognized scholars. Top reciters receive commemorative crests, achievement certificates, and academic scholarships, cultivating a lifelong love for the Qur'an that resonates across the entire school family.</p>",
                'icon' => 'assets/img/qirat.svg',
                'banner_image' => 'assets/img/subpages/aisl-hifz-classroom-hd.jpg',
                'image_row1' => 'assets/img/subpages/aisl-hifz-classroom-hd.jpg',
                'image_row2' => 'assets/img/subpages/aisl-qiraat-students-group.jpg',
                'link_url' => 'program.php?slug=qiraat-competition',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 3,
                'title' => 'Seerah Exhibition',
                'slug' => 'seerah-exhibition',
                'subtitle' => 'Exploring the Blessed Life & Timeless Character of Prophet Muhammad ﷺ',
                'summary' => "A creative and educational initiative helping students explore the life, character, and noble values of Prophet Muhammad ﷺ through interactive displays.",
                'lead_text' => "The Averroes Seerah Exhibition is an immersive, student-crafted showcase bringing the compassionate life, historical milestones, and noble character of Prophet Muhammad ﷺ to vibrant life. Through hands-on research and experiential learning, students connect textbook history with living morality.",
                'body_html' => "<p>Students work under the mentorship of our Islamic studies and history faculty to construct handmade 3D architectural scale models of ancient Makkah, historic Madinah, and the migration routes of the Hijrah. Interactive multimedia timeline boards and artifact dioramas provide visitors with a panoramic walkthrough of early Islamic civilization.</p><p>During the multi-day exhibition, students serve as docents and presenters, eloquently articulating universal values such as social justice, forgiveness, honesty (Ameen), and leadership to visiting parents, educators, and foreign dignitaries. This signature event nurtures public speaking mastery and deep pride in Islamic heritage.</p>",
                'icon' => 'assets/img/seerah.svg',
                'banner_image' => 'assets/img/subpages/aisl-seerah-sealed-nectar.jpg',
                'image_row1' => 'assets/img/subpages/aisl-seerah-sealed-nectar.jpg',
                'image_row2' => 'assets/img/subpages/aisl-seerah-arabic-conversation.jpg',
                'link_url' => 'program.php?slug=seerah-exhibition',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 4,
                'title' => 'Averroes Swimming Club',
                'slug' => 'averroes-swimming-club',
                'subtitle' => 'Aquatic Excellence, Water Safety & Structured Physical Fitness',
                'summary' => 'Developing swimming skills, physical fitness, confidence and water safety through structured and enjoyable student activities.',
                'lead_text' => "Physical stamina and water safety awareness are vital life skills emphasized in the Sunnah. The Averroes Swimming Club offers world-class aquatic training in modern, temperature-controlled campus pool facilities, instilling physical endurance, mental resilience, and healthy athletic discipline from an early age.",
                'body_html' => "<p>Our aquatic curriculum takes students from initial water confidence and breathing techniques to complete mastery of the four competitive strokes: Freestyle, Breaststroke, Backstroke, and Butterfly. All training sessions are strictly segregated by gender with dedicated schedules for boys and girls, supervised continuously by certified swimming coaches and CPR-trained lifeguards.</p><p>Beyond physical conditioning, swimming enhances cardiovascular health, motor coordination, and mental focus. Club members represent Averroes in inter-school aquatic galas, developing athletic sportsmanship and essential life-saving water survival competencies.</p>",
                'icon' => 'assets/img/swimming.svg',
                'banner_image' => 'assets/img/subpages/aisl-swimming-olympic-pool.jpg',
                'image_row1' => 'assets/img/subpages/aisl-swimming-olympic-pool.jpg',
                'image_row2' => 'assets/img/subpages/aisl-swimming-club-splash.jpg',
                'link_url' => 'program.php?slug=averroes-swimming-club',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 5,
                'title' => 'Averroes Turf',
                'slug' => 'averroes-turf',
                'subtitle' => 'High-Performance Rooftop Sports Arena & Outdoor Athletics',
                'summary' => 'A dedicated modern sports facility providing a safe and engaging space for physical exercise, football, athletics, and teamwork.',
                'lead_text' => "Overcoming urban space constraints, Averroes Turf is our state-of-the-art rooftop sports arena. Engineered with shock-absorbing, FIFA-standard artificial grass turf and surrounded by high-tension protective safety nets, it provides students with an all-weather venue for football, cricket, track events, and structured physical training.",
                'body_html' => "<p>Equipped with high-lumen LED floodlights, the turf allows for multi-sport training in all seasons. Professional physical education instructors lead regular physical drills, tactical football leagues, cricket batting camps, and agility conditioning that foster teamwork, strategic thinking, and leadership.</p><p>By ensuring daily outdoor physical play, Averroes combats sedentary indoor screen habits, promoting cardiovascular stamina, peer camaraderie, and vibrant physical well-being across our entire student body.</p>",
                'icon' => 'assets/img/turf.svg',
                'banner_image' => 'assets/img/subpages/aisl-turf-camp-squad.jpg',
                'image_row1' => 'assets/img/subpages/aisl-turf-camp-squad.jpg',
                'image_row2' => 'assets/img/subpages/aisl-turf-green-arena.jpg',
                'link_url' => 'program.php?slug=averroes-turf',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 6,
                'title' => 'National & International University Progression',
                'slug' => 'national-international-university-progression',
                'subtitle' => 'Strategic Pathways to Top-Tier Global Universities & Prestigious Scholarships',
                'summary' => 'Comprehensive academic guidance and counseling preparing students for successful transitions to leading universities in Bangladesh and abroad.',
                'lead_text' => "The University Progression Office at Averroes International School bridges senior secondary education with world-ranking higher education institutions. We provide structured, one-on-one academic counseling that prepares students for direct entry into leading universities in the UK, USA, Canada, Australia, Malaysia, and top medical and engineering colleges in Bangladesh.",
                'body_html' => "<p>Beginning in Grade 9, students receive personalized guidance through the Averroes Student Development Programme (ASDP). Our full-time advisors assist students through UCAS applications, Common App essays, scholarship portfolios, and standardized entrance exam prep (SAT, IELTS, Cambridge Advanced Tests).</p><p>We host annual on-campus University Fairs where students and parents interact directly with visiting international university deans. Our alumni maintain an exceptional track record of securing significant merit scholarships, entering world-renowned institutions with academic brilliance and steadfast Islamic values.</p>",
                'icon' => 'assets/img/university.svg',
                'banner_image' => 'assets/img/subpages/aisl-progression-auditorium.jpg',
                'image_row1' => 'assets/img/subpages/aisl-progression-auditorium.jpg',
                'image_row2' => 'assets/img/subpages/aisl-progression-graduates-group.jpg',
                'link_url' => 'program.php?slug=national-international-university-progression',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 7,
                'title' => 'Summer Camp',
                'slug' => 'summer-camp',
                'subtitle' => 'Holistic Youth Discovery: STEM Robotics, Archery, Martial Arts & Leadership',
                'summary' => 'An exciting blend of Islamic learning, creativity, sports, teamwork and fun beyond the regular classroom.',
                'lead_text' => "School breaks offer fertile ground for creative joy and character growth outside academic syllabuses. The annual Averroes Summer Camp is an action-packed holiday immersion blending hands-on STEM challenges, outdoor sports, creative arts, and inspiring Islamic stories to spark lifelong passions in young learners aged 4 to 15.",
                'body_html' => "<p>Campers step away from conventional desks to engage in introductory coding, robotics building, Arabic calligraphy, public speaking masterclasses, self-defense martial arts, archery, and team sports. Mentored by dynamic teachers and guest coaches, students build lasting cross-grade friendships in a positive, spiritually uplifting environment.</p><p>The camp culminates in a grand family exhibition where campers showcase their robotics creations, artwork, and stage performances to proud parents and community leaders.</p>",
                'icon' => 'assets/img/summer-camp.svg',
                'banner_image' => 'assets/img/subpages/aisl-summer-camp-banner-squad.jpg',
                'image_row1' => 'assets/img/subpages/aisl-summer-camp-banner-squad.jpg',
                'image_row2' => 'assets/img/subpages/aisl-summer-camp-coaching-lineup.jpg',
                'link_url' => 'program.php?slug=summer-camp',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 8,
                'title' => 'Guardians’ Waiting Lounge',
                'slug' => 'guardians-waiting-lounge',
                'subtitle' => 'Executive Comfort, Dignity & Hospitality for Our School Parents',
                'summary' => 'A comfortable and welcoming waiting space designed especially for the convenience of our valued parents and guardians.',
                'lead_text' => "Recognizing parents as vital partners in the educational journey, Averroes International School established dedicated, fully air-conditioned Guardians’ Waiting Lounges. This pioneering campus amenity provides parents with a comfortable, welcoming sanctuary during student drop-off, pick-up, and school meetings.",
                'body_html' => "<p>Gone are the days of parents enduring unpredictable weather or crowded outdoor streets. Our lounges feature ergonomic sofa seating, high-speed campus Wi-Fi, digital notice displays, clean restroom amenities, and complimentary tea and refreshments.</p><p>A dedicated parent-relations coordinator is stationed on-site to assist with administrative questions, schedule teacher consultations, and provide real-time bus departure updates, reflecting our institutional culture of respect, dignity, and family hospitality.</p>",
                'icon' => 'assets/img/lounge.svg',
                'banner_image' => 'assets/img/subpages/aisl-lounge-interior-lighting.jpg',
                'image_row1' => 'assets/img/subpages/aisl-lounge-interior-lighting.jpg',
                'image_row2' => 'assets/img/subpages/aisl-lounge-parents-meeting.jpg',
                'link_url' => 'program.php?slug=guardians-waiting-lounge',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 9,
                'title' => 'School Bus Service',
                'slug' => 'school-bus-service',
                'subtitle' => 'Safe, Air-Conditioned & Real-Time GPS-Monitored Student Transportation',
                'summary' => 'Safe, organized and convenient transportation designed to make students’ daily journey to and from school easier.',
                'lead_text' => "The daily school commute should be safe, comfortable, and punctual. Averroes International School operates a modern fleet of custom-branded, fully air-conditioned buses serving key residential neighborhoods across Dhaka, providing families with dependable, safe, and stress-free student transportation.",
                'body_html' => "<p>Every bus in our fleet is equipped with live GPS tracking monitored by our central transport desk, high-definition internal and external CCTV cameras, automated speed limiters, and first-aid kits adhering to strict safety protocols. Trained female attendants supervise students during every journey, ensuring seatbelt compliance, orderly behavior, and secure parent handovers.</p><p>By choosing the Averroes School Bus Service, parents save valuable time and fuel while students enjoy a comfortable, timely, and social start to every learning day.</p>",
                'icon' => 'assets/img/bus.svg',
                'banner_image' => 'assets/img/subpages/aisl-bus-ribbon-cutting.jpg',
                'image_row1' => 'assets/img/subpages/aisl-bus-ribbon-cutting.jpg',
                'image_row2' => 'assets/img/subpages/aisl-bus-service-launched.jpg',
                'link_url' => 'program.php?slug=school-bus-service',
                'link_text' => 'Learn More',
            ],
            [
                'id' => 10,
                'title' => 'Averroes Book Shop',
                'slug' => 'averroes-book-shop',
                'subtitle' => 'Authorized Curriculum Textbooks, Official Uniforms & Learning Kits',
                'summary' => 'Convenient access to books, educational materials and essential learning resources for the entire Averroes community.',
                'lead_text' => "The on-campus Averroes Book Shop simplifies academic preparation by providing guaranteed access to authentic, publisher-certified Pearson Edexcel, Cambridge, and Oxford curriculum textbooks, specialized workbooks, and uniforms under one roof at standardized, fair institutional rates.",
                'body_html' => "<p>We eliminate the hassle of searching through external markets by maintaining comprehensive on-site inventories of authorized curriculum literature, Islamic studies publications, Arabic readers, science laboratory kits, and premium stationery essentials.</p><p>The store also supplies tailored Averroes school uniforms, sports kits, and badges crafted from durable, breathable fabrics adhering to school dress codes. At the start of each academic session, pre-packaged grade bundles ensure families transition seamlessly into the new term with confidence.</p>",
                'icon' => 'assets/img/bookshop.svg',
                'banner_image' => 'assets/img/subpages/aisl-bookshop-main-hall.jpg',
                'image_row1' => 'assets/img/subpages/aisl-bookshop-main-hall.jpg',
                'image_row2' => 'assets/img/subpages/aisl-bookshop-aisle-shelves.jpg',
                'link_url' => 'program.php?slug=averroes-book-shop',
                'link_text' => 'Learn More',
            ],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->query("SELECT * FROM programs WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
            $rows = $stmt->fetchAll();
            return !empty($rows) ? $rows : $fallback;
        } catch (Throwable $e) {
            error_log('[get_programs] ' . $e->getMessage());
            return $fallback;
        }
    }
}

if (!function_exists('get_program_by_slug')) {
    /**
     * Get a single program by its slug or ID
     */
    function get_program_by_slug(?PDO $pdo, string $slugOrId): ?array {
        $slugOrId = trim($slugOrId);
        if ($slugOrId === '') {
            return null;
        }

        if ($pdo !== null) {
            try {
                if (is_numeric($slugOrId)) {
                    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = :val AND is_active = 1 LIMIT 1");
                    $stmt->execute(['val' => (int)$slugOrId]);
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM programs WHERE slug = :val AND is_active = 1 LIMIT 1");
                    $stmt->execute(['val' => $slugOrId]);
                }
                $row = $stmt->fetch();
                if ($row) {
                    return $row;
                }
            } catch (Throwable $e) {
                error_log('[get_program_by_slug] ' . $e->getMessage());
            }
        }

        // Fallback array lookup
        $all = get_programs(null);
        foreach ($all as $item) {
            if (is_numeric($slugOrId) && ($item['id'] ?? 0) === (int)$slugOrId) {
                return $item;
            }
            if (!empty($item['slug']) && $item['slug'] === $slugOrId) {
                return $item;
            }
        }

        return null;
    }
}

if (!function_exists('get_campuses')) {
    /**
     * Get campuses (for campus grid or footer)
     */
    function get_campuses(?PDO $pdo, bool $gridOnly = false): array {
        $fallback = [
            [
                'name'           => 'Averroes International School, Lalmatia',
                'footer_heading' => 'Averroes International School, Lalmatia',
                'phone'          => "+880 1949-000 555\n+880 1714 622 211",
                'email'          => 'admin@aisl.edu.bd',
                'address'        => "House No – 7/16, Block – B, Lalmatia, Mohammadpur, Dhaka - 1207",
                'map_url'        => 'https://maps.google.com/?q=House+No+7/16+Block+B+Lalmatia+Mohammadpur+Dhaka+1207',
                'card_link'      => '#',
                'image'          => 'assets/img/Campus-1.jpg',
                'image_alt'      => 'Lalmatia campus',
                'show_in_grid'   => 1,
            ],
            [
                'name'           => 'Averroes International School, Banasree',
                'footer_heading' => 'Averroes International School, Banasree',
                'phone'          => '+88 01901-048844',
                'email'          => '',
                'address'        => 'Plot 4, Road 13, Block A, Banasree, Dhaka - 1230',
                'map_url'        => 'https://maps.app.goo.gl/v5cDG3CMC3WdMtQZ6?g_st=aw',
                'card_link'      => '#',
                'image'          => 'assets/img/Campus-1.jpg',
                'image_alt'      => 'Banasree campus',
                'show_in_grid'   => 1,
            ],
        ];

        if ($pdo === null) {
            if ($gridOnly) {
                return array_values(array_filter($fallback, fn($c) => !empty($c['show_in_grid'])));
            }
            return $fallback;
        }

        try {
            $sql = "SELECT * FROM campuses WHERE is_active = 1";
            if ($gridOnly) {
                $sql .= " AND show_in_grid = 1";
            }
            $sql .= " ORDER BY sort_order ASC, id ASC";

            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            if (!empty($rows)) {
                $rows = array_values(array_filter($rows, fn($c) => stripos($c['name'] ?? '', 'uttara') === false && stripos($c['footer_heading'] ?? '', 'uttara') === false));
                return $rows;
            }
            return $gridOnly ? array_values(array_filter($fallback, fn($c) => !empty($c['show_in_grid']))) : $fallback;
        } catch (Throwable $e) {
            error_log('[get_campuses] ' . $e->getMessage());
            return $gridOnly ? array_values(array_filter($fallback, fn($c) => !empty($c['show_in_grid']))) : $fallback;
        }
    }
}

if (!function_exists('get_partners')) {
    /**
     * Get partners
     */
    function get_partners(?PDO $pdo): array {
        $fallback = [
            ['css_class' => 'p-moe', 'label_html' => '<img src="assets/img/partners/moe.svg" alt="Ministry of Education Bangladesh" class="partner-img">'],
            ['css_class' => 'p-cam', 'label_html' => '<img src="assets/img/partners/cambridge.svg" alt="Cambridge Assessment International Education" class="partner-img">'],
            ['css_class' => 'p-edx', 'label_html' => '<img src="assets/img/partners/edexcel.svg" alt="Pearson Edexcel" class="partner-img">'],
            ['css_class' => 'p-bc', 'label_html' => '<img src="assets/img/partners/british-council.svg" alt="British Council" class="partner-img">'],
            ['css_class' => 'p-deb', 'label_html' => '<img src="assets/img/partners/dhaka-board.svg" alt="Dhaka Education Board DSHE" class="partner-img">'],
            ['css_class' => 'p-monash', 'label_html' => '<img src="assets/img/partners/monash.svg" alt="Monash University Pathway" class="partner-img">'],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->query("SELECT * FROM partners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
            $rows = $stmt->fetchAll();
            return !empty($rows) ? $rows : $fallback;
        } catch (Throwable $e) {
            error_log('[get_partners] ' . $e->getMessage());
            return $fallback;
        }
    }
}

if (!function_exists('get_why_averroes_data')) {
    /**
     * Get Why Averroes page structured data
     */
    function get_why_averroes_data(?PDO $pdo): array {
        $fallback = [
            'hero' => [
                'eyebrow' => 'WHY CHOOSE AVERROES',
                'title' => 'Give your Child the Averroes Edge',
                'lead' => 'At Averroes, we go beyond traditional boundaries of education. Guided by our motto — "One School Serving the Purposes of Here & Hereafter" — we provide an unmatched synthesis of internationally recognized British curricula, advanced STEM education, and deep-rooted Islamic values (Tarbiyah). We nurture enlightened minds, moral leadership, and visionary scholars prepared for global distinction.',
                'metrics' => [
                    ['val' => '100%', 'lbl' => 'Cambridge & Edexcel Pass Rate'],
                    ['val' => '5.5h', 'lbl' => 'Daily Hifzul Quran Balanced Track'],
                    ['val' => '100+', 'lbl' => 'Global & National Placements'],
                ],
                'image' => 'assets/img/subpages/aisl-hifz-boy-full.jpg',
                'image_alt' => 'Averroes International School Student holding Holy Quran',
            ],
            'sections' => [
                [
                    'id' => 'research',
                    'reverse' => true,
                    'title_base' => 'Holistic Education Rooted in ',
                    'title_highlight' => 'Cambridge & Tarbiyah',
                    'lead' => 'We believe that rigorous academic exploration must be harmonized with moral enlightenment. Our dual curriculum blends the internationally acclaimed Pearson Edexcel and Cambridge Assessment frameworks with comprehensive Islamic Studies, Tajweed, and Arabic language immersion. Guided by university professors and seasoned curriculum directors, our students develop analytical mastery, scientific inquiry, and unshakeable ethical character.',
                    'type' => 'bullets',
                    'bullets' => [
                        ['title' => 'Dual International & National Recognition', 'desc' => 'Approved by the Ministry of Education Bangladesh and recognized by the British Council as a premier examination center for Pearson Edexcel and Cambridge IGCSE / GCE A-Levels.'],
                        ['title' => 'Advanced STEM & Robotics Laboratories', 'desc' => 'State-of-the-art physics, chemistry, biology, and computer science labs fostering inquiry-based scientific exploration, robotics, and coding.'],
                        ['title' => 'Curriculum Advisory Council', 'desc' => 'Academic curricula continuously enriched by an advisory council of experienced university professors, foreign lecturers, and senior English-medium pedagogues.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-hifz-classroom-hd.jpg',
                    'image_alt' => 'Averroes Modern Academic Classrooms',
                    'doodle_type' => 'science',
                ],
                [
                    'id' => 'pedagogy',
                    'reverse' => false,
                    'title_base' => 'Innovative Teaching ',
                    'title_highlight' => 'Methods & Dual-Track Hifz',
                    'lead' => 'Our pedagogical model ensures that every child receives customized attention, active engagement, and the tools to excel both spiritually and academically. Through systematic dual-track scheduling, students can memorize the entire Quran while securing top percentiles in Cambridge IGCSE examinations.',
                    'type' => 'features',
                    'features' => [
                        ['title' => 'Visible Learning & Formative Assessment', 'desc' => 'Continuous diagnostic feedback, benchmark testing, and personalized learning plans ensure zero academic blind spots from primary through A-Levels.'],
                        ['title' => '5.5-Hour Daily Hifzul Quran Integration', 'desc' => 'A structured daily regimen (3.5 hours morning recitation + 2 hours evening revision) with dedicated Junior, Mid, and Senior Hifz wings, ensuring balanced academic progression.'],
                        ['title' => 'Faculty from Al-Azhar & Madinah University', 'desc' => 'Our Islamic Studies and Arabic Foreign Language departments are steered by distinguished scholars holding degrees from Al-Azhar University (Cairo) and the Islamic University of Madinah.'],
                        ['title' => 'Gender-Appropriate Learning Environments', 'desc' => 'Thoughtfully separated classroom wings and campus sections for boys and girls ensure utmost modesty, personal comfort, and focused academic concentration.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-qiraat-stage-reciter.jpg',
                    'image_alt' => 'Averroes Student Reciting at Quran Competition',
                    'doodle_type' => 'books',
                ],
                [
                    'id' => 'merit',
                    'reverse' => true,
                    'title_base' => 'Rewarding Merit, Inspiring ',
                    'title_highlight' => 'Brilliance',
                    'lead' => 'Excellence deserves recognition. Averroes maintains a proud tradition of celebrating scholastic triumph, leadership potential, and spiritual devotion through generous institutional scholarships and national awards.',
                    'type' => 'bullets',
                    'bullets' => [
                        ['title' => 'Cambridge & Edexcel High Achievers Awards', 'desc' => 'Honoring students who achieve Country Highest and World Highest percentile scores in Pearson Edexcel and Cambridge board examinations.'],
                        ['title' => 'The Daily Star National High Achievers Recognition', 'desc' => 'Regular felicitations at national platforms honoring O and A Level straight A/A* scholars.'],
                        ['title' => 'Hafiz-e-Quran Distinction Scholarships', 'desc' => 'Full and partial tuition waivers granted to students who successfully complete complete memorization of the Holy Quran.'],
                        ['title' => 'Academic & Talent Merit Waivers', 'desc' => 'Comprehensive financial aid and merit scholarships for deserving scholars demonstrating intellectual distinction and exemplary moral character.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-hifz-student-felicitation.jpg',
                    'image_alt' => 'Averroes Lalmatia Student Award Felicitation',
                    'doodle_type' => 'merit',
                ],
                [
                    'id' => 'universities',
                    'reverse' => false,
                    'title_base' => 'Targeting the World\'s Top ',
                    'title_highlight' => 'Universities',
                    'lead' => 'From early secondary years, our dedicated University Guidance and Career Counseling Cell mentors students to curate competitive profiles, master standardized tests, and secure admissions with scholarships at elite institutions worldwide.',
                    'type' => 'bullets',
                    'bullets' => [
                        ['title' => 'Averroes Student Development Program (ASDP)', 'desc' => 'Tailored profile building, research projects, Model United Nations (MUN), and international leadership retreats.'],
                        ['title' => 'Global Higher Education Pathways', 'desc' => 'Direct admissions into leading institutions across the UK (Oxford, Cambridge, Imperial, UCL), USA, Canada (Toronto, UBC), and Australia (Monash University, Melbourne).'],
                        ['title' => 'National Elite University Success', 'desc' => 'Exemplary entry records into BUET, IBA (Dhaka University), and government medical colleges.'],
                        ['title' => 'Standardized Exam & Profile Masterclass', 'desc' => 'In-house preparation for IELTS, SAT, SOP writing, and university interview coaching.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-progression-auditorium.jpg',
                    'image_alt' => 'Averroes International University Progression Convocation',
                    'doodle_type' => 'globe',
                ],
                [
                    'id' => 'cocurricular',
                    'reverse' => true,
                    'title_base' => 'Co-Curricular & Sports ',
                    'title_highlight' => 'Excellence',
                    'lead' => 'True education extends far beyond textbooks. At Averroes, athletic vigor, creative expression, and teamwork are essential pillars of student development. Our campus infrastructure provides specialized professional facilities rarely matched by other institutions in Bangladesh.',
                    'type' => 'bullets',
                    'bullets' => [
                        ['title' => 'Averroes Swimming Club', 'desc' => 'Professional semi-Olympic standard pool with certified male and female coaches, separate timing, and safety lifeguards.'],
                        ['title' => 'Averroes Turf', 'desc' => 'FIFA-standard artificial green turf arena for inter-school football tournaments, cricket, and athletic training.'],
                        ['title' => 'Annual Sports & Summer Camps', 'desc' => 'City-wide tournaments, seasonal athletics bootcamps, and martial arts training.'],
                        ['title' => 'Student Leadership & Specialized Clubs', 'desc' => 'Active Debate Society, Quran & Husnut Tilawah Club, Robotics Club, Seerah Exhibition, and the Averroes Students\' Representative Council.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-swimming-pool-students.jpg',
                    'image_alt' => 'Averroes Swimming Club Students',
                    'doodle_type' => 'sports',
                ],
            ],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM subpage_contents WHERE page_key = 'why_averroes' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute();
            $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($contents)) {
                return $fallback;
            }

            $itemsStmt = $pdo->prepare("SELECT * FROM subpage_items WHERE page_key = 'why_averroes' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $itemsStmt->execute();
            $allItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $itemsBySec = [];
            foreach ($allItems as $it) {
                $itemsBySec[$it['section_key']][] = $it;
            }

            $result = [
                'hero' => $fallback['hero'],
                'sections' => [],
            ];

            foreach ($contents as $c) {
                if ($c['section_key'] === 'hero') {
                    $heroMetrics = [];
                    if (!empty($itemsBySec['hero'])) {
                        foreach ($itemsBySec['hero'] as $m) {
                            $heroMetrics[] = ['val' => $m['val'], 'lbl' => $m['lbl']];
                        }
                    }
                    $result['hero'] = [
                        'eyebrow'   => !empty($c['eyebrow']) ? $c['eyebrow'] : ($fallback['hero']['eyebrow'] ?? ''),
                        'title'     => !empty($c['title_base']) ? $c['title_base'] : ($fallback['hero']['title'] ?? ''),
                        'lead'      => !empty($c['lead_text']) ? $c['lead_text'] : ($fallback['hero']['lead'] ?? ''),
                        'metrics'   => !empty($heroMetrics) ? $heroMetrics : ($fallback['hero']['metrics'] ?? []),
                        'image'     => !empty($c['image']) ? $c['image'] : ($fallback['hero']['image'] ?? ''),
                        'image_alt' => !empty($c['image_alt']) ? $c['image_alt'] : ($fallback['hero']['image_alt'] ?? ''),
                    ];
                } else {
                    $secItems = [];
                    if (!empty($itemsBySec[$c['section_key']])) {
                        foreach ($itemsBySec[$c['section_key']] as $it) {
                            $secItems[] = [
                                'title' => $it['title'],
                                'desc'  => $it['description'] ?? '',
                            ];
                        }
                    }
                    $secType = $c['type'] ?? 'bullets';
                    $secArr = [
                        'id'              => $c['section_key'],
                        'reverse'         => ($c['layout_mode'] === 'reverse'),
                        'title_base'      => $c['title_base'] ?? '',
                        'title_highlight' => $c['title_highlight'] ?? '',
                        'lead'            => $c['lead_text'] ?? '',
                        'type'            => $secType,
                        'image'           => $c['image'] ?? '',
                        'image_alt'       => $c['image_alt'] ?? '',
                        'doodle_type'     => $c['doodle_type'] ?? 'books',
                    ];
                    if ($secType === 'features') {
                        $secArr['features'] = $secItems;
                    } else {
                        $secArr['bullets'] = $secItems;
                    }
                    $result['sections'][] = $secArr;
                }
            }

            return $result;
        } catch (Throwable $e) {
            error_log('[get_why_averroes_data] ' . $e->getMessage());
            return $fallback;
        }
    }
}

if (!function_exists('get_life_at_averroes_data')) {
    /**
     * Get Life at Averroes page structured data
     */
    function get_life_at_averroes_data(?PDO $pdo): array {
        $fallback = [
            'hero' => [
                'eyebrow' => 'STUDENT LIFE AT AVERROES',
                'title' => 'Every Day Inspires New Ideas, Passion, and Character',
                'lead' => 'At Averroes International School, education is not confined to textbooks—it is an invigorating daily adventure of intellectual discovery, spiritual grounding, and vibrant friendship. Rooted in our philosophy of nurturing the whole child for "Here & Hereafter", every morning opens new horizons for our students to lead with confidence, learn with joy, and live with purposeful conviction.',
                'metrics' => [
                    ['val' => '8', 'lbl' => 'Dynamic Daily Periods'],
                    ['val' => '15+', 'lbl' => 'Active Clubs & Societies'],
                    ['val' => '100%', 'lbl' => 'Daily Congregational Salah'],
                ],
                'image' => 'assets/img/subpages/aisl-student-life-hero.jpg',
                'image_alt' => 'Averroes Student Life Explorer',
            ],
            'sections' => [
                [
                    'id' => 'learning',
                    'reverse' => false,
                    'title_base' => 'A Day of Purposeful ',
                    'title_highlight' => 'Learning',
                    'lead' => 'Students engage in 8 structured, vibrant periods each day, seamlessly balancing the world-renowned Pearson Edexcel and Cambridge international curricula with Quran memorization, Arabic language proficiency, and Islamic studies.',
                    'bullets' => [
                        ['title' => 'Cambridge & Edexcel Core Academics', 'desc' => 'Advanced English Literature, Mathematics, Pure Sciences, and Global Perspectives taught with interactive pedagogy.'],
                        ['title' => 'Bangla & National Heritage Studies', 'desc' => 'Deep pride in national history, cultural heritage, and Bengali literature.'],
                        ['title' => '5.5-Hour Hifzul Quran & Halaqah Sessions', 'desc' => 'Structured daily memorization, Tajweed, and recitation with zero academic lag.'],
                        ['title' => 'Quranic Arabic & Foreign Language Program', 'desc' => 'Systematic Arabic language immersion led by Al-Azhar and Madinah University scholars.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-hifz-classroom-hd.jpg',
                    'image_alt' => 'Averroes Students Engaged in Purposeful Classroom Learning',
                    'doodle_type' => 'aura',
                ],
                [
                    'id' => 'movement',
                    'reverse' => true,
                    'title_base' => 'Movement, Expression & Creative ',
                    'title_highlight' => 'Exploration',
                    'lead' => 'Physical vitality, creative expression, and teamwork are vital cornerstones of life at Averroes. We nurture disciplined athletes, eloquent speakers, and inspired artists through exceptional sporting and cultural avenues.',
                    'bullets' => [
                        ['title' => 'Averroes Swimming Club & Karate Club', 'desc' => 'Certified professional coaches providing Olympic-standard swim training, self-defense, and martial arts discipline with dedicated separate sessions.'],
                        ['title' => 'Averroes Turf Football & Cricket Arena', 'desc' => 'FIFA-standard artificial green turf hosting regular house matches and inter-school tournaments.'],
                        ['title' => 'Cultural Forum & Writers Guild', 'desc' => 'Islamic nasheed, poetry recitations, creative storytelling, calligraphy, and annual student publications.'],
                        ['title' => 'English & Bangla Debating Society', 'desc' => 'Public speaking masterclasses, Model United Nations (MUN), and parliamentary debate training.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-swimming-pool-students.jpg',
                    'image_alt' => 'Averroes Swimming Club & Sports',
                    'doodle_type' => 'motion',
                ],
                [
                    'id' => 'beyond',
                    'reverse' => false,
                    'title_base' => 'Beyond the ',
                    'title_highlight' => 'Classroom',
                    'lead' => 'Some of the most powerful lessons are learned beyond campus walls. Through educational field trips, environmental excursions, community welfare drives, and leadership camps, Averroes students apply their knowledge in the real world.',
                    'bullets' => [
                        ['title' => 'Fleet of GPS-Monitored School Buses', 'desc' => 'Safe, comfortable daily transportation across Dhaka equipped with real-time GPS tracking and dedicated attendants.'],
                        ['title' => 'Social Welfare & Community Service Club', 'desc' => 'Food distribution, humanitarian relief campaigns, and orphan support programs teaching empathy and social responsibility.'],
                        ['title' => 'Science Fairs & Heritage Excursions', 'desc' => 'Hands-on botanical visits, planetarium excursions, and national museum heritage tours.'],
                        ['title' => 'Annual Seerah Exhibition', 'desc' => 'City-wide interactive cultural exhibitions bringing the prophetic biography to life.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-bus-service-launched.jpg',
                    'image_alt' => 'Averroes School Bus Fleet and Beyond the Classroom',
                    'doodle_type' => 'bus',
                ],
                [
                    'id' => 'achievement',
                    'reverse' => true,
                    'title_base' => 'Achievement Through ',
                    'title_highlight' => 'Challenge',
                    'lead' => 'We encourage our students to test their abilities, step outside their comfort zones, and strive for peak distinction in national and international arenas.',
                    'bullets' => [
                        ['title' => 'Husnut Tilawah & Qira\'at Competitions', 'desc' => 'Annual inter-campus Quran recitation contests judged by world-renowned Qaris.'],
                        ['title' => 'Pearson Edexcel & Cambridge High Achievers', 'desc' => 'Consistent World Highest and Country Highest distinctions in O and A Levels.'],
                        ['title' => 'National Olympiads & Robotics Battles', 'desc' => 'High honors in Mathematics Olympiads, Bangladesh Junior Science Olympiads, and inter-school robotics hackathons.'],
                        ['title' => 'Inter-School Sports Championships', 'desc' => 'Trophies in regional football tournaments, cricket leagues, and swimming galas.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-hifz-student-felicitation.jpg',
                    'image_alt' => 'Averroes Student Award Presentation & Trophies',
                    'doodle_type' => 'trophy',
                ],
                [
                    'id' => 'pastoral',
                    'reverse' => false,
                    'title_base' => 'Personal Growth and Pastoral ',
                    'title_highlight' => 'Care',
                    'lead' => 'Every child\'s emotional, moral, and physical well-being is held sacred. Through our comprehensive pastoral care framework, house mentorship system, and nurturing Tarbiyah guidance, students grow up in a safe, compassionate, and uplifting atmosphere.',
                    'bullets' => [
                        ['title' => 'Structured Tarbiyah & Moral Mentorship', 'desc' => 'Daily ethical discussions instilling integrity, modesty, humility, and leadership.'],
                        ['title' => 'Dedicated Student Counseling Cell', 'desc' => 'Professional guidance counselors supporting mental well-being, peer collaboration, and academic confidence.'],
                        ['title' => 'Gender-Segregated Learning Environments', 'desc' => 'Purposefully separate classrooms and facilities for boys and girls, ensuring comfort, respect, and focused development.'],
                        ['title' => 'Guardians\' Partnership & Waiting Lounge', 'desc' => 'Modern dedicated lounge for parents with regular parent-teacher dialogues and pastoral conferences.'],
                    ],
                    'image' => 'assets/img/subpages/aisl-lounge-interior-lighting.jpg',
                    'image_alt' => 'Averroes Pastoral Care & Guardians Lounge',
                    'doodle_type' => 'jigsaw',
                ],
            ],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM subpage_contents WHERE page_key = 'life_at_averroes' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute();
            $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($contents)) {
                return $fallback;
            }

            $itemsStmt = $pdo->prepare("SELECT * FROM subpage_items WHERE page_key = 'life_at_averroes' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $itemsStmt->execute();
            $allItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $itemsBySec = [];
            foreach ($allItems as $it) {
                $itemsBySec[$it['section_key']][] = $it;
            }

            $result = [
                'hero' => $fallback['hero'],
                'sections' => [],
            ];

            foreach ($contents as $c) {
                if ($c['section_key'] === 'hero') {
                    $heroMetrics = [];
                    if (!empty($itemsBySec['hero'])) {
                        foreach ($itemsBySec['hero'] as $m) {
                            $heroMetrics[] = ['val' => $m['val'], 'lbl' => $m['lbl']];
                        }
                    }
                    $result['hero'] = [
                        'eyebrow'   => !empty($c['eyebrow']) ? $c['eyebrow'] : ($fallback['hero']['eyebrow'] ?? ''),
                        'title'     => !empty($c['title_base']) ? $c['title_base'] : ($fallback['hero']['title'] ?? ''),
                        'lead'      => !empty($c['lead_text']) ? $c['lead_text'] : ($fallback['hero']['lead'] ?? ''),
                        'metrics'   => !empty($heroMetrics) ? $heroMetrics : ($fallback['hero']['metrics'] ?? []),
                        'image'     => !empty($c['image']) ? $c['image'] : ($fallback['hero']['image'] ?? ''),
                        'image_alt' => !empty($c['image_alt']) ? $c['image_alt'] : ($fallback['hero']['image_alt'] ?? ''),
                    ];
                } else {
                    $secBullets = [];
                    if (!empty($itemsBySec[$c['section_key']])) {
                        foreach ($itemsBySec[$c['section_key']] as $it) {
                            $secBullets[] = [
                                'title' => $it['title'],
                                'desc'  => $it['description'] ?? '',
                            ];
                        }
                    }
                    $result['sections'][] = [
                        'id'              => $c['section_key'],
                        'reverse'         => ($c['layout_mode'] === 'reverse'),
                        'title_base'      => $c['title_base'] ?? '',
                        'title_highlight' => $c['title_highlight'] ?? '',
                        'lead'            => $c['lead_text'] ?? '',
                        'bullets'         => $secBullets,
                        'image'           => $c['image'] ?? '',
                        'image_alt'       => $c['image_alt'] ?? '',
                        'doodle_type'     => $c['doodle_type'] ?? 'aura',
                    ];
                }
            }

            return $result;
        } catch (Throwable $e) {
            error_log('[get_life_at_averroes_data] ' . $e->getMessage());
            return $fallback;
        }
    }
}

if (!function_exists('get_admission_page_data')) {
    /**
     * Get structured admissions page data for Lalmatia or Banasree
     */
    function get_admission_page_data(?PDO $pdo, string $campus = 'lalmatia'): array {
        $campus = strtolower(trim($campus));
        if ($campus !== 'banasree') {
            $campus = 'lalmatia';
        }

        $isLalmatia = ($campus === 'lalmatia');

        $contacts = [
            'lalmatia' => [
                'campus_key' => 'lalmatia',
                'name' => 'Averroes International School, Lalmatia',
                'short_name' => 'Averroes Lalmatia',
                'subtitle' => 'Main Campus, Mohammadpur, Dhaka',
                'address' => 'House # 7/16, Block # B, Lalmatia, Mohammadpur, Dhaka-1207',
                'phone' => '+880 1954-123 123',
                'whatsapp' => '+880 1954-123 123',
                'email' => 'admin@aisl.edu.bd',
                'visiting_hours' => 'Sunday – Thursday: 8:00 AM – 3:30 PM | Saturday: 9:00 AM – 2:00 PM',
                'image' => 'assets/img/Campus-1.jpg',
            ],
            'banasree' => [
                'campus_key' => 'banasree',
                'name' => 'Averroes International School, Banasree',
                'short_name' => 'Averroes Banasree',
                'subtitle' => 'Eastern Campus, Rampura, Dhaka',
                'address' => 'Block # B, Main Road, Banasree, Rampura, Dhaka',
                'phone' => '+88 01901-048844',
                'whatsapp' => '+88 01901-048844',
                'email' => 'banasree@aisl.edu.bd',
                'visiting_hours' => 'Sunday – Thursday: 8:00 AM – 3:30 PM | Saturday: 9:00 AM – 2:00 PM',
                'image' => 'assets/img/Campus-1.jpg',
            ],
        ];

        $currentContact = $contacts[$campus];

        $data = [
            'campus' => $campus,
            'campus_info' => $currentContact,
            'all_campuses' => $contacts,
            'hero' => [
                'eyebrow' => 'ADMISSIONS OPEN FOR SESSION 2026–2027',
                'title_base' => 'Open the Doors to a ',
                'title_highlight' => 'Brighter Future',
                'lead' => 'Thank you for considering ' . $currentContact['name'] . ' for your child\'s educational journey. Guided by our foundational motto — "One School Serving the Purposes of Here & Hereafter" — we deliver an unmatched synthesis of internationally recognized British curricula (Cambridge & Pearson Edexcel), advanced STEM learning, and deep-rooted Islamic values (Tarbiyah).',
                'metrics' => [
                    ['val' => '100%', 'lbl' => 'Cambridge & Edexcel Pass Rate'],
                    ['val' => '5.5h', 'lbl' => 'Daily Hifzul Quran & Balanced Track'],
                    ['val' => '100+', 'lbl' => 'Global & National Placements'],
                    ['val' => 'PG–A Level', 'lbl' => 'Comprehensive Academic Streams'],
                ],
            ],
            'query_form' => [
                'title_base' => 'Admissions ',
                'title_highlight' => 'Query',
                'lead' => 'Have questions or ready to begin? Submit your admission inquiry below and our Admissions & Lead Management Team will connect with you within 24 hours to schedule your personalized campus tour and student assessment.',
            ],
            'process' => [
                'title_base' => 'Admissions ',
                'title_highlight' => 'Process',
                'lead' => 'Our transparent, parent-friendly 7-step enrollment workflow ensures a welcoming and seamless onboarding experience for your family.',
                'steps' => [
                    [
                        'step_num' => 1,
                        'pill' => 'Step 1',
                        'icon' => 'form',
                        'title' => 'Fill up the Web form above / Social Media Form',
                        'desc' => 'Submit the digital inquiry form on this page or collect the prescribed Admission Form directly from the campus Admission Office.',
                    ],
                    [
                        'step_num' => 2,
                        'pill' => 'Step 2',
                        'icon' => 'calendar',
                        'title' => 'Set an appointment to visit school through Phone Call, SMS, Email from Lead Management Team',
                        'desc' => 'Our admissions counselors will get in touch to confirm a dedicated time slot that fits your schedule.',
                    ],
                    [
                        'step_num' => 3,
                        'pill' => 'Step 3',
                        'icon' => 'door',
                        'title' => 'Visit School and attend session with Admission Team.',
                        'desc' => 'Tour our classrooms, Quranic learning halls, labs, turf, and semi-Olympic pool while consulting with senior educators.',
                    ],
                    [
                        'step_num' => 4,
                        'pill' => 'Step 4',
                        'icon' => 'assessment',
                        'title' => 'Form Purchase and Student Assessment',
                        'desc' => 'Complete form verification and appear for the age-appropriate diagnostic assessment (Oral for Early Years, Written for Class 1+).',
                    ],
                    [
                        'step_num' => 5,
                        'pill' => 'Step 5',
                        'icon' => 'result',
                        'title' => 'Result Published (Within Next Working Day)',
                        'desc' => 'Assessment outcomes and class placement feedback are published within 24 hours via SMS and parent portal.',
                    ],
                    [
                        'step_num' => 6,
                        'pill' => 'Step 6',
                        'icon' => 'hod',
                        'title' => 'Session with Academic Head of Department (HOD)',
                        'desc' => 'An insightful dialogue with the Academic Section Head and Principal to finalize curriculum streams and individualized goals.',
                    ],
                    [
                        'step_num' => 7,
                        'pill' => 'Step 7',
                        'icon' => 'payment',
                        'title' => 'Admission Fees Payment',
                        'desc' => 'Settle the admission and session fees through the Accounts Counter or online banking gateway to secure enrollment.',
                    ],
                ],
                'milestone_box' => 'After payment your seat will be booked automatically and notification will go from our system to your phone.',
            ],
            'assessment' => [
                'title_base' => 'Structured ',
                'title_highlight' => 'Assessment',
                'lead' => 'At Averroes, assessment is designed to identify and celebrate each child\'s unique learning style and foundational preparedness in a supportive, stress-free atmosphere.',
                'items' => [
                    [
                        'level' => 'Play Group and Nursery',
                        'badge' => 'Early Years',
                        'body' => 'No written tests are conducted for Play Group and Nursery. A gentle, friendly oral interaction with the child and the parents is conducted. Attendance of both the father and the mother is mandatory to ensure unified pastoral partnership.',
                    ],
                    [
                        'level' => 'KG-I & KG-II (Kindergarten)',
                        'badge' => 'Kindergarten',
                        'body' => 'Children participate in interactive oral and visual cognitive assessments. The review focuses on foundational phonics, number familiarity (1–50), color and shape recognition, active listening, and social responsiveness.',
                    ],
                    [
                        'level' => 'Class 1 to Class 8 (Primary & Middle School)',
                        'badge' => 'Standard 1–8',
                        'body' => 'Candidates appear for written diagnostic assessments in English Language, Mathematics, and Bengali. Only candidates who successfully meet the benchmark qualify for the subsequent oral session with both parents.',
                    ],
                    [
                        'level' => 'Class 9 & 10 (Pearson Edexcel & Cambridge O Level)',
                        'badge' => 'O Level Track',
                        'body' => 'Admission into Class IX and X is subject to selective seat availability, as the formal 3-year O-Level curriculum starts from Class 8. Prior academic transcripts, conduct certificates, and stream selection interviews are required.',
                    ],
                    [
                        'level' => 'Class 11 & 12 (Cambridge & Edexcel International A Level)',
                        'badge' => 'A Level Dual Intake',
                        'body' => 'Admissions occur in two distinct annual cycles: Spring Stream (classes commencing in January/February) and Autumn Stream (classes commencing in July/August). Admission requires verified O-Level transcripts with high grades.',
                    ],
                ],
                'notice' => 'The selection of candidates is purely merit-based according to assessment performance and vacancy availability. Results are strictly confidential and governed by the Academic Board.',
            ],
            'session_docs' => [
                'title_base' => 'Academic Session & Required ',
                'title_highlight' => 'Documents',
                'lead' => 'Ensure all required photocopies and attested documents are submitted with your application dossier.',
                'sessions' => [
                    ['stream' => 'Playgroup to Class 8', 'period' => 'July to June Annual Session (Main Academic Cycle)'],
                    ['stream' => 'O Level (Class 9 & 10)', 'period' => '3-Year Integrated Cambridge/Edexcel Board Cycle'],
                    ['stream' => 'A Level Stream A', 'period' => 'January / February Intake (Accelerated & Standard)'],
                    ['stream' => 'A Level Stream B', 'period' => 'July / August Intake (Standard Full 2-Year Program)'],
                ],
                'documents' => [
                    '4 recent passport-size color photographs of the student.',
                    '2 recent passport-size color photographs of each parent / guardian.',
                    'Attested photocopy of Digital Birth Certificate (English / Bilingual format).',
                    'Photocopies of previous two years\' official academic report cards.',
                    'Transfer Certificate (TC) / Clearance letter from the last attended institution.',
                    'Photocopies of parents\' National Identity (NID) Cards or Passports.',
                    'Photocopy of parents\' Tax Return Acknowledgment / TIN Certificate.',
                ],
            ],
        ];

        if ($pdo !== null) {
            try {
                $tStmt = $pdo->query("SELECT * FROM admission_timeline WHERE is_active = 1 ORDER BY sort_order ASC, step_num ASC");
                $dbSteps = $tStmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($dbSteps)) {
                    $mapped = [];
                    foreach ($dbSteps as $s) {
                        $mapped[] = [
                            'step_num' => (int)$s['step_num'],
                            'pill'     => $s['pill'],
                            'icon'     => $s['icon'],
                            'title'    => $s['title'],
                            'desc'     => $s['description'],
                        ];
                    }
                    $data['process']['steps'] = $mapped;
                }

                $admHeroStmt = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'hero' AND is_active = 1 LIMIT 1");
                $admHero = $admHeroStmt ? $admHeroStmt->fetch(PDO::FETCH_ASSOC) : null;
                if ($admHero) {
                    if (!empty($admHero['eyebrow'])) $data['hero']['eyebrow'] = $admHero['eyebrow'];
                    if (!empty($admHero['title_base'])) $data['hero']['title_base'] = $admHero['title_base'];
                    if (!empty($admHero['title_highlight'])) $data['hero']['title_highlight'] = $admHero['title_highlight'];
                    if (!empty($admHero['lead_text'])) $data['hero']['lead'] = $admHero['lead_text'];
                }

                // Dynamic Structured Assessment Section
                $admAssessStmt = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'assessment' AND is_active = 1 LIMIT 1");
                $admAssess = $admAssessStmt ? $admAssessStmt->fetch(PDO::FETCH_ASSOC) : null;
                if ($admAssess) {
                    if (!empty($admAssess['title_base'])) $data['assessment']['title_base'] = $admAssess['title_base'];
                    if (!empty($admAssess['title_highlight'])) $data['assessment']['title_highlight'] = $admAssess['title_highlight'];
                    if (!empty($admAssess['lead_text'])) $data['assessment']['lead'] = $admAssess['lead_text'];
                    if (!empty($admAssess['body_html'])) $data['assessment']['notice'] = $admAssess['body_html'];
                }

                $assessItemsStmt = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'assessment' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
                $dbAssessItems = $assessItemsStmt ? $assessItemsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                if (!empty($dbAssessItems)) {
                    $mappedAssess = [];
                    foreach ($dbAssessItems as $ai) {
                        $mappedAssess[] = [
                            'id'    => (int)$ai['id'],
                            'level' => $ai['title'] ?? '',
                            'badge' => $ai['lbl'] ?? '',
                            'body'  => $ai['description'] ?? '',
                        ];
                    }
                    $data['assessment']['items'] = $mappedAssess;
                }

                // Dynamic Academic Session & Required Documents Section
                $admDocsSecStmt = $pdo->query("SELECT * FROM subpage_contents WHERE page_key = 'admission' AND section_key = 'session_docs' AND is_active = 1 LIMIT 1");
                $admDocsSec = $admDocsSecStmt ? $admDocsSecStmt->fetch(PDO::FETCH_ASSOC) : null;
                if ($admDocsSec) {
                    if (!empty($admDocsSec['title_base'])) $data['session_docs']['title_base'] = $admDocsSec['title_base'];
                    if (!empty($admDocsSec['title_highlight'])) $data['session_docs']['title_highlight'] = $admDocsSec['title_highlight'];
                    if (!empty($admDocsSec['lead_text'])) $data['session_docs']['lead'] = $admDocsSec['lead_text'];
                }

                $sessionsStmt = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'academic_sessions' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
                $dbSessions = $sessionsStmt ? $sessionsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                if (!empty($dbSessions)) {
                    $mappedSessions = [];
                    foreach ($dbSessions as $sess) {
                        $mappedSessions[] = [
                            'id'     => (int)$sess['id'],
                            'stream' => $sess['title'] ?? '',
                            'period' => $sess['subtitle'] ?? '',
                        ];
                    }
                    $data['session_docs']['sessions'] = $mappedSessions;
                }

                $docsListStmt = $pdo->query("SELECT * FROM subpage_items WHERE page_key = 'admission' AND section_key = 'document_checklist' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
                $dbDocsList = $docsListStmt ? $docsListStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                if (!empty($dbDocsList)) {
                    $mappedDocs = [];
                    foreach ($dbDocsList as $dItem) {
                        $mappedDocs[] = $dItem['title'];
                    }
                    $data['session_docs']['documents'] = $mappedDocs;
                }
            } catch (Throwable $e) {
                error_log('[get_admission_page_data] ' . $e->getMessage());
            }
        }

        return $data;
    }
}

if (!function_exists('get_contact_page_data')) {
    /**
     * Get structured Contact Us subpage data
     */
    function get_contact_page_data(?PDO $pdo): array {
        $fallback = [
            'hero' => [
                'eyebrow' => 'CONNECT WITH AVERROES INTERNATIONAL SCHOOL',
                'title_base' => "We'd Love to ",
                'title_highlight' => "Hear From You",
                'lead' => "Whether you are a prospective parent seeking admission guidance for your child, a current member of our school community, or simply curious to learn more about our distinctive synthesis of Cambridge/Edexcel curricula and Islamic Tarbiyah, we are here to assist. Our dedicated admissions counselors, academic leads, and administrative coordinators are eager to welcome you. Reach out through the form below, call our helpline, or visit our campuses in person — we look forward to connecting with your family!",
                'quick_contacts' => [
                    ['icon' => 'phone', 'label' => 'Direct Helpline', 'val' => '+880 1954-123 123', 'link' => 'tel:+8801954123123'],
                    ['icon' => 'whatsapp', 'label' => 'WhatsApp Instant', 'val' => '+880 1954-123 123', 'link' => 'https://wa.me/8801954123123'],
                    ['icon' => 'email', 'label' => 'Official Desk', 'val' => 'info@aisl.edu.bd', 'link' => 'mailto:info@aisl.edu.bd'],
                ],
            ],
            'campuses_section' => [
                'title_base' => 'Our ',
                'title_highlight' => 'Campuses',
                'lead' => 'With modern campuses situated in prime locations across Dhaka, Averroes International School provides safe, stimulating, and value-based learning environments tailored for every stage of your child\'s growth.',
                'campuses' => [
                    [
                        'id' => 'lalmatia',
                        'name' => 'Averroes International School, Lalmatia',
                        'role' => 'Main Campus & Administrative HQ',
                        'image' => 'assets/img/Campus-1.jpg',
                        'image_alt' => 'Averroes International School Lalmatia Campus Building',
                        'address' => 'House No – 7/16, Block – B, Lalmatia, Mohammadpur, Dhaka - 1207',
                        'map_url' => 'https://www.google.com/maps/search/?api=1&query=Averroes+International+School,+House+No+7/16,+Block+B,+Lalmatia,+Dhaka',
                        'map_embed_url' => 'https://maps.google.com/maps?q=Averroes+International+School,+House+No+7/16,+Block+B,+Lalmatia,+Dhaka+1207&t=&z=16&ie=UTF8&iwloc=B&output=embed',
                        'phones' => ['+880 1954-123 123', '+880 1949-000 555', '+880 1714 622 211'],
                        'email' => 'admin@aisl.edu.bd',
                        'hours' => "Sunday – Thursday 8:00 AM – 3:30 PM\nSaturday 9:00 AM – 2:00 PM",
                    ],
                    [
                        'id' => 'banasree',
                        'name' => 'Averroes International School, Banasree',
                        'role' => 'Eastern Dhaka Campus',
                        'image' => 'assets/img/Campus-1.jpg',
                        'image_alt' => 'Averroes International School Banasree Campus',
                        'address' => 'Block # B, Main Road, (Plot 4, Road 13), Banasree, Rampura, Dhaka - 1230',
                        'map_url' => 'https://maps.app.goo.gl/v5cDG3CMC3WdMtQZ6?g_st=aw',
                        'map_embed_url' => 'https://maps.google.com/maps?q=Averroes+International+School,+Block+B,+Main+Road,+Banasree,+Dhaka&t=&z=16&ie=UTF8&iwloc=B&output=embed',
                        'phones' => ['+88 01901-048844', '+880 1954-123 123'],
                        'email' => 'banasree@aisl.edu.bd',
                        'hours' => "Sunday – Thursday 8:00 AM – 3:30 PM\nSaturday 9:00 AM – 2:00 PM",
                    ],
                ],
            ],
            'inquiry_form' => [
                'title_base' => 'Send Us an ',
                'title_highlight' => 'Inquiry',
                'lead' => 'Complete the quick form below. Our admissions and communication team will contact you promptly to address your queries or coordinate a guided campus tour.',
                'campuses_options' => [
                    'lalmatia' => 'Averroes International School, Lalmatia (Main Campus)',
                    'banasree' => 'Averroes International School, Banasree',
                    'general' => 'General Information / Central Desk',
                ],
                'grades_options' => [
                    'playgroup' => 'Playgroup (Ages 2.5–3.5)',
                    'nursery' => 'Nursery (Ages 3.5–4.5)',
                    'kg1' => 'KG I (Ages 4.5–5.5)',
                    'kg2' => 'KG II (Ages 5.5–6.5)',
                    'primary' => 'Class 1 – Class 5 (Primary Years)',
                    'middle' => 'Class 6 – Class 8 (Middle Years)',
                    'olevel' => 'Class 9 – Class 10 (Cambridge/Edexcel O Level)',
                    'alevel' => 'Class 11 – Class 12 (Cambridge/Edexcel A Level)',
                ],
            ],
        ];

        if ($pdo === null) {
            return $fallback;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM subpage_contents WHERE page_key = 'contact' AND section_key = 'hero' AND is_active = 1 LIMIT 1");
            $stmt->execute();
            $heroRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($heroRow) {
                if (!empty($heroRow['eyebrow'])) $fallback['hero']['eyebrow'] = $heroRow['eyebrow'];
                if (!empty($heroRow['title_base'])) $fallback['hero']['title_base'] = $heroRow['title_base'];
                if (!empty($heroRow['title_highlight'])) $fallback['hero']['title_highlight'] = $heroRow['title_highlight'];
                if (!empty($heroRow['lead_text'])) $fallback['hero']['lead_text'] = $heroRow['lead_text'];
                if (!empty($heroRow['image'])) $fallback['hero']['image'] = $heroRow['image'];

                $qcStmt = $pdo->prepare("SELECT * FROM subpage_items WHERE page_key = 'contact' AND section_key = 'hero' AND is_active = 1 ORDER BY sort_order ASC");
                $qcStmt->execute();
                $dbQc = $qcStmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($dbQc)) {
                    $mappedQc = [];
                    foreach ($dbQc as $q) {
                        $mappedQc[] = [
                            'icon'  => $q['icon'],
                            'label' => $q['lbl'],
                            'val'   => $q['val'],
                            'link'  => $q['link'],
                        ];
                    }
                    $fallback['hero']['quick_contacts'] = $mappedQc;
                }
            }
            return $fallback;
        } catch (Throwable $e) {
            error_log('[get_contact_page_data] ' . $e->getMessage());
            return $fallback;
        }
    }
}
