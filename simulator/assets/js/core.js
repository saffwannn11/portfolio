/* Akbar Academy Simulation Lab - core: module registry, router, XP, toasts, palette, effects.
   A simulator is just an object passed to AA.register({...}) - see README.md. */
(function () {
  'use strict';
  const AA = (window.AA = window.AA || {});
  AA.modules = [];
  AA.byId = {};
  AA.current = null;

  AA.$ = (s, r) => (r || document).querySelector(s);
  AA.$$ = (s, r) => Array.from((r || document).querySelectorAll(s));
  AA.esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  AA.pad = (n, l) => String(n).padStart(l || 2, '0');
  AA.money = (n) => Math.round(n).toLocaleString('en-US');
  AA.register = (m) => { AA.modules.push(m); AA.byId[m.id] = m; };

  /* ---------- persistent state ---------- */
  const KEY = 'akbar-sim-v1';
  const state = (AA.state = { xp: 0, done: {}, best: {}, ach: {} });
  try { Object.assign(state, JSON.parse(localStorage.getItem(KEY) || '{}')); } catch (e) { /* storage blocked */ }
  AA.save = () => { try { localStorage.setItem(KEY, JSON.stringify(state)); } catch (e) { /* ignore */ } };

  const need = (l) => 120 * (l - 1) * (l - 1);
  const TITLES = ['Trainee', 'Agent', 'Senior Agent', 'Specialist', 'Consultant', 'Travel Director'];
  AA.levelInfo = () => {
    let l = 1;
    while (state.xp >= need(l + 1)) l++;
    const a = need(l), b = need(l + 1);
    return { level: l, title: TITLES[Math.min(l - 1, TITLES.length - 1)], pct: (state.xp - a) / (b - a), into: state.xp - a, span: b - a };
  };
  AA.addXP = (n, why) => {
    const before = AA.levelInfo().level;
    state.xp += n; AA.save(); AA.toast('+' + n + ' XP', why || '', 'xp'); renderXP();
    const after = AA.levelInfo();
    if (after.level > before) { setTimeout(() => { AA.toast('Level ' + after.level + ' reached', 'You are now: ' + after.title, 'gold'); AA.confetti(); }, 600); }
  };

  const ACH = {
    first_pnr: ['First Blood', 'Completed your first GDS mission'],
    ticketed: ['Ticket Issued', 'Issued a live e-ticket in the GDS lab'],
    clean: ['Clean Sheet', 'Finished a mission with zero errors'],
    speed: ['Speed Demon', 'Finished a mission in under 90 seconds'],
    all_gds: ['Three Systems', 'Completed a mission in Amadeus, Sabre and Galileo'],
    dubai_pro: ['Dubai Insider', 'Scored 90+ on a Dubai itinerary'],
    profit: ['Margin Master', 'Won a Dubai brief with 18%+ margin'],
  };
  AA.ACH = ACH;
  AA.unlock = (id) => {
    if (state.ach[id] || !ACH[id]) return;
    state.ach[id] = Date.now(); AA.save();
    AA.toast('Achievement unlocked', ACH[id][0] + ' - ' + ACH[id][1], 'gold');
    if (AA.current && AA.current.onAchievement) AA.current.onAchievement(id);
  };

  /* ---------- toasts ---------- */
  AA.toast = (title, msg, kind) => {
    const box = AA.$('#toasts'); if (!box) return;
    const t = document.createElement('div');
    t.className = 'toast ' + (kind || '');
    t.innerHTML = '<b>' + AA.esc(title) + '</b>' + (msg ? '<span>' + AA.esc(msg) + '</span>' : '');
    box.appendChild(t);
    requestAnimationFrame(() => t.classList.add('in'));
    setTimeout(() => { t.classList.remove('in'); setTimeout(() => t.remove(), 400); }, 3600);
  };

  /* ---------- animated number ---------- */
  AA.countTo = (el, to, opts) => {
    opts = opts || {};
    const from = parseFloat(el.dataset.v || '0') || 0, dur = opts.dur || 700, t0 = performance.now();
    el.dataset.v = to;
    const fmt = opts.fmt || ((v) => AA.money(v));
    (function step(now) {
      const p = Math.min(1, (now - t0) / dur), e = 1 - Math.pow(1 - p, 3);
      el.textContent = (opts.prefix || '') + fmt(from + (to - from) * e) + (opts.suffix || '');
      if (p < 1) requestAnimationFrame(step);
    })(t0);
  };

  /* ---------- confetti ---------- */
  AA.confetti = () => {
    const c = document.createElement('canvas');
    c.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:9999';
    document.body.appendChild(c);
    const W = (c.width = innerWidth), H = (c.height = innerHeight), g = c.getContext('2d');
    const cols = ['#e8c27a', '#4fd1c5', '#6ea8ff', '#ff6b81', '#ffffff'];
    const P = Array.from({ length: 160 }, () => ({ x: W / 2 + (Math.random() - .5) * 200, y: H * .6, vx: (Math.random() - .5) * 16, vy: -Math.random() * 18 - 6, s: Math.random() * 7 + 3, r: Math.random() * 6, vr: Math.random() * .3 - .15, c: cols[(Math.random() * cols.length) | 0] }));
    let f = 0;
    (function tick() {
      g.clearRect(0, 0, W, H);
      P.forEach((p) => { p.vy += .45; p.x += p.vx; p.y += p.vy; p.r += p.vr; g.save(); g.translate(p.x, p.y); g.rotate(p.r); g.fillStyle = p.c; g.globalAlpha = Math.max(0, 1 - f / 110); g.fillRect(-p.s / 2, -p.s / 4, p.s, p.s / 2); g.restore(); });
      if (++f < 120) requestAnimationFrame(tick); else c.remove();
    })();
  };

  /* ---------- sidebar + router ---------- */
  function renderXP() {
    const i = AA.levelInfo(), box = AA.$('#xpbox'); if (!box) return;
    box.innerHTML =
      '<div class="xp-top"><span class="lvl">LVL ' + i.level + '</span><span>' + AA.esc(i.title) + '</span></div>' +
      '<div class="xp-bar"><i style="width:' + Math.round(i.pct * 100) + '%"></i></div>' +
      '<div class="xp-sub">' + state.xp + ' XP &middot; ' + (i.span - i.into) + ' to next level</div>';
  }

  function renderNav() {
    const nav = AA.$('#nav');
    nav.innerHTML = AA.modules.map((m) =>
      '<a class="nav-item' + (m.status === 'soon' ? ' soon' : '') + '" href="#/' + m.id + '" data-id="' + m.id + '">' +
      '<span class="ni-ico">' + m.icon + '</span><span class="ni-txt"><b>' + AA.esc(m.title) + '</b><small>' + AA.esc(m.tagline) + '</small></span>' +
      (m.status === 'soon' ? '<em>SOON</em>' : '') + '</a>').join('');
  }

  function route() {
    const id = (location.hash.replace(/^#\/?/, '') || 'hub').split('/')[0];
    const m = AA.byId[id] || AA.byId.hub;
    if (AA.current && AA.current.unmount) { try { AA.current.unmount(); } catch (e) { console.error(e); } }
    const stage = AA.$('#stage');
    stage.className = 'stage m-' + m.id;
    stage.innerHTML = '';
    AA.current = m;
    AA.$$('.nav-item').forEach((a) => a.classList.toggle('on', a.dataset.id === m.id));
    document.body.classList.remove('menu-open');
    document.title = m.title + ' - Akbar Academy Simulation Lab';
    stage.scrollTop = 0; window.scrollTo(0, 0);
    m.mount(stage);
    stage.classList.add('enter');
    setTimeout(() => stage.classList.remove('enter'), 600);
  }

  /* ---------- command palette (Ctrl/Cmd + K) ---------- */
  function palette() {
    if (AA.$('#palette')) return;
    const wrap = document.createElement('div');
    wrap.id = 'palette';
    wrap.innerHTML = '<div class="pal"><input placeholder="Jump to a simulator...  (Esc to close)" autofocus><ul></ul></div>';
    document.body.appendChild(wrap);
    const input = AA.$('input', wrap), ul = AA.$('ul', wrap);
    let sel = 0, items = [];
    const draw = () => {
      const q = input.value.toLowerCase();
      items = AA.modules.filter((m) => (m.title + ' ' + m.tagline).toLowerCase().includes(q));
      ul.innerHTML = items.map((m, i) => '<li class="' + (i === sel ? 'on' : '') + '" data-i="' + i + '"><span>' + m.icon + '</span><b>' + AA.esc(m.title) + '</b><small>' + AA.esc(m.tagline) + '</small></li>').join('') || '<li class="empty">Nothing found</li>';
    };
    const go = (i) => { if (items[i]) { location.hash = '#/' + items[i].id; close(); } };
    const close = () => { wrap.remove(); document.removeEventListener('keydown', key, true); };
    const key = (e) => {
      if (e.key === 'Escape') { close(); }
      else if (e.key === 'ArrowDown') { sel = Math.min(items.length - 1, sel + 1); draw(); e.preventDefault(); }
      else if (e.key === 'ArrowUp') { sel = Math.max(0, sel - 1); draw(); e.preventDefault(); }
      else if (e.key === 'Enter') { go(sel); e.preventDefault(); }
    };
    document.addEventListener('keydown', key, true);
    input.addEventListener('input', () => { sel = 0; draw(); });
    ul.addEventListener('click', (e) => { const li = e.target.closest('li[data-i]'); if (li) go(+li.dataset.i); });
    wrap.addEventListener('click', (e) => { if (e.target === wrap) close(); });
    draw(); input.focus();
  }

  AA.start = () => {
    renderNav(); renderXP();
    AA.$('#menuBtn').addEventListener('click', () => document.body.classList.toggle('menu-open'));
    AA.$('#scrim').addEventListener('click', () => document.body.classList.remove('menu-open'));
    AA.$('#palBtn').addEventListener('click', palette);
    document.addEventListener('keydown', (e) => { if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); palette(); } });
    window.addEventListener('hashchange', route);
    route();
  };
})();
