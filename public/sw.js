const CACHE_NAME = 'sela-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/assets/css/style.css?v=2.5',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css',
  'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css',
  'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;600;700&display=swap'
];

// Install Event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Service Worker: Caching offline assets');
      return cache.addAll(ASSETS_TO_CACHE).catch(err => {
        console.warn('Cache addAll failed. Some assets might be offline/unavailable: ', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// Activate Event
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('Service Worker: Clearing old cache');
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event (Network First, Cache Fallback for HTML/Pages; Cache First for static styles/assets)
self.addEventListener('fetch', event => {
  // Only cache GET requests
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // If it's a static assets request (like style sheets, fonts, icons), use Cache-First, fallback to Network
  if (
    url.pathname.includes('/assets/') ||
    url.host.includes('cdn.jsdelivr.net') ||
    url.host.includes('unpkg.com') ||
    url.host.includes('fonts.googleapis.com') ||
    url.host.includes('fonts.gstatic.com')
  ) {
    event.respondWith(
      caches.match(event.request).then(cachedResponse => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(event.request).then(networkResponse => {
          if (networkResponse.status === 200) {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseToCache);
            });
          }
          return networkResponse;
        }).catch(() => {
          // Fallback if offline
        });
      })
    );
  } else {
    // For standard HTML pages and dynamic routes, use Network-First, fallback to Cache
    event.respondWith(
      fetch(event.request)
        .then(networkResponse => {
          if (networkResponse.status === 200 && networkResponse.type === 'basic') {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseToCache);
            });
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(event.request).then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            // Offline fallback could be defined here
          });
        })
    );
  }
});
