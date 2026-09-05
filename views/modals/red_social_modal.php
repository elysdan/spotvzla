<!-- Modal Crear/Editar Red Social (Maestro) -->
<div class="overlay" id="modal-red-social">
  <div class="modal" style="width:min(480px, 100%)" role="dialog" aria-modal="true" aria-label="Maestro de Redes Sociales">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
      <div>
        <span class="eyebrow" id="modal-red-social-eyebrow">Maestro de Redes</span>
        <h3 style="margin-top:.3rem;" id="modal-red-social-title">Nueva Red Social</h3>
      </div>
      <button class="icon-btn" id="btn-close-red-social" aria-label="Cerrar"><svg class="ico"><use href="#i-x"></use></svg></button>
    </div>

    <form id="form-red-social" onsubmit="return false;">
      <input type="hidden" id="red-id" value="">
      <div id="red-social-error" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem;"></div>

      <div class="f">
        <label for="red-nombre">Nombre de la red social <span style="color:var(--hot)">*</span></label>
        <input type="text" id="red-nombre" placeholder="Ej: Discord, Twitch, Threads..." required>
      </div>

      <div class="f">
        <label for="red-icono">Clase de icono Font Awesome <span style="color:var(--hot)">*</span></label>
        <div style="display:flex; gap:.6rem; align-items:center;">
          <input type="text" id="red-icono" placeholder="Ej: fa-brands fa-discord" required style="flex:1;">
          <div id="red-icono-preview" style="width:42px; height:42px; border-radius:10px; background:var(--surface); border:1px solid var(--border); display:grid; place-items:center; font-size:1.3rem; flex-shrink:0;">
            <i class="fa-solid fa-circle-question" style="color:var(--muted)"></i>
          </div>
        </div>
        <small style="color:var(--muted); font-size:.76rem; margin-top:.3rem; display:block;">
          Usa clases oficiales de Font Awesome 6 (ej: <code>fa-brands fa-facebook</code>, <code>fa-solid fa-globe</code>).
        </small>
      </div>

      <div class="f">
        <label for="red-url-base">URL Base / Prefijo (Opcional)</label>
        <input type="text" id="red-url-base" placeholder="Ej: https://discord.gg/ o https://x.com/">
        <small style="color:var(--muted); font-size:.76rem; margin-top:.3rem; display:block;">
          Permite generar enlaces automáticamente cuando el comercio ingresa solo un <code>@usuario</code>.
        </small>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:.8rem;">
        <div class="f">
          <label for="red-color">Color Distintivo</label>
          <div style="display:flex; gap:.4rem; align-items:center;">
            <input type="color" id="red-color-picker" value="#1877F2" style="width:38px; height:38px; padding:0; border:none; border-radius:8px; cursor:pointer; background:none;">
            <input type="text" id="red-color" placeholder="#1877F2" style="flex:1;">
          </div>
        </div>

        <div class="f">
          <label for="red-orden">Orden</label>
          <input type="number" id="red-orden" value="0" min="0" max="999">
        </div>
      </div>

      <div class="f" style="margin-top:.4rem;">
        <label for="red-activo">Estado</label>
        <select id="red-activo">
          <option value="1">Activo (visible en selectores)</option>
          <option value="0">Inactivo (oculto en selectores)</option>
        </select>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:.8rem; margin-top:1.8rem;">
        <button type="button" class="btn btn-ghost" id="btn-cancel-red-social">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btn-save-red-social">Guardar Red Social</button>
      </div>
    </form>
  </div>
</div>
