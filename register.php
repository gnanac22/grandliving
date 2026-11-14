<?php
declare(strict_types=1);
$page_title = "Create New Customer Account | Grand Living Vizag";
$current_page = 'register';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page-section">
  <div class="auth-page-card">
    
    <!-- Promo Banner Column with Buddha Offer Image -->
    <div class="auth-modal-promo" style="display:flex;">
      <div>
        <div class="auth-promo-img-wrap">
          <img src="/assets/images/signup-redeem-500.jpg" alt="Sign Up &amp; Redeem 500 On Your First Purchase with code HAPPY500">
        </div>
        <div style="background:rgba(213,190,154,0.12);border:1px dashed #D5BE9A;border-radius:8px;padding:14px;text-align:center;">
          <span style="font-size:0.72rem;color:#B7AA98;text-transform:uppercase;letter-spacing:0.12em;display:block;">SIGN UP PRIVILEGE</span>
          <strong style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:#F5F0E8;display:block;">REDEEM &#8377;500/- ON FIRST ORDER</strong>
          <span style="font-size:0.8rem;color:#D5BE9A;font-weight:700;">USE CODE: HAPPY500 &bull; Min Purchase &#8377;25,000</span>
        </div>
      </div>
      
      <ul class="auth-promo-features" style="margin-top:20px;">
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Browsing our catalogue &amp; prices requires no account</li>
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Account unlocks instant &#8377;500 discount &amp; priority showroom reservations</li>
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> You can also buy directly as a guest at any time!</li>
      </ul>
    </div>

    <!-- Registration Form Column -->
    <div class="auth-modal-form-wrap">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 class="auth-form-title" style="margin-bottom:0;">Create Account</h2>
        <a href="/login.php" style="font-size:0.8rem;color:#D5BE9A;text-decoration:none;font-weight:600;">Sign In Instead &rarr;</a>
      </div>
      <p class="auth-form-sub">Fill details below to activate your profile &amp; claim &#8377;500 off (Code: HAPPY500).</p>

      <form id="standaloneRegisterForm">
        <div class="auth-grid-2">
          <div class="auth-field-group">
            <label class="auth-label" for="stRegFirstName">First Name *</label>
            <input type="text" id="stRegFirstName" class="auth-input" required placeholder="e.g. Rahul">
          </div>
          <div class="auth-field-group">
            <label class="auth-label" for="stRegLastName">Last Name *</label>
            <input type="text" id="stRegLastName" class="auth-input" required placeholder="e.g. Sharma">
          </div>
        </div>

        <div class="auth-field-group">
          <label class="auth-label" for="stRegMobile">Mobile Number *</label>
          <div class="auth-input-wrap">
            <span class="auth-prefix">+91</span>
            <input type="tel" id="stRegMobile" class="auth-input has-prefix" required pattern="[0-9]{10}" maxlength="10" placeholder="98765 43210">
          </div>
        </div>

        <div class="auth-grid-2">
          <div class="auth-field-group">
            <label class="auth-label" for="stRegPincode">Pincode *</label>
            <input type="text" id="stRegPincode" class="auth-input" required pattern="[0-9]{6}" maxlength="6" placeholder="530016">
          </div>
          <div class="auth-field-group">
            <label class="auth-label" for="stRegEmail">Email ID *</label>
            <input type="email" id="stRegEmail" class="auth-input" required placeholder="rahul@example.com">
          </div>
        </div>

        <div style="background:rgba(213,190,154,0.08);border:1px dashed rgba(213,190,154,0.3);border-radius:6px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
          <input type="checkbox" id="stApplyCoupon" checked style="accent-color:#D5BE9A;width:18px;height:18px;">
          <label for="stApplyCoupon" style="font-size:0.78rem;color:#F5F0E8;cursor:pointer;">
            Apply <strong>HAPPY500</strong> (&#8377;500 discount on first order &ge; &#8377;25,000)
          </label>
        </div>

        <button type="submit" class="auth-submit-btn">Create Account &amp; Unlock &#8377;500</button>
      </form>

      <div class="auth-guest-divider">OR</div>

      <!-- Buy Without Login Option -->
      <a href="/catalogue.php" class="auth-guest-btn" style="text-decoration:none;">
        <span>⚡ Continue as Guest (Buy Without Login) &rarr;</span>
      </a>

      <div style="margin-top:20px;text-align:center;font-size:0.78rem;color:#B7AA98;">
        Already registered? <a href="/login.php" style="color:#D5BE9A;font-weight:700;text-decoration:none;">Sign In with Mobile</a>
      </div>
    </div>

  </div>
</section>

<script>
(function() {
  var form = document.getElementById('standaloneRegisterForm');
  var fn = document.getElementById('stRegFirstName');
  var ln = document.getElementById('stRegLastName');
  var ph = document.getElementById('stRegMobile');
  var pin = document.getElementById('stRegPincode');
  var em = document.getElementById('stRegEmail');

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!fn.value.trim() || !ln.value.trim() || !ph.value.trim() || !pin.value.trim() || !em.value.trim()) {
      alert('Please fill out all required fields.');
      return;
    }
    if (ph.value.trim().length !== 10) {
      alert('Please enter a valid 10-digit mobile number.');
      return;
    }
    if (pin.value.trim().length !== 6) {
      alert('Please enter a valid 6-digit pincode.');
      return;
    }

    var user = {
      firstName: fn.value.trim(),
      lastName: ln.value.trim(),
      phone: ph.value.trim(),
      pincode: pin.value.trim(),
      email: em.value.trim(),
      hasVoucher: true,
      voucherCode: 'HAPPY500',
      voucherAmount: 500
    };
    GrandAuth.setUser(user);
    if (window.GrandFurniture && window.GrandFurniture.showToast) {
      window.GrandFurniture.showToast('Account created for ' + user.firstName + '! Voucher HAPPY500 unlocked!', 'success');
    }
    setTimeout(function() {
      window.location.href = '/catalogue.php';
    }, 900);
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
