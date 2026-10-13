/* Dubai Tourism Studio - build a day-by-day itinerary on a live map, cost it, score client satisfaction,
   and produce a printable proposal. Content lives in data/dubai.js. */
(function () {
  'use strict';
  const { $, $$, esc, money } = AA;
  const D = AA.dubai;
  const MN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const SL = 'MAE', SLN = { M: 'Morning', A: 'Afternoon', E: 'Evening' };
  const ACT = {}; D.activities.forEach((a) => (ACT[a.id] = a));
  const HOT = {}; D.hotels.forEach((h) => (HOT[h.id] = h));
  const TR = {}; D.transport.forEach((t) => (TR[t.id] = t));
  let st, el, dragId = null, mapHold = null;

  const brief = () => D.briefs[st.bi];
  const newState = (bi) => ({ bi: bi, hotel: 'rove', transport: 'public', margin: 15, days: Array.from({ length: D.briefs[bi].nights + 1 }, () => [null, null, null]), filter: 'all', q: '', won: false });
  const inSeason = (a, m) => !a.season || (a.season[0] <= a.season[1] ? m >= a.season[0] && m <= a.season[1] : m >= a.season[0] || m <= a.season[1]);
  const summer = (m) => m >= 6 && m <= 9;
  const km = (a, b) => AA.geo.dist({ lat: a.lat, lon: a.lon }, { lat: b.lat, lon: b.lon });
  const lockedWhy = (d, s) => (d === 0 && s === 0 ? 'Arrival - flight lands' : d === st.days.length - 1 && s > 0 ? 'Departure - airport transfer' : null);

  function canPlace(a, d, s) {
    const b = brief();
    if (!inSeason(a, b.month)) return 'Closed in ' + MN[b.month - 1];
    if (a.cost[1] === null && b.kids) return 'Adults only';
    if (placedIds().includes(a.id)) return 'Already in the plan';
    if (a.day) {
      if (d === 0 || d === st.days.length - 1) return 'Needs a full free day';
      if (st.days[d].some((x) => x)) return 'Needs an empty day';
      return null;
    }
    const lw = lockedWhy(d, s); if (lw) return lw;
    if (st.days[d][s]) return 'Slot taken';
    if (!a.ok.includes(SL[s])) return 'Only ' + a.ok.split('').map((c) => SLN[c]).join(' / ');
    return null;
  }
  function placedIds() { const out = []; st.days.forEach((day) => day.forEach((v) => { if (v && v[0] !== '~') out.push(v); })); return out; }
  function place(a, d, s) { if (a.day) st.days[d] = [a.id, '~' + a.id, '~' + a.id]; else st.days[d][s] = a.id; }
  function remove(d, s) {
    const v = st.days[d][s]; if (!v) return;
    const id = v[0] === '~' ? v.slice(1) : v;
    if (ACT[id].day) st.days[d] = [null, null, null]; else st.days[d][s] = null;
  }
  function autoAdd(a) {
    const order = [];
    for (let d = 0; d < st.days.length; d++) {
      if (a.day) { order.push([d, 0, 0]); continue; }
      for (let s = 0; s < 3; s++) order.push([d, s, a.best.includes(SL[s]) ? 0 : 1]);
    }
    order.sort((x, y) => x[2] - y[2] || x[0] - y[0] || x[1] - y[1]);
    for (const o of order) if (!canPlace(a, o[0], o[1])) { place(a, o[0], o[1]); return o; }
    return null;
  }

  /* ---------------- scoring ---------------- */
  function compute() {
    const b = brief(), pax = b.adults + b.kids, h = HOT[st.hotel], tr = TR[st.transport], days = st.days.length;
    const placed = []; st.days.forEach((day, d) => day.forEach((v, s) => { if (v && v[0] !== '~') placed.push({ a: ACT[v], d: d, s: s }); }));
    const cAct = placed.reduce((t, p) => t + b.adults * p.a.cost[0] + b.kids * (p.a.cost[1] || 0), 0);
    const cHotel = h.rate * b.rooms * b.nights, cTr = tr.perDay * days * (pax > 4 ? 2 : 1), cVisa = D.visaPerPax * pax, cXfer = D.airportTransfer * 2;
    const cost = cAct + cHotel + cTr + cVisa + cXfer, price = cost * (1 + st.margin / 100), profit = price - cost;

    /* interest match (35) */
    const tagCount = {}; const addTag = (t, w) => (tagCount[t] = (tagCount[t] || 0) + w);
    placed.forEach((p) => p.a.tags.forEach((t) => addTag(t, 1))); h.tags.forEach((t) => addTag(t, .8));
    const wants = Object.keys(b.want), wsum = wants.reduce((t, k) => t + b.want[k], 0);
    let interest = 0; const miss = [];
    wants.forEach((k) => { const cov = Math.min(1, (tagCount[k] || 0) / (b.want[k] >= 3 ? 3 : 2)); interest += b.want[k] * cov; if (cov < .5) miss.push(k); });
    interest = placed.length ? interest / wsum * 35 : 0;

    /* slot fit (10) */
    const fit = placed.length ? placed.reduce((t, p) => t + (p.a.best.includes(SL[p.s]) || p.a.day ? 1 : .55), 0) / placed.length * 10 : 0;

    /* pace (15) */
    let pace = 0; const dayLoad = [];
    st.days.forEach((day, d) => {
      let load = 0; day.forEach((v) => { if (v && v[0] !== '~') { const a = ACT[v]; load += a.day ? 3 : (a.heavy || 1); } });
      dayLoad.push(load);
      const edge = d === 0 || d === days - 1, ideal = b.kids ? 2 : 3;
      let q;
      if (edge) q = load === 0 ? .7 : load <= 2 ? 1 : .5;
      else q = load === 0 ? 0 : load === 1 ? .6 : load <= ideal ? 1 : load === ideal + 1 ? .6 : .2;
      pace += q;
    });
    pace = pace / days * 15;

    /* heat (5) */
    let heat = 5;
    if (summer(b.month)) placed.forEach((p) => { if (p.a.outdoor) heat -= p.s === 1 ? 2.5 : p.s === 0 ? .8 : 0; });
    heat = Math.max(0, heat);

    /* geography (10) */
    let legs = 0, good = 0;
    st.days.forEach((day) => { const ids = day.filter((v) => v && v[0] !== '~').map((v) => ACT[v]); for (let i = 1; i < ids.length; i++) { legs++; if (km(ids[i - 1], ids[i]) <= 28) good++; } });
    const geo = placed.length ? (legs ? good / legs : 1) * 10 : 0;

    /* budget (10) */
    const ratio = price / b.budget;
    const budget = ratio > 1 ? Math.max(0, 10 - (ratio - 1) * 40) : ratio < .45 ? 6 : 10;

    /* stay & comfort (10) */
    let hf = .5;
    if (b.style === 'family') hf = h.tags.includes('family') ? 1 : h.tier >= 4 ? .7 : .45;
    else if (b.style === 'luxury') hf = h.tier === 5 ? 1 : h.tier === 4 ? .5 : .2;
    else hf = h.tier === 3 ? 1 : h.tier === 4 ? .5 : .2;
    const tf = Math.min(1, tr.comfort / b.comfort), stay = (hf * .6 + tf * .4) * 10;

    /* variety (5) */
    const cats = new Set(placed.map((p) => p.a.cat)); const variety = Math.min(1, cats.size / 4) * 5;

    const parts = [['Interest match', interest, 35], ['Slot timing', fit, 10], ['Pace', pace, 15], ['Heat & comfort', heat, 5], ['Travel time', geo, 10], ['Budget', budget, 10], ['Stay & transport', stay, 10], ['Variety', variety, 5]];
    const score = Math.round(parts.reduce((t, p) => t + p[1], 0));

    /* coach tips, most valuable first */
    const tips = [];
    st.days.forEach((day, d) => { const l = dayLoad[d], edge = d === 0 || d === days - 1; if (!edge && l === 0) tips.push([15, 'Day ' + (d + 1) + ' is empty - the client is paying for hotel nights, give them something to do.']); if (!edge && l > (b.kids ? 3 : 4)) tips.push([12, 'Day ' + (d + 1) + ' is overloaded - ' + (b.kids ? 'the kids will melt down.' : 'nobody enjoys a rushed holiday.')]); });
    if (ratio > 1) tips.push([20, 'Over budget by AED ' + money(price - b.budget) + '. Trim an activity, lower the margin or choose a cheaper hotel.']);
    miss.forEach((k) => tips.push([10 + b.want[k], 'The client cares about "' + k + '" - add something that delivers it.']));
    if (summer(b.month) && placed.some((p) => p.a.outdoor && p.s === 1)) tips.push([14, 'Outdoor activity in an ' + MN[b.month - 1] + ' afternoon - 45C heat. Move it to the evening or choose an indoor option.']);
    if (legs && good < legs) tips.push([9, 'Some stops on the same day are far apart - cluster by area to cut traffic time.']);
    if (b.kids && tr.comfort < 3) tips.push([9, 'Two kids and luggage? A private SUV makes transfers painless.']);
    if (b.style === 'luxury' && h.tier < 5) tips.push([11, 'This is a luxury honeymoon - the hotel should wow them.']);
    if (b.style === 'budget' && h.tier > 3) tips.push([11, 'This client is watching every dirham - a 5-star hotel eats the budget.']);
    if (!placed.length) tips.unshift([99, 'Start by clicking activity cards, or drag them into a time slot.']);
    tips.sort((x, y) => y[0] - x[0]);
    return { b: b, pax: pax, placed: placed, cAct: cAct, cHotel: cHotel, cTr: cTr, cVisa: cVisa, cXfer: cXfer, cost: cost, price: price, profit: profit, parts: parts, score: score, tips: tips.map((t) => t[1]).slice(0, 3), ratio: ratio };
  }

  /* ---------------- map ---------------- */
  const M = D.map, X = (lon) => (lon - M.lon0) / (M.lon1 - M.lon0) * M.w, Y = (lat) => (M.lat1 - lat) / (M.lat1 - M.lat0) * M.h;
  const COAST = [[54.95, 24.95], [55.03, 25.01], [55.09, 25.05], [55.13, 25.085], [55.165, 25.12], [55.19, 25.15], [55.215, 25.185], [55.245, 25.225], [55.28, 25.265], [55.315, 25.285], [55.35, 25.31], [55.4, 25.355], [55.45, 25.4]];
  const DAYCOL = ['#e8c27a', '#4fd1c5', '#ff8a8a', '#a99bff', '#8cf08c', '#ffb454'];
  function palm() {
    const a = [X(55.145), Y(25.103)], b = [X(55.108), Y(25.128)], dx = b[0] - a[0], dy = b[1] - a[1], L = Math.hypot(dx, dy), nx = -dy / L, ny = dx / L;
    let s = '<line x1="' + a[0] + '" y1="' + a[1] + '" x2="' + b[0] + '" y2="' + b[1] + '" class="palm"/>';
    for (let i = 1; i < 9; i++) { const t = i / 9, cx = a[0] + dx * t, cy = a[1] + dy * t, w = 20 + Math.sin(t * 3.14) * 14; s += '<line x1="' + (cx - nx * w) + '" y1="' + (cy - ny * w) + '" x2="' + (cx + nx * w) + '" y2="' + (cy + ny * w) + '" class="palm fr"/>'; }
    const r = 52, ang = Math.atan2(dy, dx);
    s += '<path class="palm cr" d="M' + (b[0] + Math.cos(ang + 1.5) * r) + ' ' + (b[1] + Math.sin(ang + 1.5) * r) + ' A' + r + ' ' + r + ' 0 1 0 ' + (b[0] + Math.cos(ang - 1.5) * r) + ' ' + (b[1] + Math.sin(ang - 1.5) * r) + '"/>';
    return s;
  }
  function mapSVG(c) {
    const coast = COAST.map((p) => X(p[0]).toFixed(1) + ',' + Y(p[1]).toFixed(1)).join(' ');
    const road = COAST.map((p) => X(p[0] + .038).toFixed(1) + ',' + Y(p[1] - .032).toFixed(1)).join(' ');
    const road2 = COAST.map((p) => X(p[0] + .085).toFixed(1) + ',' + Y(p[1] - .062).toFixed(1)).join(' ');
    let s = '<svg viewBox="0 0 ' + M.w + ' ' + M.h + '" class="dmap" role="img" aria-label="Illustrative map of Dubai">' +
      '<defs><linearGradient id="sea" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#0c2a4d"/><stop offset="1" stop-color="#071a33"/></linearGradient><linearGradient id="land" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1b2338"/><stop offset="1" stop-color="#141a2b"/></linearGradient><filter id="gl"><feGaussianBlur stdDeviation="5"/></filter></defs>' +
      '<rect width="' + M.w + '" height="' + M.h + '" fill="url(#sea)"/>' +
      '<polygon points="' + coast + ' ' + X(M.lon1) + ',' + Y(M.lat1) + ' ' + X(M.lon1) + ',' + Y(M.lat0) + ' ' + X(M.lon0) + ',' + Y(M.lat0) + '" fill="url(#land)"/>' +
      '<polyline points="' + coast + '" class="coast glow" filter="url(#gl)"/><polyline points="' + coast + '" class="coast"/>' +
      '<polyline points="' + road + '" class="road"/><polyline points="' + road2 + '" class="road thin"/>' +
      '<path class="creek" d="M' + X(55.292) + ' ' + Y(25.272) + ' Q' + X(55.31) + ' ' + Y(25.235) + ' ' + X(55.345) + ' ' + Y(25.19) + '"/>' + palm() +
      '<text x="' + X(54.99) + '" y="' + Y(25.2) + '" class="lbl sea">ARABIAN GULF</text><text x="' + X(55.4) + '" y="' + Y(25.0) + '" class="lbl">DESERT</text><text x="' + X(55.29) + '" y="' + Y(25.165) + '" class="lbl">DOWNTOWN</text><text x="' + X(55.31) + '" y="' + Y(25.31) + '" class="lbl">DEIRA</text><text x="' + X(55.1) + '" y="' + Y(25.055) + '" class="lbl">MARINA</text><text x="' + X(55.04) + '" y="' + Y(25.165) + '" class="lbl sea">PALM JUMEIRAH</text>';
    /* day routes */
    const h = HOT[st.hotel];
    st.days.forEach((day, d) => {
      const pts = [[h.lon, h.lat]]; day.forEach((v) => { if (v && v[0] !== '~') { const a = ACT[v]; pts.push(a.day ? [M.lon0 + .004, M.lat0 + .06] : [a.lon, a.lat]); } });
      if (pts.length > 1) s += '<polyline class="route" style="stroke:' + DAYCOL[d % 6] + '" points="' + pts.map((p) => X(p[0]).toFixed(1) + ',' + Y(p[1]).toFixed(1)).join(' ') + '"/>';
    });
    /* pins */
    const used = {};
    c.placed.forEach((p) => {
      const a = p.a, px = a.day ? 70 : X(a.lon), py = a.day ? 840 : Y(a.lat), key = Math.round(px / 14) + ',' + Math.round(py / 14); used[key] = (used[key] || 0) + 1;
      const jx = (used[key] - 1) * 20, col = D.cats[a.cat][1];
      s += '<g class="pin" style="--d:' + (used[key] * 60) + 'ms" transform="translate(' + (px + jx) + ' ' + py + ')"><circle r="17" fill="' + col + '" opacity=".22"/><circle r="12" fill="' + col + '" stroke="#fff" stroke-width="2"/><text y="4.5" class="pn">' + (p.d + 1) + '</text><title>Day ' + (p.d + 1) + ' - ' + esc(a.name) + '</title></g>';
    });
    s += '<g transform="translate(' + X(h.lon) + ' ' + Y(h.lat) + ')"><circle r="22" fill="#e8c27a" opacity=".2" class="hpulse"/><path d="M0 -17 5 -5 18 -4 8 5 11 18 0 11 -11 18 -8 5 -18 -4 -5 -5z" fill="#e8c27a" stroke="#fff" stroke-width="1.5"/><title>' + esc(h.name) + '</title></g>';
    if (c.placed.some((p) => p.a.day)) s += '<text x="40" y="880" class="lbl hi">&#8601; ABU DHABI 90 KM</text>';
    return s + '</svg>';
  }

  /* ---------------- render ---------------- */
  function mount(root) {
    st = newState(st ? st.bi : 0);
    el = root;
    root.innerHTML =
      '<div class="dub">' +
        '<div class="gds-head"><div><span class="eyebrow">Dubai Studio &middot; Destination Simulator</span><h2>Design the perfect Dubai trip</h2></div><div class="seg-tabs" id="btabs"></div></div>' +
        '<div class="dub-grid"><div class="dcol" id="dleft"></div><aside class="dside" id="dside"></aside></div>' +
      '</div>';
    $('#btabs').innerHTML = D.briefs.map((b, i) => '<button data-b="' + i + '" class="' + (i === st.bi ? 'on' : '') + '">' + b.e + ' ' + esc(b.name.split(' - ')[0].replace('The ', '')) + '</button>').join('');
    $('#btabs').addEventListener('click', (e) => { const b = e.target.closest('button'); if (b) { st = newState(+b.dataset.b); mount(el); } });
    renderLeft(); update();
    wire();
  }

  function renderLeft() {
    const b = brief();
    $('#dleft').innerHTML =
      '<div class="card dbrief"><div class="av">' + b.e + '</div><div class="bi"><span class="lvl-tag">CLIENT BRIEF &middot; ' + b.diff.toUpperCase() + '</span><h3>' + esc(b.name) + '</h3><p>' + esc(b.quote) + '</p>' +
        '<div class="bf"><span class="chip">&#128205; ' + esc(b.from) + '</span><span class="chip">&#128101; ' + b.adults + ' adult' + (b.adults > 1 ? 's' : '') + (b.kids ? ' + ' + b.kids + ' kids' : '') + '</span><span class="chip">&#127769; ' + b.nights + ' nights &middot; ' + MN[b.month - 1] + (summer(b.month) ? ' &#9728; hot' : '') + '</span><span class="chip gold">Budget AED ' + money(b.budget) + '</span></div>' +
        '<div class="wants">' + Object.keys(b.want).map((k) => '<span class="want">' + k + ' <i>' + '★'.repeat(b.want[k]) + '</i></span>').join('') + '</div></div></div>' +
      '<div class="sec-h sm"><h3>1 &middot; Stay &amp; transport</h3></div>' +
      '<div class="hotels" id="hotels"></div><div class="trs" id="trs"></div>' +
      '<div class="sec-h sm"><h3>2 &middot; Build the itinerary</h3><span>Click a card to auto-place it &middot; or drag it into a slot</span></div>' +
      '<div class="planner" id="planner"></div>' +
      '<div class="sec-h sm"><h3>3 &middot; Experience library</h3></div>' +
      '<div class="libbar"><div class="fchips" id="fchips"></div><input id="fq" placeholder="Search experiences..." type="search"></div>' +
      '<div class="lib" id="lib"></div>';
    $('#hotels').innerHTML = D.hotels.map((h) => '<button class="hcard" data-h="' + h.id + '"><span class="stars">' + '★'.repeat(h.tier) + '</span><b>' + esc(h.name) + '</b><small>' + esc(h.note) + '</small><span class="rate">AED ' + money(h.rate) + '<em>/room/night</em></span><span class="tot">Stay: AED ' + money(h.rate * b.rooms * b.nights) + '</span></button>').join('');
    $('#trs').innerHTML = D.transport.map((t) => '<button class="tcard" data-t="' + t.id + '"><b>' + esc(t.name) + '</b><small>' + esc(t.note) + '</small><span>AED ' + t.perDay + '/day</span></button>').join('');
    $('#fchips').innerHTML = '<button data-f="all" class="on">All</button>' + Object.keys(D.cats).map((k) => '<button data-f="' + k + '">' + D.cats[k][0] + '</button>').join('') + '<button data-f="match">&#10024; Fits client</button>';
    renderLib();
  }

  function renderLib() {
    const b = brief(), wants = Object.keys(b.want), q = st.q.toLowerCase();
    const list = D.activities.filter((a) => (st.filter === 'all' || (st.filter === 'match' ? a.tags.some((t) => wants.includes(t)) : a.cat === st.filter)) && (!q || (a.name + ' ' + a.tags.join(' ')).toLowerCase().includes(q)));
    $('#lib').innerHTML = list.map((a) => {
      const closed = !inSeason(a, b.month) ? 'Closed in ' + MN[b.month - 1] : a.cost[1] === null && b.kids ? 'Adults only' : '';
      const match = a.tags.filter((t) => wants.includes(t)).length;
      return '<div class="acard' + (closed ? ' off' : '') + '" draggable="' + (closed ? 'false' : 'true') + '" data-a="' + a.id + '" style="--c:' + D.cats[a.cat][1] + '">' +
        '<div class="ae">' + a.e + '</div><div class="ab"><b>' + esc(a.name) + '</b><small>' + esc(a.desc) + '</small>' +
        '<div class="at"><span class="pill" style="--c:' + D.cats[a.cat][1] + '">' + D.cats[a.cat][0] + '</span><span class="pill">Best: ' + a.best.split('').map((c) => SLN[c].slice(0, 3)).join('/') + '</span>' + (a.day ? '<span class="pill">Full day</span>' : '') + (match ? '<span class="pill hit">&#10024; ' + match + ' match' + (match > 1 ? 'es' : '') + '</span>' : '') + '</div></div>' +
        '<div class="ac"><b>' + (a.cost[0] ? 'AED ' + a.cost[0] : 'Free') + '</b><small>' + (a.cost[0] ? 'per adult' : '&nbsp;') + '</small><span class="added">&#10003; Added</span></div>' +
        (closed ? '<div class="closed">' + closed + '</div>' : '') + '</div>';
    }).join('') || '<p class="muted">Nothing matches that filter.</p>';
    markLib();
  }
  function markLib() { const ids = placedIds(); $$('.acard', el).forEach((c) => c.classList.toggle('in', ids.includes(c.dataset.a))); }

  function renderPlanner() {
    $('#planner').innerHTML = st.days.map((day, d) => {
      const last = d === st.days.length - 1;
      return '<div class="day" style="--dc:' + DAYCOL[d % 6] + '"><div class="dh"><b>Day ' + (d + 1) + '</b><span>' + (d === 0 ? 'Arrival' : last ? 'Departure' : 'Explore') + '</span></div>' +
        day.map((v, s) => {
          const lw = lockedWhy(d, s);
          if (lw) return '<div class="slot lock"><small>' + SLN[SL[s]] + '</small><div>&#9992; ' + lw + '</div></div>';
          if (v && v[0] === '~') return '<div class="slot cont" data-d="' + d + '" data-s="' + s + '"><small>' + SLN[SL[s]] + '</small><div>&#8627; ' + esc(ACT[v.slice(1)].name) + ' (continued)</div><button class="x" data-rm="' + d + ',' + s + '" aria-label="Remove">&times;</button></div>';
          if (v) { const a = ACT[v]; return '<div class="slot full" data-d="' + d + '" data-s="' + s + '" style="--c:' + D.cats[a.cat][1] + '"><small>' + SLN[SL[s]] + '</small><div class="sf"><span>' + a.e + '</span><b>' + esc(a.name) + '</b></div><button class="x" data-rm="' + d + ',' + s + '" aria-label="Remove">&times;</button></div>'; }
          return '<div class="slot empty" data-d="' + d + '" data-s="' + s + '"><small>' + SLN[SL[s]] + '</small><div>Drop here</div></div>';
        }).join('') + '</div>';
    }).join('');
  }

  function renderSide(c) {
    const b = c.b, over = c.price > b.budget, win = c.score >= 75 && !over;
    const pctB = Math.min(130, c.ratio * 100);
    const R = 52, circ = 2 * Math.PI * R;
    const col = c.score >= 85 ? 'var(--ok)' : c.score >= 65 ? 'var(--gold)' : 'var(--rose)';
    $('#dside').innerHTML =
      '<div class="card mapcard">' + mapSVG(c) + '<div class="mleg">' + Object.keys(D.cats).map((k) => '<span><i style="background:' + D.cats[k][1] + '"></i>' + D.cats[k][0] + '</span>').join('') + '<span><i class="star"></i>Hotel</span></div><div class="mnote">Illustrative map. Pins show the day number.</div></div>' +
      '<div class="card scorecard"><div class="ring"><svg viewBox="0 0 120 120"><circle cx="60" cy="60" r="' + R + '" class="trk"/><circle cx="60" cy="60" r="' + R + '" class="val" style="stroke:' + col + ';stroke-dasharray:' + circ.toFixed(1) + ';stroke-dashoffset:' + (circ * (1 - c.score / 100)).toFixed(1) + '"/></svg><div><b id="scoreN" data-v="0">0</b><small>SATISFACTION</small></div></div>' +
        '<div class="parts">' + c.parts.map((p) => '<div class="pt"><span>' + p[0] + '</span><div class="bar"><i style="width:' + Math.round(p[1] / p[2] * 100) + '%;background:' + (p[1] / p[2] > .8 ? 'var(--ok)' : p[1] / p[2] > .5 ? 'var(--gold)' : 'var(--rose)') + '"></i></div><em>' + p[1].toFixed(1) + '/' + p[2] + '</em></div>').join('') + '</div></div>' +
      '<div class="card costcard"><div class="bud"><div class="bt"><span>Selling price vs budget</span><b class="' + (over ? 'bad' : 'good') + '">' + (over ? '+' : '') + Math.round((c.ratio - 1) * 100) + '%</b></div><div class="bbar"><i style="width:' + Math.min(100, pctB / 1.3) + '%;background:' + (over ? 'var(--rose)' : 'var(--ok)') + '"></i><u style="left:' + (100 / 1.3) + '%"></u></div></div>' +
        '<table class="ct">' +
          '<tr><td>Experiences</td><td>' + money(c.cAct) + '</td></tr><tr><td>Hotel (' + b.nights + ' nights x ' + b.rooms + ' room' + (b.rooms > 1 ? 's' : '') + ')</td><td>' + money(c.cHotel) + '</td></tr><tr><td>Transport</td><td>' + money(c.cTr) + '</td></tr><tr><td>UAE visas (' + c.pax + ')</td><td>' + money(c.cVisa) + '</td></tr><tr><td>Airport transfers</td><td>' + money(c.cXfer) + '</td></tr>' +
          '<tr class="sub"><td>Net cost</td><td>AED ' + money(c.cost) + '</td></tr></table>' +
        '<label class="mg"><span>Your margin <b id="mgv">' + st.margin + '%</b></span><input type="range" id="mg" min="8" max="30" value="' + st.margin + '"></label>' +
        '<div class="tot2"><div><small>CLIENT PRICE</small><b id="priceN" data-v="0">0</b></div><div><small>PER PERSON</small><b id="ppN" data-v="0">0</b></div><div><small>YOUR PROFIT</small><b id="profN" class="gld" data-v="0">0</b></div></div></div>' +
      '<div class="card coach"><h4>&#129489;&#8205;&#127979; Coach says</h4><ul>' + (c.tips.length ? c.tips.map((t) => '<li>' + esc(t) + '</li>').join('') : '<li class="good">Flawless plan. Time to send the proposal.</li>') + '</ul>' +
        '<button class="btn gold wide" id="genBtn" ' + (c.placed.length ? '' : 'disabled') + '>' + (win ? '✨ Send proposal to client' : 'Preview proposal') + '</button></div>';
    AA.countTo($('#scoreN'), c.score, { dur: 600 });
    AA.countTo($('#priceN'), Math.round(c.price), { prefix: 'AED ' }); AA.countTo($('#ppN'), Math.round(c.price / c.pax), { prefix: 'AED ' }); AA.countTo($('#profN'), Math.round(c.profit), { prefix: 'AED ' });
    $('#mg').addEventListener('input', (e) => { st.margin = +e.target.value; $('#mgv').textContent = st.margin + '%'; });
    $('#mg').addEventListener('change', update);
    $('#genBtn').addEventListener('click', () => proposal(c));
  }

  function update() {
    renderPlanner();
    $$('.hcard', el).forEach((x) => x.classList.toggle('on', x.dataset.h === st.hotel));
    $$('.tcard', el).forEach((x) => x.classList.toggle('on', x.dataset.t === st.transport));
    markLib();
    const c = compute(); st.last = c; renderSide(c);
  }

  function wire() {
    const left = $('#dleft');
    left.addEventListener('click', (e) => {
      const rm = e.target.closest('[data-rm]'); if (rm) { const [d, s] = rm.dataset.rm.split(',').map(Number); remove(d, s); update(); return; }
      const h = e.target.closest('[data-h]'); if (h) { st.hotel = h.dataset.h; update(); return; }
      const t = e.target.closest('[data-t]'); if (t) { st.transport = t.dataset.t; update(); return; }
      const f = e.target.closest('[data-f]'); if (f) { st.filter = f.dataset.f; $$('#fchips button').forEach((x) => x.classList.toggle('on', x === f)); renderLib(); return; }
      const ac = e.target.closest('.acard');
      if (ac) {
        const a = ACT[ac.dataset.a];
        if (ac.classList.contains('in')) { toast('Already in your plan', 'Remove it from the planner to move it.'); return; }
        const why = !inSeason(a, brief().month) ? 'Closed in ' + MN[brief().month - 1] : a.cost[1] === null && brief().kids ? 'Adults only - this client has children' : '';
        if (why) { toast(a.name, why); return; }
        const o = autoAdd(a);
        if (!o) { toast('No free slot', 'Every suitable slot is taken - free one up first.'); return; }
        update(); const sl = $('.slot[data-d="' + o[0] + '"][data-s="' + o[1] + '"]'); if (sl) { sl.classList.add('flash'); sl.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
      }
    });
    $('#fq').addEventListener('input', (e) => { st.q = e.target.value; renderLib(); });
    /* drag & drop */
    left.addEventListener('dragstart', (e) => {
      const ac = e.target.closest('.acard'); if (!ac) return;
      dragId = ac.dataset.a; e.dataTransfer.setData('text/plain', dragId); e.dataTransfer.effectAllowed = 'copy';
      const a = ACT[dragId]; $$('.slot', el).forEach((sl) => { if (sl.dataset.d === undefined) return; sl.classList.add(canPlace(a, +sl.dataset.d, +sl.dataset.s) ? 'cant' : 'can'); });
    });
    left.addEventListener('dragend', () => { dragId = null; $$('.slot', el).forEach((sl) => sl.classList.remove('can', 'cant', 'over')); });
    left.addEventListener('dragover', (e) => { const sl = e.target.closest('.slot'); if (sl && dragId) { e.preventDefault(); $$('.slot.over', el).forEach((x) => x.classList.remove('over')); sl.classList.add('over'); } });
    left.addEventListener('drop', (e) => {
      const sl = e.target.closest('.slot'); if (!sl || !dragId) return; e.preventDefault();
      const a = ACT[dragId], d = +sl.dataset.d, s = +sl.dataset.s, why = canPlace(a, d, s);
      if (why) { sl.classList.add('shake'); setTimeout(() => sl.classList.remove('shake'), 500); toast(a.name, why); dragId = null; $$('.slot', el).forEach((x) => x.classList.remove('can', 'cant', 'over')); return; }
      place(a, d, s); dragId = null; update();
      const ns = $('.slot[data-d="' + d + '"][data-s="' + s + '"]'); if (ns) ns.classList.add('flash');
    });
  }
  function toast(t, m) { AA.toast(t, m); }

  /* ---------------- proposal ---------------- */
  function proposal(c) {
    const b = c.b, h = HOT[st.hotel], tr = TR[st.transport], over = c.price > b.budget, win = c.score >= 75 && !over;
    const ref = 'AAT-' + (1000 + Math.floor(Math.random() * 9000)), dt = new Date(), until = new Date(Date.now() + 7 * 864e5);
    const f = (d) => d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    let xp = 0, verdict;
    if (win) {
      const key = 'dubai-' + b.id, first = !AA.state.done[key];
      xp = first ? 200 + Math.max(0, c.score - 75) * 4 : 30; AA.state.done[key] = Math.max(AA.state.done[key] || 0, c.score); if (c.score > (AA.state.best.dubai || 0)) AA.state.best.dubai = c.score; AA.save();
      if (!st.won) { st.won = true; AA.addXP(xp, 'Client said YES - ' + b.name); AA.confetti(); if (c.score >= 90) AA.unlock('dubai_pro'); if (st.margin >= 18) AA.unlock('profit'); }
      verdict = '<div class="verdict yes"><b>&#127881; ' + esc(b.name) + ' accepted your proposal!</b><span>Satisfaction ' + c.score + '/100 &middot; margin ' + st.margin + '% &middot; profit AED ' + money(c.profit) + '</span></div>';
    } else {
      verdict = '<div class="verdict no"><b>&#9888; The client wants changes</b><span>' + (over ? 'Price is AED ' + money(c.price - b.budget) + ' over budget. ' : '') + (c.score < 75 ? 'Satisfaction ' + c.score + '/100 - they need 75+. ' : '') + 'Use the coach tips and try again.</span></div>';
    }
    const days = st.days.map((day, d) => {
      const rows = day.map((v, s) => (v && v[0] !== '~') ? '<tr><td>' + SLN[SL[s]] + '</td><td>' + ACT[v].e + ' ' + esc(ACT[v].name) + '</td></tr>' : '').join('') || '<tr><td colspan="2" class="free">Free time</td></tr>';
      return '<div class="pd"><h5>Day ' + (d + 1) + (d === 0 ? ' - Arrival in Dubai' : d === st.days.length - 1 ? ' - Departure' : '') + '</h5><table>' + rows + '</table></div>';
    }).join('');
    const m = document.createElement('div'); m.className = 'modal';
    m.innerHTML = '<div class="modal-box wide"><button class="modal-x noprint">&times;</button>' + '<div class="noprint">' + verdict + '</div>' +
      '<div class="paper"><header><div><b class="ptitle">AKBAR ACADEMY TOURS</b><small>Dubai Experience Proposal</small></div><div class="pref"><b>' + ref + '</b><small>' + f(dt) + ' &middot; valid until ' + f(until) + '</small></div></header>' +
      '<div class="pcl"><div><small>PREPARED FOR</small><b>' + esc(b.name) + '</b><span>' + esc(b.from) + '</span></div><div><small>TRAVELLERS</small><b>' + b.adults + ' adult' + (b.adults > 1 ? 's' : '') + (b.kids ? ' + ' + b.kids + ' children' : '') + '</b><span>' + b.nights + ' nights &middot; ' + MN[b.month - 1] + '</span></div><div><small>STAY</small><b>' + esc(h.name) + '</b><span>' + '★'.repeat(h.tier) + ' &middot; ' + b.rooms + ' room' + (b.rooms > 1 ? 's' : '') + '</span></div><div><small>GETTING AROUND</small><b>' + esc(tr.name) + '</b><span>+ airport transfers</span></div></div>' +
      '<h4>Your itinerary</h4><div class="pdays">' + days + '</div>' +
      '<h4>Included</h4><p class="pinc">' + b.nights + ' nights at ' + esc(h.name) + ' &middot; all listed experiences &middot; ' + esc(tr.name) + ' &middot; return airport transfers &middot; UAE tourist visas for ' + c.pax + ' traveller(s).</p>' +
      '<div class="pprice"><div><small>TOTAL</small><b>AED ' + money(c.price) + '</b></div><div><small>PER PERSON</small><b>AED ' + money(c.price / c.pax) + '</b></div><div><small>DEPOSIT TO CONFIRM (30%)</small><b>AED ' + money(c.price * .3) + '</b></div></div>' +
      '<footer>Illustrative training proposal - prices are simulated. Flights not included. Akbar Academy &middot; onlinecourses.akbaracademy.com</footer></div>' +
      '<div class="row noprint" style="margin-top:16px;justify-content:flex-end"><button class="btn" id="pPrint">&#128424; Print / Save PDF</button><button class="btn gold" id="pClose">Keep editing</button></div></div>';
    document.body.appendChild(m);
    const close = () => m.remove(); $('.modal-x', m).onclick = close; $('#pClose', m).onclick = close; $('#pPrint', m).onclick = () => window.print();
    m.addEventListener('click', (e) => { if (e.target === m) close(); });
  }

  function unmount() { /* nothing to tear down */ }
  AA.register({
    id: 'dubai', title: 'Dubai Studio', tagline: 'Itineraries, costing & client proposals', icon: '🏙', status: 'live', badge: '4 client briefs - 24 experiences', mount: mount, unmount: unmount,
    pitch: 'Plan day-by-day trips on a live map. Balance cost, margin, season, heat and pace to win the client.',
    features: ['Drag & drop planner with live map routes', 'Live costing, margin & profit', 'Client satisfaction engine with a coach', 'Printable branded proposal (PDF)'],
  });
})();
