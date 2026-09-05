  <main id="v-mapa" class="view">
    <div class="maplayout">
      <aside class="filters">
        <div class="field">
          <svg class="pre">
            <use href="#i-buscar"></use>
          </svg>
          <input type="text" id="q" placeholder="Nombre, categoría o método de pago" aria-label="Buscar comercios">
        </div>

        <div class="flabel">Categoría</div>
        <div class="chips" id="f-cats"></div>

        <div class="flabel">Métodos de pago aceptados</div>
        <div class="pay-row" id="f-pays">
          <span class="pay pay-cashea pay-toggle" data-p="cashea"><i>C</i>Cashea</span>
          <span class="pay pay-zelle pay-toggle" data-p="zelle"><i>Z</i>Zelle</span>
          <span class="pay pay-zinlli pay-toggle" data-p="zinlli"><i>Z</i>Zinlli</span>
          <span class="pay pay-paypal pay-toggle" data-p="paypal"><i>P</i>PayPal</span>
          <span class="pay pay-movil pay-toggle" data-p="movil"><i>PM</i>Pago Móvil</span>
          <span class="pay pay-punto pay-toggle" data-p="punto"><i>P</i>Punto</span>
          <span class="pay pay-binance pay-toggle" data-p="binance"><i>B</i>Binance</span>
          <span class="pay pay-efectivo pay-toggle" data-p="efectivo"><i>$</i>Efectivo</span>
        </div>
        <p class="hint">Mostramos los locales que aceptan <b>todos</b> los métodos que marques.</p>

        <div class="flabel">Distancia <span class="mono" id="d-val">5,0 km</span></div>
        <input type="range" id="dist" min="1" max="15" value="5" step="0.5" aria-label="Distancia máxima en kilómetros">

        <div class="flabel">Otros filtros</div>
        <div class="switch"><span>Abierto ahora</span><span class="sw" id="sw-open" role="switch" tabindex="0"
            aria-label="Solo abiertos ahora"></span></div>
        <div class="switch"><span>Con delivery</span><span class="sw" id="sw-del" role="switch" tabindex="0"
            aria-label="Solo con delivery"></span></div>
        <div class="switch"><span>Verificado por Spot</span><span class="sw on" id="sw-ver" role="switch" tabindex="0"
            aria-label="Solo verificados"></span></div>

        <div style="display:flex; gap:.6rem; margin-top:1.6rem">
          <button class="btn btn-ghost btn-sm" id="clear" style="flex:1">Limpiar filtros</button>
          <button class="btn btn-primary btn-sm" style="flex:1" data-go="categorias">Ver en lista</button>
        </div>
      </aside>

      <div class="map-wrap">
        <div id="map"></div>
        <div class="map-float">
          <button class="btn btn-ghost btn-sm" id="locate"><svg class="ico">
              <use href="#i-pin"></use>
            </svg> Mi ubicación</button>
        </div>
        <div class="reslist">
          <h4>
            <span id="res-count">0 comercios en esta zona</span>
            <span class="mono" style="font-size:.72rem; color:var(--muted)">ordenado por distancia</span>
          </h4>
          <div class="res-scroll" id="res-scroll"></div>
        </div>
      </div>
    </div>
  </main>

  <!-- ============ CATEGORÍAS ============ -->
