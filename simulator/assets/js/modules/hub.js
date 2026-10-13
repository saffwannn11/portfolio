/* Hub: animated route map, live clocks, split-flap departure board, module cards, progress. */
(function () {
  'use strict';
  const { $, $$, esc } = AA;
  let raf = 0, clockTimer = 0, alive = false;

  const ROUTES = [['DXB','LHR'],['DXB','JFK'],['DXB','SIN'],['DXB','SYD'],['DXB','BOM'],['DXB','NBO'],['DXB','IST'],['LHR','JFK'],['SIN','SYD'],['DOH','FRA'],['JFK','LAX'],['DXB','GRU'],['DXB','JNB'],['DXB','HKG'],['HKG','NRT'],['DEL','DXB'],['DXB','CAI'],['KHI','DXB'],['FRA','JFK'],['LHR','SIN']];
  const NODES = ['DXB','LHR','JFK','SIN','SYD','BOM','NBO','IST','DOH','FRA','LAX','GRU','JNB','HKG','NRT','DEL','CAI','KHI'];
  const ZONES = [['DXB','Asia/Dubai'],['LON','Europe/London'],['NYC','America/New_York'],['SIN','Asia/Singapore'],['SYD','Australia/Sydney']];

  function inPoly(x, y, p) {
    let c = false;
    for (let i = 0, j = p.length - 1; i < p.length; j = i++) {
      if (((p[i][1] > y) !== (p[j][1] > y)) && (x < (p[j][0] - p[i][0]) * (y - p[i][1]) / (p[j][1] - p[i][1]) + p[i][0])) c = !c;
    }
    return c;
  }

  function startMap(cv) {
    const ctx = cv.getContext('2d');
    let W, H, s, ox, oy, dots = [];
    const geo = AA.geo, step = 2.4;
    for (let lat = 80; lat > -58; lat -= step) for (let lon = -178; lon < 180; lon += step) {
      if (geo.land.some((p) => inPoly(lon, lat, p))) dots.push([lon, lat]);
    }
    const P = (lon, lat) => [ox + (lon + 180) * s, oy + (82 - lat) * s];
    function size() {
      const r = cv.getBoundingClientRect(), d = Math.min(2, window.devicePixelRatio || 1);
      W = r.width; H = r.height; cv.width = W * d; cv.height = H * d; ctx.setTransform(d, 0, 0, d, 0, 0);
      s = Math.max(W / 360, H / 140);
      ox = Math.min(0, Math.max(W - 360 * s, W * 0.56 - 235 * s));
      oy = (H - 140 * s) / 2;
    }
    size();
    const ro = new ResizeObserver(size); ro.observe(cv);
    const routes = ROUTES.map((r, i) => ({ a: geo.airports[r[0]], b: geo.airports[r[1]], off: i * 0.37, sp: 0.00011 + (i % 5) * 0.00002 }));
    function frame(now) {
      if (!alive) { ro.disconnect(); return; }
      ctx.clearRect(0, 0, W, H);
      ctx.fillStyle = 'rgba(160,178,220,.30)';
      const rr = Math.max(0.9, s * 0.42);
      dots.forEach((d) => { const p = P(d[0], d[1]); ctx.beginPath(); ctx.arc(p[0], p[1], rr, 0, 6.283); ctx.fill(); });
      routes.forEach((r) => {
        const a = P(r.a.lon, r.a.lat), b = P(r.b.lon, r.b.lat);
        const dx = b[0] - a[0], dy = b[1] - a[1], len = Math.hypot(dx, dy);
        const c = [(a[0] + b[0]) / 2, (a[1] + b[1]) / 2 - len * 0.28];
        const at = (t) => { const u = 1 - t; return [u * u * a[0] + 2 * u * t * c[0] + t * t * b[0], u * u * a[1] + 2 * u * t * c[1] + t * t * b[1]]; };
        ctx.strokeStyle = 'rgba(232,194,122,.10)'; ctx.lineWidth = 1; ctx.beginPath(); ctx.moveTo(a[0], a[1]); ctx.quadraticCurveTo(c[0], c[1], b[0], b[1]); ctx.stroke();
        const t = ((now * r.sp + r.off) % 1.5);
        if (t <= 1) {
          const t0 = Math.max(0, t - 0.22);
          for (let k = 0; k < 14; k++) {
            const u0 = t0 + (t - t0) * (k / 14), u1 = t0 + (t - t0) * ((k + 1) / 14), p0 = at(u0), p1 = at(u1);
            ctx.strokeStyle = 'rgba(255,224,160,' + (0.05 + 0.85 * k / 14) + ')'; ctx.lineWidth = 1.6; ctx.beginPath(); ctx.moveTo(p0[0], p0[1]); ctx.lineTo(p1[0], p1[1]); ctx.stroke();
          }
          const h = at(t);
          const g = ctx.createRadialGradient(h[0], h[1], 0, h[0], h[1], 9);
          g.addColorStop(0, 'rgba(255,240,200,1)'); g.addColorStop(1, 'rgba(232,194,122,0)');
          ctx.fillStyle = g; ctx.beginPath(); ctx.arc(h[0], h[1], 9, 0, 6.283); ctx.fill();
        }
      });
      NODES.forEach((c) => {
        const a = geo.airports[c], p = P(a.lon, a.lat), hq = c === 'DXB';
        ctx.fillStyle = hq ? '#e8c27a' : '#4fd1c5'; ctx.beginPath(); ctx.arc(p[0], p[1], hq ? 4 : 2.6, 0, 6.283); ctx.fill();
        const ph = ((now / 1600) + c.charCodeAt(0) * 0.13) % 1;
        ctx.strokeStyle = (hq ? 'rgba(232,194,122,' : 'rgba(79,209,197,') + (0.55 * (1 - ph)) + ')'; ctx.lineWidth = 1.2;
        ctx.beginPath(); ctx.arc(p[0], p[1], (hq ? 5 : 3) + ph * (hq ? 20 : 11), 0, 6.283); ctx.stroke();
        if (W > 560 || hq) { ctx.fillStyle = hq ? '#f6dca3' : 'rgba(200,210,235,.7)'; ctx.font = (hq ? '700 11px' : '600 9.5px') + ' JetBrains Mono, monospace'; ctx.fillText(c, p[0] + 8, p[1] - 6); }
      });
      raf = requestAnimationFrame(frame);
    }
    raf = requestAnimationFrame(frame);
  }

  function flap(el, text, delay) {
    el.innerHTML = '';
    const set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    text.split('').forEach((ch, i) => {
      const sp = document.createElement('span'); sp.className = 'flap' + (ch === ' ' ? ' sp' : ''); sp.textContent = ch === ' ' ? ' ' : ' ';
      el.appendChild(sp);
      if (ch === ' ') return;
      let n = 0; const total = 5 + ((i * 3) % 6);
      setTimeout(function tick() {
        if (!alive) return;
        n++; sp.textContent = n >= total ? ch : set[(Math.random() * set.length) | 0];
        if (n < total) setTimeout(tick, 45);
      }, (delay || 0) + i * 35);
    });
  }

  function clocks() {
    const box = $('#clocks'); if (!box) return;
    box.innerHTML = ZONES.map((z) => '<div class="clock"><b data-z="' + z[1] + '">--:--</b><small>' + z[0] + '</small></div>').join('');
    const upd = () => $$('[data-z]', box).forEach((b) => { b.textContent = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: b.dataset.z }).format(new Date()); });
    upd(); clockTimer = setInterval(upd, 1000);
  }

  function mount(el) {
    alive = true;
    const mods = AA.modules.filter((m) => m.id !== 'hub');
    const done = Object.keys(AA.state.done).length;
    const bestG = AA.state.best.gds || 0, bestD = AA.state.best.dubai || 0;
    const ach = Object.keys(AA.state.ach).length, total = Object.keys(AA.ACH).length;
    el.innerHTML =
      '<section class="hero">' +
        '<canvas id="map"></canvas><div class="live"><i></i>LIVE NETWORK</div>' +
        '<div class="hero-txt"><span class="eyebrow">Akbar Academy &middot; Simulation Lab</span>' +
        '<h1>Train on the <em>real desk</em>, before you ever sit at one.</h1>' +
        '<p>Real GDS terminals, real PNR logic, real Dubai trip costing. Every mission is scored, timed and ends with a proper e-ticket or a client-ready quote.</p>' +
        '<div class="hero-cta"><a class="btn gold" href="#/gds">Launch GDS Lab &rarr;</a><a class="btn" href="#/dubai">Plan a Dubai trip</a></div></div>' +
        '<div class="clocks" id="clocks"></div>' +
      '</section>' +
      '<section class="kpis">' +
        '<div class="kpi"><small>Missions cleared</small><b data-c="' + done + '">0</b><span>across all simulators</span></div>' +
        '<div class="kpi"><small>Total XP</small><b data-c="' + AA.state.xp + '">0</b><span>' + esc(AA.levelInfo().title) + ' &middot; level ' + AA.levelInfo().level + '</span></div>' +
        '<div class="kpi"><small>Best GDS score</small><b data-c="' + bestG + '">0</b><span>out of 1000</span></div>' +
        '<div class="kpi"><small>Best Dubai score</small><b data-c="' + bestD + '">0</b><span>client satisfaction / 100</span></div>' +
      '</section>' +
      '<div class="sec-h"><h2>Departures</h2><span>Click a flight to board a simulator</span></div>' +
      '<section class="board"><div class="board-h"><span>TIME</span><span>FLIGHT</span><span>SIMULATOR</span><span>GATE</span><span>STATUS</span></div>' +
        mods.map((m, i) => '<div class="board-r" data-go="' + m.id + '"><span class="f" data-t="' + (m.status === 'soon' ? '--:--' : 'NOW') + '"></span><span class="f" data-t="AK' + (101 + i) + '"></span><span class="f" data-t="' + esc(m.title.toUpperCase()) + '"></span><span class="f" data-t="' + String.fromCharCode(65 + i) + (1 + i) + '"></span><span class="f ' + (m.status === 'soon' ? 'st-soon' : 'st-live') + '" data-t="' + (m.status === 'soon' ? 'DELAYED' : 'BOARDING') + '"></span></div>').join('') +
      '</section>' +
      '<div class="sec-h"><h2>Simulators</h2><span>Each one is a plug-in module - add more any time</span></div>' +
      '<section class="mods">' + mods.map((m) =>
        '<a class="mcard' + (m.status === 'soon' ? ' soon' : '') + '" href="#/' + m.id + '">' +
          '<span class="ribbon ' + (m.status === 'soon' ? 'soonr' : 'live') + '">' + (m.status === 'soon' ? 'COMING SOON' : 'LIVE') + '</span>' +
          '<div class="ico">' + m.icon + '</div><h3>' + esc(m.title) + '</h3><p>' + esc(m.pitch || m.tagline) + '</p>' +
          '<ul>' + (m.features || []).map((f) => '<li>' + esc(f) + '</li>').join('') + '</ul>' +
          '<div class="foot"><span class="chip">' + esc(m.badge || '') + '</span><span class="btn sm ' + (m.status === 'soon' ? '' : 'gold') + '">' + (m.status === 'soon' ? 'Preview' : 'Open') + '</span></div></a>').join('') +
      '</section>' +
      '<div class="sec-h"><h2>Achievements</h2><span>' + ach + ' / ' + total + ' unlocked</span></div>' +
      '<section class="ach-grid">' + Object.keys(AA.ACH).map((k) => '<div class="ach ' + (AA.state.ach[k] ? 'on' : '') + '"><div class="m">' + (AA.state.ach[k] ? '★' : '?') + '</div><div><b>' + esc(AA.ACH[k][0]) + '</b><small>' + esc(AA.ACH[k][1]) + '</small></div></div>').join('') + '</section>';

    startMap($('#map', el));
    clocks();
    $$('[data-c]', el).forEach((n) => AA.countTo(n, +n.dataset.c, { dur: 1100 }));
    $$('.board-r', el).forEach((r, ri) => {
      $$('.f', r).forEach((c, ci) => flap(c, c.dataset.t, ri * 220 + ci * 90));
      r.addEventListener('click', () => { location.hash = '#/' + r.dataset.go; });
    });
    $$('.mcard', el).forEach((c) => {
      c.addEventListener('mousemove', (e) => {
        const r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        c.style.setProperty('--ry', ((x - .5) * 8).toFixed(2) + 'deg'); c.style.setProperty('--rx', ((.5 - y) * 8).toFixed(2) + 'deg');
        c.style.setProperty('--mx', (x * 100) + '%'); c.style.setProperty('--my', (y * 100) + '%');
      });
      c.addEventListener('mouseleave', () => { c.style.setProperty('--rx', '0deg'); c.style.setProperty('--ry', '0deg'); });
    });
  }

  function unmount() { alive = false; cancelAnimationFrame(raf); clearInterval(clockTimer); }

  AA.register({ id: 'hub', title: 'Command Centre', tagline: 'Progress, rankings & launchpad', icon: '◈', status: 'live', mount: mount, unmount: unmount, onAchievement: null });
})();
