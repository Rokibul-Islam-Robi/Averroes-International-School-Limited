<?php
declare(strict_types=1);

/**
 * Averroes International School
 * Frontend Footer Component
 */

if (!defined('APP_ROOT')) {
    exit('Direct access not permitted.');
}

$settings   = $settings   ?? get_all_settings($pdo ?? null);
$campuses   = $campuses   ?? get_campuses($pdo ?? null, false);
$footer_nav = $footer_nav ?? get_nav_items($pdo ?? null, 'footer');
$legal_nav  = $legal_nav  ?? get_nav_items($pdo ?? null, 'legal');
// Ensure Uttara Senior School is excluded from footer
$campuses   = array_values(array_filter($campuses, fn($c) => stripos($c['name'] ?? '', 'uttara') === false && stripos($c['footer_heading'] ?? '', 'uttara') === false));
?>
    <footer id="footer" class="foot">
        <div class="wrap">
            <div class="fcols">
                <?php foreach ($campuses as $idx => $campus): ?>
                    <div>
                        <?php if ($idx === 0): ?>
                            <img class="flogo" src="<?= asset_url($settings['footer_logo'] ?? 'img/footer-logo-banner.jpg') ?>" alt="Averroes Lalmatia">
                            <h4><?= e($campus['footer_heading']) ?></h4>
                        <?php elseif ($idx === 1 || stripos($campus['name'] ?? '', 'banasree') !== false): ?>
                            <img class="flogo" src="<?= asset_url('img/footer-logo-banasree.jpg') ?>" alt="Averroes Banasree">
                            <h4><?= e($campus['footer_heading']) ?></h4>
                        <?php else: ?>
                            <h4 class="pt"><?= e($campus['footer_heading']) ?></h4>
                        <?php endif; ?>
                        <a class="map" href="<?= e($campus['map_url'] ?? '#') ?>" target="_blank" rel="noopener">Open in Google Map</a>
                        <p><?= nl2br(e($campus['phone'])) ?><?php if (!empty($campus['email'])): ?><br><a href="mailto:<?= e($campus['email']) ?>" style="color: inherit; text-decoration: underline;"><?= e($campus['email']) ?></a><?php endif; ?></p>
                        <p class="ad"><?= nl2br(e($campus['address'])) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (count($campuses) === 2): ?>
                    <div>
                        <h4 class="pt">Central Admissions Desk</h4>
                        <a class="map" href="tel:+8801954123123">Direct Admission Helpline</a>
                        <p><strong>Operating Hours:</strong><br>Saturday &ndash; Thursday: 8:00 AM &ndash; 4:00 PM<br><strong>WhatsApp Helpline:</strong> +880 1954-123 123<br><a href="mailto:info@aisl.edu.bd" style="color: inherit; text-decoration: underline;">info@aisl.edu.bd</a></p>
                        <p class="ad">Central Admissions Office, Lalmatia &amp; Banasree Information Desk</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="fcols fb">
                <div>
                    <h4>Important Links</h4>
                    <ul class="links">
                        <?php foreach ($footer_nav as $nav): ?>
                            <?php
                            $fUrl = $nav['url'] ?? '#';
                            if (trim((string)($nav['label'] ?? '')) === 'Why Averroes' || $fUrl === '#why') {
                                $fUrl = 'why-averroes.php';
                            } elseif (trim((string)($nav['label'] ?? '')) === 'Life at Averroes' || $fUrl === '#life' || $fUrl === '#') {
                                if (trim((string)($nav['label'] ?? '')) === 'Life at Averroes') {
                                    $fUrl = 'life-at-averroes.php';
                                }
                            } elseif (stripos((string)($nav['label'] ?? ''), 'contact') !== false || $fUrl === '#footer') {
                                $fUrl = 'contact.php';
                            }
                            ?>
                            <li><a href="<?= e(base_url($fUrl)) ?>"><?= e($nav['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div></div>
                <div>
                    <h5>Subscribe to our Newsletter</h5>
                    <form id="nl" action="<?= e(base_url('newsletter.php')) ?>" method="POST" novalidate>
                        <input type="email" name="email" placeholder="Email" aria-label="Email" required><button
                            class="btn out light" type="submit">Subscribe</button>
                        <p id="nlmsg" role="status"></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="copy">
            <div class="wrap cp-in">
                <span><?= e($settings['copyright_text'] ?? '© 2026 Averroes International School. All Rights Reserved.') ?></span>
                <span class="cp-dev">Developed By <a href="https://cognovait.com" target="_blank" rel="noopener">COGNOVA IT</a></span>
                <span>
                    <?php foreach ($legal_nav as $legal): ?>
                        <a href="<?= e(base_url($legal['url'])) ?>"><?= e($legal['label']) ?></a>
                    <?php endforeach; ?>
                </span>
            </div>
        </div>
        <a class="wa" href="<?= e($settings['whatsapp_url'] ?? '#') ?>" aria-label="WhatsApp" target="_blank" rel="noopener">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="#ffffff" aria-hidden="true" style="display:block; margin:auto;">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
        </a>
        <button id="chatToggle" class="chat" aria-label="Chat with Us" type="button">
            <?= e($settings['chat_text'] ?? 'Chat with Us') ?>
        </button>
    </footer>

    <!-- Campus Selection Modal (Image 2 Replica) -->
    <div id="campusModal" class="campus-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="campusModalTitle">
        <div class="campus-modal-card">
            <button id="campusModalClose" class="campus-modal-close" aria-label="Close dialog">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="campus-modal-body">
                <h3 id="campusModalTitle" class="campus-modal-heading">Select your Preferred Campus</h3>
                <div class="campus-modal-btns">
                    <a id="campusChoiceLalmatia" href="#" class="campus-select-btn">Averroes Lalmatia</a>
                    <a id="campusChoiceBanasree" href="#" class="campus-select-btn">Averroes Banasree</a>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assistant Chat Widget (Image 1 Replica) -->
    <div id="aiChatWidget" class="ai-chat-container" aria-hidden="true">
        <!-- Chat Header -->
        <div class="ai-chat-header">
            <div class="ai-chat-header-left">
                <button id="aiChatBack" class="ai-chat-icon-btn" aria-label="Back to main menu" title="Back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <div class="ai-chat-avatar">
                    <img src="<?= asset_url('img/logo.png') ?>" alt="Averroes" style="display:block; width:100%; height:100%; object-fit:contain;">
                </div>
                <div class="ai-chat-title-group">
                    <h4 class="ai-chat-name">Averroes Internation...</h4>
                    <span class="ai-chat-badge"><span class="status-dot"></span> AI Assistant</span>
                </div>
            </div>
            <div class="ai-chat-header-right">
                <button class="ai-chat-icon-btn" id="aiChatSchedule" title="Admissions Schedule" aria-label="Admissions Schedule">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </button>
                <button class="ai-chat-icon-btn" id="aiChatMenu" title="More options" aria-label="More options">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle><circle cx="5" cy="12" r="1.5"></circle></svg>
                </button>
                <button id="aiChatClose" class="ai-chat-icon-btn ai-chat-close-btn" aria-label="Close Chat" title="Close">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <!-- Chat Scrollable Body -->
        <div id="aiChatBody" class="ai-chat-body">
            <!-- Initial Welcome & Question Options View -->
            <div id="aiChatWelcome" class="ai-chat-welcome">
                <p class="ai-chat-greeting">Hi, Welcome to Averroes International School!</p>
                <h3 class="ai-chat-headline">How can I help you today?</h3>

                <!-- Quick Action Prompt Cards (Image 1 replica) -->
                <div class="ai-chat-prompts">
                    <button class="ai-prompt-card" data-question="curriculum" type="button">
                        <span class="ai-prompt-icon book">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </span>
                        <span class="ai-prompt-text">What curriculum does Averroes follow?</span>
                        <svg class="ai-prompt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>

                    <button class="ai-prompt-card" data-question="timings" type="button">
                        <span class="ai-prompt-icon clock">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d946ef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </span>
                        <span class="ai-prompt-text">What are the school timings and transport options?</span>
                        <svg class="ai-prompt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>

                    <button class="ai-prompt-card" data-question="why" type="button">
                        <span class="ai-prompt-icon map">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                        </span>
                        <span class="ai-prompt-text">Why should I choose Averroes International School?</span>
                        <svg class="ai-prompt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>

                    <button class="ai-prompt-card" data-question="admission" type="button">
                        <span class="ai-prompt-icon grad">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f58220" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        </span>
                        <span class="ai-prompt-text">How do I apply for admission?</span>
                        <svg class="ai-prompt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>

                    <button class="ai-prompt-card" data-question="campuses" type="button">
                        <span class="ai-prompt-icon pin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </span>
                        <span class="ai-prompt-text">Where are Lalmatia &amp; Banasree campuses located?</span>
                        <svg class="ai-prompt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- Active Conversation Thread -->
            <div id="aiChatThread" class="ai-chat-thread" style="display: none;"></div>
        </div>

        <!-- Chat Input Footer -->
        <div class="ai-chat-footer">
            <form id="aiChatForm" class="ai-chat-input-wrapper">
                <button type="button" class="ai-chat-plus" id="aiChatPlus" title="Quick Options" aria-label="Quick options">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <input type="text" id="aiChatInput" class="ai-chat-input" placeholder="Ask anything about Averroes" autocomplete="off" aria-label="Ask anything about Averroes">
                <button type="submit" id="aiChatSend" class="ai-chat-send" aria-label="Send message">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                </button>
            </form>
            <div class="ai-chat-powered">
                Powered by <a href="https://cognovait.com" target="_blank" rel="noopener"><strong>COGNOVA IT</strong></a>
            </div>
        </div>
    </div>
    <!-- High-Performance GPU-Accelerated Scroll Animations -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 700,
                    easing: 'ease-out-cubic',
                    once: true,
                    offset: 60,
                    disableMutationObserver: false
                });
            }
        });
    </script>
    <script src="<?= asset_url('js/main.js') ?>"></script>
</body>

</html>
