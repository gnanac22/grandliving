  </main><!-- /admin-content -->
</div><!-- /admin-main -->
</div><!-- /admin-layout -->

<script>
// Sidebar toggle
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar       = document.getElementById('adminSidebar');
if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}

// User dropdown
const userMenuBtn  = document.getElementById('userMenuBtn');
const userDropdown = document.getElementById('userDropdown');
if (userMenuBtn) {
  userMenuBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('show');
  });
  document.addEventListener('click', () => userDropdown.classList.remove('show'));
}

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
  setTimeout(() => el.style.opacity = '0', 4000);
  setTimeout(() => el.remove(), 4500);
});

// Slug auto-generation
const nameInputs = document.querySelectorAll('[data-slug-source]');
nameInputs.forEach(src => {
  const targetId = src.dataset.slugSource;
  const target   = document.getElementById(targetId);
  if (!target) return;
  let userEdited = target.value !== '';
  src.addEventListener('input', () => {
    if (userEdited) return;
    target.value = src.value.toLowerCase().trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/[\s-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  });
  target.addEventListener('input', () => { userEdited = target.value !== ''; });
});

// Char counter
document.querySelectorAll('[data-maxlength]').forEach(el => {
  const max     = parseInt(el.dataset.maxlength);
  const counter = document.getElementById(el.id + '-counter');
  if (!counter) return;
  const update = () => {
    const left = max - el.value.length;
    counter.textContent = left + ' chars remaining';
    counter.className = left < 20 ? 'char-counter warn' : 'char-counter';
  };
  el.addEventListener('input', update);
  update();
});

// CSRF meta helper for AJAX
const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
window.CSRF_TOKEN = csrfMeta ? csrfMeta.getAttribute('content') : '';

// Generic AJAX toggle status
document.querySelectorAll('[data-toggle-status]').forEach(btn => {
  btn.addEventListener('click', async function () {
    const { type, id, field, value } = this.dataset;
    const newVal = value === '1' ? '0' : '1';
    try {
      const res  = await fetch('<?= ADMIN_URL ?>/toggle-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `type=${type}&id=${id}&field=${field}&value=${newVal}&csrf_token=${window.CSRF_TOKEN}`
      });
      const data = await res.json();
      if (data.success) {
        this.dataset.value = newVal;
        this.textContent   = newVal === '1' ? '✓ Active' : '✗ Inactive';
        this.classList.toggle('btn-success', newVal === '1');
        this.classList.toggle('btn-ghost',   newVal !== '1');
      }
    } catch (e) { console.error(e); }
  });
});

// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', function (e) {
    if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
  });
});
</script>
</body>
</html>
