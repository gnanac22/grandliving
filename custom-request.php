<?php require_once 'config/db.php'; require_once 'includes/functions.php'; ?>
<?php $page_title = "Custom Furniture Request | Grand Furniture Vizag"; ?>
<?php $page_description = "Request bespoke, custom-made furniture tailored to your exact specifications."; ?>
<?php
// Flash message handling
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
$flash_error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
// CSRF token
$csrf_token = generateCSRFToken();
?>
<?php include 'includes/header.php'; ?>

<style>
/* ============================================================
   CUSTOM REQUEST PAGE — Grand Furniture Vizag
   Design System: Obsidian #171614, Espresso #29221D,
   Ivory #F5F0E8, Champagne #D5BE9A, Walnut #76543B,
   Stone #B7AA98, Sand #E8DFD2
   Fonts: Cormorant Garamond (display), Manrope (body)
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Manrope:wght@300;400;500;600;700&display=swap');

*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ---- HERO ---- */
.cr-hero {
    min-height: 60vh;
    background: linear-gradient(135deg, #171614 0%, #29221D 60%, #76543B 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 24px;
    position: relative;
    overflow: hidden;
}

.cr-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 60% 40%, rgba(213,190,154,0.07) 0%, transparent 65%);
    pointer-events: none;
}

.cr-hero__inner {
    position: relative;
    z-index: 1;
    max-width: 800px;
}

.cr-hero__label {
    font-family: 'Manrope', sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.22em;
    color: #D5BE9A;
    text-transform: uppercase;
    font-variant: small-caps;
    margin-bottom: 24px;
    display: block;
}

.cr-hero__headline {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(3.5rem, 8vw, 6rem);
    font-weight: 300;
    color: #F5F0E8;
    line-height: 1.0;
    letter-spacing: 0.04em;
    margin-bottom: 28px;
}

.cr-hero__subtitle {
    font-family: 'Manrope', sans-serif;
    font-size: 1.05rem;
    font-weight: 300;
    color: #B7AA98;
    line-height: 1.75;
    max-width: 540px;
    margin: 0 auto;
}

/* ---- INTRO STRIP ---- */
.cr-strip {
    background: #29221D;
    padding: 60px 0;
}

.cr-strip__container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
}

.cr-strip__item {
    text-align: center;
    padding: 32px 24px;
    border: 1px solid rgba(213,190,154,0.12);
    border-radius: 2px;
    transition: border-color 0.3s ease, background 0.3s ease;
}

.cr-strip__item:hover {
    border-color: rgba(213,190,154,0.3);
    background: rgba(213,190,154,0.04);
}

.cr-strip__icon {
    width: 44px;
    height: 44px;
    margin: 0 auto 20px;
    color: #D5BE9A;
}

.cr-strip__icon svg {
    width: 100%;
    height: 100%;
    stroke: #D5BE9A;
    fill: none;
}

.cr-strip__title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.35rem;
    font-weight: 500;
    color: #F5F0E8;
    letter-spacing: 0.03em;
    margin-bottom: 10px;
}

.cr-strip__desc {
    font-family: 'Manrope', sans-serif;
    font-size: 0.82rem;
    font-weight: 400;
    color: #B7AA98;
    line-height: 1.65;
}

/* ---- FORM SECTION ---- */
.cr-form-section {
    background: #171614;
    padding: 100px 0;
}

.cr-form-section__container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 35% 65%;
    gap: 0;
    align-items: start;
}

/* Left Info Panel */
.cr-info-panel {
    background: #29221D;
    padding: 48px;
    position: sticky;
    top: 80px;
    border-right: 1px solid rgba(213,190,154,0.1);
}

.cr-info-panel__eyebrow {
    font-family: 'Manrope', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    color: #D5BE9A;
    text-transform: uppercase;
    font-variant: small-caps;
    margin-bottom: 20px;
    display: block;
}

.cr-info-panel__heading {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.75rem;
    font-weight: 400;
    font-style: italic;
    color: #F5F0E8;
    line-height: 1.4;
    margin-bottom: 32px;
}

.cr-info-panel__divider {
    width: 40px;
    height: 1px;
    background: rgba(213,190,154,0.4);
    margin-bottom: 32px;
}

.cr-info-panel__list {
    list-style: none;
    margin-bottom: 40px;
}

.cr-info-panel__list li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.88rem;
    font-weight: 400;
    color: #B7AA98;
    line-height: 1.6;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(213,190,154,0.08);
}

.cr-info-panel__list li:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.cr-info-panel__list li::before {
    content: '';
    display: block;
    width: 6px;
    height: 6px;
    min-width: 6px;
    border-radius: 50%;
    background: #D5BE9A;
    margin-top: 7px;
}

.cr-info-panel__contact-label {
    font-family: 'Manrope', sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    color: rgba(213,190,154,0.6);
    text-transform: uppercase;
    margin-bottom: 10px;
    display: block;
}

.cr-info-panel__phone {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.3rem;
    font-weight: 500;
    color: #D5BE9A;
    text-decoration: none;
    transition: color 0.2s;
}

.cr-info-panel__phone:hover {
    color: #F5F0E8;
}

.cr-info-panel__phone svg {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    fill: none;
}

/* Right Form Panel */
.cr-form-panel {
    padding: 48px 56px;
    background: #171614;
}

.cr-form-panel__heading {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    font-weight: 400;
    color: #F5F0E8;
    margin-bottom: 8px;
    letter-spacing: 0.03em;
}

.cr-form-panel__subheading {
    font-family: 'Manrope', sans-serif;
    font-size: 0.85rem;
    color: #B7AA98;
    margin-bottom: 48px;
    line-height: 1.6;
}

/* ---- FORM FIELDS ---- */
.cr-form {
    width: 100%;
}

.cr-form__group {
    margin-bottom: 32px;
    position: relative;
}

.cr-form__label {
    display: block;
    font-family: 'Manrope', sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    color: #D5BE9A;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.cr-form__label .optional-tag {
    font-size: 0.65rem;
    color: rgba(183,170,152,0.6);
    font-weight: 400;
    text-transform: none;
    letter-spacing: 0;
    margin-left: 6px;
}

.cr-form__input,
.cr-form__select,
.cr-form__textarea {
    width: 100%;
    font-family: 'Manrope', sans-serif;
    font-size: 1rem;
    font-weight: 400;
    color: #F5F0E8;
    background: rgba(255,255,255,0.03);
    border: none;
    border-bottom: 1px solid rgba(213,190,154,0.25);
    border-radius: 0;
    padding: 12px 0;
    outline: none;
    transition: border-color 0.25s ease;
    -webkit-appearance: none;
    appearance: none;
}

.cr-form__input::placeholder,
.cr-form__textarea::placeholder {
    color: rgba(183,170,152,0.45);
    font-size: 0.9rem;
}

.cr-form__input:focus,
.cr-form__select:focus,
.cr-form__textarea:focus {
    border-bottom-color: #D5BE9A;
    background: rgba(213,190,154,0.03);
}

.cr-form__select {
    cursor: pointer;
    color: rgba(245,240,232,0.8);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23D5BE9A' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 4px center;
    padding-right: 28px;
}

.cr-form__select option {
    background: #29221D;
    color: #F5F0E8;
}

.cr-form__select option:disabled {
    color: rgba(183,170,152,0.5);
}

.cr-form__textarea {
    resize: vertical;
    min-height: 130px;
    line-height: 1.65;
}

.cr-form__helper {
    display: block;
    margin-top: 7px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.72rem;
    color: rgba(183,170,152,0.55);
    line-height: 1.5;
}

/* File Input */
.cr-file-input__native {
    display: none;
}

.cr-file-input__trigger {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: #D5BE9A;
    border: 1px solid rgba(213,190,154,0.35);
    padding: 12px 24px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: transparent;
    text-transform: uppercase;
}

.cr-file-input__trigger:hover {
    background: rgba(213,190,154,0.08);
    border-color: rgba(213,190,154,0.6);
}

.cr-file-input__trigger svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
}

.cr-file-input__name {
    display: block;
    margin-top: 10px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.8rem;
    color: #B7AA98;
}

.cr-file-preview {
    margin-top: 16px;
    display: none;
}

.cr-file-preview img {
    max-width: 160px;
    max-height: 120px;
    object-fit: cover;
    border: 1px solid rgba(213,190,154,0.25);
    display: block;
}

/* Field error */
.field-error {
    display: block;
    margin-top: 6px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.72rem;
    color: #ff6b6b;
    letter-spacing: 0.03em;
}

.cr-form__input.has-error,
.cr-form__select.has-error,
.cr-form__textarea.has-error {
    border-bottom-color: #ff6b6b;
}

/* Row layout for two inputs */
.cr-form__row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

/* Submit Button */
.cr-form__submit {
    display: block;
    width: 100%;
    font-family: 'Manrope', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #171614;
    background: #D5BE9A;
    border: none;
    padding: 20px 32px;
    cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease, transform 0.15s ease;
    margin-top: 16px;
    position: relative;
    overflow: hidden;
}

.cr-form__submit:hover {
    background: #c4ad89;
    transform: translateY(-1px);
}

.cr-form__submit:active {
    transform: translateY(0);
}

.cr-form__submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.cr-form__submit .btn-loading {
    display: none;
}

.cr-form__submit.is-loading .btn-text {
    display: none;
}

.cr-form__submit.is-loading .btn-loading {
    display: inline-block;
}

/* Flash / Result Messages */
.cr-message {
    padding: 20px 24px;
    margin-bottom: 32px;
    border-radius: 2px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    font-family: 'Manrope', sans-serif;
    font-size: 0.88rem;
    line-height: 1.6;
}

.cr-message--success {
    background: rgba(76,175,80,0.1);
    border: 1px solid rgba(76,175,80,0.3);
    color: #a5d6a7;
}

.cr-message--error {
    background: rgba(244,67,54,0.1);
    border: 1px solid rgba(244,67,54,0.3);
    color: #ef9a9a;
}

.cr-message__icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
    margin-top: 1px;
}

.cr-message.is-hidden {
    display: none;
}

/* Responsive */
@media (max-width: 1100px) {
    .cr-form-section__container {
        grid-template-columns: 1fr;
    }
    .cr-info-panel {
        position: static;
        border-right: none;
        border-bottom: 1px solid rgba(213,190,154,0.1);
    }
    .cr-form-panel {
        padding: 48px 32px;
    }
}

@media (max-width: 992px) {
    .cr-strip__container {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .cr-form-section {
        padding: 70px 0;
    }
    .cr-form__row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}

@media (max-width: 768px) {
    .cr-strip__container {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .cr-info-panel {
        padding: 32px 24px;
    }
    .cr-form-panel {
        padding: 36px 20px;
    }
    .cr-hero__headline {
        font-size: clamp(2.8rem, 10vw, 4rem);
    }
    .cr-form-section {
        padding: 50px 0;
    }
}

@media (max-width: 576px) {
    .cr-hero {
        min-height: 55vh;
        padding: 60px 20px;
    }
    .cr-strip {
        padding: 40px 0;
    }
    .cr-strip__item {
        padding: 24px 16px;
    }
    .cr-form-panel {
        padding: 28px 16px;
    }
    .cr-info-panel {
        padding: 28px 16px;
    }
    .cr-form__submit {
        font-size: 0.78rem;
        padding: 18px 24px;
    }
}
</style>

<!-- ===================== HERO ===================== -->
<section class="cr-hero">
    <div class="cr-hero__inner">
        <span class="cr-hero__label">Bespoke Furniture</span>
        <h1 class="cr-hero__headline">MADE FOR YOU.</h1>
        <p class="cr-hero__subtitle">Every detail crafted to your vision. Tell us your dream, and we'll bring it to life.</p>
    </div>
</section>

<!-- ===================== INTRO STRIP ===================== -->
<section class="cr-strip">
    <div class="cr-strip__container">

        <!-- Design Consultation -->
        <div class="cr-strip__item">
            <div class="cr-strip__icon">
                <svg viewBox="0 0 24 24" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <h3 class="cr-strip__title">Design Consultation</h3>
            <p class="cr-strip__desc">Expert guidance to translate your ideas into refined, functional designs.</p>
        </div>

        <!-- Custom Fabrication -->
        <div class="cr-strip__item">
            <div class="cr-strip__icon">
                <svg viewBox="0 0 24 24" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <h3 class="cr-strip__title">Custom Fabrication</h3>
            <p class="cr-strip__desc">Handcrafted by master artisans using premium materials to your exact specifications.</p>
        </div>

        <!-- White Glove Delivery -->
        <div class="cr-strip__item">
            <div class="cr-strip__icon">
                <svg viewBox="0 0 24 24" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 4v4h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <h3 class="cr-strip__title">White Glove Delivery</h3>
            <p class="cr-strip__desc">Safe, professional delivery and in-home assembly at your convenience.</p>
        </div>

    </div>
</section>

<!-- ===================== FORM SECTION ===================== -->
<section class="cr-form-section">
    <div class="cr-form-section__container">

        <!-- LEFT: Info Panel -->
        <aside class="cr-info-panel">
            <span class="cr-info-panel__eyebrow">Why Choose Custom?</span>
            <p class="cr-info-panel__heading">"Your space is unique. Your furniture should be too."</p>
            <div class="cr-info-panel__divider"></div>
            <ul class="cr-info-panel__list">
                <li>Exact dimensions for your space</li>
                <li>Choice of wood, fabric &amp; finish</li>
                <li>Built by master craftsmen</li>
                <li>Delivered and assembled</li>
            </ul>
            <span class="cr-info-panel__contact-label">Have questions? Call us</span>
            <a href="tel:09052751666" class="cr-info-panel__phone">
                <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.37 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6.03 6.03l.72-.72a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                09052751666
            </a>
        </aside>

        <!-- RIGHT: Form Panel -->
        <div class="cr-form-panel">
            <h2 class="cr-form-panel__heading">Request Your Custom Piece</h2>
            <p class="cr-form-panel__subheading">Fill in the details below and our design team will reach out within 24 hours.</p>

            <!-- PHP Flash Messages -->
            <?php if (!empty($flash_success)): ?>
            <div class="cr-message cr-message--success">
                <svg class="cr-message__icon" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?= sanitizeOutput($flash_success) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($flash_error)): ?>
            <div class="cr-message cr-message--error">
                <svg class="cr-message__icon" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= sanitizeOutput($flash_error) ?>
            </div>
            <?php endif; ?>

            <!-- AJAX Result Messages -->
            <div id="ajaxSuccess" class="cr-message cr-message--success is-hidden" role="alert">
                <svg class="cr-message__icon" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span id="ajaxSuccessText">Your request has been submitted! We'll contact you within 24 hours.</span>
            </div>
            <div id="ajaxError" class="cr-message cr-message--error is-hidden" role="alert">
                <svg class="cr-message__icon" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span id="ajaxErrorText">Something went wrong. Please try again.</span>
            </div>

            <form
                action="/process-custom-request.php"
                method="POST"
                enctype="multipart/form-data"
                id="customRequestForm"
                novalidate
            >
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Row: Name + Phone -->
                <div class="cr-form__row">
                    <div class="cr-form__group">
                        <label class="cr-form__label" for="name">Full Name <span aria-hidden="true">*</span></label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="cr-form__input"
                            placeholder="Your full name"
                            autocomplete="name"
                            maxlength="100"
                        >
                        <span class="field-error" id="name-error" aria-live="polite"></span>
                    </div>
                    <div class="cr-form__group">
                        <label class="cr-form__label" for="phone">Phone Number <span aria-hidden="true">*</span></label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            class="cr-form__input"
                            placeholder="+91 XXXXX XXXXX"
                            autocomplete="tel"
                            maxlength="15"
                        >
                        <span class="field-error" id="phone-error" aria-live="polite"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="cr-form__group">
                    <label class="cr-form__label" for="email">Email Address <span class="optional-tag">(Optional)</span></label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="cr-form__input"
                        placeholder="your@email.com"
                        autocomplete="email"
                        maxlength="254"
                    >
                    <span class="field-error" id="email-error" aria-live="polite"></span>
                </div>

                <!-- Category -->
                <div class="cr-form__group">
                    <label class="cr-form__label" for="category">Furniture Category <span aria-hidden="true">*</span></label>
                    <select name="category" id="category" class="cr-form__select">
                        <option value="" disabled selected>— Select Category —</option>
                        <option value="Living Room">Living Room</option>
                        <option value="Bedroom">Bedroom</option>
                        <option value="Dining Room">Dining Room</option>
                        <option value="Home Office">Home Office</option>
                        <option value="Kids Room">Kids Room</option>
                        <option value="Outdoor">Outdoor</option>
                        <option value="Storage &amp; Cabinets">Storage &amp; Cabinets</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="field-error" id="category-error" aria-live="polite"></span>
                </div>

                <!-- Row: Dimensions + Material -->
                <div class="cr-form__row">
                    <div class="cr-form__group">
                        <label class="cr-form__label" for="dimensions">Preferred Dimensions <span class="optional-tag">(Optional)</span></label>
                        <input
                            type="text"
                            name="dimensions"
                            id="dimensions"
                            class="cr-form__input"
                            placeholder="e.g., 6ft × 3ft × 4ft (L×W×H)"
                            maxlength="200"
                        >
                        <span class="cr-form__helper">Length × Width × Height in ft or cm</span>
                        <span class="field-error" id="dimensions-error" aria-live="polite"></span>
                    </div>
                    <div class="cr-form__group">
                        <label class="cr-form__label" for="material">Material Preference <span class="optional-tag">(Optional)</span></label>
                        <input
                            type="text"
                            name="material"
                            id="material"
                            class="cr-form__input"
                            placeholder="e.g., Teak wood, Sheesham, Fabric sofa"
                            maxlength="200"
                        >
                        <span class="field-error" id="material-error" aria-live="polite"></span>
                    </div>
                </div>

                <!-- Budget Range -->
                <div class="cr-form__group">
                    <label class="cr-form__label" for="budget_range">Budget Range <span aria-hidden="true">*</span></label>
                    <select name="budget_range" id="budget_range" class="cr-form__select">
                        <option value="" disabled selected>— Select Budget —</option>
                        <option value="Under ₹50,000">Under ₹50,000</option>
                        <option value="₹50,000 – ₹1,00,000">₹50,000 – ₹1,00,000</option>
                        <option value="₹1,00,000 – ₹2,00,000">₹1,00,000 – ₹2,00,000</option>
                        <option value="Above ₹2,00,000">Above ₹2,00,000</option>
                    </select>
                    <span class="field-error" id="budget_range-error" aria-live="polite"></span>
                </div>

                <!-- Project Description -->
                <div class="cr-form__group">
                    <label class="cr-form__label" for="description">Project Description <span aria-hidden="true">*</span></label>
                    <textarea
                        name="description"
                        id="description"
                        class="cr-form__textarea"
                        rows="5"
                        placeholder="Describe your vision in detail — style, purpose, special requirements..."
                        maxlength="2000"
                    ></textarea>
                    <span class="cr-form__helper">Minimum 20 characters. The more detail, the better!</span>
                    <span class="field-error" id="description-error" aria-live="polite"></span>
                </div>

                <!-- Reference Image -->
                <div class="cr-form__group">
                    <label class="cr-form__label">Reference Image <span class="optional-tag">(Optional)</span></label>
                    <input
                        type="file"
                        name="reference_image"
                        id="reference_image"
                        class="cr-file-input__native"
                        accept="image/jpeg,image/png,image/webp"
                    >
                    <label for="reference_image" class="cr-file-input__trigger">
                        <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        Choose Image
                    </label>
                    <span class="cr-file-input__name" id="fileName">No file chosen</span>
                    <span class="cr-form__helper">JPG, PNG, or WEBP · Max 5MB</span>
                    <div class="cr-file-preview" id="filePreview">
                        <img id="previewImg" src="" alt="Reference preview">
                    </div>
                    <span class="field-error" id="reference_image-error" aria-live="polite"></span>
                </div>

                <!-- Submit -->
                <button type="submit" class="cr-form__submit" id="submitBtn">
                    <span class="btn-text">SUBMIT REQUEST &nbsp;→</span>
                    <span class="btn-loading">Submitting…</span>
                </button>

            </form>
        </div><!-- /.cr-form-panel -->

    </div><!-- /.cr-form-section__container -->
</section>

<script>
(function () {
    'use strict';

    /* ---- Helpers ---- */
    function el(id) { return document.getElementById(id); }
    function showError(fieldId, msg) {
        var errEl = el(fieldId + '-error');
        var inputEl = el(fieldId);
        if (errEl) errEl.textContent = msg;
        if (inputEl) inputEl.classList.add('has-error');
    }
    function clearError(fieldId) {
        var errEl = el(fieldId + '-error');
        var inputEl = el(fieldId);
        if (errEl) errEl.textContent = '';
        if (inputEl) inputEl.classList.remove('has-error');
    }
    function clearAllErrors() {
        ['name','phone','email','category','budget_range','description','reference_image'].forEach(clearError);
    }

    /* ---- Live clear on input ---- */
    ['name','phone','email','category','budget_range','description'].forEach(function(id) {
        var el_ = el(id);
        if (!el_) return;
        el_.addEventListener('input', function() { clearError(id); });
        el_.addEventListener('change', function() { clearError(id); });
    });

    /* ---- File Input Handling ---- */
    var fileInput  = el('reference_image');
    var fileNameEl = el('fileName');
    var filePreviewEl = el('filePreview');
    var previewImg = el('previewImg');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            clearError('reference_image');
            var file = fileInput.files[0];
            if (!file) {
                fileNameEl.textContent = 'No file chosen';
                filePreviewEl.style.display = 'none';
                return;
            }

            /* Validate type */
            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showError('reference_image', 'Please upload a JPG, PNG, or WEBP image.');
                fileInput.value = '';
                fileNameEl.textContent = 'No file chosen';
                filePreviewEl.style.display = 'none';
                return;
            }

            /* Validate size 5MB */
            if (file.size > 5 * 1024 * 1024) {
                showError('reference_image', 'Image must be under 5MB.');
                fileInput.value = '';
                fileNameEl.textContent = 'No file chosen';
                filePreviewEl.style.display = 'none';
                return;
            }

            fileNameEl.textContent = file.name;

            /* Preview */
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                filePreviewEl.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    /* ---- Validation ---- */
    function validate() {
        var valid = true;

        /* Name */
        var name = el('name').value.trim();
        if (name.length < 2) {
            showError('name', 'Please enter your full name (at least 2 characters).');
            valid = false;
        }

        /* Phone */
        var phone = el('phone').value.replace(/\s+/g, '').replace(/^\+91/, '');
        if (!/^[6-9]\d{9}$/.test(phone)) {
            showError('phone', 'Enter a valid 10-digit Indian mobile number.');
            valid = false;
        }

        /* Email — optional but validate if filled */
        var email = el('email').value.trim();
        if (email.length > 0) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('email', 'Please enter a valid email address.');
                valid = false;
            }
        }

        /* Category */
        var category = el('category').value;
        if (!category) {
            showError('category', 'Please select a furniture category.');
            valid = false;
        }

        /* Budget */
        var budget = el('budget_range').value;
        if (!budget) {
            showError('budget_range', 'Please select a budget range.');
            valid = false;
        }

        /* Description */
        var desc = el('description').value.trim();
        if (desc.length < 20) {
            showError('description', 'Please describe your project in at least 20 characters.');
            valid = false;
        }

        /* File — revalidate if present */
        if (fileInput && fileInput.files[0]) {
            var file = fileInput.files[0];
            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showError('reference_image', 'Please upload a JPG, PNG, or WEBP image.');
                valid = false;
            } else if (file.size > 5 * 1024 * 1024) {
                showError('reference_image', 'Image must be under 5MB.');
                valid = false;
            }
        }

        return valid;
    }

    /* ---- Show result messages ---- */
    function showSuccess(msg) {
        var box = el('ajaxSuccess');
        var txt = el('ajaxSuccessText');
        if (txt) txt.textContent = msg;
        if (box) box.classList.remove('is-hidden');
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function showAjaxError(msg) {
        var box = el('ajaxError');
        var txt = el('ajaxErrorText');
        if (txt) txt.textContent = msg;
        if (box) box.classList.remove('is-hidden');
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function hideMessages() {
        var s = el('ajaxSuccess'); if (s) s.classList.add('is-hidden');
        var e = el('ajaxError'); if (e) e.classList.add('is-hidden');
    }

    /* ---- Form Submit ---- */
    var form = el('customRequestForm');
    var submitBtn = el('submitBtn');

    if (form) {
        form.addEventListener('submit', function (evt) {
            evt.preventDefault();
            clearAllErrors();
            hideMessages();

            if (!validate()) {
                /* Scroll to first error */
                var firstErr = form.querySelector('.field-error:not(:empty)');
                if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            /* Loading state */
            submitBtn.disabled = true;
            submitBtn.classList.add('is-loading');

            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('is-loading');

                if (data.success) {
                    var msg = data.message || 'Your custom request has been submitted! We\'ll contact you within 24 hours.';
                    if (data.reference_id) {
                        msg = 'Your request has been submitted! Reference ID: #' + data.reference_id + '. We will contact you within 24 hours.';
                    }
                    showSuccess(msg);
                    form.reset();
                    fileNameEl.textContent = 'No file chosen';
                    filePreviewEl.style.display = 'none';
                } else {
                    showAjaxError(data.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(function (err) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('is-loading');
                showAjaxError('A network error occurred. Please check your connection and try again.');
                console.error('Request error:', err);
            });
        });
    }

})();
</script>

<?php include 'includes/footer.php'; ?>
