  <main id="v-negocio" class="view">
    <div class="wrap wizard">
      <span class="eyebrow">Para dueños de comercio</span>
      <h2 style="margin-top:.6rem">Publica tu comercio en Spot</h2>
      <p style="color:var(--muted); margin-top:.7rem">Tres pasos. Guardamos el avance, puedes terminar después.</p>

      <div class="wz-steps">
        <div class="wz-step act" data-s="1">
          <div class="wz-bar"></div><small>01 · Tu comercio</small>
        </div>
        <div class="wz-step" data-s="2">
          <div class="wz-bar"></div><small>02 · Ubicación y pagos</small>
        </div>
        <div class="wz-step" data-s="3">
          <div class="wz-bar"></div><small>03 · Confirmación</small>
        </div>
      </div>

      <!-- paso 1 -->
      <section class="panel wz-panel" data-p="1">
        <div class="f"><label for="n-name">Nombre del comercio</label>
          <input type="text" id="n-name" placeholder="La Cocina de Mamá">
        </div>
        <div class="f-row">
          <div class="f"><label for="n-cat">Categoría</label>
            <select id="n-cat">
              <option>Restaurante</option>
              <option>Café</option>
              <option>Panadería</option>
              <option>Supermercado</option>
              <option>Hotel</option>
              <option>Tienda</option>
              <option>Servicios</option>
              <option>Entretenimiento</option>
              <option>Tecnología</option>
            </select>
          </div>
          <div class="f"><label for="n-rif">RIF <em>· opcional</em></label>
            <input type="text" id="n-rif" placeholder="J-123456789">
          </div>
        </div>
        <div class="f"><label for="n-desc">Descripción corta</label>
          <textarea id="n-desc"
            placeholder="Comida criolla casera, almuerzos ejecutivos y postres. Ambiente familiar."></textarea>
          <p class="hint">Aparece debajo del nombre en los resultados. Máximo 160 caracteres.</p>
        </div>
        <div class="f-row">
          <div class="f"><label for="n-tel">Teléfono</label><input type="tel" id="n-tel" placeholder="0412 000 0000">
          </div>
          <div class="f"><label for="n-mail">Correo de contacto</label><input type="email" id="n-mail"
              placeholder="hola@tucomercio.com"></div>
        </div>
        <div class="f-row">
          <div class="f"><label for="n-insta">Instagram <em>· opcional</em></label><input type="text" id="n-insta" placeholder="@tucomercio"></div>
          <div class="f"><label for="n-ws">WhatsApp <em>· opcional</em></label><input type="tel" id="n-ws" placeholder="+58 412 0000000"></div>
        </div>
        <div class="f-row">
          <div class="f"><label for="n-tiktok">TikTok <em>· opcional</em></label><input type="text" id="n-tiktok" placeholder="@tucomercio"></div>
          <div class="f"><label for="n-web">Sitio Web / Menú <em>· opcional</em></label><input type="url" id="n-web" placeholder="https://..."></div>
        </div>
        <div class="f" style="margin-top:.6rem;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.4rem;">
            <label style="font-weight:600; margin:0; font-size:.88rem;">Otras Redes Sociales <em>· opcional</em></label>
            <button type="button" class="btn btn-ghost btn-sm" id="btn-add-red-negocio" style="display:inline-flex; align-items:center; gap:.4rem; font-size:.82rem; padding:.35rem .75rem; border-color:var(--line);">
              <svg class="ico" style="width:14px; height:14px; stroke-width:2.5;"><use href="#i-plus"></use></svg> Agregar red social
            </button>
          </div>
          <div id="container-otras-redes-negocio" style="display:flex; flex-direction:column; gap:.6rem;"></div>
        </div>
        <div class="wz-foot"><span></span><button class="btn btn-primary" data-next="2">Continuar</button></div>
      </section>

      <!-- paso 2 -->
      <section class="panel wz-panel" data-p="2" hidden>
        <div class="f"><label>Ubicación exacta</label>
          <div class="picker" id="pick-map"></div>
          <p class="hint">Arrastra el pin hasta la entrada del local. Es lo que verá el cliente al pedir la ruta.</p>
        </div>
        <div class="f-row">
          <div class="f"><label for="n-dir">Dirección</label><input type="text" id="n-dir"
              placeholder="Av. Francisco de Miranda, Chacao"></div>
          <div class="f"><label for="n-zona">Zona</label>
            <select id="n-zona">
              <option>Chacao</option>
              <option>Las Mercedes</option>
              <option>Altamira</option>
              <option>La Castellana</option>
              <option>Sabana Grande</option>
              <option>Los Palos Grandes</option>
            </select>
          </div>
        </div>
        <div class="f"><label>Métodos de pago que aceptas</label>
          <div class="pay-row" id="n-pays" style="margin-top:.5rem">
            <span class="pay pay-cashea pay-toggle on" data-p="cashea"><i>C</i>Cashea</span>
            <span class="pay pay-zelle pay-toggle" data-p="zelle"><i>Z</i>Zelle</span>
            <span class="pay pay-zinlli pay-toggle" data-p="zinlli"><i>Z</i>Zinlli</span>
            <span class="pay pay-paypal pay-toggle" data-p="paypal"><i>P</i>PayPal</span>
            <span class="pay pay-movil pay-toggle on" data-p="movil"><i>PM</i>Pago Móvil</span>
            <span class="pay pay-punto pay-toggle on" data-p="punto"><i>P</i>Punto</span>
            <span class="pay pay-binance pay-toggle" data-p="binance"><i>B</i>Binance</span>
            <span class="pay pay-efectivo pay-toggle on" data-p="efectivo"><i>$</i>Efectivo</span>
          </div>
          <p class="hint">Puedes cambiarlos cuando quieras desde tu panel.</p>
        </div>
        <div class="f"><label>Logo o foto de portada</label>
          <input type="file" id="n-file" accept="image/jpeg,image/png,image/webp" style="display:none">
          <div class="drop" id="drop" role="button" tabindex="0" aria-label="Subir logo o foto de portada">
            <div id="drop-prompt">
              <svg class="ico" style="width:26px;height:26px;margin:0 auto .5rem">
                <use href="#i-subir"></use>
              </svg>
              <b style="display:block;color:var(--ink)">Arrastra una imagen o haz clic para elegirla</b>
              <span style="font-size:.8rem">JPG, PNG o WebP · hasta 5 MB</span>
            </div>
            <div id="drop-preview" style="display:none; flex-direction:column; align-items:center; gap:.5rem;">
              <img id="img-preview" src="" alt="Vista previa del logo" style="max-height:120px; max-width:100%; border-radius:var(--r-s); object-fit:contain; box-shadow:var(--shadow);">
              <span style="font-size:.82rem; color:var(--brand-ink); font-weight:600;">Clic para cambiar imagen</span>
            </div>
          </div>
          <p id="upload-status-text" class="hint" style="display:none; color:var(--brand);"></p>
        </div>
        <div class="wz-foot"><button class="btn btn-ghost" data-next="1">Atrás</button><button class="btn btn-primary"
            data-next="3">Revisar y enviar</button></div>
      </section>

      <!-- paso 3 -->
      <section class="panel wz-panel" data-p="3" hidden>
        <div class="demo-note"><svg>
            <use href="#i-info"></use>
          </svg> Así queda tu ficha. Revisa antes de enviarla a verificación.</div>
        <div class="review">
          <div class="kv"><span>Comercio</span><b id="r-name">—</b></div>
          <div class="kv"><span>Categoría</span><b id="r-cat">—</b></div>
          <div class="kv"><span>Zona</span><b id="r-zona">—</b></div>
          <div class="kv"><span>Contacto</span><b id="r-tel">—</b></div>
          <div class="kv"><span>Pagos</span><b id="r-pays" style="display:flex;gap:.25rem">—</b></div>
          <div class="kv" id="r-redes-row" style="display:none;"><span>Redes</span><b id="r-redes" style="display:flex; flex-wrap:wrap; gap:.3rem; font-size:.84rem;">—</b></div>
          <div class="kv" id="r-logo-row" style="display:none;"><span>Foto/Logo</span><b id="r-logo-wrap">—</b></div>
        </div>
        <div class="f" style="margin-top:1.4rem"><label
            style="display:flex; gap:.6rem; align-items:flex-start; font-weight:400; font-size:.9rem">
            <input type="checkbox" id="n-terms" checked style="margin-top:.25rem">
            <span>Confirmo que soy el responsable del comercio y que la información es correcta.</span></label></div>
        <div class="wz-foot"><button class="btn btn-ghost" data-next="2">Atrás</button><button class="btn btn-primary"
            id="send-biz">Enviar a verificación</button></div>
      </section>
    </div>
  </main>

  <!-- ============ PANEL ADMIN ============ -->
