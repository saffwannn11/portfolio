/* Roadmap modules. Each one is a placeholder that shows how little code a new simulator needs:
   copy this block, change the fields, and replace mount() with the real thing (see README.md). */
(function () {
  'use strict';
  const SOON = [
    { id: 'visa', title: 'Visa Interview', tagline: 'AI examiner & document check', icon: '🛂', badge: 'Planned',
      pitch: 'Face a tough consular officer, defend your documents and learn the refusal reasons before the real day.',
      features: ['Branching interview with scoring', 'Document checklist builder', 'Refusal letter analysis'] },
    { id: 'cabin', title: 'Cabin Crew Drill', tagline: 'Emergencies & service scenarios', icon: '🧑‍✈️', badge: 'Planned',
      pitch: 'Decision-tree scenarios for turbulence, medical events and difficult passengers - timed and scored.',
      features: ['Interactive cabin map', 'Emergency commands', 'Grooming & etiquette checks'] },
    { id: 'airport', title: 'Airport Check-in', tagline: 'Counter, baggage & boarding', icon: '🧳', badge: 'Planned',
      pitch: 'Run a check-in counter: weigh bags, handle special passengers and close the flight on time.',
      features: ['Baggage weight & fees', 'Seat map allocation', 'Boarding gate rush'] },
    { id: 'fares', title: 'Fare Builder', tagline: 'Rules, taxes & refunds', icon: '🧮', badge: 'Planned',
      pitch: 'Construct international fares, apply fare rules and calculate change and refund penalties.',
      features: ['Fare construction (NUC/ROE)', 'Tax breakdown', 'Refund calculator'] },
  ];
  SOON.forEach(function (m) {
    AA.register(Object.assign({}, m, {
      status: 'soon',
      mount: function (el) {
        el.innerHTML =
          '<section class="soon-page card"><span class="eyebrow">Coming soon</span><div class="soon-ico">' + m.icon + '</div><h2>' + AA.esc(m.title) + '</h2><p>' + AA.esc(m.pitch) + '</p>' +
          '<ul>' + m.features.map(function (f) { return '<li>' + AA.esc(f) + '</li>'; }).join('') + '</ul>' +
          '<div class="row" style="justify-content:center"><a class="btn gold" href="#/gds">Try the GDS Lab</a><a class="btn" href="#/dubai">Open Dubai Studio</a></div></section>';
      },
      unmount: function () {},
    }));
  });
})();
