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
