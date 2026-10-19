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

	// Desktop menu: a liquid pill glides to the hovered item.
	(function () {
		var nav = $('.main-nav');
		var pill = nav && $('.nav-pill', nav);
		if (!nav || !pill) return;
		var items = $$('.main-nav__list > li', nav);
		function move(li) {
			var a = li.querySelector('a');
			if (!a) return;
			var nr = nav.getBoundingClientRect(), r = a.getBoundingClientRect();
			pill.style.width = r.width + 'px';
			pill.style.setProperty('--x', (r.left - nr.left) + 'px');
			items.forEach(function (x) { x.classList.toggle('is-hot', x === li); });
			nav.classList.add('is-hovering');
		}
		items.forEach(function (li) {
			li.addEventListener('pointerenter', function () { move(li); });
			li.addEventListener('focusin', function () { move(li); });
		});
		nav.addEventListener('pointerleave', function () { nav.classList.remove('is-hovering'); items.forEach(function (x) { x.classList.remove('is-hot'); }); });
	})();

	// Enquiry popup: centred dialog opened by any #enquiry link or [data-modal].
	(function () {
		var modal = d.getElementById('prime-modal');
		if (!modal) return;
		var title = $('[data-modal-title]', modal), head = $('[data-modal-headline]', modal), kick = $('[data-modal-kicker]', modal);
		var copy = {
			employer: ['Tell us who you need', 'The right people, in your inbox fast.', 'Hiring made simple'],
			candidate: ['Find your next role', 'Your next career move starts here.', 'Always free for job seekers'],
			gifting: ['Plan your corporate gifts', 'Gifts that make every relationship memorable.', 'Corporate gifting']
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
		var bar = null;
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
				header.classList.toggle('is-hidden', !menuOpen && y > 400 && y > last + 4);
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

		// Job filters
		$$('[data-jobs]', scope).forEach(function (wrap) {
			if (!once(wrap)) return;
			var tabs = $$('.filter-tabs button', wrap);
			var cards = $$('.job-card', wrap);
			tabs.forEach(function (btn) {
				btn.addEventListener('click', function () {
					tabs.forEach(function (t) { t.classList.toggle('is-active', t === btn); t.setAttribute('aria-pressed', String(t === btn)); });
					var f = btn.getAttribute('data-filter');
					var shown = [];
					cards.forEach(function (c) {
						var match = f === 'all' || c.getAttribute('data-cat') === f;
						c.classList.toggle('is-hidden', !match);
						if (match) shown.push(c);
					});
					if (hasGSAP && !reduce) {
						gsap.fromTo(shown, { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out', stagger: 0.05, overwrite: true });
						ScrollTrigger.refresh();
					}
				});
			});
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
					gsap.fromTo(slides[i].querySelector('blockquote'), { y: 24, opacity: 0 }, { y: 0, opacity: 1, duration: 0.9, ease: 'power3.out' });
					gsap.fromTo(slides[i].querySelector('figcaption'), { y: 14, opacity: 0 }, { y: 0, opacity: 1, duration: 0.9, delay: 0.12, ease: 'power3.out' });
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
	gsap.defaults({ ease: 'power3.out', duration: 1 });

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
			lenis.scrollTo(target, { offset: -100, duration: 1.2 });
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
		var wait = Math.max(0, 700 - (Date.now() - t0));
		setTimeout(function () {
			pre.classList.add('is-done');
			setTimeout(done, 120);
		}, wait);
	}

	/* ------------------------------------------------------------------
	 * Scroll animations
	 * ---------------------------------------------------------------- */
	function initAnimations() {
		// Headings: words rise gently out of masked lines.
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
							yPercent: 100,
							duration: 1,
							stagger: 0.04,
							ease: 'power3.out',
							scrollTrigger: { trigger: el, start: 'top 90%', once: true }
						});
					}
				});
			});
		}
		root.classList.add('anim-ready');

		var intro = function (el) { return el.closest('.hero, .banner') ? 0.3 : 0; };
		$$('[data-anim="fade-up"]').forEach(function (el) {
			gsap.fromTo(el, { opacity: 0, y: 28 }, { opacity: 1, y: 0, duration: 0.9, delay: intro(el), scrollTrigger: { trigger: el, start: 'top 90%', once: true } });
		});
		$$('[data-anim="rise"]').forEach(function (el) {
			gsap.fromTo(el, { opacity: 0, y: 50 }, { opacity: 1, y: 0, duration: 1.1, delay: intro(el) + 0.1, scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
		});
		$$('[data-anim="stagger"]').forEach(function (el) {
			gsap.fromTo(el.children, { opacity: 0, y: 26 }, { opacity: 1, y: 0, duration: 0.8, stagger: 0.07, delay: intro(el), scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
		});
		// Image reveal: a curtain wipes away, the photo settles from a slight zoom.
		$$('[data-anim="reveal"]').forEach(function (el) {
			var img = el.querySelector('img');
			var tl = gsap.timeline({ scrollTrigger: { trigger: el, start: 'top 82%', once: true } });
			tl.fromTo(el, { opacity: 0 }, { opacity: 1, duration: 0.01 })
				.fromTo(el.querySelector('.split__img') || el, { clipPath: 'inset(0 0 100% 0)' }, { clipPath: 'inset(0 0 0% 0)', duration: 1.3, ease: 'power4.inOut' })
				.fromTo(img, { scale: 1.18 }, { scale: 1, duration: 1.6, ease: 'power3.out' }, '<')
				.from(el.querySelector('.split__badge'), { opacity: 0, y: 30, duration: 0.8 }, '-=0.6');
		});

		hero();
		media();
		footerMega();
		statement();
		counters();
		processLine();
		reel();
		bento();
		geo();
		timeline();

		if (d.fonts && d.fonts.ready) d.fonts.ready.then(function () { ScrollTrigger.refresh(); });
		window.addEventListener('load', function () { ScrollTrigger.refresh(); });
	}

	/* Hero: talent cards pop in and follow the pointer, rotating word */
	function hero() {
		var heroEl = $('[data-hero]');
		if (!heroEl) return;
		var cards = $$('.tcard--float, .hero__bubble', heroEl);
		if (cards.length) {
			gsap.from(cards, { opacity: 0, scale: 0.6, y: 40, duration: 1, stagger: 0.12, delay: 0.5, ease: 'back.out(1.6)' });
			if (finePointer) {
				var movers = cards.map(function (el, k) {
					var depth = (k % 3 + 1) * 10;
					return { x: gsap.quickTo(el, 'x', { duration: 1.2, ease: 'power3' }), y: gsap.quickTo(el, 'y', { duration: 1.2, ease: 'power3' }), d: depth };
				});
				heroEl.addEventListener('pointermove', function (e) {
					var r = heroEl.getBoundingClientRect();
					var px = (e.clientX - r.left) / r.width - 0.5, py = (e.clientY - r.top) / r.height - 0.5;
					movers.forEach(function (m) { m.x(px * m.d * -2); m.y(py * m.d * -2); });
				});
			}
		}
		gsap.from($$('.hero__stack .tcard', heroEl), { opacity: 0, y: 30, stagger: 0.15, duration: 0.9, delay: 0.7 });
		var rot = $('[data-rotator]', heroEl);
		if (rot) {
			var words = rot.getAttribute('data-rotator').split(',');
			var em = rot.querySelector('em');
			var mark = rot.querySelector('.rotator__mark');
			var idx = 0;
			if (words.length > 1) {
				setInterval(function () {
					idx = (idx + 1) % words.length;
					gsap.timeline()
						.to(em, { yPercent: -60, opacity: 0, filter: 'blur(6px)', duration: 0.4, ease: 'power2.in' })
						.add(function () { em.textContent = words[idx]; if (mark) { mark.style.animation = 'none'; void mark.offsetWidth; mark.style.animation = ''; } })
						.fromTo(em, { yPercent: 60, opacity: 0, filter: 'blur(6px)' }, { yPercent: 0, opacity: 1, filter: 'blur(0px)', duration: 0.6, ease: 'power3.out' });
				}, 2800);
			}
		}
	}

	/* Footer: giant PRIME letters rise in one by one */
	function footerMega() {
		var letters = $$('.footer-mega__word span');
		if (!letters.length) return;
		gsap.from(letters, { yPercent: 110, rotate: 8, duration: 1.2, stagger: 0.08, ease: 'power4.out', scrollTrigger: { trigger: '.footer-mega', start: 'top 92%', once: true } });
		gsap.from('.footer-mega__sub', { opacity: 0, y: 20, duration: 0.8, delay: 0.5, scrollTrigger: { trigger: '.footer-mega', start: 'top 92%', once: true } });
	}

	/* Banners and photo bands: subtle parallax on the background photo */
	function media() {
		$$('.audience__img').forEach(function (img) {
			var sec = img.closest('section') || img;
			gsap.fromTo(img, { yPercent: -6, scale: 1.12 }, { yPercent: 6, scale: 1.12, ease: 'none', scrollTrigger: { trigger: sec, start: 'top bottom', end: 'bottom top', scrub: true } });
		});
	}

	/* Statement words light up as you scroll */
	function statement() {
		$$('[data-scrub-text]').forEach(function (el) {
			var words = el.textContent.trim().split(/\s+/);
			el.innerHTML = words.map(function (w) { return '<span class="w">' + w.replace(/[<>&]/g, '') + '</span>'; }).join(' ');
			gsap.to($$('.w', el), { opacity: 1, stagger: 0.1, ease: 'none', scrollTrigger: { trigger: el, start: 'top 80%', end: 'bottom 45%', scrub: 0.6 } });
		});
	}

	/* Count-up numbers */
	function counters() {
		$$('[data-count]').forEach(function (el) {
			var target = parseFloat(el.getAttribute('data-count'));
			if (isNaN(target)) { el.textContent = el.getAttribute('data-count'); return; }
			var o = { v: 0 };
			el.textContent = '0';
			ScrollTrigger.create({
				trigger: el,
				start: 'top 90%',
				once: true,
				onEnter: function () {
					gsap.to(o, { v: target, duration: 1.8, ease: 'power2.out', onUpdate: function () { el.textContent = Math.round(o.v); } });
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
		gsap.to(line, { scaleY: 1, ease: 'none', scrollTrigger: { trigger: $('.process__steps', wrap), start: 'top 65%', end: 'bottom 65%', scrub: true } });
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
		var rows = $$('.reel__row', wrap).filter(function (r) { return r.getAttribute('data-img'); });
		if (!rows.length) return;
		var preloaded = false;
		wrap.addEventListener('pointerenter', function () {
			if (preloaded) return;
			preloaded = true;
			rows.forEach(function (r) { var i = new Image(); i.src = r.getAttribute('data-img'); });
		});
		var xTo = gsap.quickTo(preview, 'x', { duration: 0.6, ease: 'power3' });
		var yTo = gsap.quickTo(preview, 'y', { duration: 0.6, ease: 'power3' });
		gsap.set(preview, { xPercent: -50, yPercent: -50, scale: 0.8 });
		rows.forEach(function (r) {
			r.addEventListener('pointerenter', function () {
				img.src = r.getAttribute('data-img');
				gsap.to(preview, { opacity: 1, scale: 1, duration: 0.45, overwrite: 'auto' });
			});
		});
		$('.reel', wrap).addEventListener('pointerleave', function () { gsap.to(preview, { opacity: 0, scale: 0.8, duration: 0.35, overwrite: 'auto' }); });
		wrap.addEventListener('pointermove', function (e) { xTo(e.clientX + 180); yTo(e.clientY); });
	}

	/* Why-us funnel bars */
	function bento() {
		var rows = $$('.funnel__row');
		if (!rows.length) return;
		gsap.from(rows, { scaleX: 0.2, opacity: 0, stagger: 0.12, duration: 1.1, scrollTrigger: { trigger: rows[0], start: 'top 88%', once: true } });
	}

	/* Geo map: cities appear, lines fade in */
	function geo() {
		var cities = $$('.city');
		if (!cities.length) return;
		gsap.from(cities, { scale: 0, opacity: 0, stagger: 0.05, duration: 0.6, ease: 'back.out(1.7)', scrollTrigger: { trigger: '.geo__map', start: 'top 78%', once: true } });
		gsap.from('.geo__lines path', { opacity: 0, stagger: 0.04, duration: 0.8, scrollTrigger: { trigger: '.geo__map', start: 'top 72%', once: true } });
	}

	/* Inner pages: horizontal timeline */
	function timeline() {
		$$('[data-timeline]').forEach(function (t) {
			var line = $('.timeline__line i', t);
			var steps = $$('.timeline__step', t);
			var tl = gsap.timeline({ scrollTrigger: { trigger: t, start: 'top 78%', once: true } });
			tl.from(steps, { opacity: 0, y: 30, stagger: 0.12, duration: 0.8 });
			if (line) tl.to(line, { scaleX: 1, duration: 1.6, ease: 'power2.inOut' }, 0.2);
			steps.forEach(function (s, k) { tl.add(function () { s.classList.add('is-active'); }, 0.4 + k * 0.35); });
		});
	}

	runPreloader(initAnimations);
})();
