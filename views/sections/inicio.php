  <main id="v-inicio" class="view on">
    <section class="hero">
      <div class="wrap hero-grid">
        <div>
<?php
$numVerif = isset($statsPublicas['verificadas']) ? (int)$statsPublicas['verificadas'] : 0;
$textoVerif = $numVerif === 1 ? '1 comercio verificado' : "{$numVerif} comercios verificados";
$numTotalComercios = isset($comerciosAprobados) ? count($comerciosAprobados) : 0;
$textoCoinciden = $numTotalComercios === 1 ? 'comercio coincide ahora mismo' : 'comercios coinciden ahora mismo';
?>
          <span class="eyebrow" id="hero-verified-count">Venezuela · <?= $textoVerif ?></span>
          <h1 style="margin-top:1rem">Encuentra tu sitio ideal<br><span>y disfruta sin complicaciones.</span></h1>
          <p class="hero-sub">La plataforma para descubrir los mejores restaurantes, locales y sitios de
            entretenimiento cerca de ti. Organiza tu salida fácilmente y conoce toda la información antes de ir.</p>

          <div class="builder">
            <div class="builder-line">
              Busco un
              <span class="slot"><select id="b-cat">
                  <option value="all">comercio</option>
                  <?php if (!empty($categoriasActivas)): ?>
                    <?php foreach ($categoriasActivas as $c): ?>
                      <option value="<?= htmlspecialchars($c['k']) ?>"><?= htmlspecialchars(strtolower($c['n'])) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select><svg viewBox="0 0 24 24">
                  <path d="m6 9 6 6 6-6" />
                </svg></span>
              en
              <span class="slot"><select id="b-zona">
                  <option value="all">toda Caracas</option>
                  <option>Chacao</option>
                  <option>Las Mercedes</option>
                  <option>Altamira</option>
                  <option>Sabana Grande</option>
                  <option>La Castellana</option>
                  <option>San Bernardino</option>
                </select><svg viewBox="0 0 24 24">
                  <path d="m6 9 6 6 6-6" />
                </svg></span>
              que acepte
            </div>
            <div class="pay-row" style="margin-top:.9rem" id="b-pays">
              <span class="pay pay-cashea pay-toggle" data-p="cashea"><i>C</i>Cashea</span>
              <span class="pay pay-zelle pay-toggle" data-p="zelle"><i>Z</i>Zelle</span>
              <span class="pay pay-zinlli pay-toggle" data-p="zinlli"><i>Z</i>Zinlli</span>
              <span class="pay pay-paypal pay-toggle" data-p="paypal"><i>P</i>PayPal</span>
              <span class="pay pay-movil pay-toggle" data-p="movil"><i>PM</i>Pago Móvil</span>
              <span class="pay pay-punto pay-toggle" data-p="punto"><i>P</i>Punto</span>
              <span class="pay pay-binance pay-toggle" data-p="binance"><i>B</i>Binance</span>
              <span class="pay pay-efectivo pay-toggle" data-p="efectivo"><i>$</i>Efectivo</span>
            </div>
            <div class="builder-foot">
              <button class="btn btn-primary btn-lg" data-go="mapa">
                <svg class="ico">
                  <use href="#i-pin"></use>
                </svg> Ver en el mapa
              </button>
              <span class="builder-count"><b id="b-count"><?= $numTotalComercios ?></b> <span id="b-count-text"><?= $textoCoinciden ?></span></span>
            </div>
          </div>
        </div>

        <div>
          <div class="hero-card">
            <div class="hero-map" id="hero-map"></div>
            <div class="hero-legend">
              <svg class="ico" style="stroke:var(--brand)">
                <use href="#i-capas"></use>
              </svg>
              Cada pin muestra arriba los pagos que acepta el local.
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="sec sec-alt">
      <div class="wrap">
        <div class="sec-head">
          <div>
            <span class="eyebrow">Categorías</span>
            <h2 style="margin-top:.6rem">Explora por lo que necesitas</h2>
          </div>
          <button class="btn btn-ghost" data-go="categorias">Ver todas las categorías</button>
        </div>
        <div class="rail" id="cat-rail"></div>
      </div>
    </section>

    <section class="sec">
      <div class="wrap">
        <div class="sec-head">
          <div>
            <span class="eyebrow">Cómo funciona</span>
            <h2 style="margin-top:.6rem">Organiza tu salida en tres simples pasos</h2>
            <p>Descubre, elige y disfruta sin complicaciones.</p>
          </div>
        </div>
        <div class="steps">
          <div class="step">
            <span class="step-n">PASO 01</span>
            <h3>✨ Explora y Descubre</h3>
            <p>Encuentra los mejores restaurantes, cafés, bares y sitios de entretenimiento según la zona o lo que se te
              antoje hoy.</p>
          </div>
          <div class="step">
            <span class="step-n">PASO 02</span>
            <h3>📍 Compara opciones</h3>
            <p>Revisa fotos, ubicación en tiempo real, valoraciones y toda la información útil del local antes de salir
              de casa.</p>
          </div>
          <div class="step">
            <span class="step-n">PASO 03</span>
            <h3>🎉 Disfruta a tu manera</h3>
            <p>Arma tu ruta en el mapa y paga con tranquilidad sabiendo de antemano qué métodos de pago acepta el
              comercio.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="sec sec-alt">
      <div class="wrap">
        <div class="sec-head">
          <div>
            <span class="eyebrow">Destacados</span>
            <h2 style="margin-top:.6rem">Comercios recomendados esta semana</h2>
          </div>
          <button class="btn btn-ghost" data-go="mapa">Abrir el mapa completo</button>
        </div>
        <div class="grid-cards" id="home-cards"></div>
      </div>
    </section>

    <section class="sec">
      <div class="wrap">
        <div class="cta">
          <div>
            <span class="eyebrow" style="color:var(--brand)">Para dueños de comercio</span>
            <h2 style="margin-top:.7rem">Pon tu negocio en el mapa y atrae más clientes.</h2>
            <p>Haz que miles de personas encuentren tu local de forma rápida y visual. Muestra tus instalaciones,
              servicios y métodos de pago en una plataforma diseñada para hacer crecer tu marca.</p>
            <div style="margin-top:1.6rem; display:flex; gap:.7rem; flex-wrap:wrap">
              <button class="btn btn-primary btn-lg" data-go="negocio">Registrar mi comercio</button>
              <button class="btn btn-ghost btn-lg" style="border-color:currentColor" data-modal="login">Ya tengo
                cuenta</button>
            </div>
          </div>
          <div class="cta-list">
            <div><svg>
                <use href="#i-check"></use>
              </svg> Ficha completa: fotos, horarios, contacto y menú/servicios.</div>
            <div><svg>
                <use href="#i-check"></use>
              </svg> Aparece en el mapa y filtros de búsqueda en tiempo real.</div>
            <div><svg>
                <use href="#i-check"></use>
              </svg> Estadísticas de visitas y clientes interesados.</div>
            <div><svg>
                <use href="#i-check"></use>
              </svg> Registro 100% gratuito.</div>
          </div>
        </div>
      </div>
    </section>

    <section class="sec" style="padding-top:0">
      <div class="wrap" style="display:grid; grid-template-columns:1fr 1fr; gap:2.5rem; align-items:center">
        <div>
          <span class="eyebrow">Aplicación móvil</span>
          <h2 style="margin-top:.6rem">Lleva tu guía de salidas a todas partes</h2>
          <p style="color:var(--muted); margin-top:.8rem; max-width:44ch">Descarga Spot y descubre al instante los
            mejores sitios a tu alrededor, consulta información en tiempo real y planea tu ruta perfecta desde donde
            estés.</p>
        </div>
        <div class="store">
          <a href="#inicio"><svg class="ico">
              <use href="#i-pin"></use>
            </svg>
            <div><span>Descárgala en</span><b>App Store</b></div>
          </a>
          <a href="#inicio"><svg class="ico">
              <use href="#i-pin"></use>
            </svg>
            <div><span>Disponible en</span><b>Google Play</b></div>
          </a>
        </div>
      </div>
    </section>
  </main>
  <!-- ============ MAPA ============ -->
