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
