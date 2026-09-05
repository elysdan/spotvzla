  <main id="v-detalle" class="view">
    <div class="wrap detail">
      <div>
        <button class="btn btn-ghost btn-sm" data-go="mapa" style="margin-bottom:1.2rem">← Volver al mapa</button>
        <div class="detail-art" id="d-art"></div>
        <span class="eyebrow" id="d-cat"></span>
        <h2 style="margin:.6rem 0 .7rem" id="d-name"></h2>
        <div class="meta" id="d-meta"></div>
        <p style="margin-top:1.4rem; color:var(--ink-2); max-width:60ch" id="d-desc"></p>

        <div class="flabel">Cómo puedes pagar aquí</div>
        <div class="pay-row" id="d-pays"></div>
        <p class="hint" id="d-verif"></p>

        <div id="d-redes-section" style="display:none; margin-top:1.4rem;">
          <div class="flabel">Redes Sociales y Contacto</div>
          <div id="d-redes" class="social-icons-wrap"></div>
        </div>

        <!-- Galería de fotos del comercio (Local, Fachada y Equipo) -->
        <div id="d-galeria-section" style="display:none; margin-top:1.8rem;">
          <div class="flabel" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Fotos del Local y Equipo</span>
            <span id="d-galeria-count" style="font-size:.78rem; color:var(--muted); font-weight:normal;"></span>
          </div>
          <div id="d-galeria" class="comercio-gallery-grid"></div>
        </div>

        <div class="flabel">Horario</div>
        <div style="max-width:420px" id="d-hours"></div>
      </div>
      <aside>
        <div class="side">
          <div style="display:flex; align-items:baseline; justify-content:space-between">
            <span class="eyebrow">Valoración</span>
            <span class="rate" id="d-rate"></span>
          </div>
          <div class="detail-map" id="d-map"></div>
          <div style="margin-top:1.1rem">
            <div class="kv"><span>Dirección</span><b id="d-addr" style="text-align:right"></b></div>
            <div class="kv"><span>Distancia</span><b class="mono" id="d-dist"></b></div>
            <div class="kv"><span>Rango de precio</span><b id="d-price"></b></div>
            <div class="kv"><span>Delivery</span><b id="d-del"></b></div>
          </div>
          <button class="btn btn-primary" style="width:100%; margin-top:1.2rem"
            data-toast="Abriendo la ruta en tu app de mapas…">Cómo llegar</button>
          <button class="btn btn-ghost" style="width:100%; margin-top:.5rem"
            data-toast="Gracias, revisaremos el reporte.">Reportar un cambio</button>
        </div>
      </aside>
    </div>
  </main>

  <!-- Modal Lightbox para visualización de fotos en grande -->
  <div class="overlay lightbox-overlay" id="modal-lightbox" role="dialog" aria-modal="true" aria-label="Visor de Fotos">
    <div class="lightbox-container">
      <button class="lightbox-close-btn" id="lightbox-close" aria-label="Cerrar visor">
        <i class="fa-solid fa-xmark"></i>
      </button>
      
      <button class="lightbox-nav-btn lightbox-prev" id="lightbox-prev" aria-label="Foto anterior">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
      
      <div class="lightbox-media-wrap">
        <img id="lightbox-img" src="" alt="Foto del comercio">
        <div class="lightbox-caption-bar">
          <span id="lightbox-title"></span>
          <span id="lightbox-counter" class="mono"></span>
        </div>
      </div>

      <button class="lightbox-nav-btn lightbox-next" id="lightbox-next" aria-label="Foto siguiente">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </div>
  </div>
  <!-- ============ REGISTRO DE NEGOCIO ============ -->
