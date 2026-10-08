self.addEventListener("install", function(event) {
  event.waitUntil(initialCache());
});

var initialCache = function() {
  return caches.open("sw-cache").then(function(cache) {
    return cache.addAll(["inizio.php", "sw_offline.html"]);
  });
};

self.addEventListener("fetch", function(event) {
  event.respondWith(checkOffline(event.request).catch(function() {
    return recoverFromCache(event.request);
  }));
  event.waitUntil(updateCache(event.request));
});

var checkOffline = function(request) {
  return new Promise(function(fulfill, reject) {
    fetch(request).then(function(response) {
      if(response.status !== 404) {
        fulfill(response);
      } else {
        reject();
      }
    }, reject);
  });
};

var updateCache = function(request) {
  return caches.open("sw-cache").then(function (cache) {
    return fetch(request).then(function (response) {
      return cache.put(request, response);
    });
  });
};

var recoverFromCache = function(request) {
  return caches.open("sw-cache").then(function (cache) {
    return cache.match(request).then(function (matching) {
     if(!matching || matching.status == 404) {
       return cache.match("sw_offline.html");
     } else {
       return matching;
     }
    });
  });
};