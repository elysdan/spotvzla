  <!-- ============ MODAL EDITAR USUARIO (ADMIN CRUD) ============ -->
  <div class="overlay" id="modal-edit-user">
    <div class="modal" role="dialog" aria-modal="true" aria-label="Editar Usuario">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
        <h3 style="margin:0;">Editar Usuario</h3>
        <button class="icon-btn" id="btn-close-modal-edit-user" aria-label="Cerrar"><svg class="ico">
            <use href="#i-x"></use>
          </svg></button>
      </div>
      
      <form id="form-edit-user" onsubmit="return false;">
        <input type="hidden" id="edit-user-id">
        <div id="user-edit-error" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem;"></div>

        <div class="f">
          <label for="edit-u-nom">Nombre Completo</label>
          <input type="text" id="edit-u-nom" placeholder="Ej. Alejandro Pérez" required>
        </div>

        <div class="f">
          <label for="edit-u-mail">Correo Electrónico</label>
          <input type="email" id="edit-u-mail" placeholder="ejemplo@spotvzla.com" required>
        </div>

        <div class="f-row">
          <div class="f">
            <label for="edit-u-tel">Teléfono</label>
            <input type="tel" id="edit-u-tel" placeholder="0412 1234567">
          </div>
          <div class="f">
            <label for="edit-u-rol">Rol del Usuario</label>
            <select id="edit-u-rol">
              <option value="empresa">Dueño de Comercio</option>
              <option value="usuario">Usuario Regular</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
        </div>

        <div class="f">
          <label for="edit-u-estado">Estado de la Cuenta</label>
          <select id="edit-u-estado">
            <option value="activo">Activo (Acceso permitido)</option>
            <option value="inactivo">Inactivo (Acceso suspendido)</option>
            <option value="bloqueado">Bloqueado</option>
          </select>
        </div>

        <div class="f">
          <label for="edit-u-pass">Nueva Contraseña <em>· opcional</em></label>
          <input type="password" id="edit-u-pass" placeholder="Dejar en blanco para conservar la actual" minlength="6">
          <p class="hint">Solo escribe una contraseña si deseas cambiarla (mínimo 6 caracteres).</p>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:.6rem; margin-top:1.5rem;">
          <button type="button" class="btn btn-ghost" id="btn-cancel-edit-user">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btn-submit-edit-user">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
