self.addEventListener("install", e => {
  e.waitUntil(
    caches.open("persuasion-cache").then(cache => {
      return cache.addAll([
        "./",
        "./index.html",
        "./manifest.json",
        "https://cdn.tailwindcss.com",
        "https://i.imgur.com/ZrA1cA0.png"
      ]);
    })
  );
});

self.addEventListener("fetch", e => {
  e.respondWith(
    caches.match(e.request).then(response => {
      return response || fetch(e.request);
    })
  );
});