    /* ===================== utilidades generales ===================== */
    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
    window.escapeHtml = escapeHtml;

    /* ===================== métodos de pago ===================== */
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

    /* ===================== colecciones dinámicas ===================== */
    let CATS = (window.SPOT_INITIAL_DATA && Array.isArray(window.SPOT_INITIAL_DATA.categorias)) 
      ? [...window.SPOT_INITIAL_DATA.categorias] 
      : [];
    let CATMAP = Object.fromEntries(CATS.map(c => [c.k, c]));
    const BIZ = (window.SPOT_INITIAL_DATA && Array.isArray(window.SPOT_INITIAL_DATA.comercios)) 
      ? [...window.SPOT_INITIAL_DATA.comercios] 
      : [];

    /* ===================== utilidades DOM ===================== */
    const $ = (s, c = document) => c.querySelector(s), $$ = (s, c = document) => [...c.querySelectorAll(s)];
    const payChip = (k, mini) => `<span class="pay pay-${k}${mini ? ' mini' : ''}" title="${PAYS[k]?.n || k}"><i>${PAYS[k]?.l || '•'}</i>${mini ? '' : (PAYS[k]?.n || k)}</span>`;
    const ico = id => `<svg><use href="#${id}"></use></svg>`;
    function getSwalToast() {
      const swalInstance = (typeof Swal !== 'undefined' && Swal.mixin) ? Swal : ((typeof Sweetalert2 !== 'undefined' && Sweetalert2.mixin) ? Sweetalert2 : (window.Swal || window.Sweetalert2 || null));
      if (swalInstance && swalInstance.mixin) {
        return swalInstance.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (t) => {
            t.addEventListener('mouseenter', swalInstance.stopTimer);
            t.addEventListener('mouseleave', swalInstance.resumeTimer);
          }
        });
      }
      return null;
    }

    function toast(m, iconType) {
      const swalToast = getSwalToast();
      if (swalToast) {
        let icon = iconType;
        if (!icon) {
          const lower = String(m).toLowerCase();
          if (lower.includes('error') || lower.includes('no fue') || lower.includes('incorrect') || lower.includes('inválid') || lower.includes('falló') || lower.includes('no se')) {
            icon = 'error';
          } else if (lower.includes('advertencia') || lower.includes('atención') || lower.includes('aviso')) {
            icon = 'warning';
          } else if (lower.includes('centrando') || lower.includes('ubicación') || lower.includes('info')) {
            icon = 'info';
          } else {
            icon = 'success';
          }
        }
        swalToast.fire({
          icon: icon,
          title: m
        });
      } else {
        const t = $('#toast');
        if (t) {
          t.textContent = m;
          t.classList.add('on');
          clearTimeout(t._t);
          t._t = setTimeout(() => t.classList.remove('on'), 2600);
        }
      }
    }

    function cardHTML(b) {
      const c = CATMAP[b.cat] || { g: 'linear-gradient(135deg,#0F9B8E,#0A6E64)', i: 'i-tienda', n: b.cat || 'Comercio' };
      const artContent = b.logo_url 
        ? `<img src="${b.logo_url}" alt="${escapeHtml(b.n)}" style="width:100%; height:100%; object-fit:cover;">` 
        : ico(c.i);
      return `
<article class="card" data-id="${b.id}">
  <div class="card-art" style="background:${c.g}; overflow:hidden;">
    ${artContent}
    <span class="badge ${b.open ? '' : 'closed'}"><span class="dot"></span>${b.open ? 'Abierto ahora' : 'Cerrado'}</span>
  </div>
  <div class="card-body">
    <div class="card-top"><h3>${escapeHtml(b.n)}</h3><span class="rate">${ico('i-estrella')}${b.r ? Number(b.r).toFixed(1) : '5.0'}</span></div>
    <div class="meta"><span>${escapeHtml(c.n)}</span><span class="sep"></span><span>${escapeHtml(b.z)}</span><span class="sep"></span><span>${escapeHtml(b.p || '$$')}</span></div>
    <div class="card-foot">
      <div class="pay-row">${(b.pays || []).slice(0, 5).map(p => payChip(p, 1)).join('')}</div>
      <span class="mono" style="font-size:.78rem;color:var(--muted)">${b.d ? Number(b.d).toFixed(1) : '1.0'} km</span>
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

    /* ===================== mapas y estado global ===================== */
    const TILE = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
    const ATTR = '&copy; OpenStreetMap &copy; CARTO';
    let map = null, pickMap = null, layer = null, detMap = null;

    const state = { cat: 'all', pays: new Set(), q: '', dist: 5, open: false, del: false, ver: true };

    function pinIcon(b, act) {
      const dots = (b.pays || []).slice(0, 4).map(p => `<span style="background:${PAYS[p]?.c || '#64748B'}"></span>`).join('');
      const c = CATMAP[b.cat] || { i: 'i-tienda' };
      return L.divIcon({
        className: '', iconSize: [34, 42], iconAnchor: [17, 40], html:
          `<div class="pin ${act ? 'act' : ''}"><div class="pin-pays">${dots}</div>
     <div class="pin-body"><svg viewBox="0 0 24 24"><use href="#${c.i}"></use></svg></div></div>`
      });
    }

    function drawPins(list) {
      if (!layer) return;
      layer.clearLayers();
      list.forEach(b => {
        const c = CATMAP[b.cat] || { n: b.cat || 'Comercio' };
        const m = L.marker([b.lat, b.lng], { icon: pinIcon(b) }).addTo(layer);
        m.bindPopup(`<b style="font-family:'Bricolage Grotesque',serif;font-size:1rem">${escapeHtml(b.n)}</b><br>
      <span style="color:#667">${escapeHtml(c.n)} · ${escapeHtml(b.p || '$$')} · ${(b.d ? Number(b.d) : 1.0).toFixed(1)} km</span><br>
      <span style="font-size:.8rem">${(b.pays || []).map(p => PAYS[p]?.n || p).join(' · ')}</span>`);
        m.on('click', () => { });
        m.on('dblclick', () => openDetail(b.id));
      });
    }

    function matches(b) {
      if (state.cat !== 'all' && b.cat !== state.cat) return false;
      for (const p of state.pays) if (!b.pays.includes(p)) return false;
      if (state.q) {
        const catName = CATMAP[b.cat]?.n || b.cat || '';
        const t = (b.n + ' ' + catName + ' ' + b.z + ' ' + (b.pays || []).map(p => PAYS[p]?.n || p).join(' ')).toLowerCase();
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
      const resScroll = $('#res-scroll');
      if (resScroll) {
        resScroll.innerHTML = list.length ? list.map(cardHTML).join('')
          : `<div style="padding:1.6rem;color:var(--muted);font-size:.92rem">Ningún comercio cumple estos filtros. Prueba ampliando la distancia o quitando un método de pago.</div>`;
      }
      const resCount = $('#res-count');
      if (resCount) {
        resCount.textContent = list.length === 1 ? '1 comercio en esta zona' : `${list.length} comercios en esta zona`;
      }
      drawPins(list);
    }

    /* ===================== navegación ===================== */
    const VIEWS = ['inicio', 'mapa', 'categorias', 'detalle', 'negocio', 'admin'];
    function toggleDrawer(on) {
      const d = $('#mobile-drawer');
      if (d) d.classList.toggle('open', on);
    }
    function go(v) {
      if (!VIEWS.includes(v)) v = 'inicio';
      VIEWS.forEach(x => $('#v-' + x)?.classList.toggle('on', x === v));
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

      const catEl = e.target.closest('.cat');
      if (catEl && catEl.dataset.cat) {
        state.cat = catEl.dataset.cat;
        $$('#f-cats .chip').forEach(chip => chip.classList.toggle('on', chip.dataset.c === state.cat));
        const bCat = $('#b-cat');
        if (bCat) bCat.value = state.cat;
        updateHeroCount();
        go('mapa');
        refresh();
      }

      const chipEl = e.target.closest('#f-cats .chip');
      if (chipEl && chipEl.dataset.c) {
        state.cat = chipEl.dataset.c;
        $$('#f-cats .chip').forEach(x => x.classList.toggle('on', x === chipEl));
        const bCat = $('#b-cat');
        if (bCat) bCat.value = state.cat;
        updateHeroCount();
        refresh();
      }
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') $('#ov').classList.remove('on') });
    window.addEventListener('hashchange', () => go(location.hash.slice(1)));

    /* ===================== renderizadores dinámicos ===================== */
    function renderCategories(cats) {
      CATS = Array.isArray(cats) ? [...cats] : [];
      CATMAP = Object.fromEntries(CATS.map(c => [c.k, c]));

      const rail = $('#cat-rail');
      if (rail) {
        if (CATS.length > 0) {
          rail.innerHTML = CATS.map(c => {
            const countLabel = c.c === 1 ? '1 comercio' : `${c.c} comercios`;
            return `
  <div class="cat" data-cat="${c.k}">
    <div class="cat-art" style="background:${c.g}">${ico(c.i)}</div>
    <div class="cat-body"><b>${escapeHtml(c.n)}</b><span>${countLabel}</span></div>
  </div>`;
          }).join('');
        } else {
          rail.innerHTML = `<div style="padding:1.5rem;color:var(--muted);font-size:.92rem">No hay categorías con comercios disponibles.</div>`;
        }
      }

      const grid = $('#cat-grid');
      if (grid) {
        if (CATS.length > 0) {
          grid.innerHTML = CATS.map(c => {
            const countLabel = c.c === 1 ? '1 comercio' : `${c.c} comercios`;
            return `
  <div class="cat" data-cat="${c.k}">
    <div class="cat-art" style="background:${c.g}">${ico(c.i)}</div>
    <div class="cat-body"><b>${escapeHtml(c.n)}</b><span>${countLabel}</span></div>
  </div>`;
          }).join('');
        } else {
          grid.innerHTML = `<div style="padding:1.5rem;color:var(--muted);font-size:.92rem">No hay categorías con comercios disponibles.</div>`;
        }
      }

      const fCats = $('#f-cats');
      if (fCats) {
        fCats.innerHTML = `<span class="chip ${state.cat === 'all' ? 'on' : ''}" data-c="all">Todas</span>` +
          CATS.map(c => `<span class="chip ${state.cat === c.k ? 'on' : ''}" data-c="${c.k}">${escapeHtml(c.n)}</span>`).join('');
      }

      const bCat = $('#b-cat');
      if (bCat) {
        const currentVal = bCat.value;
        let html = '<option value="all">comercio</option>';
        CATS.forEach(c => {
          html += `<option value="${c.k}">${escapeHtml(c.n.toLowerCase())}</option>`;
        });
        bCat.innerHTML = html;
        bCat.value = (currentVal && CATS.some(c => c.k === currentVal)) ? currentVal : 'all';
      }
    }

    function renderBusinesses(bizList) {
      if (Array.isArray(bizList)) {
        BIZ.length = 0;
        bizList.forEach(b => BIZ.push(b));
      }
      const homeCards = $('#home-cards');
      if (homeCards) homeCards.innerHTML = BIZ.slice(0, 6).map(cardHTML).join('');

      const allCards = $('#all-cards');
      if (allCards) allCards.innerHTML = BIZ.map(cardHTML).join('');

      const allCount = $('#all-count');
      if (allCount) {
        allCount.textContent = BIZ.length === 1 ? '1 comercio' : `${BIZ.length} comercios`;
      }

      updateHeroCount();
      refresh();
    }

    function updateStatsUI(stats) {
      if (!stats) return;
      const numVerif = stats.verificadas ?? 0;
      const textoVerif = numVerif === 1 ? '1 comercio verificado' : `${numVerif} comercios verificados`;
      const heroVerif = $('#hero-verified-count');
      if (heroVerif) heroVerif.textContent = `Venezuela · ${textoVerif}`;
    }

    function updateHeroCount() {
      const bCount = $('#b-count');
      const bCountText = $('#b-count-text');
      if (!bCount) return;

      const n = BIZ.filter(b => {
        if (state.cat !== 'all' && b.cat !== state.cat) return false;
        for (const p of state.pays) {
          if (!b.pays.includes(p)) return false;
        }
        const bZona = $('#b-zona')?.value;
        if (bZona && bZona !== 'all' && !b.z.toLowerCase().includes(bZona.toLowerCase())) return false;
        return true;
      }).length;

      bCount.textContent = n.toLocaleString('es-VE');
      if (bCountText) {
        bCountText.textContent = n === 1 ? 'comercio coincide ahora mismo' : 'comercios coinciden ahora mismo';
      }
    }

    /* ===================== filtros interactivos ===================== */
    $('#q')?.addEventListener('input', e => { state.q = e.target.value; refresh() });
    $('#dist')?.addEventListener('input', e => { state.dist = +e.target.value; $('#d-val').textContent = (+e.target.value).toFixed(1).replace('.', ',') + ' km'; refresh() });
    $$('#f-pays .pay-toggle').forEach(p => p.addEventListener('click', () => {
      p.classList.toggle('on'); p.classList.contains('on') ? state.pays.add(p.dataset.p) : state.pays.delete(p.dataset.p); refresh()
    }));
    [['sw-open', 'open'], ['sw-del', 'del'], ['sw-ver', 'ver']].forEach(([id, k]) => {
      const el = $('#' + id);
      if (!el) return;
      const flip = () => { el.classList.toggle('on'); state[k] = el.classList.contains('on'); refresh() };
      el.addEventListener('click', flip);
      el.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip() } });
    });
    $('#clear')?.addEventListener('click', () => {
      state.cat = 'all'; state.pays.clear(); state.q = ''; state.dist = 15; state.open = false; state.del = false; state.ver = false;
      if ($('#q')) $('#q').value = '';
      if ($('#dist')) $('#dist').value = 15;
      if ($('#d-val')) $('#d-val').textContent = '15,0 km';
      $$('#f-pays .pay-toggle').forEach(p => p.classList.remove('on'));
      $$('#b-pays .pay-toggle').forEach(p => p.classList.remove('on'));
      $$('#f-cats .chip').forEach(c => c.classList.toggle('on', c.dataset.c === 'all'));
      const bCat = $('#b-cat');
      if (bCat) bCat.value = 'all';
      const bZona = $('#b-zona');
      if (bZona) bZona.value = 'all';
      $$('.sw').forEach(s => s.classList.remove('on'));
      updateHeroCount();
      refresh(); toast('Filtros limpios');
    });
    $('#locate')?.addEventListener('click', () => { if (map) map.flyTo([10.4950, -66.8560], 14); toast('Centrando en tu ubicación aproximada') });

    /* filtro del hero */
    $$('#b-pays .pay-toggle').forEach(p => p.addEventListener('click', () => {
      p.classList.toggle('on');
      p.classList.contains('on') ? state.pays.add(p.dataset.p) : state.pays.delete(p.dataset.p);
      $$('#f-pays .pay-toggle').forEach(f => f.classList.toggle('on', state.pays.has(f.dataset.p)));
      updateHeroCount();
      refresh();
    }));
    $('#b-cat')?.addEventListener('change', e => {
      state.cat = e.target.value;
      $$('#f-cats .chip').forEach(c => c.classList.toggle('on', c.dataset.c === state.cat));
      updateHeroCount();
      refresh();
    });
    $('#b-zona')?.addEventListener('change', () => {
      updateHeroCount();
    });

    /* ===================== inicialización de mapas ===================== */
    function initMaps() {
      const heroEl = document.getElementById('hero-map');
      if (heroEl && !heroEl._leaflet_id) {
        const hero = L.map('hero-map', { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false }).setView([10.4955, -66.8560], 14);
        L.tileLayer(TILE, { attribution: ATTR }).addTo(hero);
        BIZ.slice(0, 7).forEach(b => L.marker([b.lat, b.lng], { icon: pinIcon(b) }).addTo(hero));
      }

      const mapEl = document.getElementById('map');
      if (mapEl && !mapEl._leaflet_id) {
        map = L.map('map', { zoomControl: true }).setView([10.4955, -66.8560], 14);
        L.tileLayer(TILE, { attribution: ATTR }).addTo(map);
        layer = L.layerGroup().addTo(map);
      }

      const pickEl = document.getElementById('pick-map');
      if (pickEl && !pickEl._leaflet_id) {
        pickMap = L.map('pick-map', { zoomControl: false }).setView([10.4975, -66.8542], 15);
        L.tileLayer(TILE, { attribution: ATTR }).addTo(pickMap);
        const mk = L.marker([10.4975, -66.8542], {
          draggable: true, icon: L.divIcon({
            className: '', iconSize: [34, 42], iconAnchor: [17, 40],
            html: `<div class="pin"><div class="pin-body"><svg viewBox="0 0 24 24"><use href="#i-pin"></use></svg></div></div>`
          })
        }).addTo(pickMap);
        mk.on('dragend', () => { const p = mk.getLatLng(); toast(`Ubicación fijada en ${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`) });
      }
      refresh();
    }
    window.initMaps = initMaps;

    /* ===================== sincronización API ===================== */
    async function loadBusinessesFromAPI() {
      try {
        const res = await fetch('api/empresas/list.php');
        const json = await res.json();
        if (json.success && json.data) {
          if (json.data.categorias) {
            renderCategories(json.data.categorias);
          }
          if (json.data.comercios) {
            renderBusinesses(json.data.comercios);
          }
          if (json.data.stats) {
            updateStatsUI(json.data.stats);
          }
        }
      } catch (err) {
        console.warn('Error al sincronizar datos desde API:', err);
      }
    }
    window.loadBusinessesFromAPI = loadBusinessesFromAPI;

    /* ===================== arranque inicial ===================== */
    renderCategories(CATS);
    renderBusinesses(BIZ);
    if (window.SPOT_INITIAL_DATA && window.SPOT_INITIAL_DATA.stats) {
      updateStatsUI(window.SPOT_INITIAL_DATA.stats);
    }

    /* ===================== detalle ===================== */
    function openDetail(id) {
      const b = BIZ.find(x => x.id === id); if (!b) return;
      const c = CATMAP[b.cat] || { g: 'linear-gradient(135deg,#0F9B8E,#0A6E64)', i: 'i-tienda', n: b.cat || 'Comercio' };
      $('#d-art').style.background = c.g; $('#d-art').innerHTML = ico(c.i);
      $('#d-cat').textContent = c.n + ' · ' + (b.z || 'Caracas');
      $('#d-name').textContent = b.n;
      $('#d-meta').innerHTML = `<span>${b.open ? 'Abierto ahora' : 'Cerrado'}</span><span class="sep"></span><span>${escapeHtml(b.p || '$$')}</span><span class="sep"></span><span class="mono">${b.rv || 1} valoraciones</span>`;
      $('#d-desc').textContent = b.desc || 'Ficha creada por el propietario. Aún sin descripción.';
      $('#d-pays').innerHTML = (b.pays || []).map(p => payChip(p)).join('');
      $('#d-verif').textContent = b.ver ? 'Métodos confirmados por el comercio hace 3 días.' : 'Métodos aún sin verificar por el equipo de Spot.';
      $('#d-hours').innerHTML = [['Lunes a viernes', '8:00 – 20:00'], ['Sábado', '9:00 – 18:00'], ['Domingo', 'Cerrado']]
        .map(([d, h]) => `<div class="kv"><span>${d}</span><b class="mono">${h}</b></div>`).join('');
      $('#d-rate').innerHTML = ico('i-estrella') + (b.r ? Number(b.r).toFixed(1) : '5.0');
      $('#d-addr').textContent = b.dir || 'Dirección no especificada';
      $('#d-dist').textContent = (b.d ? Number(b.d).toFixed(1) : '1.0') + ' km';
      $('#d-price').textContent = b.p || '$$';
      $('#d-del').textContent = b.del ? 'Sí' : 'No disponible';

      function buildSocialIconBtn(nombre, valor, customIcon = '') {
        const nom = (nombre || 'Enlace').trim();
        const val = (valor || '').trim();
        if (!val) return '';

        const lowerNom = nom.toLowerCase();
        let url = val;
        let iconClass = customIcon;
        let btnClass = 'social-btn-' + lowerNom.replace(/[^a-z0-9]/g, '-');
        let title = `${nom}: ${val}`;

        if (lowerNom.includes('insta')) {
          const handle = val.replace(/^@/, '');
          url = `https://instagram.com/${handle}`;
          iconClass = iconClass || 'fa-brands fa-instagram';
          btnClass = 'social-btn-instagram';
          title = `Instagram (@${handle})`;
        } else if (lowerNom.includes('whats') || lowerNom.includes('ws')) {
          const num = val.replace(/[^0-9]/g, '');
          url = `https://wa.me/${num}`;
          iconClass = iconClass || 'fa-brands fa-whatsapp';
          btnClass = 'social-btn-whatsapp';
          title = `WhatsApp (${val})`;
        } else if (lowerNom.includes('tik')) {
          const handle = val.replace(/^@/, '');
          url = `https://tiktok.com/@${handle}`;
          iconClass = iconClass || 'fa-brands fa-tiktok';
          btnClass = 'social-btn-tiktok';
          title = `TikTok (@${handle})`;
        } else if (lowerNom.includes('web') || lowerNom.includes('sitio')) {
          url = val.startsWith('http') ? val : 'https://' + val;
          iconClass = iconClass || 'fa-solid fa-globe';
          btnClass = 'social-btn-web';
          title = `Sitio Web (${url})`;
        } else if (lowerNom.includes('face') || lowerNom.includes('fb')) {
          url = val.startsWith('http') ? val : (val.startsWith('@') ? `https://facebook.com/${val.slice(1)}` : `https://facebook.com/${val}`);
          iconClass = iconClass || 'fa-brands fa-facebook';
          btnClass = 'social-btn-facebook';
          title = `Facebook (${val})`;
        } else if (lowerNom.includes('telegr') || lowerNom.includes('tg')) {
          url = val.startsWith('http') ? val : (val.startsWith('@') ? `https://t.me/${val.slice(1)}` : `https://t.me/${val}`);
          iconClass = iconClass || 'fa-brands fa-telegram';
          btnClass = 'social-btn-telegram';
          title = `Telegram (${val})`;
        } else if (lowerNom.includes('you') || lowerNom.includes('yt')) {
          url = val.startsWith('http') ? val : `https://youtube.com/${val.startsWith('@') ? val : '@' + val}`;
          iconClass = iconClass || 'fa-brands fa-youtube';
          btnClass = 'social-btn-youtube';
          title = `YouTube (${val})`;
        } else if (lowerNom.includes('twitter') || lowerNom.includes(' x') || lowerNom === 'x') {
          url = val.startsWith('http') ? val : (val.startsWith('@') ? `https://x.com/${val.slice(1)}` : `https://x.com/${val}`);
          iconClass = iconClass || 'fa-brands fa-x-twitter';
          btnClass = 'social-btn-twitter';
          title = `X / Twitter (${val})`;
        } else if (lowerNom.includes('link')) {
          url = val.startsWith('http') ? val : `https://linkedin.com/in/${val}`;
          iconClass = iconClass || 'fa-brands fa-linkedin-in';
          btnClass = 'social-btn-linkedin';
          title = `LinkedIn (${val})`;
        } else if (lowerNom.includes('disc')) {
          url = val.startsWith('http') ? val : `https://discord.gg/${val}`;
          iconClass = iconClass || 'fa-brands fa-discord';
          btnClass = 'social-btn-discord';
          title = `Discord (${val})`;
        } else if (lowerNom.includes('thread')) {
          const handle = val.replace(/^@/, '');
          url = `https://threads.net/@${handle}`;
          iconClass = iconClass || 'fa-brands fa-threads';
          btnClass = 'social-btn-threads';
          title = `Threads (@${handle})`;
        } else if (lowerNom.includes('pinter')) {
          url = val.startsWith('http') ? val : `https://pinterest.com/${val}`;
          iconClass = iconClass || 'fa-brands fa-pinterest';
          btnClass = 'social-btn-pinterest';
          title = `Pinterest (${val})`;
        } else {
          const catalog = window.SPOT_REDES_CATALOG || [];
          const match = catalog.find(x => x.nombre.toLowerCase() === lowerNom);
          if (match) {
            iconClass = match.icono;
            if (match.url_base && !val.startsWith('http')) {
              url = match.url_base + val.replace(/^@/, '');
            }
          }
          if (!iconClass) iconClass = 'fa-solid fa-arrow-up-right-from-square';
          if (!url.startsWith('http')) url = 'https://' + url;
        }

        return `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="social-icon-btn ${btnClass}" title="${escapeHtml(title)}" aria-label="${escapeHtml(nom)}">
          <i class="${escapeHtml(iconClass)}"></i>
        </a>`;
      }

      const redesSection = $('#d-redes-section');
      const redesBox = $('#d-redes');
      if (redesSection && redesBox) {
        redesBox.innerHTML = '';
        const r = b.redes || {};
        let count = 0;
        let btnsHtml = '';

        if (r.instagram) {
          btnsHtml += buildSocialIconBtn('Instagram', r.instagram);
          count++;
        }
        if (r.whatsapp) {
          btnsHtml += buildSocialIconBtn('WhatsApp', r.whatsapp);
          count++;
        }
        if (r.tiktok) {
          btnsHtml += buildSocialIconBtn('TikTok', r.tiktok);
          count++;
        }
        if (r.web) {
          btnsHtml += buildSocialIconBtn('Sitio Web', r.web);
          count++;
        }
        if (Array.isArray(r.otras)) {
          r.otras.forEach(item => {
            if (!item || !item.nombre || !item.valor) return;
            btnsHtml += buildSocialIconBtn(item.nombre, item.valor, item.icono || '');
            count++;
          });
        }
        redesBox.innerHTML = btnsHtml;
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

        const redesList = [];
        if ($('#n-insta')?.value.trim()) redesList.push(`Instagram: ${$('#n-insta').value.trim()}`);
        if ($('#n-ws')?.value.trim()) redesList.push(`WhatsApp: ${$('#n-ws').value.trim()}`);
        if ($('#n-tiktok')?.value.trim()) redesList.push(`TikTok: ${$('#n-tiktok').value.trim()}`);
        if ($('#n-web')?.value.trim()) redesList.push(`Web: ${$('#n-web').value.trim()}`);
        $$('#container-otras-redes-negocio .dynamic-red-row').forEach(row => {
          const nom = row.querySelector('.input-red-nombre')?.value.trim();
          const val = row.querySelector('.input-red-valor')?.value.trim();
          if (nom && val) redesList.push(`${nom}: ${val}`);
        });

        const rRedesRow = $('#r-redes-row');
        const rRedes = $('#r-redes');
        if (rRedesRow && rRedes) {
          if (redesList.length > 0) {
            rRedesRow.style.display = 'flex';
            rRedes.innerHTML = redesList.map(r => `<span class="badge" style="font-weight:400; font-size:.78rem;">${escapeHtml(r)}</span>`).join(' ');
          } else {
            rRedesRow.style.display = 'none';
          }
        }

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
