  <!-- ============ MODAL CONFIRMAR ELIMINACIÓN USUARIO (ADMIN CRUD) ============ -->
  <div class="overlay" id="modal-delete-user">
    <div class="modal" role="dialog" aria-modal="true" aria-label="Confirmar Eliminación de Usuario">
      <div style="width:50px; height:50px; border-radius:50%; background:color-mix(in srgb, var(--hot) 14%, transparent); color:var(--hot); display:grid; place-items:center; margin:0 auto 1rem;">
        <svg class="ico" style="width:26px; height:26px;"><use href="#i-x"></use></svg>
      </div>
      <h3 style="text-align:center; margin-bottom:.5rem;">¿Eliminar este usuario?</h3>
      <p style="text-align:center; color:var(--muted); font-size:.9rem; margin-bottom:1.5rem;">
        Estás a punto de eliminar a <b id="del-user-nombre" style="color:var(--ink);"></b> (<span id="del-user-email"></span>). Esta acción no se podrá deshacer.
      </p>
      <div id="del-user-error" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem; text-align:center;"></div>
      <input type="hidden" id="del-user-id">
      <div style="display:flex; justify-content:center; gap:.8rem;">
        <button type="button" class="btn btn-ghost" id="btn-cancel-del-user">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-confirm-del-user" style="background:var(--hot); border-color:var(--hot);">
          Sí, eliminar usuario
        </button>
      </div>
    </div>
  </div>
