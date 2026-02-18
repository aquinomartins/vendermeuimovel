const propertyTypes = [
  'Apartamento', 'Casa', 'Cobertura', 'Studio', 'Terreno', 'Sala Comercial', 'Loft', 'Casa em condomínio'
];

const regions = ['Asa Sul', 'Asa Norte', 'Águas Claras', 'Sudoeste', 'Lago Sul', 'Noroeste'];

const metrics = [
  { value: 12000, prefix: '+', suffix: '', label: 'imóveis em carteira ativa' },
  { value: 500000, prefix: '+', suffix: '', label: 'clientes atendidos em todo o DF' },
  { value: 18, prefix: '', suffix: ' anos', label: 'de experiência no mercado imobiliário' }
];

const readyProperties = [
  { title: 'Residencial Horizonte', region: 'Águas Claras', city: 'Brasília', street: 'Rua 20 Norte', area: 88, rooms: 3, parking: 2, price: 'R$ 780.000', exclusive: true, type: 'Apartamento' },
  { title: 'Casa Jardim Lumière', region: 'Lago Sul', city: 'Brasília', street: 'QI 19', area: 240, rooms: 4, parking: 3, price: 'R$ 2.490.000', exclusive: false, type: 'Casa' },
  { title: 'Studio Vertex', region: 'Asa Norte', city: 'Brasília', street: 'CLN 209', area: 38, rooms: 1, parking: 1, price: 'R$ 420.000', exclusive: true, type: 'Studio' },
  { title: 'Cobertura Bela Vista', region: 'Sudoeste', city: 'Brasília', street: 'SQSW 102', area: 176, rooms: 3, parking: 2, price: 'R$ 1.850.000', exclusive: false, type: 'Cobertura' },
  { title: 'Residencial Vento Leste', region: 'Asa Sul', city: 'Brasília', street: 'SQS 311', area: 102, rooms: 3, parking: 1, price: 'R$ 930.000', exclusive: false, type: 'Apartamento' },
  { title: 'Casa Bosque Azul', region: 'Noroeste', city: 'Brasília', street: 'SQNW 111', area: 198, rooms: 4, parking: 2, price: 'R$ 2.150.000', exclusive: true, type: 'Casa em condomínio' },
  { title: 'Loft Prisma', region: 'Asa Norte', city: 'Brasília', street: 'SCRN 708', area: 52, rooms: 1, parking: 1, price: 'R$ 530.000', exclusive: false, type: 'Loft' },
  { title: 'Sala Prime Corporate', region: 'Águas Claras', city: 'Brasília', street: 'Avenida Araucárias', area: 64, rooms: 2, parking: 1, price: 'R$ 460.000', exclusive: false, type: 'Sala Comercial' }
];

const launchProperties = [
  { title: 'Aurora Park Residence', address: 'Noroeste, Brasília', areaRange: '67 a 118 m²', rooms: '2 e 3 quartos' },
  { title: 'Viva Eixo Smart Homes', address: 'Asa Norte, Brasília', areaRange: '34 a 56 m²', rooms: 'Studios e 1 quarto' },
  { title: 'Jardins do Lago', address: 'Lago Sul, Brasília', areaRange: '120 a 240 m²', rooms: '3 e 4 suítes' },
  { title: 'Boulevard das Águas', address: 'Águas Claras, Brasília', areaRange: '58 a 92 m²', rooms: '2 e 3 quartos' },
  { title: 'Vértice Sudoeste', address: 'Sudoeste, Brasília', areaRange: '80 a 146 m²', rooms: '3 quartos' }
];

const testimonials = [
  { name: 'Mariana Costa', text: 'A equipe da Aurora foi precisa em cada etapa da compra. Encontramos o apartamento ideal em menos de duas semanas.' },
  { name: 'Henrique Prado', text: 'Excelente suporte na venda do meu imóvel. A estratégia de divulgação trouxe visitas qualificadas desde o primeiro fim de semana.' },
  { name: 'Paula Menezes', text: 'Usei o serviço para investir em lançamento e gostei da clareza nas simulações. Atendimento rápido e muito profissional.' },
  { name: 'Roberto Sampaio', text: 'Do tour virtual ao contrato, tudo foi muito organizado. Senti segurança e transparência o tempo todo.' }
];

const mockPins = [
  '📍 4 imóveis disponíveis na Asa Sul',
  '📍 3 oportunidades de lançamento em Águas Claras',
  '📍 2 casas de alto padrão no Lago Sul'
];

let selectedType = 'Todos';
let selectedRegion = null;
let modalLastFocus = null;

const qs = (selector) => document.querySelector(selector);
const qsa = (selector) => Array.from(document.querySelectorAll(selector));

const readyGrid = qs('#readyGrid');
const launchGrid = qs('#launchGrid');
const metricsGrid = qs('#metricsGrid');
const typeChips = qs('#typeChips');
const regionChips = qs('#regionChips');
const testimonialGrid = qs('#testimonialGrid');
const selectedTypeText = qs('#selectedTypeText');
const searchStatus = qs('#searchStatus');
const pinsList = qs('#pinsList');

function formatMetric(metric) {
  return `${metric.prefix}${metric.value.toLocaleString('pt-BR')}${metric.suffix}`;
}

function renderMetrics() {
  metricsGrid.innerHTML = metrics.map((metric, index) => `
    <article class="metric-card">
      <div class="metric-number" data-target="${metric.value}" data-prefix="${metric.prefix}" data-suffix="${metric.suffix}" id="metric-${index}">${formatMetric({ ...metric, value: 0 })}</div>
      <p class="metric-label">${metric.label}</p>
    </article>
  `).join('');
}

function animateMetrics() {
  const numberEls = qsa('.metric-number');
  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = Number(el.dataset.target);
      const prefix = el.dataset.prefix || '';
      const suffix = el.dataset.suffix || '';
      const duration = 1000;
      const start = performance.now();

      function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const current = Math.floor(progress * target);
        el.textContent = `${prefix}${current.toLocaleString('pt-BR')}${suffix}`;
        if (progress < 1) requestAnimationFrame(tick);
      }

      requestAnimationFrame(tick);
      obs.unobserve(el);
    });
  }, { threshold: 0.4 });

  numberEls.forEach((el) => observer.observe(el));
}

function renderTypeChips() {
  const allTypes = ['Todos', ...propertyTypes];
  typeChips.innerHTML = allTypes.map((type) => `
    <button class="chip ${selectedType === type ? 'is-selected' : ''}" type="button" data-type="${type}" aria-label="Filtrar tipo ${type}">
      ${type}
    </button>
  `).join('');
}

function renderRegionChips() {
  regionChips.innerHTML = regions.map((region) => `
    <button class="chip ${selectedRegion === region ? 'is-selected' : ''}" type="button" data-region="${region}" aria-label="Filtrar região ${region}">
      ${region}
    </button>
  `).join('');
}

function propertyCard(item) {
  return `
    <article class="card">
      <div class="card-media" aria-hidden="true"></div>
      <div class="card-content">
        ${item.exclusive ? '<span class="badge">Exclusivo</span>' : ''}
        <h3>${item.title}</h3>
        <p>${item.region} · ${item.city}</p>
        <p>${item.street}</p>
        <p>${item.area} m² · ${item.rooms} quartos · ${item.parking} vagas</p>
        <p class="card-price">${item.price}</p>
        <button class="btn btn-secondary" type="button" aria-label="Ver detalhes de ${item.title}">Ver detalhes</button>
      </div>
    </article>
  `;
}

function launchCard(item) {
  return `
    <article class="card">
      <div class="card-media" aria-hidden="true"></div>
      <div class="card-content">
        <h3>${item.title}</h3>
        <p>${item.address}</p>
        <p>${item.areaRange}</p>
        <p>${item.rooms}</p>
        <button class="btn btn-secondary" type="button" aria-label="Conhecer lançamento ${item.title}">Conhecer empreendimento</button>
      </div>
    </article>
  `;
}

function renderReadyProperties() {
  let list = [...readyProperties];

  if (selectedType !== 'Todos') {
    list = list.filter((property) => property.type === selectedType);
  }

  if (selectedRegion) {
    list = list.filter((property) => property.region === selectedRegion);
  }

  readyGrid.innerHTML = list.length
    ? list.map(propertyCard).join('')
    : '<p>Nenhum imóvel encontrado para o filtro selecionado.</p>';
}

function renderLaunches() {
  launchGrid.innerHTML = launchProperties.map(launchCard).join('');
}

function renderTestimonials() {
  testimonialGrid.innerHTML = testimonials.map((item) => `
    <article class="testimonial">
      <p>“${item.text}”</p>
      <strong>${item.name}</strong>
    </article>
  `).join('');
}

function updateSelectedText() {
  const typeLabel = selectedType || 'Todos';
  selectedTypeText.textContent = `Tipo selecionado: ${typeLabel}`;
}

function setupTabs() {
  const tabs = qsa('.tab');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      tabs.forEach((item) => {
        item.classList.remove('is-active');
        item.setAttribute('aria-selected', 'false');
      });

      qsa('.tab-panel').forEach((panel) => {
        panel.classList.remove('is-active');
        panel.hidden = true;
      });

      tab.classList.add('is-active');
      tab.setAttribute('aria-selected', 'true');

      const panel = qs(`#${tab.getAttribute('aria-controls')}`);
      panel.classList.add('is-active');
      panel.hidden = false;
    });
  });
}

function setupSearchActions() {
  qs('#searchBtn').addEventListener('click', () => {
    const activeTab = qs('.tab.is-active')?.textContent?.trim() || 'Prontos';
    searchStatus.textContent = `Busca simulada para “${activeTab}” executada com sucesso. Confira os resultados em destaque abaixo.`;
  });

  qs('#openMapFromTab').addEventListener('click', () => {
    qs('#mapa').scrollIntoView({ behavior: 'smooth' });
  });

  qs('#mapSearchBtn').addEventListener('click', () => {
    pinsList.innerHTML = mockPins.map((pin) => `<li>${pin}</li>`).join('');
    searchStatus.textContent = 'Modo mapa ativado: pinos atualizados por região.';
  });

  qs('#launchCta').addEventListener('click', () => {
    searchStatus.textContent = 'Em breve: catálogo completo de lançamentos. Fale com nossa equipe para receber em primeira mão.';
  });
}

function setupFilters() {
  typeChips.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-type]');
    if (!button) return;
    selectedType = button.dataset.type;
    renderTypeChips();
    updateSelectedText();
    renderReadyProperties();
  });

  regionChips.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-region]');
    if (!button) return;

    selectedRegion = selectedRegion === button.dataset.region ? null : button.dataset.region;
    renderRegionChips();
    renderReadyProperties();
    qs('#prontos').scrollIntoView({ behavior: 'smooth' });

    if (selectedRegion) {
      searchStatus.textContent = `Filtro aplicado para região: ${selectedRegion}.`;
    }
  });
}

function setupMenuDrawer() {
  const menuToggle = qs('#menuToggle');
  const menuDrawer = qs('#menuDrawer');
  const closeDrawer = qs('#closeDrawer');
  const menuOverlay = qs('#menuOverlay');

  function openDrawer() {
    menuDrawer.classList.add('is-open');
    menuDrawer.setAttribute('aria-hidden', 'false');
    menuToggle.setAttribute('aria-expanded', 'true');
    menuOverlay.hidden = false;
    closeDrawer.focus();
  }

  function closeMenu() {
    menuDrawer.classList.remove('is-open');
    menuDrawer.setAttribute('aria-hidden', 'true');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuOverlay.hidden = true;
    menuToggle.focus();
  }

  menuToggle.addEventListener('click', openDrawer);
  closeDrawer.addEventListener('click', closeMenu);
  menuOverlay.addEventListener('click', closeMenu);

  qsa('.drawer-nav a').forEach((link) => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuDrawer.classList.contains('is-open')) {
      closeMenu();
    }
  });
}

function setupModal() {
  const modal = qs('#financeModal');
  const openBtn = qs('#openFinanceModal');
  const closeBtn = qs('#closeFinanceModal');

  if (!modal || !closeBtn) return;

  function openModal() {
    modalLastFocus = document.activeElement;
    modal.hidden = false;
    closeBtn.focus();
  }

  function closeModal() {
    modal.hidden = true;
    if (modalLastFocus instanceof HTMLElement) modalLastFocus.focus();
  }

  if (openBtn) {
    openBtn.addEventListener('click', openModal);
  }

  closeBtn.addEventListener('click', closeModal);

  modal.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) closeModal();
  });
}

function setYear() {
  qs('#currentYear').textContent = new Date().getFullYear();
}

function init() {
  renderMetrics();
  renderTypeChips();
  renderRegionChips();
  renderReadyProperties();
  renderLaunches();
  renderTestimonials();
  updateSelectedText();

  setupTabs();
  setupSearchActions();
  setupFilters();
  setupMenuDrawer();
  setupModal();

  animateMetrics();
  setYear();
}

init();