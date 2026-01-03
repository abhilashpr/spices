-- Add more reviews to products
-- Run this SQL file to add 6 more reviews to amber-garam product

USE saffron_spice;

INSERT INTO product_reviews (product_id, reviewer_name, rating, headline, review_text)
VALUES
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Priya Sharma', 5, 'Authentic flavor profile', 'This garam masala reminds me of my grandmother\'s kitchen. The saffron notes are subtle but present, and it works beautifully in both vegetarian and non-vegetarian dishes. Highly recommend!'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Rajesh Kumar', 4, 'Great for everyday cooking', 'I use this in my daily dal and sabzi preparations. The quality is excellent and the packaging keeps the spices fresh. Good value for money.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Sarah Chen', 5, 'Restaurant quality at home', 'As a home cook, I\'ve been trying to recreate restaurant-style biryanis. This spice blend made all the difference! The mace and long pepper add such complexity.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Michael Thompson', 4, 'Well balanced blend', 'The heat level is perfect - not too mild, not too spicy. I\'ve used it in curries, marinades, and even sprinkled on roasted vegetables. Very versatile.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'Anita Desai', 5, 'Premium quality spices', 'You can tell these are high-quality, freshly ground spices. The aroma when you open the container is incredible. Worth every penny.'),
  ((SELECT id FROM products WHERE slug = 'amber-garam'), 'David Wilson', 4, 'Excellent for special occasions', 'I bought this for a dinner party and everyone asked where I got the spices from. The flavor profile is sophisticated and the presentation is beautiful.');

