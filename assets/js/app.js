    /* ===================== datos de demostración ===================== */
    const PAYS = {
      cashea: { n: 'Cashea', l: 'C', c: '#F59E0B' },
      zelle: { n: 'Zelle', l: 'Z', c: '#7414CA' },
      zinlli: { n: 'Zinlli', l: 'Z', c: '#A855F7' },
      paypal: { n: 'PayPal', l: 'P', c: '#0070BA' },
      movil: { n: 'Pago Móvil', l: 'PM', c: '#06B6D4' },
      punto: { n: 'Punto', l: 'P', c: '#64748B' },
      binance: { n: 'Binance', l: 'B', c: '#C98A00' },
      efectivo: { n: 'Efectivo', l: '$', c: '#10B981' }
    };
    const CATS = [
      { k: 'restaurante', n: 'Restaurantes', i: 'i-restaurante', g: 'linear-gradient(135deg,#F25C54,#C0392B)', c: 214 },
      { k: 'cafe', n: 'Cafés', i: 'i-cafe', g: 'linear-gradient(135deg,#B07A4E,#6E4426)', c: 168 },
      { k: 'panaderia', n: 'Panaderías', i: 'i-panaderia', g: 'linear-gradient(135deg,#E8A93F,#B06E14)', c: 96 },
      { k: 'supermercado', n: 'Supermercados', i: 'i-supermercado', g: 'linear-gradient(135deg,#3FA96B,#1E6B41)', c: 132 },
      { k: 'hotel', n: 'Hoteles', i: 'i-hotel', g: 'linear-gradient(135deg,#4A7FD6,#22417F)', c: 54 },
      { k: 'tienda', n: 'Tiendas', i: 'i-tienda', g: 'linear-gradient(135deg,#E0703F,#A2411A)', c: 187 },
      { k: 'entretenimiento', n: 'Entretenimiento', i: 'i-entretenimiento', g: 'linear-gradient(135deg,#9A5ED6,#5A2C8F)', c: 143 },
      { k: 'servicios', n: 'Servicios', i: 'i-servicios', g: 'linear-gradient(135deg,#4F9FB5,#22606F)', c: 229 },
      { k: 'tecnologia', n: 'Tecnología', i: 'i-tecnologia', g: 'linear-gradient(135deg,#3D8F97,#1E5054)', c: 61 }
    ];
    const CATMAP = Object.fromEntries(CATS.map(c => [c.k, c]));
    const BIZ = [
      { id: 1, n: 'La Cocina de Mamá', cat: 'restaurante', z: 'Chacao', dir: 'Av. Francisco de Miranda', lat: 10.4975, lng: -66.8542, r: 4.9, rv: 312, p: '$$', d: 0.4, open: 1, del: 1, ver: 1, pays: ['cashea', 'punto', 'efectivo', 'movil'], desc: 'Comida criolla casera con almuerzos ejecutivos de lunes a viernes. Postres de la casa y ambiente familiar.' },
      { id: 2, n: 'Café Arábica', cat: 'cafe', z: 'Las Mercedes', dir: 'Calle París', lat: 10.4842, lng: -66.8617, r: 4.6, rv: 198, p: '$$', d: 1.8, open: 1, del: 0, ver: 1, pays: ['zelle', 'punto', 'binance', 'efectivo'], desc: 'Tostadores propios de café venezolano de altura. Buen wifi y mesas para trabajar toda la tarde.' },
      { id: 3, n: 'Panadería La Espiga', cat: 'panaderia', z: 'Altamira', dir: 'Av. Luis Roche', lat: 10.4954, lng: -66.8462, r: 4.7, rv: 421, p: '$', d: 1.1, open: 1, del: 1, ver: 1, pays: ['cashea', 'movil', 'punto', 'efectivo'], desc: 'Pan salido del horno cada dos horas, cachitos rellenos y golfeados. Cola corta antes de las 8 de la mañana.' },
      { id: 4, n: 'Abasto Express', cat: 'supermercado', z: 'La Castellana', dir: 'Av. Principal', lat: 10.4986, lng: -66.8506, r: 4.3, rv: 156, p: '$$', d: 0.9, open: 1, del: 1, ver: 1, pays: ['punto', 'movil', 'efectivo', 'zelle'], desc: 'Abasto de barrio con verdulería y charcutería. Reparte en la zona hasta las 7 de la noche.' },
      { id: 5, n: 'Hotel Ávila Suites', cat: 'hotel', z: 'San Bernardino', dir: 'Av. Vollmer', lat: 10.5122, lng: -66.8918, r: 4.5, rv: 88, p: '$$$', d: 3.6, open: 1, del: 0, ver: 1, pays: ['zelle', 'binance', 'punto'], desc: 'Habitaciones con vista al Ávila, desayuno incluido y estacionamiento propio. Tarifas en divisas.' },
      { id: 6, n: 'TecnoMundo', cat: 'tecnologia', z: 'Sabana Grande', dir: 'Bulevar de Sabana Grande', lat: 10.4919, lng: -66.8737, r: 4.1, rv: 64, p: '$$', d: 2.4, open: 0, del: 0, ver: 1, pays: ['zelle', 'binance', 'punto', 'efectivo'], desc: 'Repuestos, accesorios y servicio técnico de celulares y laptops. Garantía de 30 días por escrito.' },
      { id: 7, n: 'Bar Sunset 1010', cat: 'entretenimiento', z: 'Los Palos Grandes', dir: '4ta Avenida', lat: 10.4998, lng: -66.8420, r: 4.4, rv: 275, p: '$$$', d: 1.5, open: 0, del: 0, ver: 1, pays: ['zelle', 'punto', 'binance'], desc: 'Terraza con música en vivo los jueves. Cocteles de autor y tabla de picadas para compartir.' },
      { id: 8, n: 'Barbería El Corte', cat: 'servicios', z: 'Chacaíto', dir: 'Av. Casanova', lat: 10.4922, lng: -66.8672, r: 4.8, rv: 143, p: '$', d: 2.0, open: 1, del: 0, ver: 1, pays: ['cashea', 'movil', 'efectivo'], desc: 'Corte clásico y afeitado con toalla caliente. Se atiende con cita o por orden de llegada.' },
      { id: 9, n: 'Pizzería Nonna', cat: 'restaurante', z: 'El Rosal', dir: 'Av. Tamanaco', lat: 10.4880, lng: -66.8580, r: 4.5, rv: 389, p: '$$', d: 1.3, open: 1, del: 1, ver: 1, pays: ['cashea', 'punto', 'movil', 'efectivo', 'zelle'], desc: 'Masa madre fermentada 48 horas y horno de leña. Media pizza mitad y mitad sin recargo.' },
      { id: 10, n: 'Heladería Frío Bello', cat: 'cafe', z: 'Bello Monte', dir: 'Av. Casanova', lat: 10.4830, lng: -66.8720, r: 4.6, rv: 210, p: '$', d: 2.7, open: 1, del: 1, ver: 0, pays: ['movil', 'efectivo', 'punto'], desc: 'Helados artesanales de frutas de temporada. Sabores sin azúcar añadida disponibles todo el año.' },
      { id: 11, n: 'Bodegón Don Pablo', cat: 'tienda', z: 'La Castellana', dir: 'Calle Urdaneta', lat: 10.5002, lng: -66.8478, r: 4.2, rv: 97, p: '$$$', d: 1.0, open: 1, del: 1, ver: 1, pays: ['zelle', 'binance', 'punto', 'efectivo'] },
      { id: 12, n: 'Cines Metrópolis', cat: 'entretenimiento', z: 'Chacao', dir: 'C.C. Metrópolis', lat: 10.4960, lng: -66.8590, r: 4.0, rv: 512, p: '$$', d: 0.7, open: 1, del: 0, ver: 1, pays: ['punto', 'movil', 'cashea', 'efectivo'], desc: 'Seis salas, funciones desde el mediodía y combos familiares. Martes y miércoles a mitad de precio.' }
    ];
    const PEND = [
      { n: 'Arepera 24/7', cat: 'Restaurante', z: 'Chacao', pays: ['cashea', 'punto', 'efectivo'], t: 'hace 2 h', st: 'wait' },
      { n: 'Spa Serenidad', cat: 'Servicios', z: 'Las Mercedes', pays: ['zelle', 'punto'], t: 'hace 5 h', st: 'wait' },
      { n: 'Mercado Verde', cat: 'Supermercado', z: 'Altamira', pays: ['movil', 'efectivo', 'punto'], t: 'ayer', st: 'wait' },
      { n: 'Posada El Mirador', cat: 'Hotel', z: 'El Hatillo', pays: ['zelle', 'binance'], t: 'ayer', st: 'ok' },
      { n: 'Repuestos JM', cat: 'Tienda', z: 'La Candelaria', pays: ['efectivo'], t: 'hace 2 días', st: 'no' }
    ];

    /* ===================== utilidades ===================== */
    const $ = (s, c = document) => c.querySelector(s), $$ = (s, c = document) => [...c.querySelectorAll(s)];
    const payChip = (k, mini) => `<span class="pay pay-${k}${mini ? ' mini' : ''}" title="${PAYS[k].n}"><i>${PAYS[k].l}</i>${mini ? '' : PAYS[k].n}</span>`;
    const ico = id => `<svg><use href="#${id}"></use></svg>`;
    function toast(m) { const t = $('#toast'); t.textContent = m; t.classList.add('on'); clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('on'), 2600) }

    function cardHTML(b) {
      const c = CATMAP[b.cat] || { g: 'linear-gradient(135deg,#0F9B8E,#0A6E64)', i: 'i-tienda', n: b.cat || 'Comercio' };
      const artContent = b.logo_url 
        ? `<img src="${b.logo_url}" alt="${b.n}" style="width:100%; height:100%; object-fit:cover;">` 
        : ico(c.i);
      return `
<article class="card" data-id="${b.id}">
  <div class="card-art" style="background:${c.g}; overflow:hidden;">
    ${artContent}
    <span class="badge ${b.open ? '' : 'closed'}"><span class="dot"></span>${b.open ? 'Abierto ahora' : 'Cerrado'}</span>
  </div>
  <div class="card-body">
    <div class="card-top"><h3>${b.n}</h3><span class="rate">${ico('i-estrella')}${b.r ? b.r.toFixed(1) : '5.0'}</span></div>
    <div class="meta"><span>${c.n}</span><span class="sep"></span><span>${b.z}</span><span class="sep"></span><span>${b.p || '$$'}</span></div>
    <div class="card-foot">
      <div class="pay-row">${(b.pays || []).slice(0, 5).map(p => payChip(p, 1)).join('')}</div>
      <span class="mono" style="font-size:.78rem;color:var(--muted)">${b.d ? b.d.toFixed(1) : '1.0'} km</span>
    </div>
  </div>
</article>`}

    /* ===================== tema ===================== */
    let dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    function applyTheme() {
      document.documentElement.dataset.theme = dark ? 'dark' : 'light';
      $('#theme-ico').innerHTML = `<use href="#${dark ? 'i-sol' : 'i-luna'}"></use>`;
    }
    $('#theme').addEventListener('click', () => { dark = !dark; applyTheme(); });
    applyTheme();

    /* ===================== navegación ===================== */
    const VIEWS = ['inicio', 'mapa', 'categorias', 'detalle', 'negocio', 'admin'];
    function toggleDrawer(on) {
      const d = $('#mobile-drawer');
      if (d) d.classList.toggle('open', on);
    }
    function go(v) {
      if (!VIEWS.includes(v)) v = 'inicio';
      VIEWS.forEach(x => $('#v-' + x).classList.toggle('on', x === v));
      $$('.nav a, .mobile-nav a').forEach(a => a.classList.toggle('on', a.dataset.go === v));
      toggleDrawer(false);
      window.scrollTo({ top: 0, behavior: 'instant' });
      if (v === 'mapa' && map) setTimeout(() => map.invalidateSize(), 60);
      if (v === 'negocio' && pickMap) setTimeout(() => pickMap.invalidateSize(), 60);
      if (location.hash.slice(1) !== v) history.replaceState(null, '', '#' + v);
    }
    $('#menu-toggle')?.addEventListener('click', () => toggleDrawer(true));
    $('#mobile-drawer-close')?.addEventListener('click', () => toggleDrawer(false));
    $('#mobile-drawer-bg')?.addEventListener('click', () => toggleDrawer(false));
    document.addEventListener('click', e => {
      const g = e.target.closest('[data-go]'); if (g) { e.preventDefault(); go(g.dataset.go); }
      const m = e.target.closest('[data-modal]'); if (m) { $('#ov').classList.add('on'); }
      if (e.target.closest('[data-close]') || e.target.id === 'ov') { $('#ov').classList.remove('on'); }
      const t = e.target.closest('[data-toast]'); if (t) { toast(t.dataset.toast); }
      const c = e.target.closest('.card'); if (c) { openDetail(+c.dataset.id); }
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') $('#ov').classList.remove('on') });
    window.addEventListener('hashchange', () => go(location.hash.slice(1)));

    /* ===================== render inicial ===================== */
    $('#cat-rail').innerHTML = CATS.map(c => `
  <div class="cat" data-cat="${c.k}"><div class="cat-art" style="background:${c.g}">${ico(c.i)}</div>
  <div class="cat-body"><b>${c.n}</b><span>${c.c} comercios</span></div></div>`).join('');
    $('#cat-grid').innerHTML = $('#cat-rail').innerHTML.replace(/flex:0 0 216px/g, '');
    $('#home-cards').innerHTML = BIZ.slice(0, 6).map(cardHTML).join('');
    $('#all-cards').innerHTML = BIZ.map(cardHTML).join('');
    $('#all-count').textContent = BIZ.length + ' comercios';
    $('#f-cats').innerHTML = `<span class="chip on" data-c="all">Todas</span>` + CATS.map(c => `<span class="chip" data-c="${c.k}">${c.n}</span>`).join('');

    $$('.cat').forEach(el => el.addEventListener('click', () => {
      state.cat = el.dataset.cat; $$('#f-cats .chip').forEach(c => c.classList.toggle('on', c.dataset.c === state.cat));
      go('mapa'); refresh();
    }));

    /* ===================== estado y filtros ===================== */
    const state = { cat: 'all', pays: new Set(), q: '', dist: 5, open: false, del: false, ver: true };
    function matches(b) {
      if (state.cat !== 'all' && b.cat !== state.cat) return false;
      for (const p of state.pays) if (!b.pays.includes(p)) return false;
      if (state.q) {
        const t = (b.n + ' ' + CATMAP[b.cat].n + ' ' + b.z + ' ' + b.pays.map(p => PAYS[p].n).join(' ')).toLowerCase();
        if (!t.includes(state.q.toLowerCase())) return false;
      }
      if (b.d > state.dist) return false;
      if (state.open && !b.open) return false;
      if (state.del && !b.del) return false;
      if (state.ver && !b.ver) return false;
      return true;
    }
    function refresh() {
      const list = BIZ.filter(matches).sort((a, b) => a.d - b.d);
      $('#res-scroll').innerHTML = list.length ? list.map(cardHTML).join('')
        : `<div style="padding:1.6rem;color:var(--muted);font-size:.92rem">Ningún comercio cumple estos filtros. Prueba ampliando la distancia o quitando un método de pago.</div>`;
      $('#res-count').textContent = list.length === 1 ? '1 comercio en esta zona' : `${list.length} comercios en esta zona`;
      drawPins(list);
    }
    $('#q').addEventListener('input', e => { state.q = e.target.value; refresh() });
    $('#dist').addEventListener('input', e => { state.dist = +e.target.value; $('#d-val').textContent = (+e.target.value).toFixed(1).replace('.', ',') + ' km'; refresh() });
    $$('#f-cats .chip').forEach(c => c.addEventListener('click', () => {
      state.cat = c.dataset.c; $$('#f-cats .chip').forEach(x => x.classList.toggle('on', x === c)); refresh()
    }));
    $$('#f-pays .pay-toggle').forEach(p => p.addEventListener('click', () => {
      p.classList.toggle('on'); p.classList.contains('on') ? state.pays.add(p.dataset.p) : state.pays.delete(p.dataset.p); refresh()
    }));
    [['sw-open', 'open'], ['sw-del', 'del'], ['sw-ver', 'ver']].forEach(([id, k]) => {
      const el = $('#' + id);
      const flip = () => { el.classList.toggle('on'); state[k] = el.classList.contains('on'); refresh() };
      el.addEventListener('click', flip);
      el.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip() } });
    });
    $('#clear').addEventListener('click', () => {
      state.cat = 'all'; state.pays.clear(); state.q = ''; state.dist = 15; state.open = false; state.del = false; state.ver = false;
      $('#q').value = ''; $('#dist').value = 15; $('#d-val').textContent = '15,0 km';
      $$('#f-pays .pay-toggle').forEach(p => p.classList.remove('on'));
      $$('#f-cats .chip').forEach(c => c.classList.toggle('on', c.dataset.c === 'all'));
      $$('.sw').forEach(s => s.classList.remove('on'));
      refresh(); toast('Filtros limpios');
    });
    $('#locate').addEventListener('click', () => { map.flyTo([10.4950, -66.8560], 14); toast('Centrando en tu ubicación aproximada') });

    /* filtro del hero */
    $$('#b-pays .pay-toggle').forEach(p => p.addEventListener('click', () => {
      p.classList.toggle('on');
      p.classList.contains('on') ? state.pays.add(p.dataset.p) : state.pays.delete(p.dataset.p);
      $$('#f-pays .pay-toggle').forEach(f => f.classList.toggle('on', state.pays.has(f.dataset.p)));
      const n = BIZ.filter(b => [...state.pays].every(x => b.pays.includes(x))).length;
      $('#b-count').textContent = (n * 26).toLocaleString('es-VE');
      refresh();
    }));
    $('#b-cat').addEventListener('change', e => {
      const v = e.target.value;
      state.cat = v === 'all' ? 'all' : (CATS.find(c => c.n.toLowerCase().startsWith(v.slice(0, 4))) || { k: 'all' }).k;
      $$('#f-cats .chip').forEach(c => c.classList.toggle('on', c.dataset.c === state.cat));
      refresh();
    });

    /* ===================== mapas ===================== */
    const TILE = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
    const ATTR = '&copy; OpenStreetMap &copy; CARTO';
    let map, pickMap, layer, detMap;
    function pinIcon(b, act) {
      const dots = b.pays.slice(0, 4).map(p => `<span style="background:${PAYS[p].c}"></span>`).join('');
      return L.divIcon({
        className: '', iconSize: [34, 42], iconAnchor: [17, 40], html:
          `<div class="pin ${act ? 'act' : ''}"><div class="pin-pays">${dots}</div>
     <div class="pin-body"><svg viewBox="0 0 24 24"><use href="#${CATMAP[b.cat].i}"></use></svg></div></div>`
      });
    }
    function drawPins(list) {
      if (!layer) return; layer.clearLayers();
      list.forEach(b => {
        const m = L.marker([b.lat, b.lng], { icon: pinIcon(b) }).addTo(layer);
        m.bindPopup(`<b style="font-family:'Bricolage Grotesque',serif;font-size:1rem">${b.n}</b><br>
      <span style="color:#667">${CATMAP[b.cat].n} · ${b.p} · ${b.d.toFixed(1)} km</span><br>
      <span style="font-size:.8rem">${b.pays.map(p => PAYS[p].n).join(' · ')}</span>`);
        m.on('click', () => { });
        m.on('dblclick', () => openDetail(b.id));
      });
    }
    function initMaps() {
      const hero = L.map('hero-map', { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false }).setView([10.4955, -66.8560], 14);
      L.tileLayer(TILE, { attribution: ATTR }).addTo(hero);
      BIZ.slice(0, 7).forEach(b => L.marker([b.lat, b.lng], { icon: pinIcon(b) }).addTo(hero));

      map = L.map('map', { zoomControl: true }).setView([10.4955, -66.8560], 14);
      L.tileLayer(TILE, { attribution: ATTR }).addTo(map);
      layer = L.layerGroup().addTo(map);

      pickMap = L.map('pick-map', { zoomControl: false }).setView([10.4975, -66.8542], 15);
      L.tileLayer(TILE, { attribution: ATTR }).addTo(pickMap);
      const mk = L.marker([10.4975, -66.8542], {
        draggable: true, icon: L.divIcon({
          className: '', iconSize: [34, 42], iconAnchor: [17, 40],
          html: `<div class="pin"><div class="pin-body"><svg viewBox="0 0 24 24"><use href="#i-pin"></use></svg></div></div>`
        })
      }).addTo(pickMap);
      mk.on('dragend', () => { const p = mk.getLatLng(); toast(`Ubicación fijada en ${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`) });
      refresh();
    }

    /* ===================== detalle ===================== */
    function openDetail(id) {
      const b = BIZ.find(x => x.id === id); if (!b) return; const c = CATMAP[b.cat];
      $('#d-art').style.background = c.g; $('#d-art').innerHTML = ico(c.i);
      $('#d-cat').textContent = c.n + ' · ' + b.z;
      $('#d-name').textContent = b.n;
      $('#d-meta').innerHTML = `<span>${b.open ? 'Abierto ahora' : 'Cerrado'}</span><span class="sep"></span><span>${b.p}</span><span class="sep"></span><span class="mono">${b.rv} valoraciones</span>`;
      $('#d-desc').textContent = b.desc || 'Ficha creada por el propietario. Aún sin descripción.';
      $('#d-pays').innerHTML = b.pays.map(p => payChip(p)).join('');
      $('#d-verif').textContent = b.ver ? 'Métodos confirmados por el comercio hace 3 días.' : 'Métodos aún sin verificar por el equipo de Spot.';
      $('#d-hours').innerHTML = [['Lunes a viernes', '8:00 – 20:00'], ['Sábado', '9:00 – 18:00'], ['Domingo', 'Cerrado']]
        .map(([d, h]) => `<div class="kv"><span>${d}</span><b class="mono">${h}</b></div>`).join('');
      $('#d-rate').innerHTML = ico('i-estrella') + b.r.toFixed(1);
      $('#d-addr').textContent = b.dir; $('#d-dist').textContent = b.d.toFixed(1) + ' km';
      $('#d-price').textContent = b.p; $('#d-del').textContent = b.del ? 'Sí' : 'No disponible';

      const redesSection = $('#d-redes-section');
      const redesBox = $('#d-redes');
      if (redesSection && redesBox) {
        redesBox.innerHTML = '';
        const r = b.redes || {};
        let count = 0;
        if (r.instagram) {
          const handle = r.instagram.replace(/^@/, '');
          redesBox.innerHTML += `<a href="https://instagram.com/${handle}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm" style="display:inline-flex; align-items:center; gap:.4rem;">📸 Instagram (@${escapeHtml(handle)})</a>`;
          count++;
        }
        if (r.whatsapp) {
          const num = r.whatsapp.replace(/[^0-9]/g, '');
          redesBox.innerHTML += `<a href="https://wa.me/${num}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm" style="display:inline-flex; align-items:center; gap:.4rem; color:var(--brand-ink);">💬 WhatsApp</a>`;
          count++;
        }
        if (r.tiktok) {
          const handle = r.tiktok.replace(/^@/, '');
          redesBox.innerHTML += `<a href="https://tiktok.com/@${handle}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm" style="display:inline-flex; align-items:center; gap:.4rem;">🎵 TikTok (@${escapeHtml(handle)})</a>`;
          count++;
        }
        if (r.web) {
          const url = r.web.startsWith('http') ? r.web : 'https://' + r.web;
          redesBox.innerHTML += `<a href="${url}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm" style="display:inline-flex; align-items:center; gap:.4rem;">🌐 Sitio Web</a>`;
          count++;
        }
        redesSection.style.display = count > 0 ? 'block' : 'none';
      }

      go('detalle');
      setTimeout(() => {
        if (detMap) { detMap.remove(); detMap = null }
        detMap = L.map('d-map', { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false }).setView([b.lat, b.lng], 16);
        L.tileLayer(TILE).addTo(detMap); L.marker([b.lat, b.lng], { icon: pinIcon(b, 1) }).addTo(detMap);
      }, 80);
    }

    /* ===================== wizard ===================== */
    $$('[data-next]').forEach(btn => btn.addEventListener('click', () => {
      const s = +btn.dataset.next;
      $$('.wz-panel').forEach(p => p.hidden = +p.dataset.p !== s);
      $$('.wz-step').forEach(p => { const n = +p.dataset.s; p.classList.toggle('act', n === s); p.classList.toggle('done', n < s) });
      if (s === 3) {
        $('#r-name').textContent = $('#n-name').value || 'Sin nombre';
        $('#r-cat').textContent = $('#n-cat').value;
        $('#r-zona').textContent = $('#n-zona').value;
        $('#r-tel').textContent = $('#n-tel').value || 'Sin teléfono';
        $('#r-pays').innerHTML = $$('#n-pays .pay-toggle.on').map(p => payChip(p.dataset.p, 1)).join('') || '—';
        if (uploadedLogoUrl) {
          $('#r-logo-row').style.display = 'flex';
          $('#r-logo-wrap').innerHTML = `<img src="${uploadedLogoUrl}" alt="Logo cargado" style="max-height:48px; border-radius:6px; object-fit:contain;">`;
        } else {
          $('#r-logo-row').style.display = 'none';
        }
      }
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (s === 2) setTimeout(() => pickMap.invalidateSize(), 60);
    }));
    $$('#n-pays .pay-toggle').forEach(p => p.addEventListener('click', () => p.classList.toggle('on')));

    /* ===================== GESTIÓN DE SUBIDA DE IMAGEN ===================== */
    let uploadedLogoUrl = '';
    const dropEl = $('#drop');
    const fileInput = $('#n-file');
    const dropPrompt = $('#drop-prompt');
    const dropPreview = $('#drop-preview');
    const imgPreview = $('#img-preview');
    const uploadStatus = $('#upload-status-text');

    if (dropEl && fileInput) {
      dropEl.addEventListener('click', () => fileInput.click());

      ['dragenter', 'dragover'].forEach(evt => {
        dropEl.addEventListener(evt, e => {
          e.preventDefault();
          e.stopPropagation();
          dropEl.style.borderColor = 'var(--brand)';
          dropEl.style.background = 'var(--brand-soft)';
        });
      });

      ['dragleave', 'drop'].forEach(evt => {
        dropEl.addEventListener(evt, e => {
          e.preventDefault();
          e.stopPropagation();
          dropEl.style.borderColor = '';
          dropEl.style.background = '';
        });
      });

      dropEl.addEventListener('drop', e => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files[0]) {
          handleImageUpload(dt.files[0]);
        }
      });

      fileInput.addEventListener('change', e => {
        if (e.target.files && e.target.files[0]) {
          handleImageUpload(e.target.files[0]);
        }
      });
    }

    async function handleImageUpload(file) {
      const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
      if (!allowed.includes(file.type)) {
        toast('Formato inválido. Solo se admiten JPG, PNG o WebP.');
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        toast('La imagen no puede pesar más de 5 MB.');
        return;
      }

      // Vista previa local inmediata
      const reader = new FileReader();
      reader.onload = ev => {
        imgPreview.src = ev.target.result;
        dropPrompt.style.display = 'none';
        dropPreview.style.display = 'flex';
      };
      reader.readAsDataURL(file);

      uploadStatus.style.display = 'block';
      uploadStatus.textContent = 'Subiendo imagen…';
      uploadStatus.style.color = 'var(--brand)';

      const fd = new FormData();
      fd.append('image', file);

      try {
        const res = await fetch('api/upload/image.php', {
          method: 'POST',
          body: fd
        });
        const json = await res.json();
        if (json.success && json.data) {
          uploadedLogoUrl = json.data.url;
          uploadStatus.textContent = '✓ Imagen guardada correctamente.';
          uploadStatus.style.color = 'var(--pay-efectivo)';
          toast('Foto/logo subido con éxito.');
        } else {
          uploadStatus.textContent = '⚠ ' + (json.message || 'Error al subir');
          uploadStatus.style.color = 'var(--hot)';
          toast(json.message || 'Error al subir la imagen');
        }
      } catch (err) {
        uploadStatus.textContent = '⚠ Error de conexión al subir la imagen.';
        uploadStatus.style.color = 'var(--hot)';
        toast('No se pudo conectar para subir la imagen.');
      }
    }
