  <!-- ============ MODAL CONFIRMAR ELIMINACIÓN (ADMIN CRUD) ============ -->
  <div class="overlay" id="modal-delete-empresa">
    <div class="modal" role="dialog" aria-modal="true" aria-label="Confirmar Eliminación">
      <div style="width:50px; height:50px; border-radius:50%; background:color-mix(in srgb, var(--hot) 14%, transparent); color:var(--hot); display:grid; place-items:center; margin:0 auto 1rem;">
        <svg class="ico" style="width:26px; height:26px;"><use href="#i-x"></use></svg>
      </div>
      <h3 style="text-align:center; margin-bottom:.5rem;">¿Eliminar este comercio?</h3>
      <p style="text-align:center; color:var(--muted); font-size:.9rem; margin-bottom:1.5rem;">
        Estás a punto de eliminar <b id="del-emp-nombre" style="color:var(--ink);"></b>. Esta acción eliminará permanentemente la ficha, sus métodos de pago y no se podrá deshacer.
      </p>
      <input type="hidden" id="del-emp-id">
      <div style="display:flex; justify-content:center; gap:.8rem;">
        <button type="button" class="btn btn-ghost" id="btn-cancel-del-empresa">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-confirm-del-empresa" style="background:var(--hot); border-color:var(--hot);">
          Sí, eliminar comercio
        </button>
      </div>
    </div>
  </div>
