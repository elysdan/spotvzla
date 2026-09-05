  <!-- ============ MODAL CREAR USUARIO (ADMIN) ============ -->
  <div class="overlay" id="modal-user">
    <div class="modal" role="dialog" aria-modal="true" aria-label="Crear Nuevo Usuario">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
        <h3 style="margin:0;">Crear Nuevo Usuario</h3>
        <button class="icon-btn" id="btn-close-modal-user" aria-label="Cerrar"><svg class="ico">
            <use href="#i-x"></use>
          </svg></button>
      </div>
      
      <form id="form-create-user" onsubmit="return false;">
        <div id="user-create-error" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem;"></div>

        <div class="f">
          <label for="u-nom">Nombre Completo</label>
          <input type="text" id="u-nom" placeholder="Ej. Alejandro Pérez" required>
        </div>

        <div class="f">
          <label for="u-mail">Correo Electrónico</label>
          <input type="email" id="u-mail" placeholder="ejemplo@spotvzla.com" required>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="u-tel">Teléfono</label>
            <input type="tel" id="u-tel" placeholder="0412 1234567">
          </div>
          <div class="f">
            <label for="u-rol">Rol del Usuario</label>
            <select id="u-rol">
              <option value="empresa" selected>Dueño de Comercio</option>
              <option value="usuario">Usuario Regular</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
        </div>

        <div class="f">
          <label for="u-pass">Contraseña Inicial</label>
          <input type="password" id="u-pass" placeholder="Mínimo 6 caracteres" minlength="6" required>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:.6rem; margin-top:1.5rem;">
          <button type="button" class="btn btn-ghost" id="btn-cancel-create-user">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btn-submit-create-user">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>

