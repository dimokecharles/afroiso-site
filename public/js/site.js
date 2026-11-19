/* Mobile menu: opens and closes the main navigation on small screens. */
(function () {
    var header = document.querySelector('.header');
    var button = document.querySelector('.nav-toggle');
    var nav = document.getElementById('site-nav');
    if (!header || !button || !nav) return;

    function setOpen(open) {
        header.classList.toggle('is-open', open);
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        button.querySelector('i').className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    }

    button.addEventListener('click', function () {
        setOpen(!header.classList.contains('is-open'));
    });
    nav.addEventListener('click', function (event) {
        if (event.target.closest('a')) setOpen(false);
    });
    document.addEventListener('click', function (event) {
        if (header.classList.contains('is-open') && !header.contains(event.target)) setOpen(false);
    });
    header.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') { setOpen(false); button.focus(); }
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 860) setOpen(false);
    });
})();

/* WhatsApp chat box: the quick replies and the typed message open WhatsApp with the text filled in. */
(function () {
    var chat = document.querySelector('.chat');
    if (!chat) return;
    var launcher = chat.querySelector('.chat__launcher');
    var panel = chat.querySelector('.chat__panel');
    var form = chat.querySelector('.chat__form');
    var input = chat.querySelector('.chat__input');
    var number = chat.getAttribute('data-number');

    function setOpen(open) {
        chat.classList.toggle('is-open', open);
        launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
        launcher.setAttribute('aria-label', open ? 'Close WhatsApp chat' : 'Chat with us on WhatsApp');
        panel.hidden = !open;
        if (open && window.innerWidth > 860) input.focus();
    }

    launcher.addEventListener('click', function () {
        setOpen(!chat.classList.contains('is-open'));
    });
    chat.querySelector('.chat__close').addEventListener('click', function () {
        setOpen(false);
        launcher.focus();
    });
    chat.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') { setOpen(false); launcher.focus(); }
    });
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var text = input.value.trim();
        if (!text) { input.focus(); return; }
        window.open('https://wa.me/' + number + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
        input.value = '';
    });
})();

/* Countdown to the next intake: updates the days, hours, minutes and seconds every second. */
(function () {
    var boxes = document.querySelectorAll('[data-countdown]');
    if (!boxes.length) return;

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function tick() {
        var now = Date.now();
        for (var i = 0; i < boxes.length; i++) {
            var box = boxes[i];
            var left = Math.floor((new Date(box.getAttribute('data-countdown')).getTime() - now) / 1000);
            if (isNaN(left)) continue;
            if (left <= 0) {
                box.innerHTML = '<div class="intake__label intake__label--today">Classes have started</div>';
                box.removeAttribute('data-countdown');
                continue;
            }
            var parts = {
                days: Math.floor(left / 86400),
                hours: pad(Math.floor(left % 86400 / 3600)),
                minutes: pad(Math.floor(left % 3600 / 60)),
                seconds: pad(left % 60)
            };
            for (var unit in parts) {
                var el = box.querySelector('[data-unit="' + unit + '"]');
                if (el) el.textContent = parts[unit];
            }
        }
    }

    tick();
    window.setInterval(tick, 1000);
})();

/* Scroll-in animation: elements marked data-reveal get the class "is-in" when they come into view. */
(function () {
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));
    if (!items.length) return;
    document.documentElement.classList.add('js');

    function check() {
        var limit = (window.innerHeight || document.documentElement.clientHeight) * 0.85;
        items = items.filter(function (item) {
            var box = item.getBoundingClientRect();
            if (box.top < limit && box.bottom > 0) {
                item.classList.add('is-in');
                return false;
            }
            return true;
        });
        if (!items.length) {
            window.removeEventListener('scroll', check);
            window.removeEventListener('resize', check);
        }
    }

    window.addEventListener('scroll', check, { passive: true });
    window.addEventListener('resize', check);
    window.addEventListener('load', check);
    check();
})();

/* Language switcher. English is the site's own text. For the other languages the choice is stored in the
   "googtrans" cookie and Google Translate (loaded only then) translates the page in the browser. */
(function () {
    var buttons = document.querySelectorAll('[data-lang]');
    if (!buttons.length) return;

    function current() {
        var match = document.cookie.match(/(?:^|;\s*)googtrans=\/en\/([^;]+)/);
        return match ? decodeURIComponent(match[1]) : 'en';
    }

    function store(lang) {
        var host = location.hostname;
        var base = '.' + host.split('.').slice(-2).join('.');
        var expired = '; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        // Clear every copy first (Google also writes one on the parent domain), then set the new choice.
        ['', '; domain=' + host, '; domain=' + base].forEach(function (domain) {
            document.cookie = 'googtrans=' + expired + '; path=/' + domain;
        });
        if (lang !== 'en') document.cookie = 'googtrans=/en/' + lang + '; path=/';
    }

    var lang = current();
    var toggle = document.querySelector('.lang__toggle');
    var menu = document.getElementById('lang-menu');
    var label = document.querySelector('[data-lang-current]');

    function openMenu(open) {
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () { openMenu(menu.hidden); });
        document.addEventListener('click', function (event) {
            if (!menu.hidden && !event.target.closest('.lang')) openMenu(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !menu.hidden) { openMenu(false); toggle.focus(); }
        });
    }

    for (var i = 0; i < buttons.length; i++) {
        var active = buttons[i].getAttribute('data-lang') === lang;
        buttons[i].classList.toggle('is-active', active);
        buttons[i].setAttribute('aria-pressed', active ? 'true' : 'false');
        if (active && label && lang !== 'en') label.textContent = buttons[i].textContent.replace(/\s*\(.*\)$/, '');
        buttons[i].addEventListener('click', function () {
            var chosen = this.getAttribute('data-lang');
            if (chosen === current()) return;
            store(chosen);
            location.reload();
        });
    }

    if (lang === 'en') return;

    window.googleTranslateElementInit = function () {
        new window.google.translate.TranslateElement(
            { pageLanguage: 'en', includedLanguages: 'fr,zh-CN,pt,sn', autoDisplay: false },
            'google_translate_element'
        );
    };
    var script = document.createElement('script');
    script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    script.async = true;
    document.head.appendChild(script);

    // If Google has not started translating by itself, pick the language in its hidden menu.
    var tries = 0;
    var timer = window.setInterval(function () {
        tries++;
        var done = /\btranslated-/.test(document.documentElement.className);
        var combo = document.querySelector('.goog-te-combo');
        if (!done && combo && combo.options.length > 1 && tries > 4) {
            combo.value = lang;
            combo.dispatchEvent(new Event('change'));
        }
        if (done || tries > 40) window.clearInterval(timer);
    }, 500);
})();
