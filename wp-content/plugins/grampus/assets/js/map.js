jQuery(function($) {
  var Map,
    indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  Map = function(container, data) {
    this.params = data;
    this.map = null;
    this.container = $(container).get(0);
    if (!(indexOf.call(window, 'ymaps_loading') >= 0)) {
      window.ymaps_loading = false;
    }
    if (!(indexOf.call(window, 'google_loading') >= 0)) {
      window.google_loading = false;
    }
    this.init();
    return this;
  }

  Map.prototype.init = function() {
    if(this.params.iframe)
    {
      this.initByIframe();
    }
    else
    {
      switch (this.params.type) {
        case 'yandex':
          this.initYandex();
          break;
        case 'google':
          this.initGoogle();
          break;
      }
    }
    return this;
  };

  Map.prototype.inited = function() {
    var func, key, ref, results;
    if ('MapInited' in window) {
      ref = window.MapInited;
      results = [];
      for (key in ref) {
        func = ref[key];
        results.push(func(this));
      }
      return results;
    }
  };

  Map.prototype._loadScript = function(url, params, callback) {
    var exists, key, script, scriptsProperties, target;
    if (callback == null) {
      callback = false;
    }
    exists = $('script[src="' + url + '"]');
    params = params || {};
    if (exists.length === 0) {
      script = document.createElement('script');
      script.type = 'text/javascript';
      if (script.readyState) {
        script.onreadystatechange = function() {
          if (script.readyState === 'loaded' || script.readyState === 'complete') {
            script.onreadystatechange = null;
            if (callback) {
              return callback();
            } else {
              return true;
            }
          }
        };
      } else {
        script.onload = function() {
          if (callback) {
            return callback();
          } else {
            return true;
          }
        };
      }
      scriptsProperties = ['type', 'src', 'htmlFor', 'event', 'charset', 'async', 'defer', 'crossOrigin', 'text', 'onerror'];
      if (typeof params === 'object' && !$.isEmptyObject(params)) {
        for (key in params) {
          if (params.hasOwnProperty(key) && $.inArray(key, scriptsProperties)) {
            script[key] = params[key];
          }
        }
      }
      script.src = url;
      target = params['lazyLoad'] ? 'body' : 'head';
      return document.getElementsByTagName(target)[0].appendChild(script);
    } else {
      if (callback) {
        return callback();
      } else {
        return true;
      }
    }
  };

  Map.prototype.loadScript = function() {
    var M;
    M = this;
    switch (this.params.type) {
      case 'yandex':
        if (window.ymaps_loading === false) {
          window.ymaps_loading = true;
          setTimeout(function() {
            if('yandex_api_key' in window)
            {
              return M._loadScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=' + yandex_api_key);
            }
            else
            {
              return M._loadScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
            }
          }, 2500);
        }
        break;
      case 'google':
        if (window.google_loading === false) {
          window.google_loading = true;
          setTimeout(function() {
            return M._loadScript('https://maps.googleapis.com/maps/api/js?key=' + google_api_key);
          }, 2500);
        }
        break;
    }
  };

  Map.prototype.initByIframe = function() {
    var M;
    M = this;
    return setTimeout(function() {
      return M.initIframe();
    }, 3000);
  };

  Map.prototype.initIframe = function() {
    this.container.innerHTML = $('<textarea />').html(this.params.iframe).text();
    this.inited();
  };

  Map.prototype.initYandex = function() {
    var M;
    M = this;
    if (typeof ymaps !== 'undefined') {
      return ymaps.ready(this.buildYandex, this);
    } else {
      this.loadScript();
      return setTimeout(function() {
        return M.initYandex();
      }, 100);
    }
  };

  Map.prototype.initGoogle = function() {
    var M;
    M = this;
    if (typeof google !== 'undefined') {
      return this.buildGoogle();
    } else {
      this.loadScript();
      return setTimeout(function() {
        return M.initGoogle();
      }, 100);
    }
  };

  Map.prototype.buildYandex = function() {
    var M, az, center, mapOptions, mapState, zoom;
    M = this;
    az = false;
    if (M.params.zoom === 'auto') {
      az = true;
      zoom = 16;
    } else {
      zoom = parseFloat(M.params.zoom);
    }
    if (M.params.center) {
      center = M.params.center.split(/\s*,\s*/);
      center = [parseFloat(center[0]), parseFloat(center[1])];
    } else {
      center = false;
    }
    mapState = {
      zoom: zoom,
      center: center,
      controls: []
    };
    mapOptions = {
      suppressMapOpenBlock: true,
      yandexMapDisablePoiInteractivity: true
    };
    if (!'balloon' in M.params) {
      M.params.balloon = true;
    }
    M.map = new ymaps.Map(M.container, mapState, mapOptions);
    M.map.behaviors.disable('scrollZoom');
    M.map.controls.add('zoomControl');
    M.parseMarkersYandex();
    if (az) {
      M.map.setBounds(M.map.geoObjects.getBounds());
    }
    this.inited();
  };

  Map.prototype.buildGoogle = function() {
    var M, center, mapOptions;
    M = this;
    center = M.params.center.split(/\s*,\s*/);
    center = {
      'lat': parseFloat(center[0]),
      'lng': parseFloat(center[1])
    };
    mapOptions = {
      zoom: parseFloat(M.params.zoom),
      center: center,
      disableDefaultUI: true,
      scrollwheel: false,
      zoomControl: true,
      scaleControl: false,
      mapTypeId: 'roadmap'
    };
    M.map = new google.maps.Map(M.container, mapOptions);
    M.parseMarkersGoogle();
    this.inited();
  };

  Map.prototype.parseMarkersYandex = function() {
    var M, index, mark, ref, results;
    M = this;
    ref = M.params.markers;
    results = [];
    for (index in ref) {
      mark = ref[index];
      if (mark.type === 'Point') {
        M.appendYandexMarker(mark);
      }
      if (mark.type === 'Circle') {
        results.push(M.appendYandexCircle(mark));
      } else {
        results.push(void 0);
      }
    }
    return results;
  };

  Map.prototype.parseMarkersGoogle = function() {
    var M, index, mark, ref, results;
    M = this;
    ref = M.params.markers;
    results = [];
    for (index in ref) {
      mark = ref[index];
      if (mark.type === 'Point') {
        M.appendGoogleMarker(mark);
      }
      if (mark.type === 'Circle') {
        results.push(M.appendGoogleCircle(mark));
      } else {
        results.push(void 0);
      }
    }
    return results;
  };

  Map.prototype.appendYandexMarker = function(mark) {
    var M, marker;
    M = this;
    if (M.params.icon) {
      marker = new ymaps.Placemark(mark.coords, {
        iconCaption: mark.name,
        iconContent: mark.name,
        balloonContent: '<b>'+mark.name + '</b><br>' + mark.content
      }, {
        hasBalloon: M.params.balloon,
        iconLayout: 'default#image',
        iconImageHref: M.params.icon.url,
        iconImageSize: [M.params.icon.width, M.params.icon.height],
        iconImageOffset: [-M.params.icon.w_offset, -M.params.icon.h_offset]
      });
    } else {
      marker = new ymaps.Placemark(mark.coords, {
        iconCaption: mark.name,
        iconContent: mark.name,
        balloonContent: '<b>'+mark.name + '</b><br>' + mark.content
      }, {
        hasBalloon: M.params.balloon,
        preset: 'islands#blueStretchyIcon'
      });
    }
    return M.map.geoObjects.add(marker);
  };

  Map.prototype.appendGoogleMarker = function(mark) {
    var M, infoWindow, marker;
    M = this;
    marker = new google.maps.Marker({
      map: M.map,
      position: {
        'lat': parseFloat(mark.coords[0]),
        'lng': parseFloat(mark.coords[1])
      },
      title: M.params.title,
      icon: M.params.icon
    });
    if (mark.content !== '') {
      infoWindow = new google.maps.InfoWindow({
        content: mark.content
      });
      return marker.addListener('click', function() {
        return infoWindow.open(M.map, marker);
      });
    }
  };

  Map.prototype.appendGoogleCircle = function(mark) {
    var M, circle, infoWindow;
    M = this;
    circle = new google.maps.Circle({
      map: M.map,
      center: {
        'lat': parseFloat(mark.coords[0]),
        'lng': parseFloat(mark.coords[1])
      },
      radius: mark.circle_size,
      strokeColor: '#de3b3c',
      strokeWeight: 2,
      strokeOpacity: 0.8,
      fillColor: '#de3b3c',
      fillOpacity: 0.35
    });
    if (mark.content !== '') {
      infoWindow = new google.maps.InfoWindow({
        content: mark.content
      });
      return circle.addListener('click', function() {
        return infoWindow.open(M.map, circle);
      });
    }
  };

  Map.prototype.appendYandexCircle = function(mark) {
    var M, circle;
    M = this;
    circle = new ymaps.Circle([mark.coords, mark.circle_size], {
      balloonContent: '<b>'+mark.name + '</b><br>' + mark.content
    }, {
      hasBalloon: M.params.balloon,
      draggable: false,
      fillColor: "#de3b3c59",
      strokeColor: "#de3b3c",
      strokeOpacity: 0.8,
      strokeWidth: 2
    });
    return M.map.geoObjects.add(circle);
  };

  $(document).ready(function() {
    var container, data, ref, results;
    window.jsmaps = {};
    if (window.MapData) {
      ref = window.MapData;
      results = [];
      for (container in ref) {
        data = ref[container];
        results.push(window.jsmaps[container] = new Map(container, data));
      }
      return results;
    }
  });

});