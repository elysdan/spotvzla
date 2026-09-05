  <main id="v-categorias" class="view">
    <section class="sec" style="padding-bottom:2rem">
      <div class="wrap">
        <span class="eyebrow">Directorio</span>
        <h2 style="margin:.6rem 0 .6rem">Explora comercios por categoría</h2>
        <p style="color:var(--muted); max-width:56ch">Cada categoría muestra cuántos locales aceptan hoy cada método de
          pago.</p>
        <div class="grid-cards" style="margin-top:2rem; grid-template-columns:repeat(auto-fill,minmax(238px,1fr))"
          id="cat-grid"></div>

        <div class="flabel" style="margin-top:2.6rem">Filtros rápidos</div>
        <div class="chips" id="quick-chips">
          <span class="chip">Acepta Cashea</span><span class="chip">Zelle disponible</span>
          <span class="chip">Punto de venta</span><span class="chip">Con delivery</span>
          <span class="chip">Abierto ahora</span><span class="chip">Menos de 1 km</span>
        </div>

        <div class="sec-head" style="margin-top:3rem">
          <div><span class="eyebrow">Resultados</span>
            <h2 style="margin-top:.5rem; font-size:1.7rem">Todos los comercios</h2>
          </div>
          <span class="mono" style="font-size:.8rem; color:var(--muted)" id="all-count"></span>
        </div>
        <div class="grid-cards" id="all-cards"></div>
      </div>
    </section>
  </main>

  <!-- ============ DETALLE ============ -->
