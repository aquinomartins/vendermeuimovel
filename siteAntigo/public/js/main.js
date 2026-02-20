let homeData = { typeChips: [], metrics: [], readyCards: [], launchCards: [], regionChips: [], testimonials: [], pinsList: [], featuresList: [] };
let selectedType = 'Todos';
let selectedRegion = null;

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
const featuresList = qs('#featuresList');

function formatMetric(metric) {
  const value = Number(metric.title || metric.text || 0);
  const prefix = metric.badge || '';
  const suffix = metric.price || '';
  return `${prefix}${value.toLocaleString('pt-BR')}${suffix}`;
}

function renderMetrics() {
  metricsGrid.innerHTML = homeData.metrics.map((metric, index) => {
    const value = Number(metric.title || metric.text || 0);
    return `<article class="metric-card"><div class="metric-number" data-target="${value}" data-prefix="${metric.badge || ''}" data-suffix="${metric.price || ''}" id="metric-${index}">${formatMetric({ ...metric, title: 0 })}</div><p class="metric-label">${metric.text || ''}</p></article>`;
  }).join('');
}

function animateMetrics() {
  qsa('.metric-number').forEach((el) => {
    const target = Number(el.dataset.target || 0);
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = `${prefix}${current.toLocaleString('pt-BR')}${suffix}`;
    }, 20);
  });
}

function renderTypeChips() {
  const allTypes = ['Todos', ...homeData.typeChips.map((item) => item.title)];
  typeChips.innerHTML = allTypes.map((type) => `<button class="chip ${selectedType === type ? 'is-selected' : ''}" type="button" data-type="${type}">${type}</button>`).join('');
}

function renderRegionChips() {
  regionChips.innerHTML = homeData.regionChips.map((item) => `<button class="chip ${selectedRegion === item.title ? 'is-selected' : ''}" type="button" data-region="${item.title}">${item.title}</button>`).join('');
}

function renderReadyProperties() {
  let list = [...homeData.readyCards];
  if (selectedType !== 'Todos') list = list.filter((item) => (item.badge || '').includes(selectedType) || (item.text || '').includes(selectedType));
  if (selectedRegion) list = list.filter((item) => (item.text || '').includes(selectedRegion));

  readyGrid.innerHTML = list.length ? list.map((item) => `<article class="card"><div class="card-media" aria-hidden="true"></div><div class="card-content">${item.badge ? `<span class="badge">${item.badge}</span>` : ''}<h3>${item.title}</h3><p>${item.text || ''}</p><p class="card-price">${item.price || ''}</p><a class="btn btn-secondary" href="${item.link_url || '#'}">Ver detalhes</a></div></article>`).join('') : '<p>Nenhum imóvel encontrado para o filtro selecionado.</p>';
}

function renderLaunches() {
  launchGrid.innerHTML = homeData.launchCards.map((item) => `<article class="card"><div class="card-media" aria-hidden="true"></div><div class="card-content"><h3>${item.title}</h3><p>${item.text || ''}</p><a class="btn btn-secondary" href="${item.link_url || '#'}">Conhecer empreendimento</a></div></article>`).join('');
}

function renderTestimonials() {
  testimonialGrid.innerHTML = homeData.testimonials.map((item) => `<article class="testimonial"><p>“${item.text || ''}”</p><strong>${item.title || ''}</strong></article>`).join('');
}

function renderFeaturesList() {
  if (!featuresList) return;
  featuresList.innerHTML = homeData.featuresList.map((item) => `<li>${item.title || item.text || ''}</li>`).join('');
}

function bindUI() {
  typeChips.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-type]');
    if (!button) return;
    selectedType = button.dataset.type;
    renderTypeChips();
    selectedTypeText.textContent = `Tipo selecionado: ${selectedType}`;
    renderReadyProperties();
  });

  regionChips.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-region]');
    if (!button) return;
    selectedRegion = selectedRegion === button.dataset.region ? null : button.dataset.region;
    renderRegionChips();
    renderReadyProperties();
  });

  qsa('.tab').forEach((tab) => {
    tab.addEventListener('click', () => {
      qsa('.tab').forEach((item) => { item.classList.remove('is-active'); item.setAttribute('aria-selected', 'false'); });
      qsa('.tab-panel').forEach((panel) => { panel.classList.remove('is-active'); panel.hidden = true; });
      tab.classList.add('is-active');
      tab.setAttribute('aria-selected', 'true');
      const panel = qs(`#${tab.getAttribute('aria-controls')}`);
      panel.classList.add('is-active');
      panel.hidden = false;
    });
  });

  const menuToggle = qs('#menuToggle');
  const menuDrawer = qs('#menuDrawer');
  const closeDrawer = qs('#closeDrawer');
  const menuOverlay = qs('#menuOverlay');
  menuToggle?.addEventListener('click', () => { menuDrawer.classList.add('is-open'); menuDrawer.setAttribute('aria-hidden', 'false'); menuToggle.setAttribute('aria-expanded', 'true'); menuOverlay.hidden = false; });
  closeDrawer?.addEventListener('click', () => { menuDrawer.classList.remove('is-open'); menuDrawer.setAttribute('aria-hidden', 'true'); menuToggle.setAttribute('aria-expanded', 'false'); menuOverlay.hidden = true; });
  menuOverlay?.addEventListener('click', () => closeDrawer?.click());

  qs('#searchBtn')?.addEventListener('click', () => { searchStatus.textContent = 'Busca executada com sucesso.'; });
  qs('#mapSearchBtn')?.addEventListener('click', () => { pinsList.innerHTML = homeData.pinsList.map((item) => `<li>${item.title || item.text}</li>`).join(''); });
  qs('#openMapFromTab')?.addEventListener('click', () => qs('#mapa')?.scrollIntoView({ behavior: 'smooth' }));
  qs('#currentYear').textContent = new Date().getFullYear();
}

async function init() {
  try {
    const response = await fetch('/api/home-data.php', { headers: { Accept: 'application/json' } });
    homeData = await response.json();
  } catch (error) {
    console.error(error);
  }

  renderMetrics();
  renderTypeChips();
  renderRegionChips();
  renderReadyProperties();
  renderLaunches();
  renderTestimonials();
  renderFeaturesList();
  selectedTypeText.textContent = `Tipo selecionado: ${selectedType}`;
  animateMetrics();
  bindUI();
}

init();
