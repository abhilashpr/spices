<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'Wynvalley | Premium Spice Boutique'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>" />
  </head>
  <body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main>
      <?= $content ?? '' ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <script>
      // Sticky Navigation on Scroll
      (function() {
        const navBar = document.querySelector('.nav-bar');
        if (!navBar) return;
        
        let lastScrollTop = 0;
        const scrollThreshold = 100; // Start sticky after 100px scroll
        
        function handleScroll() {
          const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          
          if (scrollTop > scrollThreshold) {
            // Add sticky class
            if (!navBar.classList.contains('sticky')) {
              navBar.classList.add('sticky');
              document.body.classList.add('nav-sticky');
            }
          } else {
            // Remove sticky class
            if (navBar.classList.contains('sticky')) {
              navBar.classList.remove('sticky');
              document.body.classList.remove('nav-sticky');
            }
          }
          
          lastScrollTop = scrollTop;
        }
        
        // Throttle scroll events for better performance
        let ticking = false;
        window.addEventListener('scroll', function() {
          if (!ticking) {
            window.requestAnimationFrame(function() {
              handleScroll();
              ticking = false;
            });
            ticking = true;
          }
        }, { passive: true });
        
        // Check on page load
        handleScroll();
      })();
    </script>
  </body>
</html>
