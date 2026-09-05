    /* ===================== INTEGRACIÓN CON BACKEND (PHP + MYSQL) ===================== */
    let currentAuthUser = null;
    let adminComerciosList = [];
    let adminUsuariosList = [];
    let adminRedesList = (window.SPOT_INITIAL_DATA && window.SPOT_INITIAL_DATA.redes_sociales) ? window.SPOT_INITIAL_DATA.redes_sociales : [];
    window.SPOT_REDES_CATALOG = adminRedesList.filter(r => r.activo == 1);
    let currentAdminFilter = 'all';
    let dtComercios = null;
    let dtUsuarios = null;
    let dtRedes = null;


    const dtSpanish = {
      search: "Buscar:",
      searchPlaceholder: "Filtrar resultados...",
      lengthMenu: "Mostrar _MENU_ registros",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      infoFiltered: "(filtrado de _MAX_ registros en total)",
      zeroRecords: "No se encontraron resultados coincidentes",
      emptyTable: "No hay datos disponibles en la tabla",
      paginate: {
        first: "«",
        previous: "‹",
        next: "›",
        last: "»"
      },
      aria: {
        orderable: "Ordenar por esta columna",
        orderableReverse: "Invertir orden de esta columna"
      }
    };

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/'/g, '&#39;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    }

    const getSwal = () => (typeof Swal !== 'undefined' && Swal.fire) ? Swal : ((typeof Sweetalert2 !== 'undefined' && Sweetalert2.fire) ? Sweetalert2 : (window.Swal || window.Sweetalert2 || null));

    // 1. Verificación de sesión al iniciar
    async function checkAuthSession() {
      try {
        const res = await fetch('api/auth/me.php');
        const json = await res.json();
        if (json.success && json.data && json.data.authenticated) {
          currentAuthUser = json.data.user;
        } else {
          currentAuthUser = null;
        }
      } catch (e) {
        currentAuthUser = null;
      }
      renderAuthUI();
    }

    // 2. Renderizado de interfaz según estado de autenticación
    function renderAuthUI() {
      const desktopSlot = $('#hdr-auth-desktop');
      const mobileSlot = $('#mobile-drawer-auth');
      const adminGuest = $('#admin-guest-box');
      const adminContent = $('#admin-panel-content');

      if (currentAuthUser) {
        const roleName = currentAuthUser.rol === 'admin' ? 'Admin' : (currentAuthUser.rol === 'empresa' ? 'Comercio' : 'Usuario');
        if (desktopSlot) {
          desktopSlot.innerHTML = `
            <span class="tag tag-ok" style="font-size:.82rem; padding:.32rem .75rem; white-space:nowrap;">
              ${roleName}: <b>${currentAuthUser.nombre.split(' ')[0]}</b>
            </span>
            ${currentAuthUser.rol === 'admin' ? '<button class="btn btn-primary btn-sm hdr-act-desktop" data-go="admin">Panel Admin</button>' : ''}
            <button class="btn btn-ghost btn-sm" id="btn-logout" title="Cerrar sesión">Salir</button>
          `;
        }
        if (mobileSlot) {
          mobileSlot.innerHTML = `
            <span class="tag tag-ok" style="margin-bottom:.6rem; justify-content:center; width:100%;">
              ${roleName}: <b>${currentAuthUser.nombre}</b>
            </span>
            ${currentAuthUser.rol === 'admin' ? '<button class="btn btn-primary btn-lg" style="width:100%; margin-bottom:.5rem;" data-go="admin">Panel Admin</button>' : ''}
            <button class="btn btn-ghost btn-lg" style="width:100%" id="btn-logout-mobile">Cerrar sesión</button>
          `;
        }
        if (adminGuest && adminContent) {
          if (currentAuthUser.rol === 'admin') {
            adminGuest.hidden = true;
            adminContent.hidden = false;
            $('#admin-user-tag').textContent = `${currentAuthUser.nombre} (Admin)`;
            loadAdminData();
          } else {
            adminGuest.hidden = false;
            adminContent.hidden = true;
            adminGuest.querySelector('p').textContent = 'Tu cuenta no tiene permisos de administrador.';
          }
        }
      } else {
        if (desktopSlot) {
          desktopSlot.innerHTML = `
            <button class="btn btn-ghost btn-sm hdr-act-desktop" data-modal="login">Iniciar sesión</button>
            <button class="btn btn-primary btn-sm hdr-act-desktop" data-go="negocio">Registra tu comercio</button>
          `;
        }
        if (mobileSlot) {
          mobileSlot.innerHTML = `
            <button class="btn btn-primary btn-lg" style="width:100%" data-go="negocio">Registra tu comercio</button>
            <button class="btn btn-ghost btn-lg" style="width:100%" data-modal="login">Iniciar sesión</button>
          `;
        }
        if (adminGuest && adminContent) {
          adminGuest.hidden = false;
          adminContent.hidden = true;
        }
      }
    }

    // 3. Formulario de Inicio de Sesión
    $('#form-login')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = $('#m-mail').value.trim();
      const password = $('#m-pass').value;
      const errMsg = $('#login-error-msg');
      const submitBtn = $('#btn-login-submit');

      errMsg.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Comprobando…';

      try {
        const res = await fetch('api/auth/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const json = await res.json();
        if (json.success) {
          toast('¡Bienvenido, ' + json.data.user.nombre + '!');
          $('#ov').classList.remove('on');
          $('#form-login').reset();
          currentAuthUser = json.data.user;
          renderAuthUI();
          if (currentAuthUser.rol === 'admin') {
            go('admin');
          }
        } else {
          errMsg.textContent = json.message || 'Error al iniciar sesión';
          errMsg.style.display = 'block';
        }
      } catch (err) {
        errMsg.textContent = 'No fue posible conectar con el servidor.';
        errMsg.style.display = 'block';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Iniciar Sesión';
      }
    });

    // 4. Cierre de sesión
    document.addEventListener('click', async (e) => {
      if (e.target.id === 'btn-logout' || e.target.id === 'btn-logout-mobile') {
        try {
          await fetch('api/auth/logout.php', { method: 'POST' });
          currentAuthUser = null;
          renderAuthUI();
          toast('Sesión finalizada.');
          go('inicio');
        } catch (err) {
          toast('Error al cerrar sesión');
        }
      }
    });

    // 5. Carga de datos del panel de administración
    async function loadAdminData() {
      try {
        const res = await fetch('api/admin/empresas/list.php');
        const json = await res.json();
        if (json.success && json.data) {
          adminComerciosList = json.data.empresas || [];
          const st = json.data.stats || {};
          $('#stat-pendientes').textContent = st.pendientes || 0;
          $('#stat-aprobados').textContent = st.aprobados || 0;
          $('#stat-total-comercios').textContent = st.total || 0;
          renderAdminComercios();
        }
      } catch (err) {
        console.error('Error al cargar comercios en admin:', err);
      }
      loadAdminUsersCount();
      loadAdminRedes();
    }

    async function loadAdminUsersCount() {
      try {
        const res = await fetch('api/admin/usuarios/list.php');
        const json = await res.json();
        if (json.success && json.data) {
          adminUsuariosList = json.data.usuarios || [];
          $('#stat-total-usuarios').textContent = json.data.total || 0;
          renderAdminUsuarios();
        }
      } catch (err) {
        console.error('Error al cargar usuarios:', err);
      }
    }

    async function loadAdminRedes() {
      try {
        const res = await fetch('api/admin/redes_sociales/list.php?all=1');
        const json = await res.json();
        if (json.success && json.data) {
          adminRedesList = json.data.redes || [];
          window.SPOT_REDES_CATALOG = adminRedesList.filter(r => r.activo == 1);
          renderAdminRedes();
        }
      } catch (err) {
        console.error('Error al cargar catálogo de redes sociales:', err);
      }
    }

    function renderAdminRedes() {
      const tbody = $('#admin-redes-rows');
      if (!tbody) return;

      if (dtRedes) {
        dtRedes.destroy();
        dtRedes = null;
      }

      if (adminRedesList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--muted)">No hay redes sociales registradas</td></tr>';
      } else {
        tbody.innerHTML = adminRedesList.map(r => {
          const editBtn = `
            <button class="btn-action btn-action-edit" onclick="openEditRedSocial(${r.id})" title="Editar red social" aria-label="Editar">
              <svg class="ico"><use href="#i-lapiz"></use></svg>
            </button>
          `;
          const deleteBtn = `
            <button class="btn-action btn-action-delete" onclick="confirmDeleteRedSocial(${r.id}, '${escapeHtml(r.nombre)}')" title="Eliminar red social" aria-label="Eliminar">
              <svg class="ico"><use href="#i-trash"></use></svg>
            </button>
          `;

          const colorBadge = r.color 
            ? `<span style="display:inline-flex; align-items:center; gap:.4rem;"><span style="width:14px; height:14px; border-radius:3px; background:${escapeHtml(r.color)}; display:inline-block; border:1px solid rgba(0,0,0,0.2);"></span> <span class="mono" style="font-size:.8rem;">${escapeHtml(r.color)}</span></span>`
            : '<span style="color:var(--muted)">—</span>';

          const iconCell = `
            <div class="admin-icon-preview" style="color:${r.color ? escapeHtml(r.color) : 'var(--ink)'};">
              <i class="${escapeHtml(r.icono)}"></i>
            </div>
          `;

          return `
            <tr>
              <td class="mono">#${r.id}</td>
              <td style="text-align:center;">${iconCell}</td>
              <td><b>${escapeHtml(r.nombre)}</b></td>
              <td><code>${escapeHtml(r.icono)}</code></td>
              <td><span style="color:var(--muted); font-size:.82rem;">${escapeHtml(r.url_base || '—')}</span></td>
              <td>${colorBadge}</td>
              <td><span class="tag ${r.activo == 1 ? 'tag-ok' : 'tag-warn'}">${r.activo == 1 ? 'Activo' : 'Inactivo'}</span></td>
              <td style="text-align:right; white-space:nowrap;">
                <div style="display:inline-flex; gap:.35rem; justify-content:flex-end;">
                  ${editBtn}
                  ${deleteBtn}
                </div>
              </td>
            </tr>
          `;
        }).join('');
      }

      if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        dtRedes = jQuery('#table-admin-redes').DataTable({
          language: dtSpanish,
          pageLength: 10,
          responsive: true,
          order: [[0, 'asc']],
          columnDefs: [
            { orderable: false, targets: [1, 7] }
          ]
        });
      }
    }


    function renderAdminComercios() {
      const tbody = $('#admin-comercios-rows');
      if (!tbody) return;

      if (dtComercios) {
        dtComercios.destroy();
        dtComercios = null;
      }

      const filtered = adminComerciosList.filter(e => {
        if (currentAdminFilter === 'all') return true;
        return e.estado === currentAdminFilter;
      });

      if (filtered.length === 0) {
        tbody.innerHTML = '';
      } else {
        tbody.innerHTML = filtered.map(b => {
          const stBadge = b.estado === 'pendiente' 
            ? '<span class="tag tag-wait">Por revisar</span>'
            : (b.estado === 'aprobado' ? '<span class="tag tag-ok">Aprobado</span>' : '<span class="tag tag-no">Rechazado</span>');

          const payChips = (b.metodos_pago || []).map(p => payChip(p, 1)).join('') || '—';

          let actionBtns = `
            <div style="display:inline-flex; gap:.35rem; align-items:center; justify-content:flex-end;">
              <button class="btn-action btn-action-edit" onclick="openEditEmpresa(${b.id})" title="Editar comercio" aria-label="Editar">
                <svg class="ico"><use href="#i-lapiz"></use></svg>
              </button>
          `;

          if (b.estado !== 'aprobado') {
            actionBtns += `
              <button class="btn-action btn-action-approve" onclick="setBusinessStatus(${b.id}, 'aprobado')" title="Aprobar y publicar comercio" aria-label="Aprobar">
                <svg class="ico"><use href="#i-check"></use></svg>
              </button>
            `;
          } else {
            actionBtns += `
              <button class="btn-action btn-action-pause" onclick="setBusinessStatus(${b.id}, 'pendiente')" title="Pausar comercio" aria-label="Pausar">
                <svg class="ico"><use href="#i-pausa"></use></svg>
              </button>
            `;
          }

          actionBtns += `
              <button class="btn-action btn-action-delete" onclick="openDeleteEmpresa(${b.id}, '${escapeHtml(b.nombre)}')" title="Eliminar comercio" aria-label="Eliminar">
                <svg class="ico"><use href="#i-trash"></use></svg>
              </button>
            </div>
          `;

          return `
            <tr>
              <td class="mono">#${b.id}</td>
              <td><b>${escapeHtml(b.nombre)}</b>${b.rif ? `<br><small style="color:var(--muted)">${escapeHtml(b.rif)}</small>` : ''}</td>
              <td>${escapeHtml(b.categoria_nombre || b.categoria_slug || '—')}</td>
              <td><small><b>${escapeHtml(b.dueno_nombre || '—')}</b><br>${escapeHtml(b.dueno_email || '—')}</small></td>
              <td>${escapeHtml(b.zona || '—')}</td>
              <td><div class="pay-row">${payChips}</div></td>
              <td>${stBadge}</td>
              <td style="text-align:right; white-space:nowrap;">${actionBtns}</td>
            </tr>
          `;
        }).join('');
      }

      if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        dtComercios = jQuery('#table-admin-comercios').DataTable({
          language: dtSpanish,
          pageLength: 10,
          responsive: true,
          order: [[0, 'desc']],
          columnDefs: [
            { orderable: false, targets: [5, 7] }
          ]
        });
      }
    }

    window.setBusinessStatus = async function(id, newStatus) {
      try {
        const res = await fetch('api/admin/empresas/update_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, estado: newStatus })
        });
        const json = await res.json();
        if (json.success) {
          toast(`Comercio marcado como ${newStatus}.`);
          loadAdminData();
          loadBusinessesFromAPI();
        } else {
          toast(json.message || 'Error al actualizar estado.');
        }
      } catch (err) {
        toast('Error de conexión al actualizar estado.');
      }
    };

    function renderAdminUsuarios() {
      const tbody = $('#admin-usuarios-rows');
      if (!tbody) return;

      if (dtUsuarios) {
        dtUsuarios.destroy();
        dtUsuarios = null;
      }

      if (adminUsuariosList.length === 0) {
        tbody.innerHTML = '';
      } else {
        tbody.innerHTML = adminUsuariosList.map(u => {
          const rolBadge = u.rol === 'admin' 
            ? '<span class="tag tag-ok">Admin</span>' 
            : (u.rol === 'empresa' ? '<span class="tag tag-wait">Comercio</span>' : '<span class="tag">Usuario</span>');
          
          const dateStr = u.created_at ? u.created_at.slice(0, 10) : '—';
          const isSelf = currentAuthUser && currentAuthUser.id === u.id;

          const editBtn = `
            <button class="btn-action btn-action-edit" onclick="openEditUser(${u.id})" title="Editar usuario" aria-label="Editar">
              <svg class="ico"><use href="#i-lapiz"></use></svg>
            </button>
          `;
          const deleteBtn = isSelf 
            ? `
            <button class="btn-action btn-action-delete" disabled title="No puedes eliminar tu propia cuenta en sesión" aria-label="Eliminar">
              <svg class="ico"><use href="#i-trash"></use></svg>
            </button>
            `
            : `
            <button class="btn-action btn-action-delete" onclick="openDeleteUser(${u.id}, '${escapeHtml(u.nombre)}', '${escapeHtml(u.email)}', ${u.total_empresas || 0})" title="Eliminar usuario" aria-label="Eliminar">
              <svg class="ico"><use href="#i-trash"></use></svg>
            </button>
            `;

          return `
            <tr>
              <td class="mono">#${u.id}</td>
              <td><b>${escapeHtml(u.nombre)}</b></td>
              <td>${escapeHtml(u.email)}</td>
              <td>${escapeHtml(u.telefono || '—')}</td>
              <td>${rolBadge}</td>
              <td><span class="tag ${u.estado === 'activo' ? 'tag-ok' : (u.estado === 'inactivo' ? 'tag-wait' : 'tag-no')}">${u.estado}</span></td>
              <td class="mono">${u.total_empresas || 0}</td>
              <td class="mono" style="color:var(--muted)">${dateStr}</td>
              <td style="text-align:right; white-space:nowrap;">
                <div style="display:inline-flex; gap:.35rem; justify-content:flex-end;">
                  ${editBtn}
                  ${deleteBtn}
                </div>
              </td>
            </tr>
          `;
        }).join('');
      }

      if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        dtUsuarios = jQuery('#table-admin-usuarios').DataTable({
          language: dtSpanish,
          pageLength: 10,
          responsive: true,
          order: [[0, 'desc']],
          columnDefs: [
            { orderable: false, targets: [8] }
          ]
        });
      }
    }

    // 6. Navegación de pestañas Admin
    $('#admin-tab-btn-comercios')?.addEventListener('click', () => {
      $('#admin-tab-btn-comercios').classList.add('on');
      $('#admin-tab-btn-usuarios').classList.remove('on');
      $('#admin-tab-btn-redes')?.classList.remove('on');
      $('#admin-section-comercios').hidden = false;
      $('#admin-section-usuarios').hidden = true;
      $('#admin-section-redes').hidden = true;
      if (dtComercios) {
        setTimeout(() => {
          try {
            dtComercios.columns?.adjust();
            if (dtComercios.responsive?.recalc) dtComercios.responsive.recalc();
          } catch(e) {}
        }, 50);
      }
    });

    $('#admin-tab-btn-usuarios')?.addEventListener('click', () => {
      $('#admin-tab-btn-usuarios').classList.add('on');
      $('#admin-tab-btn-comercios').classList.remove('on');
      $('#admin-tab-btn-redes')?.classList.remove('on');
      $('#admin-section-usuarios').hidden = false;
      $('#admin-section-comercios').hidden = true;
      $('#admin-section-redes').hidden = true;
      loadAdminUsersCount();
      if (dtUsuarios) {
        setTimeout(() => {
          try {
            dtUsuarios.columns?.adjust();
            if (dtUsuarios.responsive?.recalc) dtUsuarios.responsive.recalc();
          } catch(e) {}
        }, 50);
      }
    });

    $('#admin-tab-btn-redes')?.addEventListener('click', () => {
      $('#admin-tab-btn-redes').classList.add('on');
      $('#admin-tab-btn-comercios').classList.remove('on');
      $('#admin-tab-btn-usuarios').classList.remove('on');
      $('#admin-section-redes').hidden = false;
      $('#admin-section-comercios').hidden = true;
      $('#admin-section-usuarios').hidden = true;
      loadAdminRedes();
      if (dtRedes) {
        setTimeout(() => {
          try {
            dtRedes.columns?.adjust();
            if (dtRedes.responsive?.recalc) dtRedes.responsive.recalc();
          } catch(e) {}
        }, 50);
      }
    });


    $$('[data-admin-filter]').forEach(chip => chip.addEventListener('click', () => {
      $$('[data-admin-filter]').forEach(c => c.classList.toggle('on', c === chip));
      currentAdminFilter = chip.dataset.adminFilter;
      renderAdminComercios();
    }));

    // ===================== CRUD COMERCIOS (ADMIN) =====================
    // Toggles de métodos de pago en modal de edición
    $$('#edit-emp-pays .pay-toggle').forEach(p => p.addEventListener('click', () => p.classList.toggle('on')));

    // Subida de logo en modal de edición
    $('#btn-change-edit-logo')?.addEventListener('click', () => {
      $('#edit-emp-file')?.click();
    });

    $('#edit-emp-file')?.addEventListener('change', async (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      const statusEl = $('#edit-upload-status');
      statusEl.textContent = 'Subiendo imagen…';
      statusEl.style.color = 'var(--brand)';

      const formData = new FormData();
      formData.append('image', file);
      formData.append('imagen', file);

      try {
        const res = await fetch('api/upload/image.php', {
          method: 'POST',
          body: formData
        });
        const json = await res.json();
        if (json.success && json.data && json.data.url) {
          $('#edit-emp-logo-url').value = json.data.url;
          $('#edit-logo-preview').innerHTML = `<img src="${json.data.url}" alt="Preview" style="width:100%; height:100%; object-fit:cover;">`;
          statusEl.textContent = '¡Imagen actualizada!';
          statusEl.style.color = 'var(--pay-efectivo)';
        } else {
          statusEl.textContent = json.message || 'Error al subir imagen.';
          statusEl.style.color = 'var(--hot)';
        }
      } catch (err) {
        statusEl.textContent = 'Error de conexión al subir imagen.';
        statusEl.style.color = 'var(--hot)';
      } finally {
        e.target.value = '';
      }
    });

    /* ===================== MAESTRO DE REDES SOCIALES (ADMIN) ===================== */
    window.openCreateRedSocial = function() {
      $('#modal-red-social-title').textContent = 'Nueva Red Social';
      $('#form-red-social').reset();
      $('#red-id').value = '';
      $('#red-icono-preview').innerHTML = '<i class="fa-solid fa-circle-question" style="color:var(--muted)"></i>';
      $('#red-color-picker').value = '#1877F2';
      $('#red-color').value = '#1877F2';
      $('#red-orden').value = (adminRedesList ? adminRedesList.length : 0) + 1;
      $('#red-activo').value = '1';
      $('#modal-red-social').classList.add('on');
      setTimeout(() => $('#red-nombre')?.focus(), 50);
    };

    window.openEditRedSocial = async function(id) {
      try {
        const res = await fetch(`api/admin/redes_sociales/get.php?id=${id}`);
        const json = await res.json();
        if (!json.success || !json.data || !json.data.red) {
          toast(json.message || 'No se encontró la red social.', 'error');
          return;
        }
        const r = json.data.red;
        $('#modal-red-social-title').textContent = 'Editar Red Social';
        $('#red-id').value = r.id;
        $('#red-nombre').value = r.nombre || '';
        $('#red-icono').value = r.icono || '';
        $('#red-url-base').value = r.url_base || '';
        $('#red-color').value = r.color || '';
        $('#red-color-picker').value = (r.color && r.color.startsWith('#') && r.color.length === 7) ? r.color : '#1877F2';
        $('#red-orden').value = r.orden || 0;
        $('#red-activo').value = r.activo;
        
        updateRedIconPreview(r.icono, r.color);
        $('#modal-red-social').classList.add('on');
      } catch (err) {
        toast('Error al consultar datos de la red social.', 'error');
      }
    };

    function updateRedIconPreview(iconClass, color) {
      const preview = $('#red-icono-preview');
      if (!preview) return;
      if (iconClass && iconClass.trim()) {
        preview.innerHTML = `<i class="${escapeHtml(iconClass.trim())}"></i>`;
        preview.style.color = color || 'var(--ink)';
      } else {
        preview.innerHTML = `<i class="fa-solid fa-circle-question" style="color:var(--muted)"></i>`;
        preview.style.color = 'var(--muted)';
      }
    }

    $('#red-icono')?.addEventListener('input', (e) => {
      updateRedIconPreview(e.target.value, $('#red-color')?.value);
    });

    $('#red-color-picker')?.addEventListener('input', (e) => {
      $('#red-color').value = e.target.value;
      updateRedIconPreview($('#red-icono')?.value, e.target.value);
    });

    $('#red-color')?.addEventListener('input', (e) => {
      if (e.target.value && e.target.value.startsWith('#') && e.target.value.length === 7) {
        $('#red-color-picker').value = e.target.value;
      }
      updateRedIconPreview($('#red-icono')?.value, e.target.value);
    });

    const closeModalRedSocial = () => $('#modal-red-social').classList.remove('on');
    $('#btn-close-red-social')?.addEventListener('click', closeModalRedSocial);
    $('#btn-cancel-red-social')?.addEventListener('click', closeModalRedSocial);
    $('#modal-red-social')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-red-social') closeModalRedSocial();
    });
    $('#btn-open-create-red')?.addEventListener('click', () => openCreateRedSocial());

    $('#form-red-social')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = $('#red-id').value;
      const nombre = $('#red-nombre').value.trim();
      const icono = $('#red-icono').value.trim();
      const url_base = $('#red-url-base').value.trim();
      const color = $('#red-color').value.trim();
      const orden = parseInt($('#red-orden').value) || 0;
      const activo = parseInt($('#red-activo').value);

      const btn = $('#btn-save-red-social');
      btn.disabled = true;
      btn.textContent = 'Guardando…';

      const endpoint = id ? 'api/admin/redes_sociales/update.php' : 'api/admin/redes_sociales/create.php';
      const payload = { nombre, icono, url_base, color, orden, activo };
      if (id) payload.id = parseInt(id);

      try {
        const res = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          toast(id ? 'Red social actualizada exitosamente.' : 'Red social creada exitosamente.', 'success');
          closeModalRedSocial();
          loadAdminRedes();
        } else {
          toast(json.message || 'Error al guardar la red social.', 'error');
        }
      } catch (err) {
        toast('Error de conexión al guardar la red social.', 'error');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar Red Social';
      }
    });

    window.confirmDeleteRedSocial = async function(id, nombre) {
      const swalConfig = getSwalThemeConfig();
      const result = await Swal.fire({
        title: '¿Eliminar red social?',
        html: `¿Estás seguro de que deseas eliminar <b>${escapeHtml(nombre)}</b> del catálogo maestro?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: 'var(--hot)',
        cancelButtonColor: 'var(--muted)',
        ...swalConfig
      });

      if (result.isConfirmed) {
        try {
          const res = await fetch('api/admin/redes_sociales/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const json = await res.json();
          if (json.success) {
            toast('Red social eliminada correctamente.', 'success');
            loadAdminRedes();
          } else {
            toast(json.message || 'Error al eliminar red social.', 'error');
          }
        } catch (err) {
          toast('Error de conexión al eliminar red social.', 'error');
        }
      }
    };

    /* ===================== GESTIÓN DINÁMICA DE OTRAS REDES SOCIALES ===================== */
    function renderRedSocialRow(container, nombre = '', valor = '') {
      if (!container) return;
      const row = document.createElement('div');
      row.className = 'dynamic-red-row';

      const catalog = (window.SPOT_REDES_CATALOG && window.SPOT_REDES_CATALOG.length > 0)
        ? window.SPOT_REDES_CATALOG
        : ((window.SPOT_INITIAL_DATA && window.SPOT_INITIAL_DATA.redes_sociales) ? window.SPOT_INITIAL_DATA.redes_sociales : []);

      let selectOptions = `<option value="" disabled ${!nombre ? 'selected' : ''}>Selecciona red social...</option>`;
      let hasMatch = false;

      catalog.forEach(r => {
        const isSelected = nombre && (r.nombre.toLowerCase() === nombre.toLowerCase());
        if (isSelected) hasMatch = true;
        selectOptions += `<option value="${escapeHtml(r.nombre)}" data-icon="${escapeHtml(r.icono)}" data-base="${escapeHtml(r.url_base || '')}" ${isSelected ? 'selected' : ''}>${escapeHtml(r.nombre)}</option>`;
      });

      if (nombre && !hasMatch) {
        selectOptions += `<option value="${escapeHtml(nombre)}" selected>${escapeHtml(nombre)}</option>`;
      }

      row.innerHTML = `
        <div class="f" style="margin:0;">
          <label style="font-size:.78rem;">Red social</label>
          <select class="input input-sm input-red-nombre" required>
            ${selectOptions}
          </select>
        </div>
        <div class="f" style="margin:0;">
          <label style="font-size:.78rem;">Usuario o enlace</label>
          <input type="text" class="input input-sm input-red-valor" placeholder="@usuario o enlace" value="${escapeHtml(valor)}" required>
        </div>
        <button type="button" class="btn-remove-red" title="Eliminar red social" aria-label="Eliminar red social">
          <svg class="ico" style="width:16px; height:16px;"><use href="#i-x"></use></svg>
        </button>
      `;

      const sel = row.querySelector('.input-red-nombre');
      const valInput = row.querySelector('.input-red-valor');

      sel?.addEventListener('change', () => {
        const opt = sel.options[sel.selectedIndex];
        const base = opt ? opt.getAttribute('data-base') : '';
        if (base) {
          valInput.placeholder = `Ej: @usuario o enlace`;
        }
      });

      row.querySelector('.btn-remove-red')?.addEventListener('click', () => {
        row.remove();
      });

      container.appendChild(row);
      if (!nombre && sel) sel.focus();
    }

    $('#btn-add-red-negocio')?.addEventListener('click', () => {
      renderRedSocialRow($('#container-otras-redes-negocio'));
    });
    $('#btn-add-red-edit')?.addEventListener('click', () => {
      renderRedSocialRow($('#container-otras-redes-edit'));
    });


    // Abrir modal de edición
    window.openEditEmpresa = async function(id) {
      try {
        if (!adminUsuariosList || adminUsuariosList.length === 0) {
          const uRes = await fetch('api/admin/usuarios/list.php');
          const uJson = await uRes.json();
          if (uJson.success && uJson.data) {
            adminUsuariosList = uJson.data.usuarios || [];
          }
        }

        const selectUser = $('#edit-emp-usuario');
        if (selectUser && adminUsuariosList.length > 0) {
          selectUser.innerHTML = adminUsuariosList.map(u => 
            `<option value="${u.id}">${escapeHtml(u.nombre)} (${escapeHtml(u.email)}) - ${u.rol}</option>`
          ).join('');
        }

        const res = await fetch(`api/admin/empresas/get.php?id=${id}`);
        const json = await res.json();
        if (!json.success || !json.data || !json.data.empresa) {
          toast(json.message || 'No se pudo cargar la información del comercio.');
          return;
        }

        const emp = json.data.empresa;
        $('#edit-emp-id').value = emp.id;
        $('#edit-emp-nombre').value = emp.nombre || '';
        $('#edit-emp-cat').value = emp.categoria_id || 1;
        $('#edit-emp-rif').value = emp.rif || '';
        if (selectUser) selectUser.value = emp.usuario_id || 1;
        $('#edit-emp-estado').value = emp.estado || 'aprobado';
        $('#edit-emp-tel').value = emp.telefono || '';
        $('#edit-emp-correo').value = emp.correo_contacto || '';
        $('#edit-emp-zona').value = emp.zona || '';
        $('#edit-emp-dir').value = emp.direccion || '';
        $('#edit-emp-desc').value = emp.descripcion || '';
        $('#edit-emp-logo-url').value = emp.logo_url || '';

        const redes = emp.redes_sociales || {};
        $('#edit-emp-instagram').value = redes.instagram || '';
        $('#edit-emp-whatsapp').value = redes.whatsapp || '';
        $('#edit-emp-tiktok').value = redes.tiktok || '';
        $('#edit-emp-web').value = redes.web || '';

        const containerOtrasEdit = $('#container-otras-redes-edit');
        if (containerOtrasEdit) {
          containerOtrasEdit.innerHTML = '';
          if (Array.isArray(redes.otras)) {
            redes.otras.forEach(item => {
              renderRedSocialRow(containerOtrasEdit, item.nombre, item.valor);
            });
          }
        }

        const preview = $('#edit-logo-preview');
        if (emp.logo_url) {
          preview.innerHTML = `<img src="${emp.logo_url}" alt="${escapeHtml(emp.nombre)}" style="width:100%; height:100%; object-fit:cover;">`;
        } else {
          preview.innerHTML = `<svg class="ico" style="color:var(--muted);"><use href="#i-tienda"></use></svg>`;
        }
        $('#edit-upload-status').textContent = 'JPG, PNG o WebP hasta 5MB';
        $('#edit-upload-status').style.color = 'var(--muted)';

        const activePays = Array.isArray(emp.metodos_pago) ? emp.metodos_pago : [];
        $$('#edit-emp-pays .pay-toggle').forEach(p => {
          p.classList.toggle('on', activePays.includes(p.dataset.p));
        });

        $('#edit-emp-error').style.display = 'none';
        $('#modal-edit-empresa').classList.add('on');
      } catch (err) {
        console.error('Error al abrir modal de edición:', err);
        toast('Error al consultar datos del comercio.');
      }
    };

    // Cerrar modal de edición
    const closeModalEditEmpresa = () => $('#modal-edit-empresa').classList.remove('on');
    $('#btn-close-edit-empresa')?.addEventListener('click', closeModalEditEmpresa);
    $('#btn-cancel-edit-empresa')?.addEventListener('click', closeModalEditEmpresa);
    $('#modal-edit-empresa')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-edit-empresa') closeModalEditEmpresa();
    });

    // Guardar edición de comercio
    $('#form-edit-empresa')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = parseInt($('#edit-emp-id').value);
      const nombre = $('#edit-emp-nombre').value.trim();
      const categoria_id = parseInt($('#edit-emp-cat').value);
      const rif = $('#edit-emp-rif').value.trim();
      const usuario_id = parseInt($('#edit-emp-usuario').value);
      const estado = $('#edit-emp-estado').value;
      const telefono = $('#edit-emp-tel').value.trim();
      const correo_contacto = $('#edit-emp-correo').value.trim();
      const zona = $('#edit-emp-zona').value.trim();
      const direccion = $('#edit-emp-dir').value.trim();
      const descripcion = $('#edit-emp-desc').value.trim();
      const logo_url = $('#edit-emp-logo-url').value || null;
      const metodos_pago = $$('#edit-emp-pays .pay-toggle.on').map(p => p.dataset.p);

      const otrasEdit = [];
      $$('#container-otras-redes-edit .dynamic-red-row').forEach(row => {
        const sel = row.querySelector('.input-red-nombre');
        const nom = sel ? sel.value.trim() : '';
        const val = row.querySelector('.input-red-valor')?.value.trim();
        const opt = sel ? sel.options[sel.selectedIndex] : null;
        const icon = opt ? opt.getAttribute('data-icon') : '';
        if (nom && val) otrasEdit.push({ nombre: nom, valor: val, icono: icon });
      });


      const redes_sociales = {
        instagram: $('#edit-emp-instagram').value.trim(),
        whatsapp: $('#edit-emp-whatsapp').value.trim(),
        tiktok: $('#edit-emp-tiktok').value.trim(),
        web: $('#edit-emp-web').value.trim(),
        otras: otrasEdit
      };

      const errMsg = $('#edit-emp-error');
      const submitBtn = $('#btn-submit-edit-empresa');

      if (!nombre) {
        errMsg.textContent = 'El nombre del comercio es obligatorio.';
        errMsg.style.display = 'block';
        return;
      }

      errMsg.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Guardando…';

      try {
        const res = await fetch('api/admin/empresas/update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id,
            nombre,
            categoria_id,
            rif,
            usuario_id,
            estado,
            telefono,
            correo_contacto,
            zona,
            direccion,
            descripcion,
            logo_url,
            metodos_pago,
            redes_sociales
          })
        });
        const json = await res.json();
        if (json.success) {
          toast('¡Comercio actualizado correctamente!');
          closeModalEditEmpresa();
          loadAdminData();
          loadBusinessesFromAPI();
        } else {
          errMsg.textContent = json.message || 'Error al actualizar el comercio.';
          errMsg.style.display = 'block';
        }
      } catch (err) {
        errMsg.textContent = 'Error de conexión al actualizar comercio.';
        errMsg.style.display = 'block';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Cambios';
      }
    });

    // Eliminar Comercio (Admin con SweetAlert2)
    window.openDeleteEmpresa = async function(id, name) {
      const swal = getSwal();
      if (swal) {
        const result = await swal.fire({
          title: '¿Eliminar este comercio?',
          html: `Estás a punto de eliminar permanentemente a <b>${escapeHtml(name)}</b>.<br><span style="font-size:.85rem; color:var(--muted); display:inline-block; margin-top:.4rem;">Esta acción no se puede deshacer y borrará la ficha del mapa.</span>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar comercio',
          cancelButtonText: 'Cancelar',
          customClass: {
            confirmButton: 'swal2-confirm swal2-danger',
            cancelButton: 'swal2-cancel'
          },
          buttonsStyling: false
        });

        if (!result.isConfirmed) return;

        try {
          const res = await fetch('api/admin/empresas/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const json = await res.json();
          if (json.success) {
            toast('Comercio eliminado permanentemente.', 'success');
            loadAdminData();
            loadBusinessesFromAPI();
          } else {
            toast(json.message || 'Error al eliminar el comercio.', 'error');
          }
        } catch (err) {
          toast('Error de conexión al eliminar.', 'error');
        }
        return;
      }

      $('#del-emp-id').value = id;
      $('#del-emp-nombre').textContent = name;
      $('#modal-delete-empresa')?.classList.add('on');
    };

    const closeModalDeleteEmpresa = () => $('#modal-delete-empresa')?.classList.remove('on');
    $('#btn-cancel-del-empresa')?.addEventListener('click', closeModalDeleteEmpresa);
    $('#modal-delete-empresa')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-delete-empresa') closeModalDeleteEmpresa();
    });

    $('#btn-confirm-del-empresa')?.addEventListener('click', async () => {
      const id = parseInt($('#del-emp-id').value);
      if (!id) return;

      const btn = $('#btn-confirm-del-empresa');
      btn.disabled = true;
      btn.textContent = 'Eliminando…';

      try {
        const res = await fetch('api/admin/empresas/delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
          toast('Comercio eliminado permanentemente.', 'success');
          closeModalDeleteEmpresa();
          loadAdminData();
          loadBusinessesFromAPI();
        } else {
          toast(json.message || 'Error al eliminar el comercio.', 'error');
        }
      } catch (err) {
        toast('Error de conexión al eliminar.', 'error');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Sí, eliminar comercio';
      }
    });

    // 7. Modal de Creación de Usuarios (Admin)
    $('#btn-open-create-user')?.addEventListener('click', () => {
      $('#user-create-error').style.display = 'none';
      $('#form-create-user').reset();
      $('#modal-user').classList.add('on');
    });

    const closeModalUser = () => $('#modal-user').classList.remove('on');
    $('#btn-close-modal-user')?.addEventListener('click', closeModalUser);
    $('#btn-cancel-create-user')?.addEventListener('click', closeModalUser);

    $('#form-create-user')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const nombre = $('#u-nom').value.trim();
      const email = $('#u-mail').value.trim();
      const telefono = $('#u-tel').value.trim();
      const rol = $('#u-rol').value;
      const password = $('#u-pass').value;
      const errMsg = $('#user-create-error');
      const submitBtn = $('#btn-submit-create-user');

      errMsg.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Guardando…';

      try {
        const res = await fetch('api/admin/usuarios/create.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ nombre, email, telefono, rol, password })
        });
        const json = await res.json();
        if (json.success) {
          toast('Usuario creado exitosamente.');
          closeModalUser();
          loadAdminUsersCount();
        } else {
          errMsg.textContent = json.message || 'Error al crear usuario.';
          errMsg.style.display = 'block';
        }
      } catch (err) {
        errMsg.textContent = 'Error de conexión con el servidor.';
        errMsg.style.display = 'block';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Usuario';
      }
    });

    // 7.1. Modal de Edición de Usuario (Admin CRUD)
    window.openEditUser = async function(id) {
      try {
        const res = await fetch(`api/admin/usuarios/get.php?id=${id}`);
        const json = await res.json();
        if (!json.success || !json.data || !json.data.usuario) {
          toast(json.message || 'Error al obtener datos del usuario.');
          return;
        }

        const u = json.data.usuario;
        $('#edit-user-id').value = u.id;
        $('#edit-u-nom').value = u.nombre || '';
        $('#edit-u-mail').value = u.email || '';
        $('#edit-u-tel').value = u.telefono || '';
        $('#edit-u-rol').value = u.rol || 'empresa';
        $('#edit-u-estado').value = u.estado || 'activo';
        $('#edit-u-pass').value = '';
        $('#user-edit-error').style.display = 'none';

        $('#modal-edit-user').classList.add('on');
      } catch (err) {
        toast('Error de conexión al consultar usuario.');
      }
    };

    const closeModalEditUser = () => $('#modal-edit-user')?.classList.remove('on');
    $('#btn-close-modal-edit-user')?.addEventListener('click', closeModalEditUser);
    $('#btn-cancel-edit-user')?.addEventListener('click', closeModalEditUser);
    $('#modal-edit-user')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-edit-user') closeModalEditUser();
    });

    $('#form-edit-user')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = parseInt($('#edit-user-id').value);
      const nombre = $('#edit-u-nom').value.trim();
      const email = $('#edit-u-mail').value.trim();
      const telefono = $('#edit-u-tel').value.trim();
      const rol = $('#edit-u-rol').value;
      const estado = $('#edit-u-estado').value;
      const password = $('#edit-u-pass').value;
      const errMsg = $('#user-edit-error');
      const submitBtn = $('#btn-submit-edit-user');

      if (!nombre) {
        errMsg.textContent = 'El nombre es obligatorio.';
        errMsg.style.display = 'block';
        return;
      }
      if (!email) {
        errMsg.textContent = 'El correo electrónico es obligatorio.';
        errMsg.style.display = 'block';
        return;
      }
      if (password && password.length < 6) {
        errMsg.textContent = 'La nueva contraseña debe tener al menos 6 caracteres.';
        errMsg.style.display = 'block';
        return;
      }

      errMsg.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Guardando…';

      try {
        const res = await fetch('api/admin/usuarios/update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, nombre, email, telefono, rol, estado, password })
        });
        const json = await res.json();
        if (json.success) {
          toast('Usuario actualizado exitosamente.');
          closeModalEditUser();
          loadAdminUsersCount();
          loadAdminData();
        } else {
          errMsg.textContent = json.message || 'Error al actualizar usuario.';
          errMsg.style.display = 'block';
        }
      } catch (err) {
        errMsg.textContent = 'Error de conexión con el servidor.';
        errMsg.style.display = 'block';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Cambios';
      }
    });

    // 7.2. Modal de Eliminación de Usuario (Admin CRUD con SweetAlert2)
    window.openDeleteUser = async function(id, nombre, email, totalEmpresas) {
      const swal = getSwal();
      if (currentAuthUser && currentAuthUser.id === id) {
        if (swal) {
          swal.fire({
            icon: 'error',
            title: 'Acción no permitida',
            text: 'No puedes eliminar tu propia cuenta de administrador en sesión.',
            confirmButtonText: 'Entendido',
            customClass: { confirmButton: 'swal2-confirm' },
            buttonsStyling: false
          });
        } else {
          toast('No puedes eliminar tu propia cuenta de administrador.', 'error');
        }
        return;
      }

      if (totalEmpresas > 0) {
        if (swal) {
          swal.fire({
            icon: 'warning',
            title: 'Usuario con comercios asignados',
            html: `El usuario <b>${escapeHtml(nombre)}</b> tiene <b>${totalEmpresas} comercio(s)</b> asignado(s).<br><span style="font-size:.85rem; color:var(--muted); display:inline-block; margin-top:.4rem;">Primero debes reasignar sus comercios a otro usuario antes de poder eliminarlo.</span>`,
            confirmButtonText: 'Entendido',
            customClass: { confirmButton: 'swal2-confirm' },
            buttonsStyling: false
          });
        } else {
          toast(`Tiene ${totalEmpresas} comercio(s) asignado(s). Reasigna primero.`, 'warning');
        }
        return;
      }

      if (swal) {
        const result = await swal.fire({
          title: '¿Eliminar este usuario?',
          html: `Estás a punto de eliminar a <b>${escapeHtml(nombre)}</b> (<i>${escapeHtml(email)}</i>).<br><span style="font-size:.85rem; color:var(--muted); display:inline-block; margin-top:.4rem;">Esta acción no se podrá deshacer.</span>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar usuario',
          cancelButtonText: 'Cancelar',
          customClass: {
            confirmButton: 'swal2-confirm swal2-danger',
            cancelButton: 'swal2-cancel'
          },
          buttonsStyling: false
        });

        if (!result.isConfirmed) return;

        try {
          const res = await fetch('api/admin/usuarios/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const json = await res.json();
          if (json.success) {
            toast('Usuario eliminado exitosamente.', 'success');
            loadAdminUsersCount();
            loadAdminData();
          } else {
            toast(json.message || 'Error al eliminar usuario.', 'error');
          }
        } catch (err) {
          toast('Error de conexión al eliminar usuario.', 'error');
        }
        return;
      }

      $('#del-user-id').value = id;
      $('#del-user-nombre').textContent = nombre;
      $('#del-user-email').textContent = email;
      $('#modal-delete-user')?.classList.add('on');
    };

    const closeModalDeleteUser = () => $('#modal-delete-user')?.classList.remove('on');
    $('#btn-cancel-del-user')?.addEventListener('click', closeModalDeleteUser);
    $('#modal-delete-user')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-delete-user') closeModalDeleteUser();
    });

    $('#btn-confirm-del-user')?.addEventListener('click', async () => {
      const id = parseInt($('#del-user-id').value);
      if (!id) return;

      const btn = $('#btn-confirm-del-user');
      btn.disabled = true;
      btn.textContent = 'Eliminando…';

      try {
        const res = await fetch('api/admin/usuarios/delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
          toast('Usuario eliminado exitosamente.', 'success');
          closeModalDeleteUser();
          loadAdminUsersCount();
          loadAdminData();
        } else {
          toast(json.message || 'Error al eliminar usuario.', 'error');
        }
      } catch (err) {
        toast('Error de conexión al eliminar usuario.', 'error');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Sí, eliminar usuario';
      }
    });

    // 8. Registro de Comercio conectando al Backend MySQL
    $('#send-biz')?.addEventListener('click', async () => {
      const name = $('#n-name').value.trim();
      if (!name) {
        toast('Indica el nombre del comercio en el paso 1.');
        return;
      }

      const catSelect = $('#n-cat').value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      const catIdMap = {
        'restaurante': 1, 'cafe': 2, 'panaderia': 3, 'supermercado': 4,
        'hotel': 5, 'tienda': 6, 'entretenimiento': 7, 'servicios': 8, 'tecnologia': 9
      };
      const catId = catIdMap[catSelect] || 1;
      const pays = $$('#n-pays .pay-toggle.on').map(p => p.dataset.p);

      const otrasNegocio = [];
      $$('#container-otras-redes-negocio .dynamic-red-row').forEach(row => {
        const sel = row.querySelector('.input-red-nombre');
        const nom = sel ? sel.value.trim() : '';
        const val = row.querySelector('.input-red-valor')?.value.trim();
        const opt = sel ? sel.options[sel.selectedIndex] : null;
        const icon = opt ? opt.getAttribute('data-icon') : '';
        if (nom && val) otrasNegocio.push({ nombre: nom, valor: val, icono: icon });
      });


      const payload = {
        usuario_id: currentAuthUser ? currentAuthUser.id : 1,
        nombre: name,
        rif: $('#n-rif').value.trim(),
        categoria_id: catId,
        descripcion: $('#n-desc').value.trim(),
        telefono: $('#n-tel').value.trim(),
        correo_contacto: $('#n-mail').value.trim(),
        direccion: $('#n-dir').value.trim() || 'Dirección en Caracas',
        zona: $('#n-zona').value || 'Chacao',
        latitud: (typeof pickMarker !== 'undefined' && pickMarker) ? pickMarker.getLatLng().lat : 10.4975,
        longitud: (typeof pickMarker !== 'undefined' && pickMarker) ? pickMarker.getLatLng().lng : -66.8542,
        logo_url: uploadedLogoUrl || null,
        redes_sociales: {
          instagram: $('#n-insta')?.value.trim() || '',
          whatsapp: $('#n-ws')?.value.trim() || '',
          tiktok: $('#n-tiktok')?.value.trim() || '',
          web: $('#n-web')?.value.trim() || '',
          otras: otrasNegocio
        },
        estado: (currentAuthUser && currentAuthUser.rol === 'admin') ? 'aprobado' : 'pendiente',
        metodos_pago: pays
      };

      try {
        const res = await fetch('api/admin/empresas/create.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          toast('¡Comercio registrado con éxito!');
          await loadBusinessesFromAPI();
          go('admin');
          if (currentAuthUser && currentAuthUser.rol === 'admin') loadAdminData();
        } else {
          toast(json.message || 'Error al registrar el comercio');
        }
      } catch (err) {
        toast('Error de conexión al registrar el comercio.');
      }
    });


    /* ===================== arranque ===================== */
    initMaps();
    checkAuthSession();
    loadBusinessesFromAPI();
    go(location.hash.slice(1) || 'inicio');
