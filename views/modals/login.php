  <!-- ============ MODAL ============ -->
  <!-- ============ MODAL INICIO DE SESIÓN ============ -->
  <div class="overlay" id="ov">
    <div class="modal" role="dialog" aria-modal="true" aria-label="Acceder a Spot">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <div class="logo"><svg viewBox="0 0 24 24">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" fill="url(#gpin)" />
            <circle cx="12" cy="10" r="3.4" fill="#fff" opacity=".92" />
          </svg><b>spot</b></div>
        <button class="icon-btn" data-close aria-label="Cerrar"><svg class="ico">
            <use href="#i-x"></use>
          </svg></button>
      </div>

      <h3 style="margin:0 0 .35rem 0;">Iniciar sesión</h3>
      <p style="color:var(--muted); font-size:.88rem; margin:0 0 1.2rem 0;">Ingresa con tus credenciales para acceder a tu cuenta.</p>

      <form id="form-login" onsubmit="return false;">
        <div id="login-error-msg" style="display:none; color:var(--hot); background:color-mix(in srgb, var(--hot) 12%, transparent); padding:.6rem .8rem; border-radius:8px; font-size:.85rem; margin-bottom:1rem;"></div>
        
        <div class="f">
          <label for="m-mail">Correo electrónico</label>
          <input type="email" id="m-mail" placeholder="tucorreo@ejemplo.com" required autocomplete="username">
        </div>
        <div class="f">
          <label for="m-pass">Contraseña</label>
          <input type="password" id="m-pass" placeholder="••••••••" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary" id="btn-login-submit" style="width:100%">
          Iniciar Sesión
        </button>
      </form>

      <div class="divider">o continúa con</div>
      <div class="oauth">
        <button class="btn btn-ghost" data-toast="OAuth disponible en la versión final">Google</button>
      </div>
    </div>
  </div>

