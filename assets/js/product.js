'use strict';

const productData = {
  'amber-garam': {
    name: 'Amber Garam',
    price: 34,
    tag: 'Chef Favourite',
    description:
      'Warm, floral garam masala with mace, long pepper, and saffron. Crafted in micro-lots for layered gravies and fragrant roasts.',
    imageClass: 'aurora',
    origin: ['Hand blended in Jaipur spice ateliers.', 'Sourced from fourth-generation spice growers.'],
    tasting: ['Opening of saffron bloom and green cardamom.', 'Mace warmth with a lingering anise trail.'],
    usage: ['Bloom in ghee for biryanis.', 'Sprinkle over roasted root vegetables.', 'Finish clarified butter sauces.'],
  },
  'cedar-zaatar': {
    name: "Cedar Zaa'tar",
    price: 26,
    tag: 'Levant Reserve',
    description:
      'Sumac, toasted sesame, and wild thyme stone-ground with preserved lemon for a bright, herbal crunch.',
    imageClass: 'verdant',
    origin: ['Wild thyme foraged in the Chouf reserve.', 'Hand-dried sumac berries sun-bleached for sweetness.'],
    tasting: ['Bright citrus zest.', 'Savory sesame depth.', 'Earthy thyme finale.'],
    usage: ['Brush over flatbreads with olive oil.', 'Dust over labneh or whipped feta.', 'Season fire-roasted vegetables.'],
  },
  'charred-harissa': {
    name: 'Charred Citrus Harissa',
    price: 29,
    tag: 'Limited Ember',
    description:
      'Burnt orange peel, smoked paprika, and black garlic layered with Aleppo chile for a deep embered heat.',
    imageClass: 'ember-rise',
    origin: ['Aleppo chiles sun-dried in Saharan winds.', 'Black garlic aged 45 days in cedar casks.'],
    tasting: ['Bright citrus smoke.', 'Molasses sweetness.', 'Cocoa nib earthiness.'],
    usage: ['Fold into roasted pepper hummus.', 'Glaze charcoal grilled poultry.', 'Finish seafood stews.'],
  },
  'kasbah-evenings': {
    name: 'Kasbah Evenings',
    price: 32,
    tag: 'Moroccan Signature',
    description: 'Ras el hanout with rose petals, saffron threads, and grains of paradise for sultry depth.',
    imageClass: 'dusk',
    origin: ['Rose petals hand-plucked at dawn in El Kelaa.', 'Saffron threads sourced from Taliouine harvests.'],
    tasting: ['Floral rose bloom.', 'Amber spice warmth.', 'Peppery finish.'],
    usage: ['Rub over lamb before slow roasting.', 'Stir into tagines and couscous.', 'Blend into roasted carrot purée.'],
  },
  'silk-road-dawn': {
    name: 'Silk Road Dawn',
    price: 28,
    tag: 'Golden Turmeric',
    description: 'Turmeric, cardamom, and candied citrus peel for luminous golden lattes and vibrant curries.',
    imageClass: 'sunrise',
    origin: ['Lakadong turmeric milled weekly.', 'Cardamom pods shade-grown in Idukki.'],
    tasting: ['Golden earthiness.', 'Cardamom sparkle.', 'Citrus honey finish.'],
    usage: ['Whisk into steamed milk for golden lattes.', 'Season coconut curries.', 'Brighten roasted cauliflower.'],
  },
  'crimson-ember': {
    name: 'Crimson Ember',
    price: 30,
    tag: 'Smoked Heat',
    description: 'Smoked paprika, Aleppo pepper, and cocoa nibs for bold, smoky heat.',
    imageClass: 'ember',
    origin: ['Paprika cold-smoked over beechwood.', 'Aleppo pepper sun-dried above Mediterranean cliffs.'],
    tasting: ['Velvet smoke.', 'Sweet chili warmth.', 'Cocoa depth.'],
    usage: ['Dust over grilled corn.', 'Blend into chili oils.', 'Finish braised beans.'],
  },
  'crimson-ember-harissa': {
    name: 'Crimson Ember Harissa',
    price: 32,
    tag: 'Fiery Signature',
    description: 'Sun-dried chilies, smoked paprika, and black garlic with charred citrus.',
    imageClass: 'ember-rise',
    origin: ['Chilies dried in Saharan sun.', 'Black garlic aged in cedar barrels.'],
    tasting: ['Charred citrus zing.', 'Smoky pepper layers.', 'Garlic molasses finish.'],
    usage: ['Spread on grilled flatbreads.', 'Toss roast potatoes.', 'Stir into tomato soups.'],
  },
  'cypress-zaatar': {
    name: "Cypress Garden Zaatar",
    price: 24,
    tag: 'Levant Wildcrafted',
    description: 'Wild thyme, toasted sesame, and sumac with preserved lemon for a coastal lift.',
    imageClass: 'verdant',
    origin: ['Thyme gathered on Mount Lebanon slopes.', 'Sesame toasted in olive-wood fires.'],
    tasting: ['Citrus brightness.', 'Savory herbaceous bite.', 'Nutty sesame base.'],
    usage: ['Finish labneh mezze.', 'Scatter over grilled halloumi.', 'Season roasted chickpeas.'],
  },
  'kesar-milk-masala': {
    name: 'Kesar Royal Milk Masala',
    price: 28,
    tag: 'Dessert Atelier',
    description: 'Saffron threads, pistachio, and cardamom for festive sweets and beverages.',
    imageClass: 'saffron-gold',
    origin: ['Saffron from Pampore valley.', 'Pistachios slow-roasted over teak embers.'],
    tasting: ['Saffron bloom.', 'Nutty pistachio.', 'Cardamom velvet.'],
    usage: ['Steep in milk for kulfi.', 'Blend into kheers and payasams.', 'Finish custards and panna cottas.'],
  },
  'midnight-caravan': {
    name: 'Midnight Caravan Pepper',
    price: 30,
    tag: 'Smoked Pepper Flight',
    description: 'Cold-smoked Tellicherry peppercorns with cacao husk and sandalwood for layered warmth.',
    imageClass: 'midnight-smoke',
    origin: ['Tellicherry pepper sun-dried on Malabar shores.', 'Smokehouse seasoned with sandalwood and cacao husk.'],
    tasting: ['Layered smoke.', 'Cacao bitterness.', 'Pepper bite.'],
    usage: ['Crush over steaks and grilled tofu.', 'Stir into dal tadkas.', 'Finish charred greens.'],
  },
  'cacao-ember': {
    name: 'Cacao Ember Rub',
    price: 27,
    tag: 'Smoker Series',
    description: 'Chipotle, cacao nibs, and piloncillo sugar for deep, smoky grilling.',
    imageClass: 'cocoa-ember',
    origin: ['Chipotle smoked over mesquite wood.', 'Cacao nibs stone-ground in Oaxaca.'],
    tasting: ['Dark chocolate smoke.', 'Warm chili sweetness.', 'Caramelized finish.'],
    usage: ['Rub onto beef brisket.', 'Season grilled portobellos.', 'Stir into barbecue sauces.'],
  },
  'damask-rose': {
    name: 'Damask Rose Latte Dust',
    price: 25,
    tag: 'Floral Atelier',
    description: 'Rose petals, beet sugar, and Tahitian vanilla for dreamy lattes and pastries.',
    imageClass: 'rose-latte',
    origin: ['Damask roses from the Valley of Roses.', 'Vanilla cured in bourbon barrels.'],
    tasting: ['Petal sweetness.', 'Berry brightness.', 'Vanilla silk.'],
    usage: ['Stir into steamed milk.', 'Dust over pavlovas.', 'Blend into buttercream.'],
  },
  'jade-amber': {
    name: 'Jade Amber Pho Broth',
    price: 29,
    tag: 'Broth Sommelier',
    description: 'Star anise, cassia bark, and roasted ginger for layered broths and soups.',
    imageClass: 'jade-amber',
    origin: ['Spices sourced from Mekong river markets.', 'Ginger charred over coconut husk fires.'],
    tasting: ['Anise brightness.', 'Warm cassia.', 'Toasted ginger depth.'],
    usage: ['Simmer pho broths.', 'Season braised tofu.', 'Finish noodle soups.'],
  },
  'mesquite-flare': {
    name: 'Mesquite Flare BBQ Dust',
    price: 31,
    tag: 'Pitmaster Edition',
    description: 'Mesquite-smoked chilies, espresso, and coriander for bold barbecue char.',
    imageClass: 'mesquite-flare',
    origin: ['Mesquite wood fired smokehouses.', 'Espresso beans roasted in small batches.'],
    tasting: ['Espresso char.', 'Smoky chili heat.', 'Coriander brightness.'],
    usage: ['Rub on ribs and jackfruit.', 'Dust over grilled corn.', 'Stir into black bean stews.'],
  },
};

const fallbackProduct = {
  name: 'Unknown Blend',
  price: 0,
  tag: 'Unavailable',
  description:
    "We're unable to find this blend. Explore our categories to discover other curated flights.",
  imageClass: 'aurora',
  origin: ['---'],
  tasting: ['---'],
  usage: ['---'],
};

const query = new URLSearchParams(window.location.search);
const productId = query.get('id');
const product = (productId && productData[productId]) || null;
const yearEl = document.getElementById('year');
if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
}

const mainSection = document.getElementById('product-main');
const emptyState = document.getElementById('product-empty');

const populateList = (containerId, items) => {
  const list = document.getElementById(containerId);
  if (!list) return;
  list.innerHTML = '';
  items.forEach((item) => {
    const li = document.createElement('li');
    li.textContent = item;
    list.appendChild(li);
  });
};

if (product) {
  mainSection?.removeAttribute('hidden');
  emptyState?.setAttribute('hidden', '');

  const data = product;
  document.getElementById('product-name').textContent = data.name;
  document.getElementById('product-price').textContent = `$${data.price}`;
  document.getElementById('product-tag').textContent = data.tag;
  document.getElementById('product-description').textContent = data.description;

  const gallery = document.getElementById('product-image');
  if (gallery) {
    gallery.className = `card-image ${data.imageClass}`;
  }

  populateList('origin-list', data.origin);
  populateList('tasting-list', data.tasting);
  populateList('usage-list', data.usage);

  const metaContainer = document.getElementById('product-meta');
  if (metaContainer) {
    metaContainer.innerHTML = '';
    const chips = [data.tag, productId.replace(/-/g, ' ')];
    chips.forEach((value) => {
      const chip = document.createElement('span');
      chip.className = 'info-chip';
      chip.textContent = value;
      metaContainer.appendChild(chip);
    });
  }
} else {
  mainSection?.setAttribute('hidden', '');
  emptyState?.removeAttribute('hidden');

  document.getElementById('product-name').textContent = fallbackProduct.name;
  document.getElementById('product-price').textContent = '--';
  document.getElementById('product-tag').textContent = fallbackProduct.tag;
  document.getElementById('product-description').textContent = fallbackProduct.description;
  populateList('origin-list', fallbackProduct.origin);
  populateList('tasting-list', fallbackProduct.tasting);
  populateList('usage-list', fallbackProduct.usage);
}
