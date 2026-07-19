/* =========================================================
   LawyerCasePro — Landing Page Language Switcher (EN <-> BN)
   Every translatable element on the page has a unique id.
   This script simply swaps each element's text between the
   English and Bengali dictionaries below when the toggle
   button is clicked. The chosen language is remembered for
   the visitor's next visit using localStorage.
   ========================================================= */

(function () {

    // ---------------------------------------------------------
    // 1. Translation dictionaries, keyed by element id
    // ---------------------------------------------------------
    const translations = {
        en: {
            "nav-home": "Home",
            "nav-features": "Features",
            "nav-about": "About",
            "nav-contact": "Contact",
            "nav-register": "Register",
            "nav-login": "Login",

            "hero-title": "Manage Cases Smarter with LCMS",
            "hero-subtitle": "Simplify your legal workflow with an all-in-one case management system built for modern lawyers.",
            "hero-cta": "Explore Features",

            "dashboard-features-title": "Dashboard Features",
            "feature-admin-title": "Admin",
            "feature-admin-text": "Admins can add new clients, define case types, and manage court types.",
            "feature-advocate-title": "Advocate",
            "feature-advocate-text": "Advocates can add and manage cases, hearings, and case status.",
            "feature-client-title": "Client",
            "feature-client-text": "Clients can view case details and hearing information in read-only mode.",
            "feature-judge-title": "Judge",
            "feature-judge-text": "Judges can manage case hearings, status updates, and case history.",

            "about-title": "About LCMS",
            "about-text": "The Lawyer Case Management System helps advocates and law firms manage cases easily.",

            "contact-title": "Get in Touch",
            "contact-subtitle": "Have questions, feedback, or need assistance? Send us a message — we'd love to hear from you.",
            "contact-form-title": "Send a Message",
            "label-name": "Full Name",
            "label-email": "Email Address",
            "label-subject": "Subject",
            "label-message": "Message",
            "contact-btn": "Send Message",

            "contact-info-title": "Contact Information",
            "info-address-title": "Address",
            "info-address-text": "Mirpur-10, Dhaka, Bangladesh",
            "info-email-title": "Email",
            "info-email-text": "info@lawyercasepro.com",
            "info-phone-title": "Phone",
            "info-phone-text": "+880 123 456789",

            "footer-brand": "LawyerCasePro",
            "footer-contact": "Contact: info@lawyercasepro.com | +880 123 456789",

            "lang-toggle-label": "বাংলা"
        },

        bn: {
            "nav-home": "হোম",
            "nav-features": "বৈশিষ্ট্য",
            "nav-about": "সম্পর্কে",
            "nav-contact": "যোগাযোগ",
            "nav-register": "নিবন্ধন",
            "nav-login": "লগইন",

            "hero-title": "এলসিএমএস দিয়ে বুদ্ধিদীপ্তভাবে মামলা পরিচালনা করুন",
            "hero-subtitle": "আধুনিক আইনজীবীদের জন্য তৈরি একটি সর্বাঙ্গীণ মামলা ব্যবস্থাপনা সিস্টেম দিয়ে আপনার আইনি কার্যপ্রবাহ সহজ করুন।",
            "hero-cta": "বৈশিষ্ট্যসমূহ দেখুন",

            "dashboard-features-title": "ড্যাশবোর্ড বৈশিষ্ট্য",
            "feature-admin-title": "অ্যাডমিন",
            "feature-admin-text": "অ্যাডমিনরা নতুন ক্লায়েন্ট যোগ করতে, মামলার ধরন নির্ধারণ করতে এবং আদালতের ধরন পরিচালনা করতে পারেন।",
            "feature-advocate-title": "অ্যাডভোকেট",
            "feature-advocate-text": "অ্যাডভোকেটরা মামলা, শুনানি এবং মামলার অবস্থা যোগ ও পরিচালনা করতে পারেন।",
            "feature-client-title": "ক্লায়েন্ট",
            "feature-client-text": "ক্লায়েন্টরা শুধুমাত্র দেখার মোডে মামলার বিবরণ এবং শুনানির তথ্য দেখতে পারেন।",
            "feature-judge-title": "বিচারক",
            "feature-judge-text": "বিচারকরা মামলার শুনানি, অবস্থা হালনাগাদ এবং মামলার ইতিহাস পরিচালনা করতে পারেন।",

            "about-title": "এলসিএমএস সম্পর্কে",
            "about-text": "লইয়ার কেস ম্যানেজমেন্ট সিস্টেম অ্যাডভোকেট এবং ল' ফার্মগুলোকে সহজে মামলা পরিচালনা করতে সাহায্য করে।",

            "contact-title": "যোগাযোগ করুন",
            "contact-subtitle": "প্রশ্ন, মতামত বা সহায়তার প্রয়োজন? আমাদের একটি বার্তা পাঠান — আমরা আপনার কথা শুনতে চাই।",
            "contact-form-title": "একটি বার্তা পাঠান",
            "label-name": "পুরো নাম",
            "label-email": "ইমেইল ঠিকানা",
            "label-subject": "বিষয়",
            "label-message": "বার্তা",
            "contact-btn": "বার্তা পাঠান",

            "contact-info-title": "যোগাযোগের তথ্য",
            "info-address-title": "ঠিকানা",
            "info-address-text": "মিরপুর-১০, ঢাকা, বাংলাদেশ",
            "info-email-title": "ইমেইল",
            "info-email-text": "info@lawyercasepro.com",
            "info-phone-title": "ফোন",
            "info-phone-text": "+৮৮০ ১২৩ ৪৫৬৭৮৯",

            "footer-brand": "LawyerCasePro",
            "footer-contact": "যোগাযোগ: info@lawyercasepro.com | +৮৮০ ১২৩ ৪৫৬৭৮৯",

            "lang-toggle-label": "English"
        }
    };

    // Input placeholders are handled separately since they use data-ph-en / data-ph-bn
    const placeholderFields = ["name", "email", "subject", "message"];

    // ---------------------------------------------------------
    // 2. Apply a given language to the page
    // ---------------------------------------------------------
    function applyLanguage(lang) {
        const dict = translations[lang];
        if (!dict) return;

        // Swap all text content by id
        Object.keys(dict).forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.textContent = dict[id];
        });

        // Swap the mobile toggle label too (it uses a class, not an id)
        document.querySelectorAll(".lang-toggle-label-mobile").forEach(function (el) {
            el.textContent = lang === "en" ? "বাংলা" : "English";
        });

        // Swap placeholders on the contact form
        placeholderFields.forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            const value = lang === "en" ? el.getAttribute("data-ph-en") : el.getAttribute("data-ph-bn");
            if (value) el.setAttribute("placeholder", value);
        });

        // Update the <html lang="..."> attribute for accessibility/SEO
        document.documentElement.setAttribute("lang", lang);

        // Remember the choice for next time
        localStorage.setItem("lcmp_lang", lang);
    }

    // ---------------------------------------------------------
    // 3. Toggle between English and Bengali
    // ---------------------------------------------------------
    function toggleLanguage() {
        const current = localStorage.getItem("lcmp_lang") || "en";
        const next = current === "en" ? "bn" : "en";
        applyLanguage(next);
    }

    // ---------------------------------------------------------
    // 4. Wire up buttons and restore saved language on load
    // ---------------------------------------------------------
    document.addEventListener("DOMContentLoaded", function () {
        const savedLang = localStorage.getItem("lcmp_lang") || "en";
        applyLanguage(savedLang);

        const desktopBtn = document.getElementById("lang-toggle");
        const mobileBtn = document.getElementById("lang-toggle-mobile");

        if (desktopBtn) desktopBtn.addEventListener("click", toggleLanguage);
        if (mobileBtn) mobileBtn.addEventListener("click", toggleLanguage);
    });

})();
