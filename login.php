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

        <div class="auth-field-group" id="stOtpWrap" style="display:none;margin-top:14px;">
          <div style="background:rgba(37,211,102,0.1);border:1px solid rgba(37,211,102,0.3);border-radius:8px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#25D366" style="flex-shrink:0;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/></svg>
            <div style="font-size:0.76rem;color:#E8DFD2;line-height:1.4;">
              <strong style="color:#25D366;">OTP Sent to +91 <span id="stSentPhone"></span></strong><br>
              <span>Check your WhatsApp / SMS messages for your 4-digit code.</span>
            </div>
          </div>

          <label class="auth-label" for="stLoginOtp">Enter 4-Digit OTP *</label>
          <div style="display:flex;gap:10px;">
            <input type="text" id="stLoginOtp" class="auth-input" maxlength="4" placeholder="••••" style="text-align:center;letter-spacing:0.35em;font-size:1.15rem;font-weight:700;">
            <a href="#" id="stWhatsappOtpLink" target="_blank" rel="noopener" style="padding:10px 14px;background:#25D366;color:#fff;border-radius:6px;text-decoration:none;font-size:0.75rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
              💬 Open WhatsApp
            </a>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;font-size:0.72rem;">
            <span style="color:#B7AA98;">Expires in 5 minutes</span>
            <button type="button" id="stResendBtn" style="background:none;border:none;color:#D5BE9A;cursor:pointer;font-size:0.72rem;text-decoration:underline;">Resend Code</button>
          </div>
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
// Standalone Login Submit with Direct OTP to Phone & Registered Check
(function() {
  var form = document.getElementById('standaloneLoginForm');
  var mobile = document.getElementById('stLoginMobile');
  var otpWrap = document.getElementById('stOtpWrap');
  var otpInput = document.getElementById('stLoginOtp');
  var sentPhoneSpan = document.getElementById('stSentPhone');
  var waLink = document.getElementById('stWhatsappOtpLink');
  var resendBtn = document.getElementById('stResendBtn');
  var btn = document.getElementById('stSubmitBtn');
  var requested = false;
  var matchedUser = null;
  var generatedOtp = null;

  function dispatchOtp(ph) {
    generatedOtp = String(Math.floor(1000 + Math.random() * 9000));
    sessionStorage.setItem('grand_auth_otp_' + ph, generatedOtp);

    var msg = encodeURIComponent("🔒 *Grand Living Visakhapatnam — Verification Code*\n\n" +
      "Your secret sign-in OTP is: *" + generatedOtp + "*\n\n" +
      "Use this code on the website to sign in to your Grand Living account. Valid for 5 minutes. Do not share this with anyone.");
    var waUrl = "https://wa.me/91" + ph + "?text=" + msg;
    
    if (waLink) waLink.href = waUrl;
    if (sentPhoneSpan) sentPhoneSpan.textContent = ph;

    window.open(waUrl, "_blank");

    otpWrap.style.display = 'block';
    otpInput.value = '';
    otpInput.focus();
    btn.textContent = 'Verify OTP & Sign In as ' + matchedUser.firstName;

    if (window.GrandFurniture && window.GrandFurniture.showToast) {
      window.GrandFurniture.showToast('OTP sent directly to +91 ' + ph + ' via WhatsApp / Mobile!', 'success');
    }
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var ph = mobile.value.trim().replace(/\D/g, '').slice(-10);
    if (ph.length !== 10) {
      alert('Please enter a valid 10-digit mobile number.');
      return;
    }

    // 1. CHECK IF ACCOUNT EXISTS
    matchedUser = GrandAuth.findUserByMobile(ph);
    if (!matchedUser) {
      alert('⚠️ No account found with mobile number +91 ' + ph + '!\n\nOnly registered customers can sign in. Please create an account first to sign in and claim ₹500 off (Code: HAPPY500).');
      if (window.GrandFurniture && window.GrandFurniture.showToast) {
        window.GrandFurniture.showToast('No account found for +91 ' + ph + '. Please register first!', 'error');
      }
      setTimeout(function() {
        if (confirm('No account found for +91 ' + ph + '.\n\nWould you like to register now and get ₹500 OFF with code HAPPY500?')) {
          window.location.href = '/register.php?phone=' + ph;
        }
      }, 200);
      return;
    }

    // 2. DISPATCH OTP DIRECTLY TO THEIR PHONE
    if (!requested) {
      requested = true;
      dispatchOtp(ph);
      return;
    }

    // 3. VERIFY ENTERED OTP AGAINST THE ONE SENT
    var entered = otpInput.value.trim();
    if (!entered || entered.length !== 4) {
      alert('Please enter the 4-digit OTP sent to +91 ' + ph);
      otpInput.focus();
      return;
    }

    if (entered !== generatedOtp) {
      alert('❌ Incorrect OTP!\n\nPlease enter the exact 4-digit code sent to +91 ' + ph + ' via WhatsApp / SMS.');
      otpInput.value = '';
      otpInput.focus();
      return;
    }

    // Successful Login
    GrandAuth.setUser(matchedUser);
    if (window.GrandFurniture && window.GrandFurniture.showToast) {
      window.GrandFurniture.showToast('Welcome back, ' + matchedUser.firstName + '! Signed in successfully.', 'success');
    }
    setTimeout(function() {
      window.location.href = '/catalogue.php';
    }, 900);
  });

  resendBtn?.addEventListener('click', function() {
    var ph = mobile.value.trim().replace(/\D/g, '').slice(-10);
    if (ph && ph.length === 10) {
      dispatchOtp(ph);
    }
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
