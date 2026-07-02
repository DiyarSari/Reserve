(function () {
    'use strict';

    function getCurrentLang() {
        var stored = null;
        try {
            stored = window.localStorage.getItem('reserveai_lang');
        } catch (error) {
            stored = null;
        }
        if (stored === 'tr' || stored === 'en') {
            return stored;
        }
        return document.documentElement.lang === 'tr' ? 'tr' : 'en';
    }

    function sanitizeTurkishPhone(value) {
        var cleaned = String(value || '').replace(/[^\d+]/g, '');
        if (!cleaned) {
            return '';
        }

        if (cleaned.indexOf('+') > 0) {
            cleaned = cleaned.replace(/\+/g, '');
        }
        if (cleaned.indexOf('+') === 0) {
            cleaned = '+' + cleaned.slice(1).replace(/\+/g, '');
            return cleaned.slice(0, 13);
        }

        return cleaned.slice(0, 12);
    }

    function isValidTurkishPhone(value) {
        var compact = sanitizeTurkishPhone(value);
        return /^(\+90|0)?[1-9][0-9]{9}$/.test(compact);
    }

    function getPhoneValidationMessage(required) {
        var isTurkish = getCurrentLang() === 'tr';
        if (required) {
            return isTurkish ? 'Telefon alanı zorunludur.' : 'Phone is required.';
        }
        return isTurkish
            ? 'Türkiye telefon formatı: 5XXXXXXXXX, 05XXXXXXXXX veya +905XXXXXXXXX.'
            : 'Türkiye phone format: 5XXXXXXXXX, 05XXXXXXXXX or +905XXXXXXXXX.';
    }

    function getLocalizedValidationMessage(field) {
        var isTurkish = getCurrentLang() === 'tr';
        var value = String(field.value || '');
        var requiredLabel = isTurkish ? 'Bu alan zorunludur.' : 'This field is required.';

        if (field.validity.valueMissing) {
            return requiredLabel;
        }
        if (field.validity.typeMismatch) {
            if (field.type === 'email') {
                return isTurkish ? 'Geçerli bir e-posta adresi girin.' : 'Enter a valid email address.';
            }
            if (field.type === 'url') {
                return isTurkish ? 'Geçerli bir URL girin.' : 'Enter a valid URL.';
            }
            return isTurkish ? 'Geçerli bir değer girin.' : 'Enter a valid value.';
        }
        if (field.validity.patternMismatch) {
            return isTurkish ? 'Girdi formatı uygun değil.' : 'Input format is invalid.';
        }
        if (field.validity.tooShort) {
            var min = parseInt(field.getAttribute('minlength') || '0', 10);
            return isTurkish
                ? 'En az ' + String(min) + ' karakter girin.'
                : 'Enter at least ' + String(min) + ' characters.';
        }
        if (field.validity.tooLong) {
            var max = parseInt(field.getAttribute('maxlength') || '0', 10);
            return isTurkish
                ? 'En fazla ' + String(max) + ' karakter girin.'
                : 'Enter no more than ' + String(max) + ' characters.';
        }
        if (field.validity.rangeUnderflow) {
            var minValue = field.getAttribute('min') || '';
            return isTurkish
                ? 'Değer ' + minValue + ' veya daha büyük olmalı.'
                : 'Value must be ' + minValue + ' or greater.';
        }
        if (field.validity.rangeOverflow) {
            var maxValue = field.getAttribute('max') || '';
            return isTurkish
                ? 'Değer ' + maxValue + ' veya daha küçük olmalı.'
                : 'Value must be ' + maxValue + ' or smaller.';
        }
        if (field.validity.stepMismatch) {
            return isTurkish ? 'Geçerli bir adım değeri girin.' : 'Enter a valid step value.';
        }
        if (field.validity.badInput) {
            return isTurkish ? 'Geçersiz giriş.' : 'Invalid input.';
        }

        if (value.trim() === '' && field.required) {
            return requiredLabel;
        }

        return '';
    }

    function applyLocalizedValidationMessage(field) {
        if (!field || !field.willValidate) {
            return;
        }

        if (field.dataset.unsafeContent === '1') {
            return;
        }

        if (field.validity.customError && field.dataset.validationSource && field.dataset.validationSource !== 'i18n') {
            return;
        }

        field.setCustomValidity('');
        if (field.checkValidity()) {
            field.dataset.i18nValidation = '0';
            if (field.dataset.validationSource === 'i18n') {
                field.dataset.validationSource = '';
            }
            return;
        }

        var localized = getLocalizedValidationMessage(field);
        if (localized !== '') {
            field.setCustomValidity(localized);
            field.dataset.i18nValidation = '1';
            field.dataset.validationSource = 'i18n';
            return;
        }

        field.dataset.i18nValidation = '0';
        if (field.dataset.validationSource === 'i18n') {
            field.dataset.validationSource = '';
        }
    }

    function getUnsafeInputMessage() {
        return getCurrentLang() === 'tr'
            ? 'Güvenlik nedeniyle bu alana kod veya zararlı ifade girilemez.'
            : 'Code-like or unsafe input is not allowed in this field.';
    }

    function hasUnsafeInputPattern(value) {
        var text = String(value || '');
        if (!text) {
            return false;
        }

        return /<\s*\/?\s*(script|iframe|object|embed|style|link|meta)\b|javascript\s*:|data\s*:\s*text\/html|on[a-z]+\s*=|<\?php|union\s+select|drop\s+table|truncate\s+table|(?:'|")\s*(or|and)\s*(?:'|")?\s*\d?/i.test(text);
    }

    function validateUnsafeInputs(form) {
        form.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]), textarea').forEach(function (input) {
            var value = String(input.value || '').trim();
            if (value === '') {
                if (input.dataset.unsafeContent === '1') {
                    input.setCustomValidity('');
                }
                input.dataset.unsafeContent = '0';
                if (input.dataset.validationSource === 'unsafe') {
                    input.dataset.validationSource = '';
                }
                return;
            }

            if (hasUnsafeInputPattern(value)) {
                input.dataset.unsafeContent = '1';
                input.dataset.i18nValidation = '0';
                input.dataset.validationSource = 'unsafe';
                input.setCustomValidity(getUnsafeInputMessage());
                return;
            }

            if (input.dataset.unsafeContent === '1') {
                input.setCustomValidity('');
            }
            input.dataset.unsafeContent = '0';
            if (input.dataset.validationSource === 'unsafe') {
                input.dataset.validationSource = '';
            }
        });
    }

    function trimTextInputs(form) {
        form.querySelectorAll('input[type="text"], input[type="email"], textarea').forEach(function (input) {
            input.value = input.value.trim();
        });
    }

    function validateDateInputs(form) {
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() + 60);

        form.querySelectorAll('input[type="date"]').forEach(function (input) {
            if (input.dataset.unsafeContent === '1') {
                return;
            }
            if (!input.value) {
                input.dataset.validationSource = '';
                input.setCustomValidity('');
                return;
            }

            var selected = new Date(input.value + 'T00:00:00');
            if (selected < today) {
                input.dataset.validationSource = 'reservation_date';
                input.setCustomValidity('Geçmiş tarih seçilemez.');
                return;
            }

            input.dataset.validationSource = selected > maxDate ? 'reservation_date' : '';
            input.setCustomValidity(selected > maxDate ? 'En fazla 60 gün sonrasına rezervasyon yapabilirsiniz.' : '');
        });
    }

    function validateReservationTimeInputs(form) {
        form.querySelectorAll('input[type="time"][name="reservation_time"]').forEach(function (input) {
            if (input.dataset.unsafeContent === '1') {
                return;
            }
            if (!input.value) {
                input.dataset.validationSource = '';
                input.setCustomValidity('');
                return;
            }

            var parts = input.value.split(':');
            var minutes = (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
            input.dataset.validationSource = minutes % 30 === 0 ? '' : 'reservation_time';
            input.setCustomValidity(minutes % 30 === 0 ? '' : 'Saat 30 dakikalık aralıklarla seçilmelidir.');
        });
    }

    function validateTurkishPhoneInputs(form) {
        form.querySelectorAll('input[data-validate-phone-tr="1"]').forEach(function (input) {
            if (input.dataset.unsafeContent === '1') {
                return;
            }
            input.value = sanitizeTurkishPhone(input.value);

            if (!input.value) {
                input.dataset.validationSource = input.required ? 'phone' : '';
                input.setCustomValidity(input.required ? getPhoneValidationMessage(true) : '');
                return;
            }

            input.dataset.validationSource = isValidTurkishPhone(input.value) ? '' : 'phone';
            input.setCustomValidity(isValidTurkishPhone(input.value) ? '' : getPhoneValidationMessage(false));
        });
    }

    function getDirectChildByClass(element, className) {
        if (!element || !element.children) {
            return null;
        }
        for (var index = 0; index < element.children.length; index++) {
            var child = element.children[index];
            if (child.classList && child.classList.contains(className)) {
                return child;
            }
        }
        return null;
    }

    function findInvalidFeedbackElement(field) {
        var node = field.parentElement;
        while (node && node !== field.form) {
            var directFeedback = getDirectChildByClass(node, 'invalid-feedback');
            if (directFeedback) {
                if (!directFeedback.dataset.defaultMessage) {
                    directFeedback.dataset.defaultMessage = directFeedback.textContent.trim();
                }
                return directFeedback;
            }
            node = node.parentElement;
        }
        return null;
    }

    function ensureInvalidFeedbackElement(field) {
        var existing = findInvalidFeedbackElement(field);
        if (existing) {
            return existing;
        }

        var feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.dataset.dynamic = '1';
        feedback.dataset.defaultMessage = '';

        var anchor = field;
        if (field.parentElement && field.parentElement.classList.contains('auth-input-group')) {
            anchor = field.parentElement;
        }
        anchor.insertAdjacentElement('afterend', feedback);
        return feedback;
    }

    function setInvalidFeedbackVisibility(field, showInvalid) {
        var feedback = showInvalid ? ensureInvalidFeedbackElement(field) : findInvalidFeedbackElement(field);
        if (!feedback) {
            return;
        }

        if (showInvalid) {
            var message = field.validationMessage || feedback.dataset.defaultMessage || '';
            feedback.textContent = message;
            feedback.style.display = 'block';
            return;
        }

        feedback.style.display = '';
    }

    function updateFieldValidationState(field, forceDisplay) {
        if (!field || field.disabled || field.type === 'hidden' || !field.willValidate) {
            return;
        }

        applyLocalizedValidationMessage(field);

        var value = String(field.value || '').trim();
        var shouldEvaluate = forceDisplay || field.required || value !== '';
        if (!shouldEvaluate) {
            field.classList.remove('is-invalid');
            field.classList.remove('is-valid');
            setInvalidFeedbackVisibility(field, false);
            return;
        }

        var valid = field.checkValidity();

        field.classList.toggle('is-invalid', !valid);
        field.classList.toggle('is-valid', valid && value !== '');
        setInvalidFeedbackVisibility(field, !valid && shouldEvaluate);
    }

    function refreshFormValidation(form, forceDisplay) {
        validateUnsafeInputs(form);
        validateDateInputs(form);
        validateReservationTimeInputs(form);
        validateTurkishPhoneInputs(form);

        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            updateFieldValidationState(field, forceDisplay);
        });
    }

    function attachRealtimeValidation(form) {
        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.addEventListener('input', function () {
                refreshFormValidation(form, false);
            });

            field.addEventListener('change', function () {
                refreshFormValidation(form, false);
            });

            field.addEventListener('blur', function () {
                refreshFormValidation(form, false);
            });
        });
    }

    function attachBootstrapValidation() {
        document.querySelectorAll('.needs-validation').forEach(function (form) {
            attachRealtimeValidation(form);
            refreshFormValidation(form, true);
            form.classList.add('was-validated');

            form.addEventListener('submit', function (event) {
                trimTextInputs(form);
                refreshFormValidation(form, true);

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });
        });
    }

    function attachTurkishPhoneInputGuard() {
        document.querySelectorAll('input[data-validate-phone-tr="1"]').forEach(function (input) {
            input.addEventListener('input', function () {
                if (input.dataset.unsafeContent === '1') {
                    return;
                }
                input.value = sanitizeTurkishPhone(input.value);
                if (!input.value) {
                    input.dataset.validationSource = '';
                    input.setCustomValidity('');
                    return;
                }
                input.dataset.validationSource = isValidTurkishPhone(input.value) ? '' : 'phone';
                input.setCustomValidity(isValidTurkishPhone(input.value) ? '' : getPhoneValidationMessage(false));
            });

            input.addEventListener('blur', function () {
                if (input.dataset.unsafeContent === '1') {
                    return;
                }
                if (!input.value) {
                    input.dataset.validationSource = input.required ? 'phone' : '';
                    input.setCustomValidity(input.required ? getPhoneValidationMessage(true) : '');
                    return;
                }
                input.dataset.validationSource = isValidTurkishPhone(input.value) ? '' : 'phone';
                input.setCustomValidity(isValidTurkishPhone(input.value) ? '' : getPhoneValidationMessage(false));
            });
        });
    }

    function autoCloseAlerts() {
        document.querySelectorAll('.js-auto-alert').forEach(function (alert) {
            window.setTimeout(function () {
                if (window.bootstrap && bootstrap.Alert) {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }
            }, 5000);
        });
    }

    function attachTableFilter() {
        document.querySelectorAll('.js-filter-table').forEach(function (table) {
            var input = document.createElement('input');
            input.className = 'form-control mb-2';
            input.placeholder = 'Tabloda ara';
            input.setAttribute('data-i18n-placeholder', 'tables.search');
            table.parentNode.insertBefore(input, table);

            input.addEventListener('input', function () {
                var term = input.value.toLowerCase();
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    row.hidden = row.textContent.toLowerCase().indexOf(term) === -1;
                });
            });
        });
    }

    function getLocaleBasePath() {
        return window.location.pathname.indexOf('/views/') !== -1 ? '../locales/' : 'locales/';
    }

    function interpolate(template, vars) {
        return template.replace(/\{\{(\w+)\}\}/g, function (match, key) {
            return Object.prototype.hasOwnProperty.call(vars, key) ? vars[key] : match;
        });
    }

    function parseVars(element) {
        var raw = element.getAttribute('data-i18n-vars');
        if (!raw) {
            return {};
        }

        try {
            return JSON.parse(raw);
        } catch (error) {
            return {};
        }
    }

    function applyTranslations(dictionary, lang) {
        document.documentElement.lang = lang;

        document.querySelectorAll('[data-i18n]').forEach(function (element) {
            var key = element.getAttribute('data-i18n');
            if (!dictionary[key]) {
                return;
            }

            element.textContent = interpolate(dictionary[key], parseVars(element));
        });

        document.querySelectorAll('[data-i18n-placeholder]').forEach(function (element) {
            var key = element.getAttribute('data-i18n-placeholder');
            if (dictionary[key]) {
                element.setAttribute('placeholder', interpolate(dictionary[key], parseVars(element)));
            }
        });
    }

    function setLanguage(lang, persistManualPreference) {
        var safeLang = lang === 'tr' ? 'tr' : 'en';
        var select = document.getElementById('languageSelect');
        var isManual = persistManualPreference === true;

        try {
            window.localStorage.setItem('reserveai_lang', safeLang);
            if (isManual) {
                window.localStorage.setItem('reserveai_lang_manual', '1');
            }
        } catch (error) {
        }
        document.cookie = 'reserveai_lang=' + safeLang + '; path=/; max-age=31536000; SameSite=Lax';
        if (isManual) {
            document.cookie = 'reserveai_lang_manual=1; path=/; max-age=31536000; SameSite=Lax';
        }

        if (select) {
            select.value = safeLang;
        }

        return fetch(getLocaleBasePath() + safeLang + '.json', { cache: 'no-store' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Locale file could not be loaded.');
                }
                return response.json();
            })
            .then(function (dictionary) {
                applyTranslations(dictionary, safeLang);
            })
            .catch(function () {
                if (safeLang !== 'en') {
                    return setLanguage('en', false);
                }
                return null;
            });
    }

    function getPersistedLanguage() {
        var stored = null;
        var manual = null;
        try {
            stored = window.localStorage.getItem('reserveai_lang');
            manual = window.localStorage.getItem('reserveai_lang_manual');
        } catch (error) {
            stored = null;
            manual = null;
        }

        if (manual !== '1') {
            var manualCookieMatch = document.cookie.match(/(?:^|;\s*)reserveai_lang_manual=(1)/);
            manual = manualCookieMatch ? '1' : null;
        }

        if (manual === '1' && (stored === 'tr' || stored === 'en')) {
            return stored;
        }

        var match = document.cookie.match(/(?:^|;\s*)reserveai_lang=(tr|en)/);
        if (manual === '1' && match) {
            return match[1];
        }

        return document.documentElement.lang === 'tr' ? 'tr' : 'en';
    }

    function attachLanguageSwitcher() {
        var select = document.getElementById('languageSelect');
        var languageButtons = document.querySelectorAll('[data-language-choice]');
        var lang = getPersistedLanguage();

        if (select) {
            select.value = lang;
            select.addEventListener('change', function () {
                setLanguage(select.value, true);
            });
        }

        languageButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                setLanguage(button.getAttribute('data-language-choice'), true);
            });
        });

        setLanguage(lang, false);
    }

    function updateHashActiveNav() {
        var aboutLink = document.querySelector('.js-about-link');
        if (!aboutLink) {
            return;
        }

        var isFooterHash = window.location.hash === '#siteFooter';
        if (isFooterHash) {
            document.querySelectorAll('.navbar .nav-link.active').forEach(function (link) {
                link.classList.remove('active');
            });
            aboutLink.classList.add('active');
        }
    }

    function getCookieValue(name) {
        var match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setCookieValue(name, value, maxAgeSeconds) {
        document.cookie = name + '=' + encodeURIComponent(value)
            + '; path=/; max-age=' + String(maxAgeSeconds)
            + '; SameSite=Lax';
    }

    function deleteCookieValue(name) {
        document.cookie = name + '=; path=/; max-age=0; SameSite=Lax';
    }

    function attachCookieConsent() {
        var banner = document.getElementById('cookieConsentBanner');
        if (!banner) {
            return;
        }

        var consentKey = 'reserve_cookie_consent_v2';
        var legacyConsentKey = 'reserve_cookie_consent';
        var acceptButton = document.getElementById('cookieAcceptBtn');
        var essentialButton = document.getElementById('cookieEssentialBtn');
        var stored = '';

        try {
            stored = window.localStorage.getItem(consentKey) || '';
            window.localStorage.removeItem(legacyConsentKey);
        } catch (error) {
            stored = '';
        }
        if (!stored) {
            stored = getCookieValue(consentKey);
        }
        deleteCookieValue(legacyConsentKey);

        if (stored === 'accepted' || stored === 'essential') {
            return;
        }

        banner.classList.add('is-visible');

        function saveConsent(value) {
            try {
                window.localStorage.setItem(consentKey, value);
            } catch (error) {
            }
            setCookieValue(consentKey, value, 31536000);
            deleteCookieValue(legacyConsentKey);
            banner.classList.remove('is-visible');
        }

        if (acceptButton) {
            acceptButton.addEventListener('click', function () {
                saveConsent('accepted');
            });
        }
        if (essentialButton) {
            essentialButton.addEventListener('click', function () {
                saveConsent('essential');
            });
        }
    }

    function attachRestaurantFilterGuard() {
        var matrixElement = document.getElementById('restaurantFilterMatrix');
        var citySelect = document.querySelector('.js-restaurant-city-filter');
        var districtSelect = document.querySelector('.js-restaurant-district-filter');
        var cuisineSelect = document.querySelector('.js-restaurant-cuisine-filter');
        var guestSelect = document.querySelector('.js-restaurant-guest-filter');

        if (!matrixElement || !citySelect || !districtSelect || !cuisineSelect || !guestSelect) {
            return;
        }

        var matrix = [];
        try {
            matrix = JSON.parse(matrixElement.textContent || '[]');
        } catch (error) {
            return;
        }

        function hasMatch(city, district, cuisine, guests) {
            var guestCount = parseInt(guests || '0', 10);
            return matrix.some(function (item) {
                return (!city || item.city === city)
                    && (!district || item.district === district)
                    && (!cuisine || item.cuisine === cuisine)
                    && (!guestCount || item.maxGuests >= guestCount);
            });
        }

        function updateOptions() {
            var selectedCity = citySelect.value;
            var selectedDistrict = districtSelect.value;
            var selectedCuisine = cuisineSelect.value;
            var selectedGuests = guestSelect.value;

            Array.prototype.forEach.call(citySelect.options, function (option) {
                if (!option.value) {
                    option.disabled = false;
                    return;
                }
                option.disabled = !hasMatch(option.value, selectedDistrict, selectedCuisine, selectedGuests);
            });

            Array.prototype.forEach.call(districtSelect.options, function (option) {
                if (!option.value) {
                    option.disabled = false;
                    option.hidden = false;
                    return;
                }
                var hasDistrictMatch = hasMatch(selectedCity, option.value, selectedCuisine, selectedGuests);
                option.disabled = !hasDistrictMatch;
                option.hidden = Boolean(selectedCity) && !hasDistrictMatch;
            });

            Array.prototype.forEach.call(cuisineSelect.options, function (option) {
                if (!option.value) {
                    option.disabled = false;
                    return;
                }
                option.disabled = !hasMatch(selectedCity, selectedDistrict, option.value, selectedGuests);
            });

            Array.prototype.forEach.call(guestSelect.options, function (option) {
                if (!option.value) {
                    option.disabled = false;
                    return;
                }
                option.disabled = !hasMatch(selectedCity, selectedDistrict, selectedCuisine, option.value);
            });

            if (citySelect.selectedOptions[0] && citySelect.selectedOptions[0].disabled) {
                citySelect.value = '';
            }
            if (districtSelect.selectedOptions[0] && districtSelect.selectedOptions[0].disabled) {
                districtSelect.value = '';
            }
            if (cuisineSelect.selectedOptions[0] && cuisineSelect.selectedOptions[0].disabled) {
                cuisineSelect.value = '';
            }
            if (guestSelect.selectedOptions[0] && guestSelect.selectedOptions[0].disabled && guestSelect.options.length > 0) {
                guestSelect.value = guestSelect.options[0].value;
            }
        }

        [citySelect, districtSelect, cuisineSelect, guestSelect].forEach(function (select) {
            select.addEventListener('change', updateOptions);
        });

        updateOptions();
    }

    document.addEventListener('DOMContentLoaded', function () {
        attachBootstrapValidation();
        attachTurkishPhoneInputGuard();
        autoCloseAlerts();
        attachTableFilter();
        attachLanguageSwitcher();
        attachCookieConsent();
        updateHashActiveNav();
        attachRestaurantFilterGuard();
        window.addEventListener('hashchange', updateHashActiveNav);
    });
})();
