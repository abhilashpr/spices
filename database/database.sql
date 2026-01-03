CREATE DATABASE IF NOT EXISTS saffron_spice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saffron_spice;

DROP TABLE IF EXISTS hero_slides;
DROP TABLE IF EXISTS product_media;
DROP TABLE IF EXISTS related_products;
DROP TABLE IF EXISTS product_reviews;
DROP TABLE IF EXISTS products;
CREATE TABLE hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  tag_line VARCHAR(150) NOT NULL,
  headline VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  primary_label VARCHAR(120) DEFAULT NULL,
  primary_url VARCHAR(255) DEFAULT NULL,
  secondary_label VARCHAR(120) DEFAULT NULL,
  secondary_url VARCHAR(255) DEFAULT NULL,
  card_title VARCHAR(120) DEFAULT NULL,
  card_subtitle VARCHAR(150) DEFAULT NULL,
  price DECIMAL(10,2) DEFAULT NULL,
  media_orbs VARCHAR(255) DEFAULT NULL,
  display_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO hero_slides
  (slug, tag_line, headline, description, primary_label, primary_url, secondary_label, secondary_url, card_title, card_subtitle, price, media_orbs, display_order)
VALUES
  ('signature-flight', 'Artisanal Spice Atelier', 'Elevate every dish with rare, hand-curated spices.', 'Discover small-batch blends sourced from heritage farms, carefully roasted and perfected by master blenders. Experience the aroma, depth, and authenticity of spices treasured around the world.', 'Explore Blends', '/online-sp/index.php#collections', 'Our Story', '/online-sp/index.php#stories', 'Signature Flight', 'Six exotic blends | Limited harvest', 89.00, 'saffron, pepper, cinnamon', 1),
  ('saffron-reserve', 'Limited Reserve', 'Experience the glow of our saffron reserve.', 'Threads plucked at dawn from Pampore’s violet fields, sealed in hand-blown glass to preserve potency for your desserts and savory infusions.', 'Shop Saffron', '/online-sp/product.php?id=saffron-reserve', 'Join Taste Club', '/online-sp/index.php#taste-club', 'Saffron Reserve', 'Grade A+ stigmas | Harvest 2025', 54.00, 'saffron large, ember', 2),
  ('smoked-pepper', 'Chef Spotlight', 'Smoked peppercorns curated by Chef Nalini Rao.', 'Cold-smoked over sandalwood and cacao husk for layered warmth. A finishing spice for steaks, lentils, and charred greens.', 'Taste the Flight', '/online-sp/index.php#best-sellers', 'View Smoked Range', '/online-sp/categories.php?craft=smoked', 'Smoked Noir Pepper', 'Single-estate Tellicherry | 75g', 22.00, 'pepper large, dusk', 3);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  tag_line VARCHAR(150) DEFAULT NULL,
  summary TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  offer_price DECIMAL(10,2) DEFAULT NULL,
  full_description TEXT DEFAULT NULL,
  image_class VARCHAR(80) NOT NULL,
  region VARCHAR(60) NOT NULL,
  craft VARCHAR(60) NOT NULL,
  heat VARCHAR(30) NOT NULL,
  best_seller TINYINT(1) NOT NULL DEFAULT 0,
  best_seller_order INT DEFAULT NULL,
  collection TINYINT(1) NOT NULL DEFAULT 0,
  collection_order INT DEFAULT NULL,
  origin_notes TEXT DEFAULT NULL,
  tasting_notes TEXT DEFAULT NULL,
  usage_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE product_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  media_type ENUM('image', 'video') NOT NULL DEFAULT 'image',
  media_url VARCHAR(255) NOT NULL,
  caption VARCHAR(255) DEFAULT NULL,
  display_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE related_products (
  product_id INT NOT NULL,
  related_product_id INT NOT NULL,
  PRIMARY KEY (product_id, related_product_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (related_product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  reviewer_name VARCHAR(120) NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  headline VARCHAR(150) DEFAULT NULL,
  review_text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  display_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO admins (username, password) VALUES
  ('admin@gmail.com', '$2y$10$JizZxyDZYCA09dRE1Rd92uA5o/t0HutddxIaDeBMSLja92tyzHHVi');

INSERT INTO products
  (name, slug, tag_line, summary, price, offer_price, full_description, image_class, region, craft, heat, best_seller, best_seller_order, collection, collection_order, origin_notes, tasting_notes, usage_notes)
VALUES
  ('Amber Garam', 'amber-garam', 'Chef Favourite', 'Warm, floral garam masala with mace, long pepper, and saffron.', 34.00, 29.00,
    'Amber Garam is our chef-curated house blend built for signature biryanis, rich gravies, and celebratory dishes. Every batch is stone-ground in Jaipur ateliers to deliver a velvety finish and a golden hue that compliments both vegetarian and meat-based courses.',
    'aurora', 'south-asia', 'heat', 'medium', 1, 1, 0, NULL,
    'Hand blended in Jaipur spice ateliers.
Sourced from fourth-generation spice growers.',
    'Opening of saffron bloom and green cardamom.
Mace warmth with a lingering anise trail.',
    'Bloom in ghee for biryanis.
Sprinkle over roasted root vegetables.
Finish clarified butter sauces.'),
  ('Cedar Zaa''tar', 'cedar-zaatar', 'Levant Reserve', 'Sumac, toasted sesame, and wild thyme stone-ground with preserved lemon.', 26.00, 24.00,
    'Inspired by twilight picnics in the Chouf reserve, this zaa''tar balances sun-dried herbs with citrus brightness. Serve with warm flatbreads, incorporate into salad dressings, or finish roasted vegetables for a lively Levantine flourish.',
    'verdant', 'middle-east', 'herbal', 'mild', 1, 2, 0, NULL,
    'Wild thyme foraged in the Chouf reserve.
Hand-dried sumac berries sun-bleached for sweetness.',
    'Bright citrus zest.
Savory sesame depth.
Earthy thyme finale.',
    'Brush over flatbreads with olive oil.
Dust over labneh or whipped feta.
Season fire-roasted vegetables.'),
  ('Charred Citrus Harissa', 'charred-harissa', 'Limited Ember', 'Burnt orange peel, smoked paprika, and black garlic layered with Aleppo chile.', 29.00, 27.00,
    'Our limited ember series harissa is slow-fermented for complexity and cold-smoked over sandalwood. Stir into braises, fold through soups, or brush atop roasted vegetables for depth and gentle heat.',
    'ember-rise', 'south-asia', 'heat', 'fiery', 1, 3, 0, NULL,
    'Aleppo chiles sun-dried in Saharan winds.
Black garlic aged 45 days in cedar casks.',
    'Bright citrus smoke.
Molasses sweetness.
Cocoa nib earthiness.',
    'Fold into roasted pepper hummus.
Glaze charcoal grilled poultry.
Finish seafood stews.'),
  ('Kasbah Evenings', 'kasbah-evenings', 'Moroccan Signature', 'Ras el hanout with rose petals, saffron threads, and grains of paradise.', 32.00, NULL,
    'Layered with over twenty spices, Kasbah Evenings brings dusky souk aromas to your kitchen. Use it as a dry rub, bloom in oil for tagines, or whisk into couscous for a floral, romantic finish.',
    'dusk', 'middle-east', 'herbal', 'medium', 0, NULL, 1, 1,
    'Rose petals hand-plucked at dawn in El Kelaa.
Saffron threads sourced from Taliouine harvests.',
    'Floral rose bloom.
Amber spice warmth.
Peppery finish.',
    'Rub over lamb before slow roasting.
Stir into tagines and couscous.
Blend into roasted carrot purée.'),
  ('Silk Road Dawn', 'silk-road-dawn', 'Golden Turmeric', 'Turmeric, cardamom, and candied citrus peel for luminous golden dishes.', 28.00, 25.00,
    'This golden latte blend is an ode to dawn-lit bazaars along the Silk Road. Expect mellow turmeric with the sparkle of cardamom and a finish of candied citrus peel, crafted for beverages and curries alike.',
    'sunrise', 'south-asia', 'sweet', 'mild', 0, NULL, 1, 2,
    'Lakadong turmeric milled weekly.
Cardamom pods shade-grown in Idukki.',
    'Golden earthiness.
Cardamom sparkle.
Citrus honey finish.',
    'Whisk into steamed milk for golden lattes.
Season coconut curries.
Brighten roasted cauliflower.'),
  ('Crimson Ember', 'crimson-ember', 'Smoked Heat', 'Smoked paprika, Aleppo pepper, and cocoa nibs for bold, smoky heat.', 30.00, 26.00,
    'Crimson Ember evokes the glow of desert campfires—smoked paprika and Aleppo pepper layered with cocoa nib richness. It is an instant upgrade for grilled meats, fire-roasted vegetables, and hearty stews.',
    'ember', 'silk-road', 'smoked', 'fiery', 0, NULL, 1, 3,
    'Paprika cold-smoked over beechwood.
Aleppo pepper sun-dried above Mediterranean cliffs.',
    'Velvet smoke.
Sweet chili warmth.
Cocoa depth.',
    'Dust over grilled corn.
Blend into chili oils.
Finish braised beans.'),
  ('Crimson Ember Harissa', 'crimson-ember-harissa', 'Fiery Signature', 'Sun-dried chilies, smoked paprika, and black garlic with charred citrus.', 32.00, 28.00,
    'An elevated pantry staple for chefs wanting complexity without the work. Our harissa paste is crafted in small batches, combining charred citrus zest, fermented garlic, and sun-dried chilies for a deep, savory finish.',
    'ember-rise', 'silk-road', 'heat', 'fiery', 0, NULL, 0, NULL,
    'Chilies dried in Saharan sun.
Black garlic aged in cedar barrels.',
    'Charred citrus zing.
Smoky pepper layers.
Garlic molasses finish.',
    'Spread on grilled flatbreads.
Toss roast potatoes.
Stir into tomato soups.'),
  ('Cypress Garden Zaatar', 'cypress-zaatar', 'Levant Wildcrafted', 'Wild thyme, toasted sesame, and sumac with preserved lemon for a coastal lift.', 24.00, NULL,
    'Coastal breezes and mountain herbs converge in this wildcrafted zaatar. Sprinkle generously over whipped labneh, salads, or oven-baked breads for an instant Riviera escape.',
    'verdant', 'middle-east', 'herbal', 'mild', 0, NULL, 0, NULL,
    'Thyme gathered on Mount Lebanon slopes.
Sesame toasted in olive-wood fires.',
    'Citrus brightness.
Savory herbaceous bite.
Nutty sesame base.',
    'Finish labneh mezze.
Scatter over grilled halloumi.
Season roasted chickpeas.'),
  ('Kesar Royal Milk Masala', 'kesar-milk-masala', 'Dessert Atelier', 'Saffron threads, pistachio, and cardamom for festive sweets and beverages.', 28.00, 24.00,
    'Handcrafted for celebratory desserts, Kesar Royal unfurls notes of saffron, pistachio, and cardamom. Stir into custards, simmer through milk, or infuse whipped cream for a regal finish.',
    'saffron-gold', 'south-asia', 'sweet', 'mild', 0, NULL, 0, NULL,
    'Saffron from Pampore valley.
Pistachios slow-roasted over teak embers.',
    'Saffron bloom.
Nutty pistachio.
Cardamom velvet.',
    'Steep in milk for kulfi.
Blend into kheers and payasams.
Finish custards and panna cottas.'),
  ('Midnight Caravan Pepper', 'midnight-caravan', 'Smoked Pepper Flight', 'Cold-smoked Tellicherry peppercorns with cacao husk and sandalwood.', 30.00, 27.00,
    'Tellicherry peppercorns, cold-smoked with cacao husk and sandalwood, create a pepper blend with profound depth. Crack over steaks, roasted vegetables, or buttery pastas.',
    'midnight-smoke', 'silk-road', 'smoked', 'medium', 0, NULL, 0, NULL,
    'Tellicherry pepper sun-dried on Malabar shores.
Smokehouse seasoned with sandalwood and cacao husk.',
    'Layered smoke.
Cacao bitterness.
Pepper bite.',
    'Crush over steaks and grilled tofu.
Stir into dal tadkas.
Finish charred greens.'),
  ('Cacao Ember Rub', 'cacao-ember', 'Smoker Series', 'Chipotle, cacao nibs, and piloncillo sugar for deep, smoky grilling.', 27.00, NULL,
    'Cacao Ember layers sweet-smoke complexity across your grill. Use generously on ribs, portobellos, or tofu for a barbeque glaze with a chocolate undertone.',
    'cocoa-ember', 'americas', 'smoked', 'medium', 0, NULL, 0, NULL,
    'Chipotle smoked over mesquite wood.
Cacao nibs stone-ground in Oaxaca.',
    'Dark chocolate smoke.
Warm chili sweetness.
Caramelized finish.',
    'Rub onto beef brisket.
Season grilled portobellos.
Stir into barbecue sauces.'),
  ('Damask Rose Latte Dust', 'damask-rose', 'Floral Atelier', 'Rose petals, beet sugar, and Tahitian vanilla for dreamy lattes and pastries.', 25.00, 22.00,
    'A patisserie-inspired powder for drinks and desserts, our Damask Rose dust softens beverages with a delicate blush. Blend into lattes, whip into buttercream, or dust over cakes.',
    'rose-latte', 'middle-east', 'sweet', 'mild', 0, NULL, 0, NULL,
    'Damask roses from the Valley of Roses.
Vanilla cured in bourbon barrels.',
    'Petal sweetness.
Berry brightness.
Vanilla silk.',
    'Stir into steamed milk.
Dust over pavlovas.
Blend into buttercream.'),
  ('Jade Amber Pho Broth', 'jade-amber', 'Broth Sommelier', 'Star anise, cassia bark, and roasted ginger for layered broths and soups.', 29.00, 26.00,
    'Beginning with Mekong-market aromatics, Jade Amber delivers a soulful broth base in minutes. Simmer with vegetables, tofu, or noodles for a comforting bowl brightened by roasted ginger.',
    'jade-amber', 'silk-road', 'herbal', 'medium', 0, NULL, 0, NULL,
    'Spices sourced from Mekong river markets.
Ginger charred over coconut husk fires.',
    'Anise brightness.
Warm cassia.
Toasted ginger depth.',
    'Simmer pho broths.
Season braised tofu.
Finish noodle soups.'),
  ('Mesquite Flare BBQ Dust', 'mesquite-flare', 'Pitmaster Edition', 'Mesquite-smoked chilies, espresso, and coriander for bold barbecue char.', 31.00, 28.00,
    'Mesquite Flare is designed for pitmasters and grill aficionados searching for layers of smoke and bittersweet espresso. Rub generously over proteins or vegetables before slow cooking.',
    'mesquite-flare', 'americas', 'heat', 'fiery', 0, NULL, 0, NULL,
    'Mesquite wood fired smokehouses.
Espresso beans roasted in small batches.',
    'Espresso char.
Smoky chili heat.
Coriander brightness.',
    'Rub on ribs and jackfruit.
Dust over grilled corn.
Stir into black bean stews.'),
  ('Saffron Reserve', 'saffron-reserve', 'Limited Harvest', 'Threads plucked at dawn, sealed to preserve potency for desserts and savory infusions.', 54.00, 49.00,
    'Our limited saffron reserve captures the first bloom of harvest in Pampore. Each hand-filled vial delivers luminous color and flavor to desserts, beverages, and savory dishes alike.',
    'saffron', 'south-asia', 'sweet', 'mild', 0, NULL, 0, NULL,
    'Harvested in Pampore’s violet fields.
Packed in hand-blown glass vials.',
    'Golden hue.
Honeyed aroma.
Delicate floral finish.',
    'Infuse in custards.
Steep for tea tonics.
Bloom in clarified butter.');

INSERT INTO product_media (product_id, media_type, media_url, caption, display_order)
VALUES
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'image', 'https://images.unsplash.com/photo-1526318896980-cf78c088247c?auto=format&fit=crop&w=900&q=80', 'Fort Kochi spice merchant selection', 1),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'image', 'https://images.unsplash.com/photo-1529927066849-2fc1a32ce102?auto=format&fit=crop&w=900&q=80', 'Kerala garam masala mise en place', 2),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'video', 'https://cdn.coverr.co/videos/coverr-spices-and-mortar-2107260712?download=1', 'Stone-grinding Kerala spice blend', 3),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), 'image', 'https://images.unsplash.com/photo-1545239351-1141bd82e8a6?auto=format&fit=crop&w=900&q=80', 'Smoked chilies at Mattancherry bazaar', 1),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), 'image', 'https://images.unsplash.com/photo-1573655349934-0c0357be0a64?auto=format&fit=crop&w=900&q=80', 'Pepper and chili bounty from Wayanad', 2),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), 'video', 'https://cdn.coverr.co/videos/coverr-sizzling-skillet-2428?download=1', 'Fire-smoking peppers for harissa', 3),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), 'image', 'https://images.unsplash.com/photo-1504753793650-d4a2b783c15e?auto=format&fit=crop&w=900&q=80', 'Houseboat chai with Kerala spices', 1),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), 'image', 'https://images.unsplash.com/photo-1571687949928-69b0d71950ee?auto=format&fit=crop&w=900&q=80', 'Spice trays ready for Alleppey market', 2),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), 'video', 'https://cdn.coverr.co/videos/coverr-preparing-a-hot-drink-4?download=1', 'Infusing saffron in Kerala tea house', 3);

INSERT INTO related_products (product_id, related_product_id)
VALUES
  ((SELECT id FROM products WHERE slug = 'amber-garam'), (SELECT id FROM products WHERE slug = 'charred-harissa')),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), (SELECT id FROM products WHERE slug = 'kasbah-evenings')),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), (SELECT id FROM products WHERE slug = 'crimson-ember-harissa')),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), (SELECT id FROM products WHERE slug = 'crimson-ember')),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), (SELECT id FROM products WHERE slug = 'kesar-milk-masala')),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), (SELECT id FROM products WHERE slug = 'damask-rose'));

INSERT INTO product_reviews (product_id, reviewer_name, rating, headline, review_text)
VALUES
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Chef Nalini Rao', 5, 'Transforms every biryani', 'The depth and balance in Amber Garam make it my go-to finishing spice. It blooms instantly and the aroma lingers beautifully.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Arjun Mehta', 4, 'Perfect for slow roasts', 'Used this on a slow roasted cauliflower and it brought incredible warmth without overpowering the dish.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Priya Sharma', 5, 'Authentic flavor profile', 'This garam masala reminds me of my grandmother\'s kitchen. The saffron notes are subtle but present, and it works beautifully in both vegetarian and non-vegetarian dishes. Highly recommend!'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Rajesh Kumar', 4, 'Great for everyday cooking', 'I use this in my daily dal and sabzi preparations. The quality is excellent and the packaging keeps the spices fresh. Good value for money.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Sarah Chen', 5, 'Restaurant quality at home', 'As a home cook, I\'ve been trying to recreate restaurant-style biryanis. This spice blend made all the difference! The mace and long pepper add such complexity.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Michael Thompson', 4, 'Well balanced blend', 'The heat level is perfect - not too mild, not too spicy. I\'ve used it in curries, marinades, and even sprinkled on roasted vegetables. Very versatile.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Anita Desai', 5, 'Premium quality spices', 'You can tell these are high-quality, freshly ground spices. The aroma when you open the container is incredible. Worth every penny.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'David Wilson', 4, 'Excellent for special occasions', 'I bought this for a dinner party and everyone asked where I got the spices from. The flavor profile is sophisticated and the presentation is beautiful.'),
  ((SELECT id FROM products WHERE slug = 'charred-harissa'), 'Layla Hassan', 5, 'Complex and smoky', 'I love the layers of smoke and citrus. It elevates vegetable tagines and even simple hummus bowls.'),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), 'Mira Kapoor', 5, 'Vibrant color and aroma', 'The saffron threads are incredibly fresh. A few strands turned my desserts golden and perfumed.'),
  ((SELECT id FROM products WHERE slug = 'saffron-reserve'), 'Julien Laurent', 4, 'Elegant plating accent', 'Beautiful for finishing sauces and plating. The glass vial presentation is a lovely touch for gifting.');

