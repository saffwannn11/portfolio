/* GDS Lab - Amadeus / Sabre / Galileo style terminals with a real PNR engine, missions, hints, scoring,
   e-ticket and boarding pass. Training emulator: syntax is adapted from real entries and simplified. */
(function () {
  'use strict';
  const { $, $$, esc, pad } = AA;

  /* ================= flight inventory (deterministic per route+date) ================= */
  const AIRLINES = [['EK','Emirates','176'],['QR','Qatar Airways','157'],['EY','Etihad','607'],['TK','Turkish Airlines','235'],['BA','British Airways','125'],['LH','Lufthansa','220'],['SV','Saudia','065'],['SQ','Singapore Airlines','618'],['AI','Air India','098'],['PK','PIA','214'],['KL','KLM','074'],['AF','Air France','057'],['KQ','Kenya Airways','706'],['QF','Qantas','081']];
  const ACFT = ['388','77W','359','789','320','321','333','351'];
  const CLASSES = ['J','C','Y','B','M','H','K','Q'];
  const MULT = { J: 3.6, C: 2.9, Y: 1.0, B: 0.86, M: 0.74, H: 0.63, K: 0.53, Q: 0.46 };
  const MON = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
  const DOW = ['SU','MO','TU','WE','TH','FR','SA'];

  function rng(str) {
    let h = 1779033703 ^ str.length;
    for (let i = 0; i < str.length; i++) { h = Math.imul(h ^ str.charCodeAt(i), 3432918353); h = (h << 13) | (h >>> 19); }
    return function () {
      h = Math.imul(h ^ (h >>> 16), 2246822507); h = Math.imul(h ^ (h >>> 13), 3266489909); h ^= h >>> 16;
      return (h >>> 0) / 4294967296;
    };
  }
  function parseDate(s) { // 15JUN -> Date (next occurrence)
    const m = /^(\d{1,2})([A-Z]{3})$/.exec(s); if (!m) return null;
    const mo = MON.indexOf(m[2]); if (mo < 0) return null;
    const now = new Date(); now.setHours(0, 0, 0, 0);
    let d = new Date(now.getFullYear(), mo, +m[1]);
    if (d.getMonth() !== mo) return null;
    if (d < now) d = new Date(now.getFullYear() + 1, mo, +m[1]);
    return d;
  }
  const fmtD = (d) => pad(d.getDate()) + MON[d.getMonth()];
  const hhmm = (min) => { min = ((min % 1440) + 1440) % 1440; return pad(Math.floor(min / 60)) + pad(min % 60); };
  const t12 = (min) => { min = ((min % 1440) + 1440) % 1440; let h = Math.floor(min / 60), m = min % 60, ap = h >= 12 ? 'P' : 'A'; h = h % 12 || 12; return h + pad(m) + ap; };
  const baseFare = (km, cls) => Math.max(140, Math.round((km * 0.115 + 90) * MULT[cls] / 5) * 5);

  function genFlights(o, d, date) {
    const key = o.code + d.code + fmtD(date), R = rng(key), km = AA.geo.dist(o, d);
    const dur = Math.round(km / 830 * 60 + 38);
    const n = 5 + Math.floor(R() * 3), out = [], used = {};
    for (let i = 0; i < n; i++) {
      let a; do { a = AIRLINES[Math.floor(R() * AIRLINES.length)]; } while (used[a[0]] && Object.keys(used).length < AIRLINES.length - 1);
      used[a[0]] = 1;
      const dep = Math.floor((20 + R() * 1380) / 5) * 5, span = dur + Math.round((R() - .5) * 20);
      const arrLocal = dep + span + Math.round((d.tz - o.tz) * 60);
      const seats = {};
      CLASSES.forEach((c) => { const base = c === 'J' || c === 'C' ? 4 : 9; seats[c] = R() < (c === 'Q' ? .35 : .12) ? 0 : Math.max(1, Math.min(9, Math.round(base * (0.35 + R())))); });
      out.push({ al: a[0], alName: a[1], num: a[2], no: Math.floor(1 + R() * 899), dep: dep, arr: arrLocal, dayOff: Math.floor(arrLocal / 1440), ac: ACFT[Math.floor(R() * ACFT.length)], seats: seats, o: o, d: d, km: km, dur: span, date: date });
    }
    out.sort((a, b) => a.dep - b.dep);
    return out;
  }

  /* ================= dialects ================= */
  const N = /([A-Z]+)\/([A-Z]+(?: [A-Z]+)*?)(?: (MR|MS|MRS|MISS|MSTR|DR))?$/;
  const DL = {
    amadeus: {
      id: 'amadeus', name: 'Amadeus', sub: 'Selling Platform', office: 'DXBAA0001', theme: 'amadeus', ph: 'Enter command  e.g. AN15JUNDXBLHR',
      parse(t) {
        let m;
        if ((m = /^AN\s*(\d{1,2}[A-Z]{3})\s*([A-Z]{3})\s*([A-Z]{3})$/.exec(t))) return { a: 'avail', date: m[1], o: m[2], d: m[3] };
        if ((m = /^SS\s*(\d)\s*([A-Z])\s*(\d+)$/.exec(t))) return { a: 'sell', n: +m[1], cls: m[2], line: +m[3] };
        if ((m = /^NM\s*\d(.+)$/.exec(t))) return { a: 'name', names: m[1].split(/\s+\d(?=[A-Z]+\/)/).map((s) => s.trim()) };
        if ((m = /^AP\s*(.+)$/.exec(t))) return { a: 'phone', v: m[1] };
        if ((m = /^TK\s*(OK|TL\s*\d{1,2}[A-Z]{3}|XL\s*\d{1,2}[A-Z]{3})$/.exec(t))) return { a: 'tk', v: m[1].replace(/\s/g, '') };
        if ((m = /^RF\s*-?\s*(.+)$/.exec(t))) return { a: 'rf', v: m[1] };
        if (t === 'ER' || t === 'ET') return { a: 'end', redisplay: t === 'ER' };
        if (t === 'RT') return { a: 'redisp' };
        if ((m = /^RT\s*([A-Z0-9]{6})$/.exec(t))) return { a: 'retrieve', loc: m[1] };
        if (t === 'IG') return { a: 'ignore' };
        if ((m = /^XE\s*(\d+)$/.exec(t))) return { a: 'cancel', n: +m[1] };
        if (t === 'FXP') return { a: 'price' };
        if (t === 'TTP') return { a: 'issue' };
        if ((m = /^FQD\s*([A-Z]{3})\s*([A-Z]{3})/.exec(t))) return { a: 'fd', o: m[1], d: m[2] };
        if ((m = /^DAC\s*(.+)$/.exec(t))) return { a: 'encode', v: m[1] };
        if ((m = /^DAN\s*(.+)$/.exec(t))) return { a: 'decode', v: m[1] };
        if (t === 'HE' || t === 'HELP') return { a: 'help' };
        return null;
      },
      sell: (n, c, l) => 'SS' + n + c + l, avail: (d, o, x) => 'AN' + d + o + x, mkName: (p) => 'NM1' + p, phone: 'AP DXB 04 1234567 - A', tk: (dt) => 'TKTL' + dt, rf: 'RF AKBAR', end: 'ER', price: 'FXP', issue: 'TTP', enc: (v) => 'DAC ' + v,
      sheet: [['AN15JUNDXBLHR', 'Availability: date, origin, destination'], ['SS1Y1', 'Sell 1 seat, class Y, line 1'], ['NM1KHAN/ALI MR', 'Add passenger name'], ['AP DXB 04 1234567 - A', 'Phone contact'], ['TKTL14JUN', 'Ticketing time limit'], ['RF AKBAR', 'Received from'], ['ER', 'End transaction + redisplay'], ['RT', 'Redisplay PNR'], ['RTABC123', 'Retrieve by record locator'], ['XE2', 'Cancel element 2'], ['FXP', 'Price the itinerary'], ['TTP', 'Issue e-ticket'], ['FQDDXBLHR', 'Fare display'], ['DAC SINGAPORE', 'Encode city -> code'], ['DAN LHR', 'Decode code -> city'], ['IG', 'Ignore changes']],
      hdr: (s) => ['', 'RP/' + DL.amadeus.office + '/' + DL.amadeus.office + '            AA/SU  ' + now(s) + '   ' + (s.pnr.loc || '------')],
    },
    sabre: {
      id: 'sabre', name: 'Sabre', sub: 'Red App Terminal', office: 'DXBA', theme: 'sabre', ph: 'Enter command  e.g. 115JUNDXBLHR',
      parse(t) {
        let m;
        if ((m = /^1\s*(\d{1,2}[A-Z]{3})\s*([A-Z]{3})\s*([A-Z]{3})$/.exec(t))) return { a: 'avail', date: m[1], o: m[2], d: m[3] };
        if ((m = /^0\s*(\d)\s*([A-Z])\s*(\d+)$/.exec(t))) return { a: 'sell', n: +m[1], cls: m[2], line: +m[3] };
        if ((m = /^-\s*\d?(.+)$/.exec(t))) return { a: 'name', names: [m[1].trim()] };
        if ((m = /^9\s*(.+)$/.exec(t))) return { a: 'phone', v: m[1] };
        if ((m = /^7\s*T\s*(.*)$/.exec(t))) return { a: 'tk', v: m[1] || 'T' };
        if ((m = /^6\s*(.+)$/.exec(t))) return { a: 'rf', v: m[1] };
        if (t === 'E' || t === 'ER') return { a: 'end', redisplay: t === 'ER' };
        if (t === '*A') return { a: 'redisp' };
        if ((m = /^\*\s*([A-Z0-9]{6})$/.exec(t))) return { a: 'retrieve', loc: m[1] };
        if (t === 'I') return { a: 'ignore' };
        if ((m = /^X\s*(\d+)$/.exec(t))) return { a: 'cancel', n: +m[1] };
        if (t === 'WP') return { a: 'price' };
        if (t === 'WETR') return { a: 'issue' };
        if ((m = /^FQ\s*([A-Z]{3})\s*([A-Z]{3})/.exec(t))) return { a: 'fd', o: m[1], d: m[2] };
        if ((m = /^W\/-\s*(.+)$/.exec(t))) return { a: 'encode', v: m[1] };
        if ((m = /^W\/\*\s*(.+)$/.exec(t))) return { a: 'decode', v: m[1] };
        if (t === 'HE' || t === 'HELP') return { a: 'help' };
        return null;
      },
      sell: (n, c, l) => '0' + n + c + l, avail: (d, o, x) => '1' + d + o + x, mkName: (p) => '-' + p, phone: '9DXB04-1234567-A', tk: (dt) => '7TAW/' + dt, rf: '6AKBAR', end: 'ER', price: 'WP', issue: 'WETR', enc: (v) => 'W/-' + v,
      sheet: [['115JUNDXBLHR', 'Availability: 1 + date + from + to'], ['01Y1', 'Sell 1 seat, class Y, line 1'], ['-KHAN/ALI MR', 'Add passenger name'], ['9DXB04-1234567-A', 'Phone field'], ['7TAW/14JUN', 'Ticketing arrangement'], ['6AKBAR', 'Received from'], ['ER', 'End + redisplay'], ['*A', 'Redisplay PNR'], ['*ABC123', 'Retrieve by record locator'], ['X2', 'Cancel line 2'], ['WP', 'Price itinerary'], ['WETR', 'Issue e-ticket'], ['FQDXBLHR', 'Fare quote display'], ['W/-SINGAPORE', 'Encode city -> code'], ['W/*LHR', 'Decode code -> city'], ['I', 'Ignore changes']],
      hdr: (s) => ['', (s.pnr.loc || '------')],
    },
    galileo: {
      id: 'galileo', name: 'Galileo', sub: 'Travelport Focalpoint', office: 'DXB1', theme: 'galileo', ph: 'Enter command  e.g. A15JUNDXBLHR',
      parse(t) {
        let m;
        if ((m = /^A\s*(\d{1,2}[A-Z]{3})\s*([A-Z]{3})\s*([A-Z]{3})$/.exec(t))) return { a: 'avail', date: m[1], o: m[2], d: m[3] };
        if ((m = /^0\s*(\d)\s*([A-Z])\s*(\d+)$/.exec(t))) return { a: 'sell', n: +m[1], cls: m[2], line: +m[3] };
        if ((m = /^N\.\s*(.+)$/.exec(t))) return { a: 'name', names: [m[1].trim()] };
        if ((m = /^P\.\s*(.+)$/.exec(t))) return { a: 'phone', v: m[1] };
        if ((m = /^T\.\s*(.+)$/.exec(t))) return { a: 'tk', v: m[1] };
        if ((m = /^R\.\s*(.+)$/.exec(t))) return { a: 'rf', v: m[1] };
        if (t === 'E' || t === 'ER') return { a: 'end', redisplay: t === 'ER' };
        if (t === '*R') return { a: 'redisp' };
        if ((m = /^\*\s*([A-Z0-9]{6})$/.exec(t))) return { a: 'retrieve', loc: m[1] };
        if (t === 'I') return { a: 'ignore' };
        if ((m = /^X\s*(\d+)$/.exec(t))) return { a: 'cancel', n: +m[1] };
        if (t === 'FQ') return { a: 'price' };
        if (t === 'FT') return { a: 'issue' };
        if ((m = /^FD\s*([A-Z]{3})\s*([A-Z]{3})/.exec(t))) return { a: 'fd', o: m[1], d: m[2] };
        if ((m = /^\.CE\s*(.+)$/.exec(t))) return { a: 'encode', v: m[1] };
        if ((m = /^\.CD\s*(.+)$/.exec(t))) return { a: 'decode', v: m[1] };
        if (t === 'HE' || t === 'HELP') return { a: 'help' };
        return null;
      },
      sell: (n, c, l) => '0' + n + c + l, avail: (d, o, x) => 'A' + d + o + x, mkName: (p) => 'N.' + p, phone: 'P.DXB04-1234567-A', tk: (dt) => 'T.TAU/' + dt, rf: 'R.AKBAR', end: 'ER', price: 'FQ', issue: 'FT', enc: (v) => '.CE ' + v,
      sheet: [['A15JUNDXBLHR', 'Availability: A + date + from + to'], ['01Y1', 'Sell 1 seat, class Y, line 1'], ['N.KHAN/ALI MR', 'Add passenger name'], ['P.DXB04-1234567-A', 'Phone field'], ['T.TAU/14JUN', 'Ticketing arrangement'], ['R.AKBAR', 'Received from'], ['ER', 'End + redisplay'], ['*R', 'Redisplay PNR'], ['*ABC123', 'Retrieve by record locator'], ['X2', 'Cancel line 2'], ['FQ', 'Fare quote / price'], ['FT', 'Issue e-ticket'], ['FDDXBLHR', 'Fare display'], ['.CE SINGAPORE', 'Encode city -> code'], ['.CD LHR', 'Decode code -> city'], ['I', 'Ignore changes']],
      hdr: (s) => ['', (s.pnr.loc || '------') + '/ ' + DL.galileo.office + ' ' + now(s).slice(0, 7)],
    },
  };
  function now() { const d = new Date(); return pad(d.getDate()) + MON[d.getMonth()] + String(d.getFullYear()).slice(2) + '/' + pad(d.getHours()) + pad(d.getMinutes()) + 'Z'; }

  /* ================= missions ================= */
  const PAX = [['KHAN', 'ALI', 'MR'], ['SHAH', 'SARA', 'MS'], ['MALIK', 'OMAR', 'MR'], ['HUSSAIN', 'AYESHA', 'MRS'], ['SIDDIQUI', 'ZAID', 'MR'], ['FERNANDES', 'MARIA', 'MS'], ['PATEL', 'RAJ', 'MR'], ['NASEER', 'LAYLA', 'MS']];
  const MISSIONS = [
    { id: 'm1', level: 'ROOKIE', title: 'First PNR', xp: 150, pax: 1, legs: [['DXB', 'LHR', 14, 'Y']], ticket: false, lookup: false,
      story: 'Walk-in client at the Dubai desk. One adult, economy, Dubai to London, leaving in two weeks. Build a complete PNR.' },
    { id: 'm2', level: 'AGENT', title: 'Honeymoon to Istanbul', xp: 250, pax: 2, legs: [['DXB', 'IST', 21, 'M'], ['IST', 'DXB', 28, 'M']], ticket: false, lookup: false,
      story: 'A newly-married couple wants a return trip to Istanbul in discounted economy (class M). Two names, two segments, one PNR.' },
    { id: 'm3', level: 'SENIOR', title: 'Business to Singapore', xp: 400, pax: 1, legs: [['DXB', 'SIN', 10, 'C']], ticket: true, lookup: true,
      story: 'A corporate client asks for business class to "Singapore" - find the airport code first, build the PNR, price it and issue the e-ticket.' },
  ];

  function missionSpec(mi) {
    const m = MISSIONS[mi], R = rng('akbar' + mi + Math.floor(Date.now() / 3600000));
    const names = []; const pool = PAX.slice();
    for (let i = 0; i < m.pax; i++) names.push(pool.splice(Math.floor(R() * pool.length), 1)[0]);
    const base = new Date(); base.setHours(0, 0, 0, 0);
    const legs = m.legs.map((l) => { const d = new Date(base); d.setDate(d.getDate() + l[2]); return { o: l[0], d: l[1], date: fmtD(d), dateObj: d, cls: l[3] }; });
    return Object.assign({}, m, { names: names, legs: legs });
  }

  /* ================= session ================= */
  function newSession(dl, mi) {
    return { dl: dl, mi: mi, spec: missionSpec(mi), pnr: blank(), saved: {}, lastAvail: null, fare: null, errors: 0, entries: 0, hints: 0, t0: 0, t1: 0, looked: false, done: false, lastHintCmd: '' };
  }
  const blank = () => ({ names: [], segs: [], phones: [], tk: null, rf: null, loc: null, tickets: [], priced: false });
  const loc6 = () => { const c = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; let s = ''; for (let i = 0; i < 6; i++) s += c[Math.floor(Math.random() * c.length)]; return s; };
  const nameKey = (n) => { const m = N.exec(n); return m ? m[1] + '/' + m[2] : n; };
  const pnrCopy = (p) => JSON.parse(JSON.stringify(p), (k, v) => (k === 'dateObj' || k === 'date' ? v : v));

  function segLine(dlid, sg, i) {
    const f = sg.fl, dt = fmtD(f.date), no = String(f.no).padStart(4, ' ');
    if (dlid === 'amadeus') return ' ' + (i + 1) + '  ' + f.al + ' ' + no + ' ' + sg.cls + ' ' + dt + ' ' + (f.date.getDay() + 1) + ' ' + f.o.code + f.d.code + ' HK' + sg.n + '  ' + hhmm(f.dep) + ' ' + hhmm(f.arr) + '   ' + fmtD(new Date(f.date.getTime() + f.dayOff * 864e5)) + '  E  0 ' + f.ac;
    if (dlid === 'sabre') return ' ' + (i + 1) + ' ' + f.al + ' ' + no + sg.cls + ' ' + dt + ' ' + DOW[f.date.getDay()].slice(0, 1) + ' ' + f.o.code + f.d.code + '*HK' + sg.n + '  ' + t12(f.dep).padStart(5) + ' ' + t12(f.arr).padStart(5) + ' /DC' + f.al + ' /E';
    return ' ' + (i + 1) + '. ' + f.al + ' ' + no + ' ' + sg.cls + ' ' + dt + ' ' + f.o.code + f.d.code + ' HK' + sg.n + ' ' + hhmm(f.dep) + ' ' + hhmm(f.arr) + ' ' + f.ac;
  }

  function pnrLines(s) {
    const p = s.pnr, id = s.dl.id, L = [];
    let k = 0;
    if (id === 'amadeus') {
      L.push(...DL.amadeus.hdr(s));
      p.names.forEach((n) => L.push('  ' + (++k) + '.' + n));
      p.segs.forEach((sg, i) => L.push(' ' + (++k) + segLine(id, sg, i).slice(2)));
      p.phones.forEach((ph) => L.push('  ' + (++k) + ' AP ' + ph));
      if (p.tk) L.push('  ' + (++k) + ' TK ' + p.tk + '/' + DL.amadeus.office);
      if (p.rf) L.push('  ' + (++k) + ' RF-' + p.rf);
      p.tickets.forEach((t) => L.push('  ' + (++k) + ' FA PAX ' + t.no + '/ETEK/USD' + t.total.toFixed(2) + '/' + now().slice(0, 7)));
    } else if (id === 'sabre') {
      L.push(...DL.sabre.hdr(s));
      p.names.forEach((n, i) => L.push((i + 1) + '.1' + n));
      p.segs.forEach((sg, i) => L.push(segLine(id, sg, i)));
      if (p.tk) L.push('TKT/TIME LIMIT', '  1.' + p.tk);
      if (p.phones.length) { L.push('PHONES'); p.phones.forEach((ph, i) => L.push('  ' + (i + 1) + ' ' + ph)); }
      if (p.rf) L.push('RECEIVED FROM - ' + p.rf);
      L.push(DL.sabre.office + '.' + DL.sabre.office + '*AA ' + now().slice(8, 12) + '/' + now().slice(0, 7) + ' ' + (p.loc || '------') + ' H');
      p.tickets.forEach((t) => L.push('  ' + t.no + '  ' + t.name + '  ETKT ISSUED'));
    } else {
      L.push(...DL.galileo.hdr(s));
      p.names.forEach((n, i) => L.push(' ' + (i + 1) + '.1' + n));
      p.segs.forEach((sg, i) => L.push(segLine(id, sg, i)));
      p.phones.forEach((ph) => L.push(' PHONE: ' + ph));
      if (p.tk) L.push(' TKT: ' + p.tk);
      if (p.rf) L.push(' RCVD: ' + p.rf);
      p.tickets.forEach((t) => L.push(' ETKT ' + t.no + ' ' + t.name));
    }
    return L;
  }

  /* ---------- fares ---------- */
  function fareFor(s) {
    const pax = s.pnr.names.length, lines = [];
    let fare = 0, tax = 0;
    s.pnr.segs.forEach((sg) => {
      const f = sg.fl, base = baseFare(f.km, sg.cls), yq = Math.round(f.km * 0.021), yr = Math.round(f.km * 0.005);
      const country = f.o.country === 'AE' ? ['E3', 18.5] : f.o.country === 'GB' ? ['GB', 15.5] : ['XT', 12];
      const dest = f.d.country === 'GB' ? ['UB', 22.3] : f.d.country === 'SG' ? ['SG', 24] : ['ZR', 9];
      const sum = yq + yr + country[1] + dest[1];
      fare += base; tax += sum;
      lines.push({ seg: f.o.code + '-' + f.d.code, al: f.al, cls: sg.cls, base: base, taxes: [['YQ', yq], ['YR', yr], country, dest], sum: sum });
    });
    return { pax: pax, fare: fare, tax: tax, total: fare + tax, lines: lines };
  }

  /* ---------- engine ---------- */
  function exec(s, cmd) {
    const dl = s.dl, id = dl.id, a = dl.parse(cmd), out = [], e = (t) => out.push({ t: t, c: 'err' }), ok = (t) => out.push({ t: t || 'OK', c: 'ok' }), L = (t, c) => out.push({ t: t, c: c });
    const res = { out: out, error: false };
    if (!a) { e(id === 'amadeus' ? 'INVALID FORMAT' : id === 'sabre' ? 'FORMAT' : 'INVALID ENTRY'); L('(type HE for help or use the cheat-sheet)', 'dim'); res.error = true; return res; }
    const p = s.pnr;
    switch (a.a) {
      case 'help': L('TRAINING EMULATOR - ' + dl.name.toUpperCase() + ' STYLE ENTRIES', 'hi'); dl.sheet.forEach((r) => L('  ' + r[0].padEnd(24) + r[1], 'dim')); break;
      case 'avail': {
        const o = AA.geo.resolve(a.o), d = AA.geo.resolve(a.d), date = parseDate(a.date);
        if (!o || !d) { e(id === 'amadeus' ? 'INVALID CITY/AIRPORT' : id === 'sabre' ? 'INVALID CITY CODE' : 'INVALID CITY'); L('Tip: encode a city name first', 'dim'); res.error = true; break; }
        if (!date) { e('INVALID DATE'); res.error = true; break; }
        if (o.code === d.code) { e('ORIGIN AND DESTINATION ARE THE SAME'); res.error = true; break; }
        const fl = genFlights(o, d, date); s.lastAvail = { o: o.code, d: d.code, date: fmtD(date), flights: fl, reqO: a.o, reqD: a.d };
        const dw = DOW[date.getDay()];
        if (id === 'amadeus') L('** AMADEUS AVAILABILITY - AN ** ' + d.code + ' ' + d.city.toUpperCase() + '/' + d.name.toUpperCase().slice(0, 14) + '.' + d.country + '   ' + fmtD(date) + '  ' + dw + ' 0000', 'hi');
        else if (id === 'sabre') L(fmtD(date) + ' ' + dw + ' ' + o.code + '/' + d.code + '   ' + o.city.toUpperCase() + ' - ' + d.city.toUpperCase(), 'hi');
        else L('** ' + fmtD(date) + '-' + dw + '  ' + o.code + '-' + d.code + '  ' + d.city.toUpperCase() + ' **', 'hi');
        fl.forEach((f, i) => {
          const cells = CLASSES.map((c) => '<span class="cl' + (f.seats[c] ? '' : ' x') + '" data-cls="' + c + '" data-line="' + (i + 1) + '">' + c + f.seats[c] + '</span>').join(' ');
          const arr = id === 'sabre' ? t12(f.arr).padStart(5) : hhmm(f.arr), dep = id === 'sabre' ? t12(f.dep).padStart(5) : hhmm(f.dep);
          const plus = f.dayOff > 0 ? '+' + f.dayOff : '';
          L({ html: ' ' + (i + 1) + '  ' + f.al + ' ' + String(f.no).padStart(4) + '  ' + cells + '  ' + (id === 'galileo' ? o.code + ' ' + d.code + '  ' : id === 'sabre' ? o.code + d.code + '  ' : o.code + ' ') + dep + (id === 'amadeus' ? '  ' + d.code + ' ' : ' ') + arr + plus + '  ' + f.ac + (id === 'amadeus' ? '  0 E' : ' 0 /E') }, 'avail');
        });
        L('Click a class cell (e.g. Y9) to prefill the sell entry', 'dim');
        break;
      }
      case 'sell': {
        const av = s.lastAvail;
        if (!av) { e(id === 'amadeus' ? 'NEED AVAILABILITY DISPLAY' : 'NO AVAILABILITY'); res.error = true; break; }
        const f = av.flights[a.line - 1];
        if (!f) { e('LINE ' + a.line + ' NOT IN DISPLAY'); res.error = true; break; }
        if (!(a.cls in f.seats)) { e('INVALID BOOKING CLASS ' + a.cls); res.error = true; break; }
        if (f.seats[a.cls] < a.n) { e(f.seats[a.cls] === 0 ? 'NO SEATS - CLASS ' + a.cls + ' CLOSED' : 'ONLY ' + f.seats[a.cls] + ' SEAT(S) AVAILABLE IN ' + a.cls); res.error = true; break; }
        const last = p.segs[p.segs.length - 1];
        if (last && (f.date < last.fl.date)) { e('SEGMENT DATES NOT CHRONOLOGICAL'); res.error = true; break; }
        p.segs.push({ fl: f, cls: a.cls, n: a.n }); p.priced = false;
        ok(id === 'amadeus' ? 'OK' : 'SEGMENT SOLD');
        p.segs.forEach((sg, i) => L(segLine(id, sg, i), 'seg'));
        break;
      }
      case 'name': {
        let bad = false;
        a.names.forEach((n) => {
          if (!N.test(n)) { e('INVALID NAME FORMAT - USE SURNAME/FIRSTNAME TITLE'); bad = true; return; }
          if (p.names.some((x) => nameKey(x) === nameKey(n))) { e('DUPLICATE NAME ' + nameKey(n)); bad = true; return; }
          p.names.push(n);
        });
        if (bad) { res.error = true; break; }
        ok(); p.names.forEach((n, i) => L('  ' + (i + 1) + '.' + n, 'seg'));
        break;
      }
      case 'phone': if (a.v.replace(/\D/g, '').length < 5) { e('INVALID PHONE FIELD'); res.error = true; break; } p.phones.push(a.v); ok(); break;
      case 'tk': p.tk = a.v; ok(); break;
      case 'rf': p.rf = a.v; ok(); break;
      case 'cancel': {
        const k = a.n, nN = p.names.length, nS = p.segs.length, nP = p.phones.length;
        let i = k - 1;
        if (id !== 'amadeus') { if (i < 0 || i >= nS) { e('NOTHING TO CANCEL AT ' + k + ' (CANCELS ITINERARY LINES)'); res.error = true; break; } p.segs.splice(i, 1); }
        else {
          if (i < nN) p.names.splice(i, 1); else if ((i -= nN) < nS) p.segs.splice(i, 1); else if ((i -= nS) < nP) p.phones.splice(i, 1);
          else if ((i -= nP) === 0 && p.tk) p.tk = null; else if (i === (p.tk ? 1 : 0) && p.rf) p.rf = null; else { e('ELEMENT ' + k + ' NOT FOUND'); res.error = true; break; }
        }
        p.priced = false; ok('ELEMENT CANCELLED'); break;
      }
      case 'end': {
        const miss = [];
        if (!p.names.length) miss.push(id === 'amadeus' ? 'NAME' : 'NAME FIELD'); if (!p.segs.length) miss.push('ITINERARY');
        if (!p.phones.length) miss.push(id === 'amadeus' ? 'CONTACT (AP)' : 'PHONE FIELD'); if (!p.tk) miss.push('TICKETING ARRANGEMENT');
        if (!p.rf) miss.push('RECEIVED FROM'); if (p.segs.length && p.segs.some((sg) => sg.n !== p.names.length)) miss.push('SEATS (' + p.segs[0].n + ') = PASSENGERS (' + p.names.length + ')');
        if (miss.length) { e('UNABLE TO END - NEED: ' + miss.join(', ')); res.error = true; break; }
        const upd = !!p.loc; if (!p.loc) p.loc = loc6();
        s.saved[p.loc] = pnrCopy2(p);
        ok(upd ? 'PNR UPDATED' : 'PNR CREATED  RECORD LOCATOR ' + p.loc);
        if (a.redisplay) pnrLines(s).forEach((l) => L(l, 'seg'));
        break;
      }
      case 'redisp': if (!p.names.length && !p.segs.length) { e('NO ACTIVE PNR'); res.error = true; break; } pnrLines(s).forEach((l) => L(l, 'seg')); break;
      case 'retrieve': { const sv = s.saved[a.loc]; if (!sv) { e('RECORD LOCATOR ' + a.loc + ' NOT FOUND'); res.error = true; break; } s.pnr = pnrCopy2(sv); pnrLines(s).forEach((l) => L(l, 'seg')); break; }
      case 'ignore': s.pnr = p.loc && s.saved[p.loc] ? pnrCopy2(s.saved[p.loc]) : blank(); s.lastAvail = null; ok(id === 'amadeus' ? 'IGNORED' : 'IGNORE - NO CHANGES SAVED'); break;
      case 'encode': {
        const r = AA.geo.search(a.v);
        if (!r.length) { e('NO MATCH FOR ' + a.v); res.error = true; break; }
        s.looked = true; r.slice(0, 5).forEach((x) => L('  ' + x.cityCode + (x.cityCode !== x.code ? '/' + x.code : '').padEnd(4) + ' ' + x.city.toUpperCase() + ' - ' + x.name.toUpperCase() + ' ' + x.country, 'seg'));
        break;
      }
      case 'decode': {
        const r = AA.geo.resolve(a.v);
        if (!r) { e('CODE NOT FOUND'); res.error = true; break; }
        s.looked = true; L('  ' + r.code + '  ' + r.city.toUpperCase() + ' - ' + r.name.toUpperCase() + ' ' + r.country, 'seg');
        break;
      }
      case 'fd': {
        const o = AA.geo.resolve(a.o), d = AA.geo.resolve(a.d);
        if (!o || !d) { e('INVALID CITY/AIRPORT'); res.error = true; break; }
        const km = AA.geo.dist(o, d);
        L('FARE DISPLAY  ' + o.code + '-' + d.code + '  (ONE WAY, USD, SIMULATED)', 'hi');
        CLASSES.forEach((c) => L('  ' + c + '   ' + String(baseFare(km, c)).padStart(6) + '.00', 'seg'));
        break;
      }
      case 'price': {
        if (!p.segs.length || !p.names.length) { e('NEED ITINERARY AND NAME BEFORE PRICING'); res.error = true; break; }
        const fr = fareFor(s); s.fare = fr; p.priced = true;
        L('FARE QUOTE - ' + fr.pax + ' PASSENGER(S) ADT   CURRENCY USD (SIMULATED)', 'hi');
        fr.lines.forEach((ln, i) => { L(' ' + (i + 1) + ' ' + ln.seg + ' ' + ln.al + ' CLASS ' + ln.cls + '   FARE ' + ln.base.toFixed(2), 'seg'); L('     TAXES ' + ln.taxes.map((t) => t[0] + ' ' + t[1].toFixed(2)).join('  '), 'dim'); });
        L('PER PAX  FARE ' + fr.fare.toFixed(2) + '  TAX ' + fr.tax.toFixed(2) + '  TOTAL ' + fr.total.toFixed(2), 'ok');
        L('GRAND TOTAL ' + (fr.total * fr.pax).toFixed(2) + ' USD', 'ok');
        break;
      }
      case 'issue': {
        if (!p.loc) { e('NO RECORD LOCATOR - END TRANSACTION FIRST (ER)'); res.error = true; break; }
        if (!p.priced) { e(id === 'amadeus' ? 'NO FARE STORED - PRICE FIRST (FXP)' : 'NO PRICING RECORD - PRICE FIRST'); res.error = true; break; }
        if (p.tickets.length) { e('TICKET ALREADY ISSUED'); res.error = true; break; }
        const fr = s.fare, R = rng(p.loc + 'tkt'), num = p.segs[0].fl.al; const al = AIRLINES.find((x) => x[0] === num);
        p.names.forEach((n) => { let d = ''; for (let i = 0; i < 10; i++) d += Math.floor(R() * 10); p.tickets.push({ no: al[2] + '-' + d, name: nameKey(n), total: fr.total }); });
        s.saved[p.loc] = pnrCopy2(p);
        ok('ETICKET ISSUED  ' + p.tickets.length + ' DOCUMENT(S)'); p.tickets.forEach((t) => L('  ' + t.no + '  ' + t.name, 'seg'));
        break;
      }
    }
    return res;
  }
  function pnrCopy2(p) { // keep Date objects intact
    const c = JSON.parse(JSON.stringify(p, (k, v) => (k === 'fl' ? undefined : v)));
    c.segs = p.segs.map((sg) => ({ fl: sg.fl, cls: sg.cls, n: sg.n }));
    return c;
  }

  /* ---------- mission evaluation ---------- */
  function checks(s) {
    const sp = s.spec, p = s.pnr, c = [];
    if (sp.lookup) c.push(['Find the airport code (encode/decode)', s.looked]);
    c.push(['Passenger name(s): ' + sp.names.map((n) => n[0] + '/' + n[1]).join(' & '), sp.names.every((n) => p.names.some((x) => nameKey(x) === n[0] + '/' + n[1])) && p.names.length === sp.names.length]);
    sp.legs.forEach((l, i) => { const sg = p.segs[i]; c.push(['Segment ' + (i + 1) + ': ' + l.o + '-' + l.d + ' ' + l.date + ' class ' + l.cls + ' x' + sp.pax, !!sg && sg.fl.o.code === l.o && sg.fl.d.code === l.d && fmtD(sg.fl.date) === l.date && sg.cls === l.cls && sg.n === sp.pax]); });
    c.push(['Phone contact', p.phones.length > 0]); c.push(['Ticketing arrangement', !!p.tk]); c.push(['Received from', !!p.rf]);
    c.push(['End transaction (record locator)', !!p.loc]);
    if (sp.ticket) { c.push(['Price the itinerary', p.priced]); c.push(['Issue e-ticket', p.tickets.length === sp.pax]); }
    return c;
  }
  function nextHint(s) {
    const dl = s.dl, sp = s.spec, p = s.pnr;
    if (sp.lookup && !s.looked) return { cmd: dl.enc('SINGAPORE'), why: 'Find the code for Singapore first.' };
    const need = sp.legs[p.segs.length];
    if (need && p.segs.length < sp.legs.length) {
      const av = s.lastAvail;
      if (!av || av.o !== need.o || av.d !== need.d || av.date !== need.date) return { cmd: dl.avail(need.date, need.o, need.d), why: 'Check availability for leg ' + (p.segs.length + 1) + '.' };
      const i = av.flights.findIndex((f) => f.seats[need.cls] >= sp.pax);
      if (i < 0) return { cmd: dl.avail(need.date, need.o, need.d), why: 'No seats - try again.' };
      return { cmd: dl.sell(sp.pax, need.cls, i + 1), why: 'Sell class ' + need.cls + ' on line ' + (i + 1) + '.' };
    }
    for (const n of sp.names) if (!p.names.some((x) => nameKey(x) === n[0] + '/' + n[1])) return { cmd: dl.mkName(n[0] + '/' + n[1] + ' ' + n[2]), why: 'Add the passenger name.' };
    if (!p.phones.length) return { cmd: dl.phone, why: 'A contact phone is mandatory.' };
    if (!p.tk) { const d = new Date(); d.setDate(d.getDate() + 3); return { cmd: dl.tk(fmtD(d)), why: 'Set a ticketing time limit.' }; }
    if (!p.rf) return { cmd: dl.rf, why: 'Say who made the booking.' };
    if (!p.loc) return { cmd: dl.end, why: 'Save the booking.' };
    if (sp.ticket && !p.priced) return { cmd: dl.price, why: 'Price before ticketing.' };
    if (sp.ticket && !p.tickets.length) return { cmd: dl.issue, why: 'Issue the e-ticket.' };
    return null;
  }

  /* ================= UI ================= */
  let S, ui, timer, hist = [], hi = 0, dialect = 'amadeus', mIdx = 0;

  function mount(el) {
    S = newSession(DL[dialect], mIdx);
    el.innerHTML =
      '<div class="gds">' +
        '<div class="gds-head"><div><span class="eyebrow">GDS Lab &middot; Live Terminal</span><h2>Reservation desk</h2></div>' +
        '<div class="seg-tabs" id="dtabs">' + Object.keys(DL).map((k) => '<button data-d="' + k + '" class="' + (k === dialect ? 'on' : '') + '">' + DL[k].name + '</button>').join('') + '</div></div>' +
        '<div class="gds-grid">' +
          '<section class="termwrap th-' + dialect + '" id="termwrap">' +
            '<div class="tbar"><i></i><i></i><i></i><b id="ttitle"></b><span id="tclock">00:00</span></div>' +
            '<div class="screen" id="screen"></div>' +
            '<div class="hintbar" id="hintbar"></div>' +
            '<form class="cmdline" id="cmdform" autocomplete="off"><span class="pr">&gt;</span><input id="cmd" spellcheck="false" autocapitalize="characters" autocomplete="off"><button type="submit" class="send" aria-label="Send">&#9166;</button></form>' +
            '<div class="tstat"><span id="sEnt">ENTRIES 0</span><span id="sErr">ERRORS 0</span><span id="sHint">HINTS 0</span><span class="grow"></span><span id="sOff"></span></div>' +
          '</section>' +
          '<aside class="gside">' +
            '<div class="card mpick"><div class="mtabs" id="mtabs">' + MISSIONS.map((m, i) => '<button data-m="' + i + '" class="' + (i === mIdx ? 'on' : '') + (AA.state.done['gds-' + m.id] ? ' done' : '') + '">' + (i + 1) + '</button>').join('') + '</div><div id="brief"></div></div>' +
            '<div class="card chk"><h4>Mission checklist</h4><ul id="checks"></ul><div class="row"><button class="btn sm" id="hintBtn">&#128161; Hint (-75)</button><button class="btn sm" id="resetBtn">Restart</button><button class="btn sm gold" id="tktBtn" disabled>E-ticket</button></div></div>' +
            '<div class="card cheat"><h4>Cheat-sheet <small id="chTitle"></small></h4><div id="sheet"></div></div>' +
          '</aside>' +
        '</div>' +
        '<p class="fine">Training emulator. Entry syntax is adapted from real GDS formats and simplified for learning; fares and schedules are simulated. Not affiliated with Amadeus, Sabre or Travelport.</p>' +
      '</div>';
    ui = { screen: $('#screen'), cmd: $('#cmd'), form: $('#cmdform') };
    $('#dtabs').addEventListener('click', (e) => { const b = e.target.closest('button'); if (b) { dialect = b.dataset.d; restart(); } });
    $('#mtabs').addEventListener('click', (e) => { const b = e.target.closest('button'); if (b) { mIdx = +b.dataset.m; restart(); } });
    $('#hintBtn').addEventListener('click', hint);
    $('#resetBtn').addEventListener('click', restart);
    $('#tktBtn').addEventListener('click', showTicket);
    ui.form.addEventListener('submit', (e) => { e.preventDefault(); submit(); });
    ui.cmd.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowUp') { hi = Math.max(0, hi - 1); ui.cmd.value = hist[hi] || ''; e.preventDefault(); }
      else if (e.key === 'ArrowDown') { hi = Math.min(hist.length, hi + 1); ui.cmd.value = hist[hi] || ''; e.preventDefault(); }
    });
    $('#termwrap').addEventListener('click', (e) => {
      const c = e.target.closest('.cl');
      if (c && !c.classList.contains('x')) { ui.cmd.value = S.dl.sell(S.spec.pax, c.dataset.cls, c.dataset.line); }
      if (!window.getSelection().toString()) ui.cmd.focus();
    });
    $('#sheet').addEventListener('click', (e) => { const r = e.target.closest('[data-t]'); if (r) { ui.cmd.value = r.dataset.t; ui.cmd.focus(); } });
    boot();
  }

  function restart() {
    clearInterval(timer);
    const el = $('#stage'); mount(el);
  }

  function boot() {
    const d = S.dl;
    $('#ttitle').textContent = d.name.toUpperCase() + ' ' + d.sub.toUpperCase() + '  -  ' + d.office + '  -  SIGNED IN';
    $('#termwrap').className = 'termwrap th-' + d.theme;
    $('#chTitle').textContent = d.name;
    $('#sheet').innerHTML = d.sheet.map((r) => '<div class="srow" data-t="' + esc(r[0]) + '"><code>' + esc(r[0]) + '</code><span>' + esc(r[1]) + '</span></div>').join('');
    ui.cmd.placeholder = d.ph;
    $('#sOff').textContent = 'OFFICE ' + d.office;
    print('** ' + d.name.toUpperCase() + ' ' + d.sub.toUpperCase() + ' **', 'hi'); print('SIGN-IN ACCEPTED  OFFICE ' + d.office + '  AGENT AA/SU', 'dim');
    print('MISSION ' + (mIdx + 1) + ': ' + S.spec.title.toUpperCase() + ' - TYPE HE FOR HELP OR USE HINT', 'dim'); print('');
    refresh();
    setTimeout(() => ui.cmd.focus(), 50);
  }

  function print(t, c) {
    const line = document.createElement('div'); line.className = 'ln ' + (c || '');
    if (t && t.html) line.innerHTML = t.html; else line.textContent = t;
    ui.screen.appendChild(line);
    ui.screen.scrollTop = ui.screen.scrollHeight;
  }

  function submit() {
    if (S.done) return;
    const raw = ui.cmd.value.trim().toUpperCase(); if (!raw) return;
    ui.cmd.value = ''; hist.push(raw); hi = hist.length;
    if (!S.t0) { S.t0 = Date.now(); timer = setInterval(tick, 500); }
    print('> ' + raw, 'echo'); S.entries++;
    let r; try { r = exec(S, raw); } catch (err) { console.error(err); r = { out: [{ t: 'SYSTEM ERROR', c: 'err' }], error: true }; }
    if (r.error) S.errors++;
    r.out.forEach((o, i) => setTimeout(() => print(o.t, o.c), i * 22));
    setTimeout(() => { refresh(); complete(); }, r.out.length * 22 + 30);
  }

  function tick() {
    const sec = Math.floor((Date.now() - S.t0) / 1000);
    const el = $('#tclock'); if (el) el.textContent = pad(Math.floor(sec / 60)) + ':' + pad(sec % 60);
  }

  function refresh() {
    const c = checks(S);
    $('#checks').innerHTML = c.map((x) => '<li class="' + (x[1] ? 'ok' : '') + '"><i></i><span>' + esc(x[0]) + '</span></li>').join('');
    $('#sEnt').textContent = 'ENTRIES ' + S.entries; $('#sErr').textContent = 'ERRORS ' + S.errors; $('#sHint').textContent = 'HINTS ' + S.hints;
    $('#tktBtn').disabled = !S.pnr.tickets.length;
    const sp = S.spec;
    $('#brief').innerHTML = '<span class="lvl-tag">' + sp.level + ' &middot; ' + sp.xp + ' XP</span><h3>' + esc(sp.title) + '</h3><p>' + esc(sp.story) + '</p>' +
      '<div class="bf">' + sp.names.map((n) => '<span class="chip">&#128100; ' + n[0] + '/' + n[1] + ' ' + n[2] + '</span>').join('') + sp.legs.map((l) => '<span class="chip">&#9992; ' + l.o + '&rarr;' + l.d + ' &middot; ' + l.date + ' &middot; ' + l.cls + '</span>').join('') + (sp.ticket ? '<span class="chip">&#127915; issue ticket</span>' : '') + '</div>';
  }

  function hint() {
    if (S.done) return;
    const h = nextHint(S); if (!h) return;
    S.hints++; refresh();
    const bar = $('#hintbar');
    bar.innerHTML = '<span>&#128161; ' + esc(h.why) + '</span><code>' + esc(h.cmd) + '</code><button class="btn sm" type="button">Type it</button>';
    bar.classList.add('show');
    $('button', bar).addEventListener('click', () => { ui.cmd.value = h.cmd; ui.cmd.focus(); bar.classList.remove('show'); });
  }

  function complete() {
    if (S.done) return;
    if (!checks(S).every((x) => x[1])) return;
    S.done = true; S.t1 = Date.now(); clearInterval(timer);
    const secs = Math.round((S.t1 - S.t0) / 1000);
    const score = Math.max(100, Math.round(1000 - Math.max(0, secs - 60) * 2 - S.errors * 25 - S.hints * 75));
    const stars = score >= 850 ? 3 : score >= 600 ? 2 : 1;
    const key = 'gds-' + S.spec.id, first = !AA.state.done[key];
    AA.state.done[key] = Math.max(AA.state.done[key] || 0, stars);
    if (score > (AA.state.best.gds || 0)) AA.state.best.gds = score;
    AA.state.done['gds-sys-' + S.dl.id] = 1; AA.save();
    const xp = first ? Math.round(S.spec.xp * stars / 3) : Math.round(S.spec.xp * 0.15);
    AA.addXP(xp, S.spec.title + ' - ' + stars + ' star' + (stars > 1 ? 's' : ''));
    AA.unlock('first_pnr'); if (S.spec.ticket) AA.unlock('ticketed'); if (!S.errors) AA.unlock('clean'); if (secs < 90) AA.unlock('speed');
    if (['amadeus', 'sabre', 'galileo'].every((k) => AA.state.done['gds-sys-' + k])) AA.unlock('all_gds');
    AA.confetti();
    const next = MISSIONS[mIdx + 1];
    const m = document.createElement('div'); m.className = 'modal';
    m.innerHTML = '<div class="modal-box done-box"><button class="modal-x">&times;</button><span class="eyebrow">Mission complete</span><h2>' + esc(S.spec.title) + '</h2>' +
      '<div class="stars">' + [1, 2, 3].map((i) => '<span class="' + (i <= stars ? 'on' : '') + '" style="animation-delay:' + (i * .25) + 's">&#9733;</span>').join('') + '</div>' +
      '<div class="res"><div><small>SCORE</small><b>' + score + '</b></div><div><small>TIME</small><b>' + Math.floor(secs / 60) + ':' + pad(secs % 60) + '</b></div><div><small>ERRORS</small><b>' + S.errors + '</b></div><div><small>HINTS</small><b>' + S.hints + '</b></div><div><small>XP</small><b>+' + xp + '</b></div></div>' +
      '<p class="muted">Record locator <b class="mono" style="color:var(--gold)">' + S.pnr.loc + '</b> &middot; ' + S.dl.name + ' &middot; ' + S.pnr.segs.length + ' segment(s)</p>' +
      '<div class="row">' + (S.pnr.tickets.length ? '<button class="btn gold" id="mTkt">View e-ticket &amp; boarding pass</button>' : '') + (next ? '<button class="btn" id="mNext">Next mission &rarr;</button>' : '') + '<button class="btn" id="mAgain">Try another system</button></div></div>';
    document.body.appendChild(m);
    const close = () => m.remove();
    $('.modal-x', m).onclick = close; m.addEventListener('click', (e) => { if (e.target === m) close(); });
    const t = $('#mTkt', m); if (t) t.onclick = () => { close(); showTicket(); };
    const n = $('#mNext', m); if (n) n.onclick = () => { close(); mIdx++; restart(); };
    $('#mAgain', m).onclick = () => { close(); dialect = dialect === 'amadeus' ? 'sabre' : dialect === 'sabre' ? 'galileo' : 'amadeus'; restart(); };
  }

  /* ---------- e-ticket + boarding pass ---------- */
  function barcode(seed) {
    const R = rng(seed); let x = 0, out = '';
    while (x < 196) { const w = 1 + Math.floor(R() * 3); if (R() > .38) out += '<rect x="' + x + '" y="0" width="' + w + '" height="44"/>'; x += w + 1; }
    return '<svg viewBox="0 0 200 44" preserveAspectRatio="none">' + out + '</svg>';
  }
  function showTicket() {
    const p = S.pnr; if (!p.tickets.length) return;
    const sg = p.segs[0], f = sg.fl, R = rng(p.loc), gate = 'ABCDEF'[Math.floor(R() * 6)] + (1 + Math.floor(R() * 40)), seat = (8 + Math.floor(R() * 40)) + 'ABCDEFGHK'[Math.floor(R() * 9)];
    const boards = hhmm(f.dep - 45);
    const m = document.createElement('div'); m.className = 'modal';
    m.innerHTML = '<div class="modal-box"><button class="modal-x">&times;</button><span class="eyebrow">E-ticket issued &middot; ' + S.dl.name + '</span><h2>Boarding pass</h2>' +
      p.tickets.map((t, i) => {
        const nm = t.name.replace('/', ' / ');
        return '<div class="bpass" style="animation-delay:' + i * .2 + 's"><div class="bp-main"><div class="bp-top"><b>' + esc(f.alName.toUpperCase()) + '</b><span>' + (sg.cls === 'C' || sg.cls === 'J' ? 'BUSINESS' : 'ECONOMY') + '</span></div>' +
          '<div class="bp-route"><div><small>FROM</small><b>' + f.o.code + '</b><em>' + esc(f.o.city) + '</em></div><div class="bp-plane">&#9992;</div><div><small>TO</small><b>' + f.d.code + '</b><em>' + esc(f.d.city) + '</em></div></div>' +
          '<div class="bp-grid"><div><small>PASSENGER</small><b>' + esc(nm) + '</b></div><div><small>FLIGHT</small><b>' + f.al + String(f.no).padStart(3, '0') + '</b></div><div><small>DATE</small><b>' + fmtD(f.date) + '</b></div><div><small>BOARDS</small><b>' + boards + '</b></div><div><small>GATE</small><b>' + gate + '</b></div><div><small>SEAT</small><b>' + seat + '</b></div></div></div>' +
          '<div class="bp-stub"><small>PNR</small><b class="mono">' + p.loc + '</b><small>E-TICKET</small><b class="mono t">' + t.no + '</b><div class="bc">' + barcode(t.no) + '</div></div></div>';
      }).join('') +
      '<p class="muted" style="font-size:12.5px">Fare ' + S.fare.fare.toFixed(2) + ' + taxes ' + S.fare.tax.toFixed(2) + ' = <b>USD ' + S.fare.total.toFixed(2) + '</b> per passenger (simulated).</p></div>';
    document.body.appendChild(m);
    const close = () => m.remove(); $('.modal-x', m).onclick = close; m.addEventListener('click', (e) => { if (e.target === m) close(); });
  }

  function unmount() { clearInterval(timer); }
  AA.register({
    id: 'gds', title: 'GDS Lab', tagline: 'Amadeus - Sabre - Galileo terminals', icon: '⌨', status: 'live', badge: '3 systems - 3 missions', mount: mount, unmount: unmount,
    pitch: 'Three working reservation terminals with a real PNR engine - availability, selling, names, ticketing and e-tickets.',
    features: ['Amadeus, Sabre & Galileo entry styles', 'Guided missions with live checklist', 'Fare quote, ticket issue & boarding pass', 'Scored on speed, accuracy and hints'],
  });
})();
