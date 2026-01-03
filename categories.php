<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shop by Category | Saffron & Spice';

$filterRegion = $_GET['filter'] ?? $_GET['region'] ?? null;
$filterCraft = $_GET['craft'] ?? null;
$filterHeat = $_GET['heat'] ?? null;

$products = get_all_products();

include __DIR__ . '/includes/header.php';
?>
    <main class="category-main">
      <section class="glass-panel category-hero">
        <div class="hero-copy">
          <span class="tag">Explore the Palette</span>
          <h1>Find the blend that elevates your craft.</h1>
          <p>
            Filter by origin, technique, or heat to discover tins curated for chefs and enthusiasts alike. Each blend tells a
            story—let&apos;s match you with yours.
          </p>
        </div>
        <div class="hero-media compact">
          <div class="spice-orb saffron"></div>
          <div class="spice-orb pepper"></div>
          <div class="spice-orb cinnamon"></div>
        </div>
      </section>

      <section class="category-layout">
        <aside class="glass-panel filters-panel">
          <div class="filters-header">
            <h2>Filters</h2>
            <button type="button" class="button outline small" id="reset-filters">Clear</button>
          </div>
          <form class="filters-form" aria-label="Filter blends">
            <fieldset class="filter-group">
              <legend>Region</legend>
              <?php
                $regions = ['south-asia' => 'South Asian Heritage', 'middle-east' => 'Levant &amp; Maghreb', 'silk-road' => 'Silk Road Classics', 'americas' => 'New World Spice Routes'];
                foreach ($regions as $value => $label):
              ?>
                <label>
                  <input type="checkbox" name="region" value="<?= htmlspecialchars($value) ?>" />
                  <span><?= $label ?></span>
                </label>
              <?php endforeach; ?>
            </fieldset>

            <fieldset class="filter-group">
              <legend>Craft</legend>
              <?php
                $crafts = ['smoked' => 'Smoked &amp; Fired', 'herbal' => 'Botanical Infusions', 'heat' => 'Heat &amp; Ember', 'sweet' => 'Desserts &amp; Sweets'];
                foreach ($crafts as $value => $label):
              ?>
                <label>
                  <input type="checkbox" name="craft" value="<?= htmlspecialchars($value) ?>" />
                  <span><?= $label ?></span>
                </label>
              <?php endforeach; ?>
            </fieldset>

            <fieldset class="filter-group">
              <legend>Heat</legend>
              <?php
                $heatLevels = ['mild' => 'Mild &amp; Aromatic', 'medium' => 'Warm &amp; Balanced', 'fiery' => 'Fiery &amp; Bold'];
                foreach ($heatLevels as $value => $label):
              ?>
                <label>
                  <input type="checkbox" name="heat" value="<?= htmlspecialchars($value) ?>" />
                  <span><?= $label ?></span>
                </label>
              <?php endforeach; ?>
            </fieldset>
          </form>
          <div class="active-filters" aria-live="polite" hidden></div>
        </aside>

        <section class="glass-panel results-panel" aria-live="polite">
          <div class="results-header">
            <div>
              <span class="tag">Curated Selection</span>
              <h2>Limited Harvest Collections</h2>
            </div>
            <div class="sort-control">
              <label for="sort-select">Sort by</label>
              <select id="sort-select">
                <option value="featured">Featured</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="heat">Heat Level</option>
              </select>
            </div>
          </div>

          <div class="product-grid" id="product-grid">
            <?php foreach ($products as $product): ?>
              <article
                class="category-card product-card"
                data-product-id="<?= htmlspecialchars($product['slug']) ?>"
                data-region="<?= htmlspecialchars($product['region']) ?>"
                data-craft="<?= htmlspecialchars($product['craft']) ?>"
                data-heat="<?= htmlspecialchars($product['heat']) ?>"
                data-price="<?= htmlspecialchars($product['price']) ?>"
                data-heat-rank="<?= $product['heat'] === 'mild' ? 1 : ($product['heat'] === 'medium' ? 2 : 3) ?>"
              >
                <div class="card-image <?= htmlspecialchars($product['image_class']) ?>"></div>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['summary']) ?></p>
                <div class="card-footer">
                  <span class="price"><?= format_price((float) $product['price']) ?></span>
                  <a class="button ghost" href="/online-sp/product.php?slug=<?= urlencode($product['slug']) ?>">View details</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="empty-state" hidden>
            <h3>No blends match your filters yet.</h3>
            <p>Try adjusting your selections or clearing the filters to explore our full atelier.</p>
            <button type="button" class="button primary" id="empty-reset">Clear filters</button>
          </div>
        </section>
      </section>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
      const filtersForm = document.querySelector('.filters-form');
      const checkboxes = filtersForm.querySelectorAll("input[type='checkbox']");
      const cards = document.querySelectorAll('.category-card');
      const activeFiltersContainer = document.querySelector('.active-filters');
      const resetButton = document.getElementById('reset-filters');
      const emptyResetButton = document.getElementById('empty-reset');
      const emptyState = document.querySelector('.empty-state');
      const sortSelect = document.getElementById('sort-select');

      const selected = {
        region: new Set(),
        craft: new Set(),
        heat: new Set(),
      };

      const updateBadges = () => {
        activeFiltersContainer.innerHTML = '';
        Object.entries(selected).forEach(([group, values]) => {
          values.forEach((value) => {
            const badge = document.createElement('button');
            badge.className = 'filter-chip';
            badge.type = 'button';
            badge.dataset.group = group;
            badge.dataset.value = value;
            badge.innerHTML = `${value.replace(/-/g, ' ')}<span aria-hidden="true">×</span>`;
            badge.title = `Remove ${value.replace(/-/g, ' ')}`;
            activeFiltersContainer.appendChild(badge);
          });
        });
        activeFiltersContainer.toggleAttribute('hidden', activeFiltersContainer.children.length === 0);
      };

      const applyFilters = () => {
        let visibleCount = 0;

        cards.forEach((card) => {
          const matches = Object.entries(selected).every(([group, values]) => {
            if (values.size === 0) return true;
            return values.has(card.dataset[group]);
          });

          card.toggleAttribute('hidden', !matches);
          if (matches) visibleCount += 1;
        });

        emptyState.toggleAttribute('hidden', visibleCount !== 0);
      };

      const sortCards = () => {
        const grid = document.getElementById('product-grid');
        const items = Array.from(grid.children);
        let comparator = () => 0;

        if (sortSelect.value === 'price-asc') {
          comparator = (a, b) => Number(a.dataset.price) - Number(b.dataset.price);
        } else if (sortSelect.value === 'price-desc') {
          comparator = (a, b) => Number(b.dataset.price) - Number(a.dataset.price);
        } else if (sortSelect.value === 'heat') {
          comparator = (a, b) => Number(a.dataset.heatRank) - Number(b.dataset.heatRank);
        }

        items.sort(comparator).forEach((item) => grid.appendChild(item));
      };

      const syncSelections = () => {
        Object.keys(selected).forEach((group) => selected[group].clear());
        checkboxes.forEach((checkbox) => {
          if (checkbox.checked) {
            selected[checkbox.name].add(checkbox.value);
          }
        });
        updateBadges();
        applyFilters();
      };

      checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', syncSelections);
      });

      const clearAll = () => {
        checkboxes.forEach((checkbox) => {
          checkbox.checked = false;
        });
        syncSelections();
      };

      resetButton.addEventListener('click', clearAll);
      emptyResetButton.addEventListener('click', clearAll);

      sortSelect.addEventListener('change', () => {
        sortCards();
      });

      activeFiltersContainer.addEventListener('click', (event) => {
        const target = event.target.closest('.filter-chip');
        if (!target) return;
        const { group, value } = target.dataset;
        const checkbox = filtersForm.querySelector(`input[name='${group}'][value='${value}']`);
        if (checkbox) {
          checkbox.checked = false;
          syncSelections();
        }
      });

      // Preselect filters via query string
      const preselectFilters = () => {
        const params = new URLSearchParams(window.location.search);
        ['filter', 'region', 'craft', 'heat'].forEach((param) => {
          if (!params.has(param)) return;
          const value = params.get(param);
          if (!value) return;
          const checkbox = filtersForm.querySelector(`input[value='${CSS.escape(value)}']`);
          if (checkbox) {
            checkbox.checked = true;
          }
        });
        syncSelections();
        sortCards();
      };

      preselectFilters();
      sortCards();

      document.querySelectorAll('.product-card[data-product-id]').forEach((card) => {
        card.addEventListener('click', (event) => {
          if (event.target.closest('a')) return;
          window.location.href = `/online-sp/product.php?slug=${encodeURIComponent(card.dataset.productId)}`;
        });
        card.addEventListener('keypress', (event) => {
          if (event.key === 'Enter') {
            event.preventDefault();
            window.location.href = `/online-sp/product.php?slug=${encodeURIComponent(card.dataset.productId)}`;
          }
        });
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'link');
        card.setAttribute('aria-label', `${card.querySelector('h3').textContent} details`);
      });
    </script>
