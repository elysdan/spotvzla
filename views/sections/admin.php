  <main id="v-admin" class="view">
    <div class="wrap" style="padding:2.6rem 0 4rem">
      <!-- Vista para no autenticados o usuarios sin rol admin -->
      <div id="admin-guest-box" class="panel" style="padding:2.8rem 2rem; text-align:center; max-width:580px; margin:2rem auto;">
        <div style="width:54px; height:54px; border-radius:50%; background:var(--brand-soft); color:var(--brand-ink); display:grid; place-items:center; margin:0 auto 1.2rem;">
          <svg class="ico" style="width:28px; height:28px;"><use href="#i-info"></use></svg>
        </div>
        <h3 style="margin-bottom:.6rem;">Acceso Administrativo Requerido</h3>
        <p style="color:var(--muted); font-size:.95rem; margin-bottom:1.5rem;">
          Para gestionar las empresas y los usuarios del sistema, debes identificarte con tu cuenta de administrador.
        </p>
        <button class="btn btn-primary btn-lg" data-modal="login">Iniciar sesión como Administrador</button>
      </div>

      <!-- Vista activa para Administrador -->
      <div id="admin-panel-content" hidden>
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; margin-bottom:1.8rem;">
          <div>
            <span class="eyebrow">Panel de Control</span>
            <h2 style="margin-top:.4rem;">Administración de Spot</h2>
          </div>
          <div id="admin-user-tag" class="tag tag-ok" style="padding:.4rem .9rem; font-size:.85rem;">
            Admin conectado
          </div>
        </div>

        <div class="stats" style="margin-bottom:2rem;">
          <div class="stat"><span class="eyebrow">Por Revisar</span><b id="stat-pendientes">0</b></div>
          <div class="stat"><span class="eyebrow">Aprobados</span><b id="stat-aprobados">0</b></div>
          <div class="stat"><span class="eyebrow">Total Comercios</span><b id="stat-total-comercios">0</b></div>
          <div class="stat"><span class="eyebrow">Total Usuarios</span><b id="stat-total-usuarios">0</b></div>
        </div>

        <div class="tabs" style="max-width:340px; margin-bottom:1.8rem;">
          <button class="on" id="admin-tab-btn-comercios">Comercios</button>
          <button id="admin-tab-btn-usuarios">Usuarios</button>
        </div>

        <!-- SECCIÓN 1: GESTIÓN DE COMERCIOS -->
        <section id="admin-section-comercios">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.2rem;">
            <div style="display:flex; gap:.4rem; align-items:center;">
              <span class="chip on" data-admin-filter="all">Todos</span>
              <span class="chip" data-admin-filter="pendiente">Pendientes</span>
              <span class="chip" data-admin-filter="aprobado">Aprobados</span>
              <span class="chip" data-admin-filter="rechazado">Rechazados</span>
            </div>
            <button class="btn btn-primary btn-sm" data-go="negocio">
              + Registrar Comercio
            </button>
          </div>

          <div class="tbl-wrap">
            <table class="tbl display" id="table-admin-comercios" style="width:100%">
              <thead>
                <tr>
                  <th style="width:40px;">#</th>
                  <th>Comercio</th>
                  <th>Categoría</th>
                  <th>Dueño</th>
                  <th>Zona</th>
                  <th>Pagos</th>
                  <th>Estado</th>
                  <th style="text-align:right; min-width:140px;">Acciones</th>
                </tr>
              </thead>
              <tbody id="admin-comercios-rows">
                <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--muted)">Cargando comercios...</td></tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- SECCIÓN 2: GESTIÓN DE USUARIOS -->
        <section id="admin-section-usuarios" hidden>
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.2rem;">
            <p style="color:var(--muted); font-size:.9rem; margin:0;">Usuarios registrados con acceso al sistema.</p>
            <button class="btn btn-primary btn-sm" id="btn-open-create-user">
              + Crear Nuevo Usuario
            </button>
          </div>

          <div class="tbl-wrap">
            <table class="tbl display" id="table-admin-usuarios" style="width:100%">
              <thead>
                <tr>
                  <th style="width:40px;">#</th>
                  <th>Nombre</th>
                  <th>Correo Electrónico</th>
                  <th>Teléfono</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Empresas</th>
                  <th>Fecha</th>
                  <th style="text-align:right; min-width:100px;">Acciones</th>
                </tr>
              </thead>
              <tbody id="admin-usuarios-rows">
                <tr><td colspan="9" style="text-align:center; padding:2rem; color:var(--muted)">Cargando usuarios...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>

  <!-- ============ FOOTER ============ -->
