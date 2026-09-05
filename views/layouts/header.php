  <header class="hdr">
    <div class="wrap hdr-in">
      <a href="#inicio" class="logo" data-go="inicio">
        <img src="logo1.PNG" alt="logo spot" class="logo-img">
        <b>spot</b>
      </a>
      <nav class="nav">
        <a href="#inicio" data-go="inicio" class="on">Inicio</a>
        <a href="#mapa" data-go="mapa">Explorar mapa</a>
        <a href="#categorias" data-go="categorias">Categorías</a>
        <a href="#negocio" data-go="negocio">Para negocios</a>
      </nav>
      <div class="hdr-act">
        <button class="icon-btn" id="theme" title="Cambiar tema" aria-label="Cambiar entre modo claro y oscuro">
          <svg class="ico" id="theme-ico">
            <use href="#i-luna"></use>
          </svg>
        </button>
        <div id="hdr-auth-desktop" style="display:flex; align-items:center; gap:.5rem;">
          <button class="btn btn-ghost btn-sm hdr-act-desktop" data-modal="login">Iniciar sesión</button>
          <button class="btn btn-primary btn-sm hdr-act-desktop" data-go="negocio">Registra tu comercio</button>
        </div>
        <button class="icon-btn menu-toggle" id="menu-toggle" title="Abrir menú" aria-label="Abrir menú de navegación">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </header>

  <!-- Drawer para navegación móvil -->
  <div class="mobile-drawer" id="mobile-drawer">
    <div class="mobile-drawer-bg" id="mobile-drawer-bg"></div>
    <div class="mobile-drawer-content">
      <div class="mobile-drawer-head">
        <a href="#inicio" class="logo" data-go="inicio">
          <img src="logo1.PNG" alt="logo spot" class="logo-img">
          <b>spot</b>
        </a>
        <button class="icon-btn" id="mobile-drawer-close" aria-label="Cerrar menú">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>
      </div>
      <nav class="mobile-nav">
        <a href="#inicio" data-go="inicio" class="on">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
          </svg>
          Inicio
        </a>
        <a href="#mapa" data-go="mapa">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          Explorar mapa
        </a>
        <a href="#categorias" data-go="categorias">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="7" height="7" x="3" y="3" rx="1" />
            <rect width="7" height="7" x="14" y="3" rx="1" />
            <rect width="7" height="7" x="14" y="14" rx="1" />
            <rect width="7" height="7" x="3" y="14" rx="1" />
          </svg>
          Categorías
        </a>
        <a href="#negocio" data-go="negocio">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <path d="M9 22V12h6v10" />
          </svg>
          Para negocios
        </a>
      </nav>
      <div class="mobile-drawer-foot" id="mobile-drawer-auth">
        <button class="btn btn-primary btn-lg" style="width:100%" data-go="negocio">Registra tu comercio</button>
        <button class="btn btn-ghost btn-lg" style="width:100%" data-modal="login">Iniciar sesión</button>
      </div>
    </div>
  </div>

