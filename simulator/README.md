# Akbar Academy - Simulation Lab

Pure HTML/CSS/JS (no build step, no dependencies). Upload the `simulator/` folder to
`onlinecourses.akbaracademy.com/simulator/` (or embed `index.html` in an iframe from your LMS).
Works from any static host and even by double-clicking `index.html`.

## What is inside
| Module | File | What it does |
|---|---|---|
| Command Centre | `js/modules/hub.js` | Animated route map, live clocks, split-flap departure board, XP, achievements |
| GDS Lab | `js/modules/gds.js` | Amadeus / Sabre / Galileo terminals, PNR engine, 3 missions, hints, scoring, e-ticket + boarding pass |
| Dubai Studio | `js/modules/dubai.js`, `js/data/dubai.js` | Drag-and-drop itinerary on a live map, costing, margin, satisfaction engine, printable proposal |
| Roadmap | `js/modules/soon.js` | Visa interview, Cabin crew, Airport check-in, Fare builder (placeholders) |

## Add a new simulator (5 minutes)
1. Create `assets/js/modules/mything.js`:
```js
AA.register({
  id: 'mything', title: 'My Thing', tagline: 'one line', icon: '🛂', status: 'live',
  badge: 'label', pitch: 'card text', features: ['a', 'b', 'c'],
  mount(el) { el.innerHTML = '<h2>Hello</h2>'; },
  unmount() {}
});
```
2. Add `<script src="assets/js/modules/mything.js"></script>` in `index.html` (before `AA.start()`).
3. Use `AA.addXP(n, why)`, `AA.unlock(id)`, `AA.toast()`, `AA.confetti()`, `AA.countTo()`. It appears in the sidebar, hub, departure board and Ctrl+K palette automatically.

## Editing content
- Airports / cities: `assets/js/data/geo.js`
- Dubai activities, hotels, prices, client briefs: `assets/js/data/dubai.js` (all prices are illustrative AED)
- GDS missions: `MISSIONS` array in `gds.js`; new GDS commands: the `parse` function of each dialect + `exec`.

## Notes
- Progress (XP, scores, achievements) is saved in the browser's localStorage.
- GDS entry syntax is adapted from real formats and simplified for training; fares and schedules are simulated.
  Not affiliated with Amadeus, Sabre or Travelport.
