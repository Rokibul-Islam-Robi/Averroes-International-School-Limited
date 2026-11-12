/**
 * Averroes International School - Enterprise Admin CMS Scripts
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Dynamic Image Previews (Legacy & Modern Direct Upload Widgets)
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
        input.addEventListener('change', function() {
            var targetId = this.getAttribute('data-preview');
            var previewEl = document.getElementById(targetId);
            if (previewEl && this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewEl.src = e.target.result;
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // 1b. Direct Image Uploader Widget Handler with Instant Live Preview & Size Readout
    document.querySelectorAll('.adm-file-hidden').forEach(function(input) {
        input.addEventListener('change', function() {
            var imgId = this.getAttribute('data-target-img');
            var emptyId = this.getAttribute('data-target-empty');
            var badgeId = this.getAttribute('data-target-badge');
            var textId = this.getAttribute('data-target-text');

            var imgEl = imgId ? document.getElementById(imgId) : null;
            var emptyEl = emptyId ? document.getElementById(emptyId) : null;
            var badgeEl = badgeId ? document.getElementById(badgeId) : null;
            var textEl = textId ? document.getElementById(textId) : null;

            if (this.files && this.files[0]) {
                var file = this.files[0];
                var sizeKb = Math.round(file.size / 1024);
                var sizeText = sizeKb > 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : sizeKb + ' KB';

                if (badgeEl) {
                    badgeEl.textContent = '✓ ' + file.name + ' (' + sizeText + ')';
                    badgeEl.classList.add('has-file');
                }

                if (textEl) {
                    textEl.setAttribute('data-prev-val', textEl.value);
                    textEl.placeholder = '[Will upload: ' + file.name + ']';
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    if (imgEl) {
                        imgEl.src = e.target.result;
                        imgEl.style.display = 'block';
                    }
                    if (emptyEl) {
                        emptyEl.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                if (badgeEl) {
                    badgeEl.textContent = 'Direct upload from computer';
                    badgeEl.classList.remove('has-file');
                }
            }
        });
    });

    // Live update preview when text path input changes
    document.querySelectorAll('.adm-input-path').forEach(function(input) {
        input.addEventListener('blur', function() {
            var val = this.value.trim();
            if (val === '') return;
            var widget = this.closest('.adm-img-widget');
            if (widget) {
                var img = widget.querySelector('.adm-img-thumb');
                var empty = widget.querySelector('.adm-img-empty');
                if (img) {
                    var base = document.querySelector('base') ? document.querySelector('base').href : '/averroes/';
                    var fullSrc = (val.indexOf('http://') === 0 || val.indexOf('https://') === 0) ? val : (base + val.replace(/^\/+/, ''));
                    img.src = fullSrc;
                    img.style.display = 'block';
                    if (empty) empty.style.display = 'none';
                }
            }
        });
    });

    // 2. SweetAlert2 Delete Confirmations
    document.querySelectorAll('.btn-confirm-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var form = this.closest('form');
            var itemTitle = this.getAttribute('data-title') || 'this item';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete ' + itemTitle + '. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Yes, Delete It',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete ' + itemTitle + '?')) {
                    form.submit();
                }
            }
        });
    });

    // 3. Repeaters (Add/Remove dynamic rows)
    document.querySelectorAll('[data-repeater-add]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetListId = this.getAttribute('data-repeater-add');
            var list = document.getElementById(targetListId);
            var template = document.getElementById(targetListId + '-template');
            if (list && template) {
                var clone = template.content.cloneNode(true);
                list.appendChild(clone);
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.matches('.btn-remove-row') || e.target.closest('.btn-remove-row')) {
            var row = e.target.closest('.repeater-row');
            if (row) {
                row.remove();
            }
        }
    });
});
