  <!-- ============ MODAL EDITAR COMERCIO (ADMIN CRUD) ============ -->
  <div class="overlay" id="modal-edit-empresa">
    <div class="modal" style="width:min(580px, 100%)" role="dialog" aria-modal="true" aria-label="Editar Comercio">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
        <h3 style="margin:0;">Editar Información del Comercio</h3>
        <button class="icon-btn" id="btn-close-edit-empresa" aria-label="Cerrar"><svg class="ico"><use href="#i-x"></use></svg></button>
      </div>

      <form id="form-edit-empresa" onsubmit="return false;">
        <input type="hidden" id="edit-emp-id">
        <input type="hidden" id="edit-emp-logo-url">
        <div id="edit-emp-error" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem;"></div>

        <div class="f">
          <label for="edit-emp-nombre">Nombre del Comercio</label>
          <input type="text" id="edit-emp-nombre" placeholder="Nombre comercial" required>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="edit-emp-cat">Categoría</label>
            <select id="edit-emp-cat">
              <option value="1">Restaurantes</option>
              <option value="2">Cafés</option>
              <option value="3">Panaderías</option>
              <option value="4">Supermercados</option>
              <option value="5">Hoteles</option>
              <option value="6">Tiendas</option>
              <option value="7">Entretenimiento</option>
              <option value="8">Servicios</option>
              <option value="9">Tecnología</option>
            </select>
          </div>
          <div class="f">
            <label for="edit-emp-rif">RIF <em>· opcional</em></label>
            <input type="text" id="edit-emp-rif" placeholder="J-12345678-9">
          </div>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="edit-emp-usuario">Dueño Asignado</label>
            <select id="edit-emp-usuario" required>
              <option value="1">Administrador Spot</option>
            </select>
          </div>
          <div class="f">
            <label for="edit-emp-estado">Estado de la Ficha</label>
            <select id="edit-emp-estado">
              <option value="aprobado">Aprobado (Visible en el mapa)</option>
              <option value="pendiente">Pendiente por revisar</option>
              <option value="rechazado">Rechazado</option>
            </select>
          </div>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="edit-emp-tel">Teléfono</label>
            <input type="tel" id="edit-emp-tel" placeholder="0412 000 0000">
          </div>
          <div class="f">
            <label for="edit-emp-correo">Correo de Contacto</label>
            <input type="email" id="edit-emp-correo" placeholder="contacto@comercio.com">
          </div>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="edit-emp-zona">Zona / Municipio</label>
            <input type="text" id="edit-emp-zona" placeholder="Chacao, Las Mercedes..." required>
          </div>
          <div class="f">
            <label for="edit-emp-dir">Dirección</label>
            <input type="text" id="edit-emp-dir" placeholder="Av. o Calle" required>
          </div>
        </div>

        <div class="f">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.3rem;">
            <label style="font-weight:600; margin:0;">Redes Sociales y Enlaces <em>· opcional</em></label>
            <button type="button" class="btn btn-ghost btn-sm" id="btn-add-red-edit" style="display:inline-flex; align-items:center; gap:.4rem; font-size:.82rem; padding:.3rem .7rem; border-color:var(--line);">
              <svg class="ico" style="width:14px; height:14px; stroke-width:2.5;"><use href="#i-plus"></use></svg> Agregar red social
            </button>
          </div>
          <div class="f-row">
            <div class="f">
              <label for="edit-emp-instagram" style="font-size:.78rem;">Instagram</label>
              <input type="text" id="edit-emp-instagram" placeholder="@usuario">
            </div>
            <div class="f">
              <label for="edit-emp-whatsapp" style="font-size:.78rem;">WhatsApp</label>
              <input type="tel" id="edit-emp-whatsapp" placeholder="+58 412 0000000">
            </div>
          </div>
          <div class="f-row" style="margin-top:.4rem;">
            <div class="f">
              <label for="edit-emp-tiktok" style="font-size:.78rem;">TikTok</label>
              <input type="text" id="edit-emp-tiktok" placeholder="@usuario">
            </div>
            <div class="f">
              <label for="edit-emp-web" style="font-size:.78rem;">Sitio Web / Menú</label>
              <input type="url" id="edit-emp-web" placeholder="https://...">
            </div>
          </div>
          <div id="container-otras-redes-edit" style="display:flex; flex-direction:column; gap:.6rem; margin-top:.6rem;"></div>
        </div>

        <div class="f">
          <label>Métodos de Pago Aceptados</label>
          <div class="pay-row" id="edit-emp-pays" style="margin-top:.4rem">
            <span class="pay pay-cashea pay-toggle" data-p="cashea"><i>C</i>Cashea</span>
            <span class="pay pay-zelle pay-toggle" data-p="zelle"><i>Z</i>Zelle</span>
            <span class="pay pay-zinlli pay-toggle" data-p="zinlli"><i>Z</i>Zinlli</span>
            <span class="pay pay-paypal pay-toggle" data-p="paypal"><i>P</i>PayPal</span>
            <span class="pay pay-movil pay-toggle" data-p="movil"><i>PM</i>Pago Móvil</span>
            <span class="pay pay-punto pay-toggle" data-p="punto"><i>P</i>Punto</span>
            <span class="pay pay-binance pay-toggle" data-p="binance"><i>B</i>Binance</span>
            <span class="pay pay-efectivo pay-toggle" data-p="efectivo"><i>$</i>Efectivo</span>
          </div>
        </div>

        <div class="f">
          <label>Logo o Foto de Portada</label>
          <div style="display:flex; align-items:center; gap:1rem; margin-top:.4rem;">
            <div id="edit-logo-preview" style="width:60px; height:60px; border-radius:8px; border:1px solid var(--line); display:grid; place-items:center; overflow:hidden; background:var(--bg);">
              <svg class="ico" style="color:var(--muted);"><use href="#i-tienda"></use></svg>
            </div>
            <div>
              <input type="file" id="edit-emp-file" accept="image/jpeg,image/png,image/webp" style="display:none">
              <button type="button" class="btn btn-ghost btn-sm" id="btn-change-edit-logo">Cambiar Imagen</button>
              <span id="edit-upload-status" style="display:block; font-size:.78rem; color:var(--muted); margin-top:.2rem;">JPG, PNG o WebP hasta 5MB</span>
            </div>
          </div>
        </div>

        <!-- Fotos del Comercio (Galería del Local y Equipo) -->
        <div class="f">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.4rem;">
            <div>
              <label style="margin:0;">Fotos del Local y Equipo</label>
              <span style="display:block; font-size:.75rem; color:var(--muted);">Fachada, instalaciones, equipo de trabajo o platos</span>
            </div>
            <div>
              <input type="file" id="edit-emp-fotos-file" accept="image/jpeg,image/png,image/webp" multiple style="display:none">
              <button type="button" class="btn btn-ghost btn-sm" id="btn-add-edit-fotos" style="padding:.35rem .7rem; font-size:.8rem; gap:.3rem;">
                <i class="fa-solid fa-cloud-arrow-up"></i> Añadir Fotos
              </button>
            </div>
          </div>
          <span id="edit-galeria-status" style="display:none; font-size:.78rem; color:var(--brand); margin-bottom:.4rem;"></span>
          <div id="edit-galeria-container" class="admin-photos-grid">
            <!-- Renderizado dinámico de miniaturas con botón eliminar -->
          </div>
        </div>

        <div class="f">
          <label for="edit-emp-desc">Descripción</label>
          <textarea id="edit-emp-desc" placeholder="Breve descripción del comercio"></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:.6rem; margin-top:1.5rem;">
          <button type="button" class="btn btn-ghost" id="btn-cancel-edit-empresa">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btn-submit-edit-empresa">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

