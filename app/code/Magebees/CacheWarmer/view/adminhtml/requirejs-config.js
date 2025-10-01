var config = {
   
     paths: {
        'amcharts': 'Magebees_CacheWarmer/js/amcharts',   
        'amcharts.pie': 'Magebees_CacheWarmer/js/amchartspie',    
        'amcharts.xy': 'Magebees_CacheWarmer/js/amchartsxy',
        'amcharts.serial': 'Magebees_CacheWarmer/js/amchartsserial',
    },
     shim: {
       'amcharts': {
        exports: 'AmCharts'
    },
    'amcharts.pie': {
      deps: ['amcharts'],
      exports: 'AmChartsPie',
      init: function() {
        AmCharts.isReady = true;
      }
    },
     'amcharts.serial': {
      deps: ['amcharts'],
      exports: 'AmChartsSerial',
      init: function() {
        AmCharts.isReady = true;
      }
    },
    'amcharts.xy': {
      deps: ['amcharts'],
      exports: 'AmChartsXY',
      init: function() {
        AmCharts.isReady = true;
      }
    },}
   
};
 