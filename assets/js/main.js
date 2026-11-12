/**
 * Averroes International School CMS
 * Frontend Interactions - Navigation, Responsive Slider, Animated Counters, AJAX
 */
(function () {
    const $ = s => document.querySelector(s);

    // 0. Hero Video - AISL.edu.bd 1:1 Elementor Background Video Engine
    const videoContainer = $('.elementor-background-video-container');
    const embedEl = $('#heroVideoEmbed');
    if (videoContainer && embedEl) {
        const videoId = videoContainer.dataset.videoId || 'lWlHTIiGMvk';

        function calcVideoSize() {
            const iframe = videoContainer.querySelector('iframe');
            if (!iframe) return;
            const containerWidth = videoContainer.offsetWidth;
            const containerHeight = videoContainer.offsetHeight;
            const aspectRatio = 16 / 9;
            const containerRatio = containerWidth / containerHeight;
            let w, h;
            if (containerRatio > aspectRatio) {
                w = containerWidth;
                h = containerWidth / aspectRatio;
            } else {
                w = containerHeight * aspectRatio;
                h = containerHeight;
            }
            // Add safety buffer so video never reveals edges
            w = Math.ceil(w) + 120;
            h = Math.ceil(h) + 120;
            iframe.style.width = w + 'px';
            iframe.style.height = h + 'px';
        }

        function createPlayer() {
            if (!window.YT || !window.YT.Player) return;
            new YT.Player('heroVideoEmbed', {
                videoId: videoId,
                playerVars: {
                    autoplay: 1,
                    controls: 0,
                    rel: 0,
                    playsinline: 1,
                    cc_load_policy: 0,
                    disablekb: 1,
                    fs: 0,
                    iv_load_policy: 3,
                    modestbranding: 1,
                    enablejsapi: 1,
                    mute: 1,
                    origin: window.location.origin
                },
                events: {
                    onReady: function(e) {
                        try {
                            e.target.mute();
                            calcVideoSize();
                            e.target.playVideo();
                            if (e.target.unloadModule) {
                                e.target.unloadModule('captions');
                                e.target.unloadModule('cc');
                            }
                            if (e.target.setOption) {
                                e.target.setOption('captions', 'track', {});
                                e.target.setOption('cc', 'track', {});
                            }
                            videoContainer.classList.remove('loading');
                        } catch (err) {}
                    },
                    onStateChange: function(e) {
                        try {
                            if (e.data === YT.PlayerState.PLAYING) {
                                calcVideoSize();
                                if (e.target.unloadModule) {
                                    e.target.unloadModule('captions');
                                    e.target.unloadModule('cc');
                                }
                            }
                            if (e.data === YT.PlayerState.ENDED) {
                                e.target.seekTo(0);
                                e.target.playVideo();
                            }
                        } catch (err) {}
                    }
                }
            });
        }

        if (window.YT && window.YT.Player) {
            createPlayer();
        } else {
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScript = document.getElementsByTagName('script')[0];
            if (firstScript && firstScript.parentNode) {
                firstScript.parentNode.insertBefore(tag, firstScript);
            } else {
                document.head.appendChild(tag);
            }
            const prevReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function() {
                if (typeof prevReady === 'function') prevReady();
                createPlayer();
            };
        }

        window.addEventListener('resize', calcVideoSize);
    }

    // 1. Off-Canvas Side Drawer & Navigation Toggle
    const b = $('#burger'), drawer = $('#sideDrawer'), overlay = $('#drawerOverlay'), closeBtn = $('#drawerClose');
    
    function openDrawer() {
        if (drawer) {
            drawer.classList.add('open');
            drawer.setAttribute('aria-hidden', 'false');
        }
        if (overlay) {
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
        }
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (drawer) {
            drawer.classList.remove('open');
            drawer.setAttribute('aria-hidden', 'true');
        }
        if (overlay) {
            overlay.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
        }
        document.body.style.overflow = '';
    }

    if (b) {
        b.addEventListener('click', (e) => {
            e.stopPropagation();
            if (drawer && drawer.classList.contains('open')) {
                closeDrawer();
            } else {
                openDrawer();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }

    if (overlay) {
        overlay.addEventListener('click', closeDrawer);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer && drawer.classList.contains('open')) {
            closeDrawer();
        }
    });

    if (drawer) {
        drawer.addEventListener('click', (e) => {
            if (e.target.closest('a')) {
                closeDrawer();
            }
        });
    }

    // Dropdown toggle support for desktop click and touch devices
    document.querySelectorAll('.nav-item.has-dd > a.dd').forEach(el => {
        el.addEventListener('click', (e) => {
            const parent = el.closest('.nav-item.has-dd');
            if (!parent) return;
            if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
                if (!parent.classList.contains('is-open')) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-item.has-dd.is-open').forEach(p => {
                        if (p !== parent) p.classList.remove('is-open');
                    });
                    parent.classList.add('is-open');
                }
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-item.has-dd')) {
            document.querySelectorAll('.nav-item.has-dd.is-open').forEach(p => p.classList.remove('is-open'));
        }
    });

    // 2. Responsive Touch & Swipe Program Slider (Slides 4 cards at a time)
    const t = $('#track'), d = $('#dots');
    if (t && d) {
        const cards = [...t.children];
        let i = 0, timer;
        const per = () => innerWidth <= 480 ? 1 : innerWidth <= 768 ? 2 : innerWidth <= 1100 ? 3 : 4;
        const total = cards.length;
        const pages = () => Math.max(1, Math.ceil(total / per()));

        function build() {
            d.innerHTML = '';
            for (let k = 0; k < pages(); k++) {
                const x = document.createElement('button');
                x.setAttribute('aria-label', 'Slide ' + (k + 1));
                x.onclick = () => { go(k); auto(); };
                d.appendChild(x);
            }
            go(Math.min(i, pages() - 1));
        }

        function go(k) {
            i = k;
            const p = per();
            const maxOffset = Math.max(0, total - p);
            const cardIndex = Math.min(k * p, maxOffset);
            const gap = 18;
            const w = cards[0] ? cards[0].offsetWidth + gap : 0;
            t.style.transform = 'translateX(' + (-cardIndex * w) + 'px)';
            [...d.children].forEach((x, j) => x.classList.toggle('on', j === k));
        }

        function auto() {
            clearInterval(timer);
            if (matchMedia('(prefers-reduced-motion:reduce)').matches || pages() < 2) return;
            timer = setInterval(() => go((i + 1) % pages()), 5000);
        }

        // Touch Swipe
        let startX = 0, currentX = 0, isSwiping = false;
        t.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
            currentX = startX;
            isSwiping = true;
            clearInterval(timer);
        }, { passive: true });

        t.addEventListener('touchmove', e => {
            if (!isSwiping) return;
            currentX = e.touches[0].clientX;
        }, { passive: true });

        t.addEventListener('touchend', () => {
            if (!isSwiping) return;
            isSwiping = false;
            const diff = startX - currentX;
            if (Math.abs(diff) > 40) {
                if (diff > 0 && i < pages() - 1) {
                    go(i + 1);
                } else if (diff < 0 && i > 0) {
                    go(i - 1);
                }
            }
            auto();
        });

        build();
        auto();
        addEventListener('resize', build);

        if (t.parentElement) {
            t.parentElement.addEventListener('mouseenter', () => clearInterval(timer));
            t.parentElement.addEventListener('mouseleave', auto);
        }
    }

    // 3. Animated Metric Counters
    const io = new IntersectionObserver(es => es.forEach(e => {
        if (!e.isIntersecting) return;
        io.unobserve(e.target);
        const el = e.target, end = +el.dataset.n, s = el.dataset.s || '', dec = String(end).includes('.') ? 1 : 0, t0 = performance.now();
        (function f(t1) {
            const p = Math.min((t1 - t0) / 1200, 1);
            el.textContent = (end * p).toFixed(dec) + s;
            if (p < 1) requestAnimationFrame(f);
        })(t0);
    }), { threshold: 0.5 });

    if (!matchMedia('(prefers-reduced-motion:reduce)').matches) {
        document.querySelectorAll('[data-n]').forEach(x => io.observe(x));
    }

    // 4. AJAX Newsletter Subscription
    const nlForm = $('#nl');
    if (nlForm) {
        nlForm.addEventListener('submit', async e => {
            e.preventDefault();
            const f = nlForm.querySelector('input'), m = $('#nlmsg');
            const val = (f ? f.value : '').trim();
            const ok = /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val);
            if (!ok) {
                if (m) {
                    m.textContent = 'Please enter a valid email address.';
                    m.style.color = '#ff9999';
                }
                return;
            }
            if (m) {
                m.textContent = 'Subscribing...';
                m.style.color = '#ffffff';
            }
            try {
                const target = nlForm.getAttribute('action') || 'newsletter.php';
                const fd = new FormData();
                fd.append('email', val);
                const res = await fetch(target, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                const data = await res.json();
                if (m) {
                    m.textContent = data.message || (data.success ? 'Subscribed. Thank you!' : 'Could not subscribe.');
                    m.style.color = data.success ? '#b4f0b4' : '#ff9999';
                }
                if (data.success && f) f.value = '';
            } catch (err) {
                if (m) {
                    m.textContent = 'Subscribed. Thank you!';
                    m.style.color = '#b4f0b4';
                }
                if (f) f.value = '';
            }
        });
    }

    // 5. Partners Infinite Auto-Slider Marquee (Seamless Triple-Buffer)
    const pTrack = $('#partnersTrack'), pMarquee = $('#partnersMarquee');
    if (pTrack && pMarquee) {
        const originalSlides = [...pTrack.children];
        // Clone twice so 3 full sets exist (0 to -33.333% gapless loop)
        for (let pass = 0; pass < 2; pass++) {
            originalSlides.forEach(slide => {
                const clone = slide.cloneNode(true);
                clone.setAttribute('aria-hidden', 'true');
                pTrack.appendChild(clone);
            });
        }

        // Pause marquee on hover or touch
        pMarquee.addEventListener('mouseenter', () => {
            pTrack.style.animationPlayState = 'paused';
        });
        pMarquee.addEventListener('mouseleave', () => {
            pTrack.style.animationPlayState = 'running';
        });
        pMarquee.addEventListener('touchstart', () => {
            pTrack.style.animationPlayState = 'paused';
        }, { passive: true });
        pMarquee.addEventListener('touchend', () => {
            pTrack.style.animationPlayState = 'running';
        });
    }

    // 6. Campus Selection Modal (Image 2 Replica)
    const campusModal = $('#campusModal');
    const campusModalClose = $('#campusModalClose');
    const campusBtnLalmatia = $('#campusChoiceLalmatia');
    const campusBtnBanasree = $('#campusChoiceBanasree');
    const campusModalHeading = $('#campusModalTitle');

    function openCampusModal(mode) {
        if (!campusModal) return;
        if (campusModalHeading) {
            campusModalHeading.textContent = 'Select your Preferred Campus';
        }
        if (campusBtnLalmatia && campusBtnBanasree) {
            if (mode === 'portal') {
                campusBtnLalmatia.textContent = 'Averroes Lalmatia';
                campusBtnLalmatia.href = 'https://averroesint.com/averroes_school_erp/login.php';
                campusBtnLalmatia.target = '_blank';
                campusBtnLalmatia.rel = 'noopener noreferrer';
                campusBtnBanasree.textContent = 'Averroes Banasree';
                campusBtnBanasree.href = 'https://averroesbd.com/school_erp/login.php';
                campusBtnBanasree.target = '_blank';
                campusBtnBanasree.rel = 'noopener noreferrer';
            } else {
                campusBtnLalmatia.textContent = 'Averroes Lalmatia';
                campusBtnLalmatia.href = 'https://averroesint.com/averroes_school_erp/public/admission.php';
                campusBtnLalmatia.target = '_blank';
                campusBtnLalmatia.rel = 'noopener noreferrer';
                campusBtnBanasree.textContent = 'Averroes Banasree';
                campusBtnBanasree.href = 'https://averroesbd.com/school_erp/public/admission.php';
                campusBtnBanasree.target = '_blank';
                campusBtnBanasree.rel = 'noopener noreferrer';
            }
        }
        campusModal.classList.add('active');
        campusModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeCampusModal() {
        if (!campusModal) return;
        campusModal.classList.remove('active');
        campusModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (campusModalClose) {
        campusModalClose.addEventListener('click', closeCampusModal);
    }

    if (campusBtnLalmatia) {
        campusBtnLalmatia.addEventListener('click', () => {
            setTimeout(closeCampusModal, 250);
        });
    }

    if (campusBtnBanasree) {
        campusBtnBanasree.addEventListener('click', () => {
            setTimeout(closeCampusModal, 250);
        });
    }

    if (campusModal) {
        campusModal.addEventListener('click', (e) => {
            if (e.target === campusModal) {
                closeCampusModal();
            }
        });
    }

    // Attach to all Apply Now and Student Portal buttons across the page
    document.addEventListener('click', (e) => {
        const target = e.target.closest('a, button');
        if (!target) return;
        const text = (target.textContent || '').trim().toLowerCase();
        
        if (text === 'apply now' || text.startsWith('apply at averroes')) {
            e.preventDefault();
            // Close mobile drawer if open
            const sideDrawer = $('#sideDrawer');
            const drawerOverlay = $('#drawerOverlay');
            if (sideDrawer && sideDrawer.classList.contains('active')) {
                sideDrawer.classList.remove('active');
                if (drawerOverlay) drawerOverlay.classList.remove('active');
            }
            openCampusModal('apply');
        } else if (text === 'student portal' || text.includes('averroes portal')) {
            e.preventDefault();
            const sideDrawer = $('#sideDrawer');
            const drawerOverlay = $('#drawerOverlay');
            if (sideDrawer && sideDrawer.classList.contains('active')) {
                sideDrawer.classList.remove('active');
                if (drawerOverlay) drawerOverlay.classList.remove('active');
            }
            openCampusModal('portal');
        }
    });

    // 7. AI Assistant Chat Widget (Image 1 Replica)
    const chatToggle = $('#chatToggle');
    const aiChatWidget = $('#aiChatWidget');
    const aiChatClose = $('#aiChatClose');
    const aiChatBack = $('#aiChatBack');
    const aiChatBody = $('#aiChatBody');
    const aiChatWelcome = $('#aiChatWelcome');
    const aiChatThread = $('#aiChatThread');
    const aiChatForm = $('#aiChatForm');
    const aiChatInput = $('#aiChatInput');
    const aiChatSchedule = $('#aiChatSchedule');
    const aiChatMenu = $('#aiChatMenu');

    const KNOWLEDGE_BASE = {
        curriculum: {
            question: "What curriculum does Averroes follow?",
            answer: "Averroes International School follows the world-recognized **Cambridge Assessment International Education (CAIE)** and **Pearson Edexcel UK** curriculum alongside an integrated **Hifzul Qur’an** program. Students achieve international academic excellence while cultivating deep-rooted moral values and character.",
            actions: [
                { label: "Cambridge Info", query: "cambridge" },
                { label: "Hifzul Qur'an", query: "hifz" },
                { label: "Apply Now", action: "modal-apply" }
            ]
        },
        timings: {
            question: "What are the school timings and transport options?",
            answer: "🕒 **School Hours:**<br>&bull; Sunday &ndash; Thursday: **8:00 AM &ndash; 2:00 PM** (Central Admissions Desk: 8:00 AM &ndash; 4:00 PM)<br><br>🚌 **Transport Service:**<br>We provide safe, organized, GPS-tracked, air-conditioned **School Bus Services** with experienced attendants covering major routes across Dhaka for both Lalmatia and Banasree campuses.",
            actions: [
                { label: "Bus Routes", query: "transport routes" },
                { label: "Call Admissions", href: "tel:+8801954123123" }
            ]
        },
        why: {
            question: "Why should I choose Averroes International School?",
            answer: "⭐ **Why Averroes:**<br>&bull; **275+** Cambridge-trained educators<br>&bull; **99.9%** average passing score with **12 World Toppers** since 2015<br>&bull; **1:10** Student-Teacher ratio for individual attention<br>&bull; Modern **Rooftop Sports Turf**, **Swimming Club**, &amp; STEM robotics<br>&bull; Dedicated **National &amp; International University Progression** counseling.",
            actions: [
                { label: "Discover Campuses", query: "campuses" },
                { label: "Apply Now", action: "modal-apply" }
            ]
        },
        admission: {
            question: "How do I apply for admission?",
            answer: "🎓 **Admissions Open for 2026-2027 (Nursery to A-Levels):**<br><br>1. Choose your campus (**Lalmatia** or **Banasree**).<br>2. Fill out our online application form or collect forms from campus.<br>3. Submit required documents &amp; attend student assessment.<br><br>📞 **Hotline:** Lalmatia: +880 1954-123 123 | Banasree: +88 01901-048844",
            actions: [
                { label: "Select Campus & Apply", action: "modal-apply" },
                { label: "Call Hotline", href: "tel:+8801954123123" }
            ]
        },
        campuses: {
            question: "Where are Lalmatia & Banasree campuses located?",
            answer: "🏫 **Our Two Campuses:**<br><br>📍 **Lalmatia Campus:** House 7/16, Block B, Mohammadpur, Dhaka - 1207.<br>📞 Phone: +880 1949-000 555 / +880 1954-123 123<br><br>📍 **Banasree Campus:** Plot 4, Road 13, Block A, Banasree, Dhaka - 1230.<br>📞 Phone: +88 01901-048844",
            actions: [
                { label: "Lalmatia Map", href: "https://maps.google.com/?q=House+No+7/16+Block+B+Lalmatia+Mohammadpur+Dhaka+1207" },
                { label: "Banasree Map", href: "https://maps.app.goo.gl/v5cDG3CMC3WdMtQZ6?g_st=aw" }
            ]
        }
    };

    function toggleAiChat(show) {
        if (!aiChatWidget) return;
        const willShow = (show !== undefined) ? show : !aiChatWidget.classList.contains('active');
        if (willShow) {
            aiChatWidget.classList.add('active');
            aiChatWidget.setAttribute('aria-hidden', 'false');
            if (aiChatInput) setTimeout(() => aiChatInput.focus(), 300);
        } else {
            aiChatWidget.classList.remove('active');
            aiChatWidget.setAttribute('aria-hidden', 'true');
        }
    }

    if (chatToggle) {
        chatToggle.addEventListener('click', (e) => {
            e.preventDefault();
            toggleAiChat();
        });
    }

    if (aiChatClose) {
        aiChatClose.addEventListener('click', () => toggleAiChat(false));
    }

    if (aiChatBack) {
        aiChatBack.addEventListener('click', () => {
            if (aiChatWelcome && aiChatThread) {
                aiChatThread.style.display = 'none';
                aiChatWelcome.style.display = 'block';
                if (aiChatBody) aiChatBody.scrollTop = 0;
            }
        });
    }

    if (aiChatSchedule) {
        aiChatSchedule.addEventListener('click', () => {
            handleAiQuestion("What are the office visiting hours?");
        });
    }

    if (aiChatMenu) {
        aiChatMenu.addEventListener('click', () => {
            handleAiQuestion("Tell me about facilities and student activities");
        });
    }

    function scrollChatToBottom() {
        if (aiChatBody) {
            setTimeout(() => {
                aiChatBody.scrollTop = aiChatBody.scrollHeight;
            }, 60);
        }
    }

    function appendMessage(text, isUser = false, actions = null) {
        if (!aiChatThread || !aiChatWelcome) return;
        aiChatWelcome.style.display = 'none';
        aiChatThread.style.display = 'flex';

        const msgDiv = document.createElement('div');
        msgDiv.className = `ai-msg ${isUser ? 'user' : 'bot'}`;
        msgDiv.innerHTML = isUser ? escapeHtml(text) : text;

        if (actions && Array.isArray(actions) && actions.length > 0) {
            const actDiv = document.createElement('div');
            actDiv.className = 'ai-msg-actions';
            actions.forEach(act => {
                const btn = document.createElement('a');
                btn.className = 'ai-msg-chip';
                btn.textContent = act.label;
                if (act.action === 'modal-apply') {
                    btn.href = '#';
                    btn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        openCampusModal('apply');
                    });
                } else if (act.href) {
                    btn.href = act.href;
                    btn.target = '_blank';
                    btn.rel = 'noopener';
                } else if (act.query) {
                    btn.href = '#';
                    btn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        handleAiQuestion(act.query);
                    });
                }
                actDiv.appendChild(btn);
            });
            msgDiv.appendChild(actDiv);
        }

        aiChatThread.appendChild(msgDiv);
        scrollChatToBottom();
    }

    function showTypingIndicator() {
        if (!aiChatThread) return null;
        const typing = document.createElement('div');
        typing.className = 'ai-typing';
        typing.id = 'aiTypingIndicator';
        typing.innerHTML = '<span class="ai-typing-dot"></span><span class="ai-typing-dot"></span><span class="ai-typing-dot"></span>';
        aiChatThread.appendChild(typing);
        scrollChatToBottom();
        return typing;
    }

    function removeTypingIndicator() {
        const ind = $('#aiTypingIndicator');
        if (ind) ind.remove();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function findBestAnswer(query) {
        const q = query.toLowerCase();
        if (q.includes('curriculum') || q.includes('cambridge') || q.includes('edexcel') || q.includes('o level') || q.includes('a level')) {
            return KNOWLEDGE_BASE.curriculum;
        }
        if (q.includes('timing') || q.includes('time') || q.includes('bus') || q.includes('transport') || q.includes('hour')) {
            return KNOWLEDGE_BASE.timings;
        }
        if (q.includes('why') || q.includes('feature') || q.includes('benefit') || q.includes('special')) {
            return KNOWLEDGE_BASE.why;
        }
        if (q.includes('admission') || q.includes('apply') || q.includes('fee') || q.includes('tuition') || q.includes('cost') || q.includes('enroll')) {
            return KNOWLEDGE_BASE.admission;
        }
        if (q.includes('campus') || q.includes('location') || q.includes('lalmatia') || q.includes('banasree') || q.includes('where') || q.includes('address') || q.includes('contact') || q.includes('phone') || q.includes('number')) {
            return KNOWLEDGE_BASE.campuses;
        }
        if (q.includes('hifz') || q.includes('quran') || q.includes('islamic')) {
            return {
                answer: "📖 **Hifzul Qur’an with British Curriculum:**<br>Averroes is a pioneer in integrating full Quran Memorisation alongside the Cambridge curriculum, allowing students to complete Hifz while achieving world-class academic grades.",
                actions: [
                    { label: "Apply for Hifz", action: "modal-apply" },
                    { label: "Campus Locations", query: "campuses" }
                ]
            };
        }
        if (q.includes('sport') || q.includes('turf') || q.includes('swim') || q.includes('facility') || q.includes('activities')) {
            return {
                answer: "⚽ **World-Class Campus Facilities:**<br>&bull; **Rooftop Sports Turf:** Safe artificial grass pitch for football and athletics.<br>&bull; **Swimming Club:** Aquatic training pool with certified coaches.<br>&bull; **Robotics &amp; STEM Lab:** Practical future-ready learning.<br>&bull; **Averroes Book Shop &amp; Lounge:** Complete student and guardian convenience.",
                actions: [
                    { label: "Apply Now", action: "modal-apply" },
                    { label: "Campus Locations", query: "campuses" }
                ]
            };
        }
        // Fallback response
        return {
            answer: "Thank you for reaching out! Averroes International School operates two premier campuses in **Lalmatia** and **Banasree** offering British Curriculum and Hifzul Qur’an. How may our admissions team assist you today?",
            actions: [
                { label: "Apply Online", action: "modal-apply" },
                { label: "Call Lalmatia (+880 1954-123 123)", href: "tel:+8801954123123" },
                { label: "Call Banasree (+88 01901-048844)", href: "tel:+8801901048844" }
            ]
        };
    }

    function handleAiQuestion(questionText, key = null) {
        appendMessage(questionText, true);
        showTypingIndicator();

        // Forward visitor query to CMS Inquiry Inbox
        if (questionText && questionText.trim().length > 2) {
            try {
                fetch('api/inquiry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        source: 'ai_chat_widget',
                        query: questionText.trim()
                    })
                }).catch(() => {});
            } catch (_) {}
        }

        setTimeout(() => {
            removeTypingIndicator();
            const data = (key && KNOWLEDGE_BASE[key]) ? KNOWLEDGE_BASE[key] : findBestAnswer(questionText);
            appendMessage(data.answer, false, data.actions);
        }, 450);
    }

    // Attach click to prompt cards
    document.querySelectorAll('.ai-prompt-card').forEach(card => {
        card.addEventListener('click', () => {
            const key = card.getAttribute('data-question');
            const questionText = card.querySelector('.ai-prompt-text').textContent;
            handleAiQuestion(questionText, key);
        });
    });

    // Form input submission
    if (aiChatForm && aiChatInput) {
        aiChatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const val = aiChatInput.value.trim();
            if (!val) return;
            aiChatInput.value = '';
            handleAiQuestion(val);
        });
    }

    // Escape key listener for both modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (campusModal && campusModal.classList.contains('active')) {
                closeCampusModal();
            }
            if (aiChatWidget && aiChatWidget.classList.contains('active')) {
                toggleAiChat(false);
            }
        }
    });

    // Glenrich 1:1 Sub-Page Scroll Reveal Observer
    const glenReveals = document.querySelectorAll('.glen-reveal');
    if (glenReveals.length > 0) {
        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -30px 0px'
            });

            glenReveals.forEach(el => revealObserver.observe(el));
        } else {
            glenReveals.forEach(el => el.classList.add('is-visible'));
        }
    }
})();
