var dataCacheName = 'pwa-gosarilestari-v1'
var cacheName = 'pwa-gosarilestari-v1'
var dataUrl = 'https://gosarilestari.com/'
var PATH = dataUrl
var filesToCache = [
   '/',
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png'
]

self.addEventListener('install', function(e) {
   e.waitUntil(caches.open(cacheName).then(function(cache) {
      return cache.addAll(filesToCache)
   }))
})

self.addEventListener('activate', function(e) {
   e.waitUntil(caches.keys().then(function(keyList) {
      return Promise.all(keyList.map(function(key) {
         if (key !== cacheName) {
            return caches.delete(key)
         }
      }))
   }))
})

self.addEventListener('fetch', function(e) {
   if (e.request.url.indexOf(dataUrl) === 0) {
      e.respondWith(fetch(e.request).then(function(response) {
         return caches.open(dataCacheName).then(function(cache) {
            cache.put(e.request.url, response.clone())
            return response;
         })
      }))
   } else {
      e.respondWith(caches.match(e.request).then(function(response) {
         return response || fetch(e.request)
      }))
   }
})