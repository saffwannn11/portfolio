/* Dubai Studio content. ALL PRICES ARE ILLUSTRATIVE (AED) - edit freely to match your suppliers.
   Add an activity, hotel or client brief here and the studio picks it up automatically.
   slots: M = morning, A = afternoon, E = evening.  best = ideal slots, ok = allowed slots. */
(function () {
  window.AA = window.AA || {};
  AA.dubai = {
    cats: { sight: ['Landmarks', '#6ea8ff'], adv: ['Adventure', '#ff8a5c'], cult: ['Culture', '#c58bff'], food: ['Dining', '#ffcf5c'], shop: ['Shopping', '#ff6fb1'], rel: ['Relax & Beach', '#4fd1c5'], trip: ['Day trip', '#8cf08c'] },
    map: { lon0: 54.95, lon1: 55.6, lat0: 24.85, lat1: 25.4, w: 1000, h: 930 },
    activities: [
      { id: 'burj', name: 'At the Top, Burj Khalifa', cat: 'sight', e: '🏙', cost: [169, 129], tags: ['photo', 'romantic', 'family', 'luxury'], best: 'E', ok: 'MAE', lon: 55.2744, lat: 25.1972, desc: 'Level 124 observation deck. Sunset slots sell out - book early.' },
      { id: 'fountain', name: 'Dubai Fountain Show', cat: 'sight', e: '⛲', cost: [0, 0], tags: ['romantic', 'photo', 'family', 'budget'], best: 'E', ok: 'E', lon: 55.2796, lat: 25.1955, desc: 'Free choreographed fountain every 30 min after sunset.' },
      { id: 'aquarium', name: 'Dubai Aquarium & Underwater Zoo', cat: 'sight', e: '🦈', cost: [175, 140], tags: ['family', 'kids'], best: 'MA', ok: 'MAE', lon: 55.2802, lat: 25.1978, desc: 'Walk-through tunnel with sharks and rays inside Dubai Mall.' },
      { id: 'mall', name: 'Dubai Mall & Fashion Avenue', cat: 'shop', e: '🛍', cost: [0, 0], tags: ['shopping', 'luxury', 'family'], best: 'AE', ok: 'AE', indoor: true, lon: 55.2796, lat: 25.1985, desc: 'World\'s most visited mall. Indoor - perfect for hot days.' },
      { id: 'frame', name: 'Dubai Frame', cat: 'sight', e: '🖼', cost: [50, 20], tags: ['photo', 'culture', 'family', 'budget'], best: 'M', ok: 'MA', lon: 55.3008, lat: 25.2355, desc: 'Old Dubai on one side, new Dubai on the other.' },
      { id: 'future', name: 'Museum of the Future', cat: 'cult', e: '🔮', cost: [149, 99], tags: ['culture', 'photo', 'family', 'tech'], best: 'A', ok: 'MAE', indoor: true, lon: 55.2819, lat: 25.2195, desc: 'Immersive exhibits inside the most beautiful building on Earth.' },
      { id: 'souks', name: 'Old Dubai: Souks & Abra Ride', cat: 'cult', e: '⛵', cost: [20, 20], tags: ['culture', 'shopping', 'food', 'budget', 'photo'], best: 'M', ok: 'MAE', outdoor: true, lon: 55.2972, lat: 25.2635, desc: 'Gold & spice souks, Al Fahidi alleys, abra over the Creek.' },
      { id: 'tea', name: 'Emirati Lunch - Arabian Tea House', cat: 'food', e: '🍵', cost: [120, 70], tags: ['food', 'culture', 'budget'], best: 'M', ok: 'MA', lon: 55.2985, lat: 25.2638, desc: 'Courtyard dining in Al Fahidi. Try the machboos.' },
      { id: 'safari', name: 'Premium Desert Safari + BBQ', cat: 'adv', e: '🏜', cost: [220, 180], tags: ['adventure', 'family', 'food', 'culture'], best: 'E', ok: 'E', heavy: 2, lon: 55.5, lat: 24.95, desc: 'Dune bashing, camel ride, henna and BBQ under the stars. Pick-up 3:30 PM.' },
      { id: 'dhow', name: 'Dhow Cruise Dinner - Marina', cat: 'food', e: '⛴', cost: [150, 100], tags: ['romantic', 'food', 'photo'], best: 'E', ok: 'E', lon: 55.1385, lat: 25.0795, desc: 'Traditional wooden dhow past the Marina skyline.' },
      { id: 'aqua', name: 'Atlantis Aquaventure Waterpark', cat: 'adv', e: '🎢', cost: [340, 290], tags: ['family', 'adventure', 'beach', 'kids'], best: 'M', ok: 'MA', heavy: 2, outdoor: true, lon: 55.1171, lat: 25.1304, desc: 'Record-breaking slides on Palm Jumeirah. A full day for the whole family.' },
      { id: 'ski', name: 'Ski Dubai - Snow Park', cat: 'adv', e: '⛷', cost: [280, 280], tags: ['family', 'adventure', 'kids'], best: 'AE', ok: 'MAE', indoor: true, lon: 55.2005, lat: 25.1181, desc: 'Real snow at +40C outside. Mall of the Emirates.' },
      { id: 'miracle', name: 'Dubai Miracle Garden', cat: 'sight', e: '🌸', cost: [100, 85], tags: ['photo', 'romantic', 'family'], best: 'A', ok: 'AE', outdoor: true, season: [10, 5], lon: 55.2446, lat: 25.0597, desc: 'Millions of flowers. Open October to May only.' },
      { id: 'global', name: 'Global Village', cat: 'shop', e: '🎪', cost: [25, 25], tags: ['family', 'shopping', 'food', 'culture', 'budget'], best: 'E', ok: 'E', season: [10, 4], outdoor: true, lon: 55.3078, lat: 25.0692, desc: 'Pavilions from 90 countries. Open October to April only.' },
      { id: 'marina', name: 'Marina Walk & JBR Beach', cat: 'rel', e: '🌅', cost: [0, 0], tags: ['beach', 'food', 'budget', 'photo', 'family'], best: 'E', ok: 'MAE', outdoor: true, lon: 55.1403, lat: 25.0805, desc: 'Promenade, cafes and the free-to-use beach.' },
      { id: 'kite', name: 'Kite Beach & Jumeirah Beach Day', cat: 'rel', e: '🏖', cost: [0, 0], tags: ['beach', 'budget', 'family', 'adventure'], best: 'MA', ok: 'MAE', outdoor: true, lon: 55.2094, lat: 25.1594, desc: 'Burj Al Arab view, watersports and relaxed cafes.' },
      { id: 'baa', name: 'Madinat Jumeirah & Burj Al Arab view', cat: 'sight', e: '⛵', cost: [0, 0], tags: ['photo', 'romantic', 'luxury', 'shopping'], best: 'E', ok: 'MAE', lon: 55.1853, lat: 25.1412, desc: 'Venice-style waterways and a postcard view of the sail.' },
      { id: 'atmos', name: 'Sunset Fine Dining - At.mosphere', cat: 'food', e: '🍾', cost: [650, 350], tags: ['luxury', 'romantic', 'food'], best: 'E', ok: 'E', lon: 55.2744, lat: 25.1972, desc: 'Dinner 122 floors up in the Burj Khalifa.' },
      { id: 'abudhabi', name: 'Abu Dhabi Day Trip: Grand Mosque + Louvre', cat: 'trip', e: '🕌', cost: [380, 250], tags: ['culture', 'photo', 'family'], best: 'M', ok: 'M', day: true, lon: 54.4, lat: 24.42, desc: 'Takes the whole day (about 90 minutes each way).' },
      { id: 'heli', name: 'Helicopter Tour - Palm & Burj', cat: 'adv', e: '🚁', cost: [1100, 1100], tags: ['luxury', 'romantic', 'photo', 'adventure'], best: 'A', ok: 'MA', lon: 55.2, lat: 25.18, desc: '12-minute scenic flight from the Heliport.' },
      { id: 'spa', name: 'Arabian Hammam Spa', cat: 'rel', e: '💆', cost: [450, null], tags: ['romantic', 'luxury', 'relax'], best: 'A', ok: 'AE', indoor: true, lon: 55.1853, lat: 25.1418, desc: 'Couples ritual. Adults only.' },
      { id: 'parks', name: 'Legoland / Motiongate Dubai', cat: 'adv', e: '🎠', cost: [260, 260], tags: ['family', 'kids', 'adventure'], best: 'M', ok: 'MA', heavy: 2, outdoor: true, lon: 55.0107, lat: 24.9, desc: 'Theme parks for the younger crowd - full day.' },
      { id: 'balloon', name: 'Sunrise Hot Air Balloon', cat: 'adv', e: '🎈', cost: [1350, 1350], tags: ['adventure', 'romantic', 'photo', 'luxury'], best: 'M', ok: 'M', heavy: 1, lon: 55.52, lat: 24.9, desc: 'Floats over the dunes at dawn. Includes breakfast.' },
      { id: 'street', name: 'Old Dubai Street-Food Tour', cat: 'food', e: '🍢', cost: [150, 100], tags: ['food', 'culture', 'budget'], best: 'E', ok: 'AE', lon: 55.3115, lat: 25.2518, desc: 'Karama and Deira bites with a local guide.' },
      { id: 'quad', name: 'Morning Dune Bash & Quad Bikes', cat: 'adv', e: '🏍', cost: [250, 200], tags: ['adventure', 'budget'], best: 'M', ok: 'M', heavy: 1, outdoor: true, lon: 55.45, lat: 24.98, desc: 'Adrenaline at sunrise before the heat arrives.' },
    ],
    hotels: [
      { id: 'citymax', name: 'Citymax Bur Dubai', tier: 3, rate: 260, tags: ['budget'], lon: 55.3, lat: 25.255, note: 'Clean, central and cheap.' },
      { id: 'rove', name: 'Rove Downtown', tier: 3, rate: 430, tags: ['budget', 'central'], lon: 55.2745, lat: 25.19, note: 'Walk to Dubai Mall and the Fountain.' },
      { id: 'jbh', name: 'Jumeirah Beach Hotel', tier: 4, rate: 1050, tags: ['family', 'beach'], lon: 55.1875, lat: 25.1415, note: 'Beachfront, kids\' club, Burj Al Arab views.' },
      { id: 'atlantis', name: 'Atlantis The Palm', tier: 5, rate: 2100, tags: ['family', 'luxury', 'beach'], lon: 55.1171, lat: 25.1304, note: 'Waterpark access included in the stay.' },
      { id: 'baa', name: 'Burj Al Arab Jumeirah', tier: 5, rate: 6900, tags: ['luxury', 'romantic'], lon: 55.1853, lat: 25.1412, note: 'The icon. Butler service.' },
    ],
    transport: [
      { id: 'public', name: 'Metro & Taxi', perDay: 90, comfort: 1, note: 'Cheapest. Fine for adults on a budget.' },
      { id: 'shared', name: 'Shared Shuttle Pass', perDay: 160, comfort: 2, note: 'Hop-on hop-off style transfers.' },
      { id: 'private', name: 'Private SUV + Driver', perDay: 520, comfort: 3, note: 'Door-to-door. Great with kids and luggage.' },
    ],
    visaPerPax: 350, airportTransfer: 180,
    briefs: [
      { id: 'rahman', name: 'The Rahman Family', from: 'Mumbai, India', e: '👨‍👩‍👧‍👦', adults: 2, kids: 2, rooms: 1, nights: 4, month: 12, budget: 17500, want: { family: 3, adventure: 2, shopping: 1, beach: 1 }, style: 'family', comfort: 3,
        quote: '"Our kids are 8 and 11. We want something unforgettable but not exhausting - and one big splash day!"', diff: 'Medium' },
      { id: 'luca', name: 'Anna & Luca - Honeymoon', from: 'Milan, Italy', e: '💑', adults: 2, kids: 0, rooms: 1, nights: 5, month: 2, budget: 32000, want: { romantic: 3, luxury: 3, food: 2, photo: 1 }, style: 'luxury', comfort: 3,
        quote: '"Romance first. We do not mind spending if it is special, but we hate rushing."', diff: 'Hard' },
      { id: 'saleem', name: 'Saleem - Business Stopover', from: 'Karachi, Pakistan', e: '💼', adults: 1, kids: 0, rooms: 1, nights: 2, month: 7, budget: 5200, want: { culture: 2, photo: 2, food: 2, shopping: 1 }, style: 'budget', comfort: 2,
        quote: '"Two nights in July. I know it is hot - keep me inside during the afternoon, and show me the real Dubai."', diff: 'Hard' },
      { id: 'nairobi', name: 'The Nairobi Crew', from: 'Nairobi, Kenya', e: '🎒', adults: 3, kids: 0, rooms: 2, nights: 5, month: 11, budget: 13500, want: { adventure: 3, beach: 2, food: 1, budget: 2 }, style: 'budget', comfort: 1,
        quote: '"Three friends, big appetite for adventure, small wallet. Make every dirham count!"', diff: 'Medium' },
    ],
  };
})();
