(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {
      $('div.openlayers-map', context).once('myCustomBehavior').each(function () {
        console.log($(this).attr('data-lon'));
        const iconFeature = new ol.Feature({
          geometry: new ol.geom.Point(ol.proj.fromLonLat([$(this).attr('data-lon'), $(this).attr('data-lat')])),
          name: 'Somewhere near Nottingham',
        });
        const map = new ol.Map({
          target: $(this).get(0),
          layers: [
            new ol.layer.Tile({
              source: new ol.source.OSM(),
            }),
            new ol.layer.Vector({
              source: new ol.source.Vector({
                features: [iconFeature]
              }),
              style: new ol.style.Style({
                image: new ol.style.Icon({
                  anchor: [0.5, 46],
                  anchorXUnits: 'fraction',
                  anchorYUnits: 'pixels',
                  src: 'https://openlayers.org/en/latest/examples/data/icon.png'
                })
              })
            })
          ],
          view: new ol.View({
            center: ol.proj.fromLonLat([$(this).attr('data-lon'), $(this).attr('data-lat')]),
            zoom: 12
          })
        });
      });
    }
  };
})(jQuery, Drupal);
