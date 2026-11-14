<?php
declare(strict_types=1);
$page_title = "Customer Sign In | Grand Living Vizag";
$current_page = 'login';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page-section">
  <div class="auth-page-card">
    
    <!-- Promo Banner Column with Uploaded Buddha Image -->
    <div class="auth-modal-promo" style="display:flex;">
      <div>
        <div class="auth-promo-img-wrap">
          <img src="/assets/images/signup-redeem-500.jpg" alt="Sign Up &amp; Redeem 500 On Your First Purchase - Code HAPPY500">
        </div>
        <div style="background:rgba(213,190,154,0.12);border:1px dashed #D5BE9A;border-radius:8px;padding:14px;text-align:center;">
          <span style="font-size:0.72rem;color:#B7AA98;text-transform:uppercase;letter-spacing:0.12em;display:block;">FESTIVE WELCOME OFFER</span>
          <strong style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:#F5F0E8;display:block;">REDEEM &#8377;500/- OFF</strong>
          <span style="font-size:0.8rem;color:#D5BE9A;font-weight:700;">USE CODE: HAPPY500 &bull; Min Purchase &#8377;25,000</span>
        </div>
      </div>
      
      <ul class="auth-promo-features" style="margin-top:20px;">
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Browsing is free with zero forced login</li>
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Log in with Mobile Number to save address &amp; get &#8377;500 discount</li>
        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> You can also buy any piece as a Guest without login!</li>
      </ul>
    </div>

    <!-- Form Column -->
    <div class="auth-modal-form-wrap">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 class="auth-form-title" style="margin-bottom:0;">Customer Sign In</h2>
        <a href="/register.php" style="font-size:0.8rem;color:#D5BE9A;text-decoration:none;font-weight:600;">Create Account &rarr;</a>
      </div>
      <p class="auth-form-sub">Enter your mobile number to sign in or claim your first-purchase discount.</p>

      <form id="standaloneLoginForm">
        <div class="auth-field-group">
          <label class="auth-label" for="stLoginMobile">Mobile Number *</label>
          <div class="auth-input-wrap">
            <span class="auth-prefix">+91</span>
            <input type="tel" id="stLoginMobile" class="auth-input has-prefix" required pattern="[0-9]{10}" maxlength="10" placeholder="90527 51666">
          </div>
        </div>

        <div class="auth-field-group" id="stOtpWrap" style="display:none;">
          <label class="auth-label" for="stLoginOtp">Enter 4-Digit OTP *</label>
          <input type="text" id="stLoginOtp" class="auth-input" maxlength="4" placeholder="Enter 8492">
          <span style="font-size:0.72rem;color:#25D366;display:block;margin-top:4px;">OTP sent to your mobile. Enter 8492 to verify.</span>
        </div>

        <button type="submit" class="auth-submit-btn" id="stSubmitBtn">Request OTP &amp; Sign In</button>
      </form>

      <div class="auth-guest-divider">OR</div>

      <!-- Continue as Guest button -->
      <a href="/catalogue.php" class="auth-guest-btn" style="text-decoration:none;">
        <span>⚡ Continue as Guest (Buy Without Login) &rarr;</span>
      </a>

      <div style="margin-top:24px;text-align:center;font-size:0.8rem;color:#B7AA98;">
        New to Grand Living? <a href="/register.php" style="color:#D5BE9A;font-weight:700;text-decoration:none;">Create New Account</a> to get ₹500 off.
      </div>
    </div>

  </div>
</section>

<script>
(function() {
  var form = document.getElementById('standaloneLoginForm');
  var mobile = document.getElementById('stLoginMobile');
  var otpWrap = document.getElementById('stOtpWrap');
  var otpInput = document.getElementById('stLoginOtp');
  var btn = document.getElementById('stSubmitBtn');
  var requested = false;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var ph = mobile.value.trim();
    if (ph.length !== 10) {
      alert('Please enter a valid 10-digit mobile number.');
      return;
    }

    if (!requested) {
      requested = true;
      otpWrap.style.display = 'block';
      otpInput.value = '8492';
      btn.textContent = 'Verify OTP & Complete Sign In';
      if (window.GrandFurniture && window.GrandFurniture.showToast) {
        window.GrandFurniture.showToast('OTP sent! Auto-filled demo code: 8492', 'success');
      }
      return;
    }

    var user = {
      firstName: 'Atelier Patron',
      lastName: '',
      phone: ph,
      pincode: '530016',
      email: 'client_' + ph + '@grandliving.in',
      hasVoucher: true,
      voucherCode: 'HAPPY500',
      voucherAmount: 500
    };
    GrandAuth.setUser(user);
    if (window.GrandFurniture && window.GrandFurniture.showToast) {
      window.GrandFurniture.showToast('Signed in successfully! Code HAPPY500 unlocked.', 'success');
    }
    setTimeout(function() {
      window.location.href = '/catalogue.php';
    }, 900);
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
