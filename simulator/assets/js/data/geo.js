/* Geography data: airports, city codes and crude continent outlines for the dot-matrix map.
   Add airports here and every simulator picks them up automatically.
   [code, cityCode, city, airportName, country, lat, lon, utcOffsetHours] */
(function () {
  const A = [
    ['DXB', 'DXB', 'Dubai', 'Dubai International', 'AE', 25.25, 55.36, 4],
    ['DWC', 'DXB', 'Dubai', 'Al Maktoum Intl', 'AE', 24.89, 55.16, 4],
    ['AUH', 'AUH', 'Abu Dhabi', 'Zayed International', 'AE', 24.43, 54.65, 4],
    ['SHJ', 'SHJ', 'Sharjah', 'Sharjah International', 'AE', 25.33, 55.52, 4],
    ['DOH', 'DOH', 'Doha', 'Hamad International', 'QA', 25.27, 51.61, 3],
    ['JED', 'JED', 'Jeddah', 'King Abdulaziz Intl', 'SA', 21.68, 39.16, 3],
    ['RUH', 'RUH', 'Riyadh', 'King Khalid Intl', 'SA', 24.96, 46.7, 3],
    ['CAI', 'CAI', 'Cairo', 'Cairo International', 'EG', 30.12, 31.41, 2],
    ['IST', 'IST', 'Istanbul', 'Istanbul Airport', 'TR', 41.26, 28.74, 3],
    ['LHR', 'LON', 'London', 'Heathrow', 'GB', 51.47, -0.45, 1],
    ['LGW', 'LON', 'London', 'Gatwick', 'GB', 51.15, -0.19, 1],
    ['CDG', 'PAR', 'Paris', 'Charles de Gaulle', 'FR', 49.01, 2.55, 2],
    ['FRA', 'FRA', 'Frankfurt', 'Frankfurt am Main', 'DE', 50.04, 8.57, 2],
    ['MUC', 'MUC', 'Munich', 'Franz Josef Strauss', 'DE', 48.35, 11.79, 2],
    ['AMS', 'AMS', 'Amsterdam', 'Schiphol', 'NL', 52.31, 4.76, 2],
    ['MAD', 'MAD', 'Madrid', 'Barajas', 'ES', 40.47, -3.56, 2],
    ['FCO', 'ROM', 'Rome', 'Fiumicino', 'IT', 41.8, 12.25, 2],
    ['MXP', 'MIL', 'Milan', 'Malpensa', 'IT', 45.63, 8.72, 2],
    ['ZRH', 'ZRH', 'Zurich', 'Zurich Airport', 'CH', 47.46, 8.55, 2],
    ['JFK', 'NYC', 'New York', 'John F. Kennedy', 'US', 40.64, -73.78, -4],
    ['LAX', 'LAX', 'Los Angeles', 'Los Angeles Intl', 'US', 33.94, -118.41, -7],
    ['YYZ', 'YTO', 'Toronto', 'Pearson', 'CA', 43.68, -79.63, -4],
    ['GRU', 'SAO', 'Sao Paulo', 'Guarulhos', 'BR', -23.43, -46.47, -3],
    ['BOM', 'BOM', 'Mumbai', 'Chhatrapati Shivaji', 'IN', 19.09, 72.87, 5.5],
    ['DEL', 'DEL', 'Delhi', 'Indira Gandhi Intl', 'IN', 28.56, 77.1, 5.5],
    ['KHI', 'KHI', 'Karachi', 'Jinnah International', 'PK', 24.91, 67.16, 5],
    ['LHE', 'LHE', 'Lahore', 'Allama Iqbal Intl', 'PK', 31.52, 74.4, 5],
    ['ISB', 'ISB', 'Islamabad', 'Islamabad International', 'PK', 33.55, 72.83, 5],
    ['CMB', 'CMB', 'Colombo', 'Bandaranaike Intl', 'LK', 7.18, 79.88, 5.5],
    ['MLE', 'MLE', 'Male', 'Velana International', 'MV', 4.19, 73.53, 5],
    ['DAC', 'DAC', 'Dhaka', 'Hazrat Shahjalal', 'BD', 23.84, 90.4, 6],
    ['BKK', 'BKK', 'Bangkok', 'Suvarnabhumi', 'TH', 13.69, 100.75, 7],
    ['KUL', 'KUL', 'Kuala Lumpur', 'KLIA', 'MY', 2.75, 101.71, 8],
    ['SIN', 'SIN', 'Singapore', 'Changi', 'SG', 1.36, 103.99, 8],
    ['HKG', 'HKG', 'Hong Kong', 'Hong Kong Intl', 'HK', 22.31, 113.91, 8],
    ['NRT', 'TYO', 'Tokyo', 'Narita', 'JP', 35.77, 140.39, 9],
    ['ICN', 'SEL', 'Seoul', 'Incheon', 'KR', 37.46, 126.44, 9],
    ['SYD', 'SYD', 'Sydney', 'Kingsford Smith', 'AU', -33.94, 151.18, 10],
    ['MEL', 'MEL', 'Melbourne', 'Tullamarine', 'AU', -37.67, 144.84, 10],
    ['NBO', 'NBO', 'Nairobi', 'Jomo Kenyatta', 'KE', -1.32, 36.93, 3],
    ['ADD', 'ADD', 'Addis Ababa', 'Bole International', 'ET', 8.98, 38.8, 3],
    ['JNB', 'JNB', 'Johannesburg', 'O.R. Tambo', 'ZA', -26.14, 28.25, 2],
    ['CPT', 'CPT', 'Cape Town', 'Cape Town Intl', 'ZA', -33.97, 18.6, 2],
  ];
  const airports = {};
  const cities = {};
  A.forEach(function (r) {
    const o = { code: r[0], cityCode: r[1], city: r[2], name: r[3], country: r[4], lat: r[5], lon: r[6], tz: r[7] };
    airports[o.code] = o;
    (cities[o.cityCode] = cities[o.cityCode] || []).push(o);
  });

  /* very rough continent outlines [lon,lat] - good enough for a dot-matrix style map */
  const land = [
    [[-168,66],[-141,70],[-110,72],[-85,70],[-65,60],[-56,50],[-66,44],[-76,35],[-81,25],[-90,29],[-97,26],[-97,18],[-88,16],[-83,9],[-78,8],[-86,14],[-92,15],[-105,20],[-112,29],[-117,32],[-124,40],[-125,49],[-135,58],[-150,60],[-165,60]],
    [[-78,8],[-72,12],[-62,10],[-50,0],[-35,-6],[-39,-15],[-48,-26],[-58,-38],[-65,-42],[-68,-52],[-74,-50],[-72,-35],[-71,-18],[-81,-5],[-79,2]],
    [[-10,36],[-9,43],[-4,48],[2,51],[8,54],[10,58],[5,62],[15,69],[28,71],[40,67],[40,56],[30,46],[28,41],[24,38],[20,40],[14,38],[9,41],[3,43],[-1,37]],
    [[-17,21],[-10,35],[10,37],[32,31],[35,28],[43,12],[51,12],[41,-2],[40,-15],[35,-25],[27,-34],[18,-34],[12,-17],[9,-1],[8,4],[-8,4],[-17,14]],
    [[36,36],[26,40],[28,42],[42,42],[52,48],[62,55],[60,70],[80,74],[110,77],[140,72],[170,69],[180,65],[160,58],[142,52],[135,44],[129,36],[122,30],[120,23],[108,19],[106,10],[100,3],[98,10],[94,17],[88,22],[80,15],[77,8],[72,20],[67,24],[61,25],[57,26],[55,27],[50,30],[48,30],[40,34]],
    [[35,28],[43,13],[52,16],[59,22],[56,26],[51,25],[48,29],[39,32]],
    [[114,-22],[122,-18],[131,-12],[137,-12],[142,-11],[146,-19],[153,-26],[150,-37],[141,-38],[131,-31],[115,-34]],
    [[-6,50],[2,51],[0,57],[-5,58],[-6,55]],
    [[130,31],[135,35],[140,41],[142,45],[144,43],[140,36],[131,33]],
    [[95,5],[105,-6],[115,-8],[120,-8],[106,-3]],
    [[-55,60],[-45,60],[-20,70],[-20,82],[-60,82],[-72,76]],
  ];

  window.AA = window.AA || {};
  AA.geo = { airports: airports, cities: cities, land: land };

  /* resolve an airport OR city code to an airport record */
  AA.geo.resolve = function (code) {
    code = (code || '').toUpperCase();
    if (airports[code]) return airports[code];
    if (cities[code]) return cities[code][0];
    return null;
  };
  /* find by name fragment ("SINGAPORE", "LONDON") */
  AA.geo.search = function (txt) {
    txt = (txt || '').toUpperCase().trim();
    if (!txt) return [];
    return A.filter(function (r) {
      return r[2].toUpperCase().indexOf(txt) === 0 || r[3].toUpperCase().indexOf(txt) !== -1 || r[0] === txt || r[1] === txt;
    }).map(function (r) { return airports[r[0]]; });
  };
  AA.geo.dist = function (a, b) {
    const R = 6371, rad = Math.PI / 180;
    const dLat = (b.lat - a.lat) * rad, dLon = (b.lon - a.lon) * rad;
    const x = Math.sin(dLat / 2) ** 2 + Math.cos(a.lat * rad) * Math.cos(b.lat * rad) * Math.sin(dLon / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(x));
  };
})();
