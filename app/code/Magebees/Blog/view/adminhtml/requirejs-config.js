var config = {
    shim: {
	'importblog' : {
		deps: ['jquery','mage/template','jquery-ui-modules/core',
	'jquery-ui-modules/widget','mage/translate','Magento_Ui/js/modal/modal']
	}
		 
		
  },
     map: {
       '*': {
            'importblog': 'Magebees_Blog/js/importblog'
       }
    },
    paths: {
    },
};

