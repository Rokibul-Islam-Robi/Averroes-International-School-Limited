<?php
declare(strict_types=1);

/**
 * Averroes Admin CMS - Layout Footer
 */
?>
    </main>

    <footer class="adm-footer" style="padding: 14px 24px; font-size: 12px; color: #64748B; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #E2E8F0; background: #FFFFFF;">
        <div>
            &copy; <?= date('Y') ?> <strong style="color: var(--adm-primary);">Averroes Ltd CMS</strong> — School of Life.
        </div>
        <div style="display:flex; align-items:center; gap:6px;">
            <span>Developed by</span>
            <span style="display:inline-flex; align-items:center; gap:4px; font-weight:700; color:#25093B; background:#FFF7ED; border:1px solid #FED7AA; padding:2px 8px; border-radius:4px; font-size:11.5px;">
                ⚡ Cognova IT
            </span>
        </div>
    </footer>
</div>

<!-- jQuery & Toastr -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="<?= base_url('admin/js/admin.js') ?>"></script>

<script>
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "4000"
};

<?php if (!empty($flash)): ?>
    toastr["<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : 'success') ?>"](
        "<?= addslashes($flash['message']) ?>"
    );
<?php endif; ?>
</script>

</body>
</html>
