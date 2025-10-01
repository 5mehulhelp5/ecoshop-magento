var config = {
    paths: {
        "magebeesdealswiper" : "Magebees_Productlisting/js/swiper-bundle.min",
        'jquery_plugin': 'Magebees_TodayDealProducts/js/jquery.plugin',
        'magebees.countdown': 'Magebees_TodayDealProducts/js/jquery.countdown'
    },
    shim: {
        
        'jquery_plugin': {
            deps: ['jquery']
        },
        'magebees.countdown': {
            deps: ['jquery', 'jquery_plugin']
        }
        
    }
};
