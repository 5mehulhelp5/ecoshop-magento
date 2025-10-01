var config = {
    paths: {
			'jquerytouch' : 'Magebees_Onepagecheckout/js/jquery.ui.touch-punch.min'
	},
    shim: {
        'jquerytouch': {
            deps: ['jquery','jquery/ui']
        },
    },
    map: {
        '*': {
            mbopcFieldArray : 'Magebees_Onepagecheckout/js/field-array'
        }
    }	
};