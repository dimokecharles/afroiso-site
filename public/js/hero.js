/* Animated network behind the homepage heading.
   Falls back to the still image when the visitor has asked for reduced motion. */
(function () {
    var hero = document.querySelector('.hero');
    var canvas = document.querySelector('.hero__canvas');
    if (!hero || !canvas || !canvas.getContext) return;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var ctx = canvas.getContext('2d');
    var COLORS = { pale: '213,220,236', orange: '244,114,22', teal: '64,200,210' };
    var LINK = 190;          // longest line between two points, in pixels
    var nodes = [];
    var width = 0, height = 0, ratio = 1;
    var running = false, visible = true, last = 0;

    function makeNode() {
        var k = Math.random();
        var speed = 5 + Math.random() * 9;            // pixels per second
        var angle = Math.random() * Math.PI * 2;
        return {
            x: Math.pow(Math.random(), 0.6) * width,   // more points towards the right
            y: Math.random() * height,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            color: k > 0.84 ? COLORS.orange : (k > 0.68 ? COLORS.teal : COLORS.pale),
            accent: k > 0.68,
            r: k > 0.84 ? 4.5 : (k > 0.68 ? 3.8 : 2.4),
            phase: Math.random() * Math.PI * 2
        };
    }

    function resize() {
        var box = hero.getBoundingClientRect();
        ratio = Math.min(window.devicePixelRatio || 1, 2);
        width = Math.max(1, Math.round(box.width));
        height = Math.max(1, Math.round(box.height));
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

        var wanted = Math.max(18, Math.min(110, Math.round(width * height / 14000)));
        while (nodes.length < wanted) nodes.push(makeNode());
        nodes.length = wanted;
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].x > width) nodes[i].x = Math.random() * width;
            if (nodes[i].y > height) nodes[i].y = Math.random() * height;
        }
    }

    function draw(time) {
        ctx.clearRect(0, 0, width, height);

        // soft warm and cool glows, as in the still image
        var warm = ctx.createRadialGradient(width * 0.85, height * 0.3, 0, width * 0.85, height * 0.3, width * 0.45);
        warm.addColorStop(0, 'rgba(244,114,22,0.20)');
        warm.addColorStop(1, 'rgba(244,114,22,0)');
        ctx.fillStyle = warm;
        ctx.fillRect(0, 0, width, height);
        var cool = ctx.createRadialGradient(width * 0.55, height * 0.95, 0, width * 0.55, height * 0.95, width * 0.4);
        cool.addColorStop(0, 'rgba(64,200,210,0.14)');
        cool.addColorStop(1, 'rgba(64,200,210,0)');
        ctx.fillStyle = cool;
        ctx.fillRect(0, 0, width, height);

        var i, j, a, b, dx, dy, d;
        ctx.lineWidth = 1;
        for (i = 0; i < nodes.length; i++) {
            a = nodes[i];
            for (j = i + 1; j < nodes.length; j++) {
                b = nodes[j];
                dx = a.x - b.x; dy = a.y - b.y;
                if (dx > LINK || dx < -LINK || dy > LINK || dy < -LINK) continue;
                d = Math.sqrt(dx * dx + dy * dy);
                if (d < LINK) {
                    ctx.strokeStyle = 'rgba(' + COLORS.pale + ',' + (0.42 * (1 - d / LINK)).toFixed(3) + ')';
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                }
            }
        }
        for (i = 0; i < nodes.length; i++) {
            a = nodes[i];
            if (a.accent) {
                var pulse = 0.5 + 0.5 * Math.sin(time / 900 + a.phase);
                ctx.strokeStyle = 'rgba(' + a.color + ',' + (0.15 + 0.35 * pulse).toFixed(3) + ')';
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.arc(a.x, a.y, a.r * (2 + pulse), 0, Math.PI * 2);
                ctx.stroke();
                ctx.lineWidth = 1;
            }
            ctx.fillStyle = 'rgba(' + a.color + ',0.9)';
            ctx.beginPath();
            ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function step(time) {
        if (!running) return;
        var dt = Math.min(0.05, (time - last) / 1000 || 0);
        last = time;
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            n.x += n.vx * dt;
            n.y += n.vy * dt;
            if (n.x < -20) n.x = width + 20; else if (n.x > width + 20) n.x = -20;
            if (n.y < -20) n.y = height + 20; else if (n.y > height + 20) n.y = -20;
        }
        draw(time);
        window.requestAnimationFrame(step);
    }

    function update() {
        var shouldRun = visible && !document.hidden;
        if (shouldRun && !running) {
            running = true;
            last = performance.now();
            window.requestAnimationFrame(step);
        } else if (!shouldRun) {
            running = false;
        }
    }

    resize();
    hero.classList.add('hero--animated');
    window.addEventListener('resize', resize);
    document.addEventListener('visibilitychange', update);
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            visible = entries[0].isIntersecting;
            update();
        }).observe(hero);
    }
    update();
})();
