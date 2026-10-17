/**
 * Prime Recruitments — interactions & motion.
 * GSAP + ScrollTrigger + SplitText + Lenis (all bundled in assets/js/vendor).
 */
(function () {
	'use strict';

	var d = document;
	var root = d.documentElement;
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
	var hasGSAP = !!(window.gsap && window.ScrollTrigger);
	var $ = function (s, ctx) { return (ctx || d).querySelector(s); };
	var $$ = function (s, ctx) { return Array.prototype.slice.call((ctx || d).querySelectorAll(s)); };
	var lenis = null;

	/* ------------------------------------------------------------------
	 * Always-on UI (works without GSAP)
	 * ---------------------------------------------------------------- */

	// Mobile menu
	(function () {
		var btn = $('.burger');
		var menu = $('#mobile-menu');
		if (!btn || !menu) return;
		$$('.mobile-menu__list li', menu).forEach(function (li, i) { li.style.setProperty('--i', i); });
		function set(open) {
			root.classList.toggle('menu-open', open);
			btn.setAttribute('aria-expanded', String(open));
			btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
			if (open) { menu.hidden = false; } else { setTimeout(function () { if (!root.classList.contains('menu-open')) menu.hidden = true; }, 800); }
			if (lenis) { open ? lenis.stop() : lenis.start(); }
			d.body.style.overflow = open ? 'hidden' : '';
		}
		btn.addEventListener('click', function () { set(!root.classList.contains('menu-open')); });
		$$('a', menu).forEach(function (a) { a.addEventListener('click', function () { set(false); }); });
		d.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
	})();

	// Enquiry popup: centred dialog opened by any #enquiry link or [data-modal].
	(function () {
		var modal = d.getElementById('prime-modal');
		if (!modal) return;
		var title = $('[data-modal-title]', modal), head = $('[data-modal-headline]', modal), kick = $('[data-modal-kicker]', modal);
		var copy = {
			employer: ['Tell us who you need', 'The right people, in your inbox fast.', '// Hiring made simple'],
			candidate: ['Find your next role', 'Your next career move starts here.', '// Always free for job seekers'],
			gifting: ['Plan your corporate gifts', 'Gifts that make every relationship memorable.', '// Corporate gifting']
		};
		var lastFocus = null;
		function setPreset(p) {
			p = copy[p] ? p : 'employer';
			if (title) title.textContent = copy[p][0];
			if (head) head.textContent = copy[p][1];
			if (kick) kick.textContent = copy[p][2];
			$$('input[name="type"], input[name="enquiry_type"]', modal).forEach(function (r) {
				if (r.value === p && !r.checked) { r.checked = true; r.dispatchEvent(new Event('change', { bubbles: true })); }
			});
		}
		function focusables() {
			return $$('a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled]), textarea, select', modal).filter(function (el) { return el.offsetParent !== null; });
		}
		function open(preset) {
			lastFocus = d.activeElement;
			setPreset(preset);
			modal.hidden = false;
			requestAnimationFrame(function () { modal.classList.add('is-open'); });
			d.body.classList.add('modal-open');
			if (lenis) lenis.stop();
			setTimeout(function () {
				var f = $$('.pmodal__main input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"]), .pmodal__main textarea', modal).filter(function (el) {
					return !el.closest('.hp-field') && el.offsetParent !== null && el.tabIndex !== -1;
				})[0];
				if (f && window.innerWidth > 760) f.focus({ preventScroll: true });
			}, 380);
		}
		function close() {
			modal.classList.remove('is-open');
			d.body.classList.remove('modal-open');
			if (lenis) lenis.start();
			setTimeout(function () { modal.hidden = true; }, 420);
			if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
		}
		d.addEventListener('click', function (e) {
			var t = e.target.closest('[data-modal="enquiry"], a[href$="#enquiry"]');
			if (!t || modal.contains(t) || editMode) return;
			e.preventDefault();
			var href = t.getAttribute('href') || '';
			var pageShell = $('.cta-form .prime-form-shell');
			var preset = t.getAttribute('data-preset') || (/job-seekers|\/jobs/.test(href) ? 'candidate' : '') || (pageShell && pageShell.getAttribute('data-preset')) || 'employer';
			open(preset);
		});
		modal.addEventListener('click', function (e) { if (e.target.closest('[data-close]')) close(); });
		d.addEventListener('keydown', function (e) {
			if (!modal.classList.contains('is-open')) return;
			if (e.key === 'Escape') { close(); return; }
			if (e.key === 'Tab') {
				var f = focusables(); if (!f.length) return;
				var first = f[0], last = f[f.length - 1];
				if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
		window.primeOpenEnquiry = open;
	})();

	// Header state, progress bar, back-to-top
	(function () {
		var header = $('#site-header');
		var bar = $('.scroll-progress i');
		var top = $('.to-top');
		var last = 0;
		var ticking = false;
		function update() {
			var y = window.scrollY;
			var max = d.documentElement.scrollHeight - window.innerHeight;
			var p = max > 0 ? Math.min(1, y / max) : 0;
			if (header) {
				header.classList.toggle('is-scrolled', y > 20);
				var menuOpen = root.classList.contains('menu-open');
				header.classList.toggle('is-hidden', !menuOpen && y > 160 && y > last + 2);
				if (y < last - 2) header.classList.remove('is-hidden');
			}
			if (bar) bar.style.transform = 'scaleX(' + p + ')';
			if (top) { top.classList.toggle('is-visible', y > 600); top.style.setProperty('--p', p); }
			last = y;
			ticking = false;
		}
		window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
		update();
		if (top) top.addEventListener('click', function () { lenis ? lenis.scrollTo(0, { duration: 1.6 }) : window.scrollTo({ top: 0, behavior: 'smooth' }); });
	})();

	// Interactive widgets. Scoped so Elementor can re-initialise a single
	// widget after re-rendering it in the editor.
	function once(el) {
		if (el.__prime) return false;
		el.__prime = true;
		return true;
	}
	function initWidgets(scope) {
		// Enquiry form: employer / candidate switch
		$$('.prime-form', scope).forEach(function (form) {
			if (!once(form)) return;
			function sync() {
				var c = form.querySelector('input[name="type"]:checked');
				form.setAttribute('data-type', c ? c.value : 'employer');
			}
			$$('input[name="type"]', form).forEach(function (r) { r.addEventListener('change', sync); });
			sync();
		});

		// Spotlight glow follows the cursor
		if (finePointer) {
			$$('[data-spotlight]', scope).forEach(function (el) {
				if (!once(el)) return;
				el.addEventListener('pointermove', function (e) {
					var r = el.getBoundingClientRect();
					el.style.setProperty('--x', (e.clientX - r.left) + 'px');
					el.style.setProperty('--y', (e.clientY - r.top) + 'px');
				});
			});
		}

		// Job filters
		$$('[data-jobs]', scope).forEach(function (wrap) {
			if (!once(wrap)) return;
			var tabs = $$('.filter-tabs button', wrap);
			var pill = $('.filter-tabs__pill', wrap);
			var cards = $$('.job-card', wrap);
			function movePill(btn) {
				if (!pill || !btn) return;
				pill.style.width = btn.offsetWidth + 'px';
				pill.style.transform = 'translateX(' + btn.offsetLeft + 'px)';
			}
			tabs.forEach(function (btn) {
				btn.addEventListener('click', function () {
					tabs.forEach(function (t) { t.classList.toggle('is-active', t === btn); });
					movePill(btn);
					var f = btn.getAttribute('data-filter');
					var shown = [];
					cards.forEach(function (c) {
						var match = f === 'all' || c.getAttribute('data-cat') === f;
						c.classList.toggle('is-hidden', !match);
						if (match) shown.push(c);
					});
					if (hasGSAP && !reduce) {
						gsap.fromTo(shown, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.7, ease: 'expo.out', stagger: 0.06, overwrite: true });
						ScrollTrigger.refresh();
					}
				});
			});
			var init = function () { movePill($('.filter-tabs button.is-active', wrap)); };
			init();
			window.addEventListener('resize', init);
			if (d.fonts && d.fonts.ready) d.fonts.ready.then(init);
		});

		// Testimonial slider (autoplay, progress, swipe)
		$$('[data-slider]', scope).forEach(function (wrap) {
			if (!once(wrap)) return;
			var slides = $$('[data-slide]', wrap);
			var dots = $$('[data-goto]', wrap);
			if (slides.length < 2) return;
			var i = 0, t0 = 0, raf = null, paused = false, elapsed = 0, DUR = 6500;
			function show(n) {
				slides[i].classList.remove('is-active');
				dots.forEach(function (b, k) { b.style.setProperty('--prog', k < n ? 1 : 0); });
				i = (n + slides.length) % slides.length;
				slides[i].classList.add('is-active');
				dots.forEach(function (b, k) { b.querySelector('i').style.setProperty('--prog', k < i ? 1 : 0); });
				elapsed = 0; t0 = performance.now();
				if (hasGSAP && !reduce) {
					gsap.fromTo(slides[i].querySelector('blockquote'), { y: 40, opacity: 0 }, { y: 0, opacity: 1, duration: 1, ease: 'expo.out' });
					gsap.fromTo(slides[i].querySelector('figcaption'), { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 1, delay: 0.15, ease: 'expo.out' });
				}
			}
			function tick(now) {
				if (!paused) {
					elapsed += now - t0;
					var p = Math.min(1, elapsed / DUR);
					var bar = dots[i] && dots[i].querySelector('i');
					if (bar) bar.style.setProperty('--prog', p);
					if (p >= 1) show(i + 1);
				}
				t0 = now;
				raf = requestAnimationFrame(tick);
			}
			var next = $('[data-next]', wrap), prev = $('[data-prev]', wrap);
			if (next) next.addEventListener('click', function () { show(i + 1); });
			if (prev) prev.addEventListener('click', function () { show(i - 1); });
			dots.forEach(function (b) { b.addEventListener('click', function () { show(+b.getAttribute('data-goto')); }); });
			wrap.addEventListener('pointerenter', function () { paused = true; });
			wrap.addEventListener('pointerleave', function () { paused = false; });
			var sx = null;
			wrap.addEventListener('pointerdown', function (e) { sx = e.clientX; });
			wrap.addEventListener('pointerup', function (e) {
				if (sx === null) return;
				var dx = e.clientX - sx; sx = null;
				if (Math.abs(dx) > 50) show(dx < 0 ? i + 1 : i - 1);
			});
			if (!reduce) { t0 = performance.now(); raf = requestAnimationFrame(tick); }
		});
	}
	initWidgets(d);

	// Elementor: initialise widgets as they render (editor and frontend).
	var editMode = d.body.classList.contains('elementor-editor-active') || /[?&]elementor-preview=/.test(location.search);
	if (window.jQuery) {
		jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) editMode = true;
			elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				var el = $scope && $scope[0];
				if (!el) return;
				initWidgets(el);
				if (editMode) showStatic(el);
			});
		});
	}

	// Final, fully visible state for every animated element in a scope.
	function showStatic(scope) {
		$$('[data-count]', scope).forEach(function (el) { el.textContent = el.getAttribute('data-count'); });
		$$('.stat', scope).forEach(function (el) { el.classList.add('is-in'); });
		$$('.process__step, .timeline__step', scope).forEach(function (el) { el.classList.add('is-active'); });
	}

	/* ------------------------------------------------------------------
	 * No GSAP, reduced motion or the Elementor editor: show everything statically.
	 * ---------------------------------------------------------------- */
	if (!hasGSAP || reduce || editMode) {
		root.classList.remove('js');
		root.classList.add('anim-ready');
		showStatic(d);
		return;
	}

	gsap.registerPlugin(ScrollTrigger);
	if (window.SplitText) gsap.registerPlugin(SplitText);
	gsap.defaults({ ease: 'expo.out', duration: 1.1 });

	/* ------------------------------------------------------------------
	 * Smooth scroll (Lenis) synced with ScrollTrigger
	 * ---------------------------------------------------------------- */
	if (window.Lenis) {
		lenis = new Lenis({ lerp: 0.1, smoothWheel: true, wheelMultiplier: 1 });
		lenis.on('scroll', ScrollTrigger.update);
		gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
		gsap.ticker.lagSmoothing(0);
		d.addEventListener('click', function (e) {
			if (e.defaultPrevented) return;
			var a = e.target.closest('a[href*="#"]');
			if (!a) return;
			var url = new URL(a.href, location.href);
			if (url.pathname !== location.pathname || !url.hash || url.hash === '#') return;
			var target = d.getElementById(decodeURIComponent(url.hash.slice(1)));
			if (!target) return;
			e.preventDefault();
			lenis.scrollTo(target, { offset: -90, duration: 1.4 });
			history.replaceState(null, '', url.hash);
		});
	}

	/* ------------------------------------------------------------------
	 * Preloader (first visit per session only)
	 * ---------------------------------------------------------------- */
	function runPreloader(done) {
		var pre = $('.preloader');
		var seen = false;
		try { seen = sessionStorage.getItem('prime-loaded') === '1'; sessionStorage.setItem('prime-loaded', '1'); } catch (err) { /* storage blocked */ }
		if (!pre || seen) {
			if (pre) { pre.style.transition = 'none'; pre.classList.add('is-done'); }
			done();
			return;
		}
		// The animation is pure CSS (logo + ring draw); keep it brief: at least
		// ~0.75s since navigation start, then fade out and start the page motion.
		var t0 = (window.performance && performance.timing && performance.timing.navigationStart) || Date.now();
		var wait = Math.max(0, 750 - (Date.now() - t0));
		setTimeout(function () {
			pre.classList.add('is-done');
			setTimeout(done, 120);
		}, wait);
	}

	/* ------------------------------------------------------------------
	 * Scroll animations
	 * ---------------------------------------------------------------- */
	function initAnimations() {
		// Split headings: words rise out of masked lines.
		if (window.SplitText) {
			$$('[data-split]').forEach(function (el) {
				SplitText.create(el, {
					type: 'words,lines',
					mask: 'lines',
					linesClass: 'split-line',
					wordsClass: 'split-word',
					autoSplit: true,
					onSplit: function (self) {
						return gsap.from(self.words, {
							yPercent: 115,
							rotate: 4,
							duration: 1.25,
							stagger: 0.055,
							scrollTrigger: { trigger: el, start: 'top 92%', once: true }
						});
					}
				});
			});
		}
		root.classList.add('anim-ready');

		$$('[data-anim="fade-up"]').forEach(function (el) {
			gsap.fromTo(el, { opacity: 0, y: 40 }, { opacity: 1, y: 0, delay: el.closest('.hero, .banner') ? 0.35 : 0, scrollTrigger: { trigger: el, start: 'top 90%', once: true } });
		});
		$$('[data-anim="rise"]').forEach(function (el) {
			gsap.fromTo(el, { opacity: 0, y: 90, scale: 0.94, rotateX: 10, transformPerspective: 1200 }, { opacity: 1, y: 0, scale: 1, rotateX: 0, duration: 1.6, delay: el.closest('.hero, .banner') ? 0.45 : 0, scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
		});
		$$('[data-anim="stagger"]').forEach(function (el) {
			gsap.fromTo(el.children, { opacity: 0, y: 36 }, { opacity: 1, y: 0, stagger: 0.08, delay: el.closest('.hero, .banner') ? 0.5 : 0, scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
		});

		hero();
		banner();
		kinetic();
		statement();
		hscroll();
		counters();
		processLine();
		reel();
		bento();
		geo();
		timeline();
		wordmark();
		pointerFX();

		if (d.fonts && d.fonts.ready) d.fonts.ready.then(function () { ScrollTrigger.refresh(); });
		window.addEventListener('load', function () { ScrollTrigger.refresh(); });
	}

	/* Hero: particles, rotator, console typing, parallax */
	function hero() {
		var heroEl = $('[data-hero]');
		if (!heroEl) return;

		// Particle constellation
		var canvas = $('.hero__canvas', heroEl);
		if (canvas && canvas.getContext && window.innerWidth > 760) {
			var ctx = canvas.getContext('2d');
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			var pts = [], W = 0, H = 0, mouse = { x: -9999, y: -9999 }, running = true;
			var resize = function () {
				W = canvas.offsetWidth; H = canvas.offsetHeight;
				canvas.width = W * dpr; canvas.height = H * dpr;
				ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
				var n = Math.min(95, Math.floor(W / 15));
				pts = [];
				for (var k = 0; k < n; k++) pts.push({ x: Math.random() * W, y: Math.random() * H, vx: (Math.random() - 0.5) * 0.3, vy: (Math.random() - 0.5) * 0.3, r: Math.random() * 1.4 + 0.5 });
			};
			resize();
			window.addEventListener('resize', resize);
			heroEl.addEventListener('pointermove', function (e) { var r = canvas.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; });
			heroEl.addEventListener('pointerleave', function () { mouse.x = mouse.y = -9999; });
			new IntersectionObserver(function (en) { running = en[0].isIntersecting; }).observe(heroEl);
			var draw = function () {
				if (running) {
					ctx.clearRect(0, 0, W, H);
					for (var a = 0; a < pts.length; a++) {
						var p = pts[a];
						p.x += p.vx; p.y += p.vy;
						if (p.x < 0 || p.x > W) p.vx *= -1;
						if (p.y < 0 || p.y > H) p.vy *= -1;
						var mdx = mouse.x - p.x, mdy = mouse.y - p.y, md = Math.sqrt(mdx * mdx + mdy * mdy);
						if (md < 160) { p.x += mdx * 0.006; p.y += mdy * 0.006; }
						for (var b = a + 1; b < pts.length; b++) {
							var q = pts[b], dx = p.x - q.x, dy = p.y - q.y, dist = dx * dx + dy * dy;
							if (dist < 17000) {
								ctx.strokeStyle = 'rgba(107,141,255,' + (0.22 * (1 - dist / 17000)) + ')';
								ctx.lineWidth = 0.7;
								ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
							}
						}
						ctx.fillStyle = md < 160 ? 'rgba(56,214,245,0.95)' : 'rgba(170,190,255,0.6)';
						ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill();
					}
				}
				requestAnimationFrame(draw);
			};
			draw();
		}

		// Spotlight
		heroEl.addEventListener('pointermove', function (e) {
			var r = heroEl.getBoundingClientRect();
			heroEl.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
			heroEl.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
		});

		// Word rotator
		var rot = $('[data-rotator]', heroEl);
		if (rot) {
			var words = rot.getAttribute('data-rotator').split(',');
			var em = rot.querySelector('em');
			var idx = 0;
			setInterval(function () {
				idx = (idx + 1) % words.length;
				gsap.timeline()
					.to(em, { yPercent: -100, opacity: 0, rotateX: 60, duration: 0.45, ease: 'power3.in' })
					.add(function () { em.textContent = words[idx]; })
					.fromTo(em, { yPercent: 100, opacity: 0, rotateX: -60 }, { yPercent: 0, opacity: 1, rotateX: 0, duration: 0.8, ease: 'expo.out' });
			}, 2600);
		}

		// Console: type code line by line, then fill match bars
		var code = $('[data-typer]', heroEl);
		if (code) {
			var lines = code.innerHTML.split('\n');
			code.innerHTML = lines.map(function (l) { return '<span class="code-line" style="display:block">' + (l || ' ') + '</span>'; }).join('');
			var lineEls = $$('.code-line', code);
			var caret = d.createElement('span');
			caret.className = 'typer-caret';
			gsap.set(lineEls, { clipPath: 'inset(0 100% 0 0)' });
			var tl = gsap.timeline({ delay: 0.5 });
			lineEls.forEach(function (el) {
				tl.to(el, { clipPath: 'inset(0 0% 0 0)', duration: Math.max(0.18, el.textContent.length * 0.012), ease: 'steps(' + Math.max(4, el.textContent.length) + ')' }, '+=0.03');
			});
			tl.add(function () { lineEls[lineEls.length - 1].appendChild(caret); });
			tl.from('.match', { opacity: 0, x: -20, stagger: 0.12, duration: 0.7 }, '-=0.2');
			tl.from('.match__bar i', { scaleX: 0, stagger: 0.12, duration: 1.2 }, '<');
		}

		// Scroll-out parallax
		gsap.to('.hero__layout', { y: -80, opacity: 0.2, ease: 'none', scrollTrigger: { trigger: heroEl, start: 'top top', end: 'bottom top', scrub: true } });
		gsap.to('.hero__bg', { yPercent: 25, ease: 'none', scrollTrigger: { trigger: heroEl, start: 'top top', end: 'bottom top', scrub: true } });
	}

	/* Inner-page banner parallax */
	function banner() {
		var b = $('.banner');
		if (!b) return;
		gsap.to($('.banner__bg', b), { yPercent: 22, ease: 'none', scrollTrigger: { trigger: b, start: 'top top', end: 'bottom top', scrub: true } });
		var layout = $('.banner__layout, .banner__inner', b);
		if (layout) gsap.to(layout, { y: -60, opacity: 0.3, ease: 'none', scrollTrigger: { trigger: b, start: 'center center', end: 'bottom top', scrub: true } });
	}

	/* Marquees speed up and skew with scroll velocity */
	function kinetic() {
		var tracks = $$('[data-marquee] .kinetic__track');
		if (!tracks.length) return;
		var anims = tracks.map(function (t) { return t.getAnimations ? t.getAnimations()[0] : null; });
		var skewTo = tracks.map(function (t) { return gsap.quickTo(t, 'skewX', { duration: 0.6, ease: 'power3' }); });
		var rate = 1;
		ScrollTrigger.create({
			onUpdate: function (self) {
				var v = self.getVelocity();
				rate = 1 + Math.min(4, Math.abs(v) / 600);
				var skew = gsap.utils.clamp(-8, 8, v / -250);
				skewTo.forEach(function (fn) { fn(skew); });
			}
		});
		gsap.ticker.add(function () {
			rate += (1 - rate) * 0.05;
			anims.forEach(function (a) { if (a) a.playbackRate = rate; });
			if (Math.abs(rate - 1) < 0.02) skewTo.forEach(function (fn) { fn(0); });
		});
	}

	/* Statement words light up as you scroll */
	function statement() {
		$$('[data-scrub-text]').forEach(function (el) {
			var words = el.textContent.trim().split(/\s+/);
			el.innerHTML = words.map(function (w) { return '<span class="w">' + w.replace(/[<>&]/g, '') + '</span>'; }).join(' ');
			gsap.to($$('.w', el), { opacity: 1, stagger: 0.1, ease: 'none', scrollTrigger: { trigger: el, start: 'top 80%', end: 'bottom 40%', scrub: 0.6 } });
		});
	}

	/* Services: pinned horizontal scroll on desktop */
	function hscroll() {
		var section = $('[data-hscroll]');
		if (!section) return;
		var track = $('[data-htrack]', section);
		var viewport = $('.hscroll__viewport', section);
		var counter = $('[data-hcount]', section);
		var bar = $('[data-hbar]', section);
		var cards = $$('.svc-card', track);
		var mm = gsap.matchMedia();
		mm.add('(min-width: 1081px)', function () {
			var distance = function () {
				var pl = parseFloat(getComputedStyle(viewport).paddingLeft) || 0;
				return Math.max(0, track.scrollWidth + pl - window.innerWidth);
			};
			var tween = gsap.to(track, {
				x: function () { return -distance(); },
				ease: 'none',
				scrollTrigger: {
					trigger: section,
					start: 'top top',
					end: function () { return '+=' + distance(); },
					pin: true,
					scrub: 1,
					invalidateOnRefresh: true,
					anticipatePin: 1,
					onUpdate: function (self) {
						var total = cards.length - 1; // last card is the CTA
						var n = Math.min(total, Math.floor(self.progress * total) + 1);
						if (counter) counter.textContent = (n < 10 ? '0' : '') + n;
						if (bar) bar.style.transform = 'scaleX(' + Math.max(0.11, self.progress) + ')';
					}
				}
			});
			cards.forEach(function (card) {
				gsap.fromTo(card, { opacity: 0.35, scale: 0.9, rotate: 2 }, { opacity: 1, scale: 1, rotate: 0, ease: 'none', scrollTrigger: { trigger: card, containerAnimation: tween, start: 'left 100%', end: 'left 65%', scrub: true } });
			});
		});
	}

	/* Count-up numbers + ring fill */
	function counters() {
		$$('[data-count]').forEach(function (el) {
			var target = parseFloat(el.getAttribute('data-count'));
			var stat = el.closest('.stat');
			if (isNaN(target)) { el.textContent = el.getAttribute('data-count'); return; }
			var o = { v: 0 };
			el.textContent = '0';
			ScrollTrigger.create({
				trigger: el,
				start: 'top 88%',
				once: true,
				onEnter: function () {
					if (stat) stat.classList.add('is-in');
					gsap.to(o, { v: target, duration: 2, ease: 'power3.out', onUpdate: function () { el.textContent = Math.round(o.v); } });
				}
			});
		});
	}

	/* Process: vertical line draws, steps light up */
	function processLine() {
		var wrap = $('[data-process]');
		if (!wrap) return;
		var line = $('[data-process-line]', wrap);
		var steps = $$('[data-step]', wrap);
		gsap.to(line, { scaleY: 1, ease: 'none', scrollTrigger: { trigger: '.process__steps', start: 'top 65%', end: 'bottom 65%', scrub: true } });
		steps.forEach(function (s) {
			ScrollTrigger.create({ trigger: s, start: 'top 65%', onEnter: function () { s.classList.add('is-active'); }, onLeaveBack: function () { s.classList.remove('is-active'); } });
		});
	}

	/* Industries: floating image follows the cursor */
	function reel() {
		var wrap = $('[data-reveal-list]');
		if (!wrap || !finePointer) return;
		var preview = $('.reel__preview', wrap);
		var img = $('img', preview);
		var rows = $$('.reel__row', wrap);
		var preloaded = false;
		wrap.addEventListener('pointerenter', function () {
			if (preloaded) return;
			preloaded = true;
			rows.forEach(function (r) { var i = new Image(); i.src = r.getAttribute('data-img'); });
		});
		var xTo = gsap.quickTo(preview, 'x', { duration: 0.6, ease: 'power3' });
		var yTo = gsap.quickTo(preview, 'y', { duration: 0.6, ease: 'power3' });
		var rTo = gsap.quickTo(preview, 'rotate', { duration: 0.8, ease: 'power3' });
		var lastX = 0;
		gsap.set(preview, { xPercent: -50, yPercent: -50, scale: 0.6 });
		rows.forEach(function (r) {
			r.addEventListener('pointerenter', function () {
				img.src = r.getAttribute('data-img');
				gsap.to(preview, { opacity: 1, scale: 1, duration: 0.5, overwrite: 'auto' });
				gsap.fromTo(img, { scale: 1.25 }, { scale: 1, duration: 0.9 });
			});
		});
		$('.reel', wrap).addEventListener('pointerleave', function () { gsap.to(preview, { opacity: 0, scale: 0.6, duration: 0.4, overwrite: 'auto' }); });
		wrap.addEventListener('pointermove', function (e) {
			xTo(e.clientX + 40); yTo(e.clientY);
			rTo(gsap.utils.clamp(-12, 12, (e.clientX - lastX) * 0.6));
			lastX = e.clientX;
		});
	}

	/* Bento funnel bars */
	function bento() {
		var rows = $$('.funnel__row');
		if (!rows.length) return;
		gsap.from(rows, { scaleX: 0, opacity: 0, stagger: 0.15, duration: 1.4, scrollTrigger: { trigger: rows[0], start: 'top 85%', once: true } });
	}

	/* Geo map: cities pop in, lines draw */
	function geo() {
		var cities = $$('.city');
		if (!cities.length) return;
		gsap.from(cities, { scale: 0, opacity: 0, stagger: 0.07, duration: 0.8, ease: 'back.out(2)', scrollTrigger: { trigger: '.geo__map', start: 'top 75%', once: true } });
		gsap.from('.geo__lines path', { opacity: 0, stagger: 0.05, duration: 1, scrollTrigger: { trigger: '.geo__map', start: 'top 70%', once: true } });
	}

	/* Inner pages: horizontal timeline */
	function timeline() {
		$$('[data-timeline]').forEach(function (t) {
			var line = $('.timeline__line i', t);
			var steps = $$('.timeline__step', t);
			var tl = gsap.timeline({ scrollTrigger: { trigger: t, start: 'top 75%', once: true } });
			tl.from(steps, { opacity: 0, y: 40, stagger: 0.12, duration: 1 });
			if (line) tl.to(line, { scaleX: 1, duration: 1.8, ease: 'power2.inOut' }, 0.2);
			steps.forEach(function (s, k) { tl.add(function () { s.classList.add('is-active'); }, 0.5 + k * 0.4); });
		});
	}

	/* Footer wordmark rises */
	function wordmark() {
		var w = $('.footer-wordmark span');
		if (!w) return;
		gsap.from(w, { yPercent: 45, ease: 'none', scrollTrigger: { trigger: '.footer-wordmark', start: 'top bottom', end: 'bottom bottom', scrub: true } });
	}

	/* Cursor, magnetic buttons, 3D tilt */
	function pointerFX() {
		if (!finePointer) return;

		var cursor = $('.cursor');
		if (cursor) {
			var dot = $('.cursor__dot', cursor), ring = $('.cursor__ring', cursor);
			var dx = gsap.quickTo(dot, 'x', { duration: 0.12 }), dy = gsap.quickTo(dot, 'y', { duration: 0.12 });
			var rx = gsap.quickTo(ring, 'x', { duration: 0.5, ease: 'power3' }), ry = gsap.quickTo(ring, 'y', { duration: 0.5, ease: 'power3' });
			gsap.set([dot, ring], { xPercent: -50, yPercent: -50 });
			window.addEventListener('pointermove', function (e) { dx(e.clientX); dy(e.clientY); rx(e.clientX); ry(e.clientY); cursor.classList.remove('is-hidden'); });
			d.addEventListener('pointerleave', function () { cursor.classList.add('is-hidden'); });
			d.addEventListener('pointerover', function (e) { cursor.classList.toggle('is-hover', !!e.target.closest('a, button, summary, label, [data-spotlight]')); });
		}

		$$('[data-magnetic]').forEach(function (el) {
			var xTo = gsap.quickTo(el, 'x', { duration: 0.6, ease: 'power3' });
			var yTo = gsap.quickTo(el, 'y', { duration: 0.6, ease: 'power3' });
			el.addEventListener('pointermove', function (e) {
				var r = el.getBoundingClientRect();
				xTo((e.clientX - r.left - r.width / 2) * 0.25);
				yTo((e.clientY - r.top - r.height / 2) * 0.35);
			});
			el.addEventListener('pointerleave', function () { xTo(0); yTo(0); });
		});

		$$('[data-tilt]').forEach(function (el) {
			var area = el.closest('section') || el;
			var rxTo = gsap.quickTo(el, 'rotateX', { duration: 0.9, ease: 'power3' });
			var ryTo = gsap.quickTo(el, 'rotateY', { duration: 0.9, ease: 'power3' });
			gsap.set(el, { transformPerspective: 1400 });
			area.addEventListener('pointermove', function (e) {
				var r = area.getBoundingClientRect();
				ryTo(((e.clientX - r.left) / r.width - 0.5) * 10);
				rxTo(((e.clientY - r.top) / r.height - 0.5) * -8);
			});
			area.addEventListener('pointerleave', function () { rxTo(0); ryTo(0); });
		});
	}

	runPreloader(initAnimations);
})();
