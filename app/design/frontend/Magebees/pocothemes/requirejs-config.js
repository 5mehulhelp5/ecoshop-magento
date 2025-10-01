var config = {
    config: {
        mixins: {
            'mage/collapsible': {
                'js/mage/collapsible-mixin': true
            }
        }
    },
    map: {
       "*": {
           "pocotheme" : "js/pocotheme",
		   "productnotification" : "js/productnotification",
		   "swiperinit" : "js/swiperinit",
           "parallax" : "js/parallax",
		   "ajaxloading" : "js/ajaxloading"
		}
    },
    
	paths: {
         "magebees.magnific.min" : "js/magnific-popup.min",
		 "magebees.swiper" : "js/swiper-bundle.min",
		 "magebees.wow" : "js/wow.min"
		 
	},
	
	shim: {
		'magebees.swiper': {
            deps: ['jquery']
        },
		'magebees.wow': {
            deps: ['jquery']
        },
		'pocotheme': {
            deps: ['jquery']
        },
		'swiperinit': {
            deps: ['jquery']
        },
        'parallax': {
            deps: ['jquery']
        },
        'ajaxloading': {
            deps: ['jquery']
        }
        
    }
};