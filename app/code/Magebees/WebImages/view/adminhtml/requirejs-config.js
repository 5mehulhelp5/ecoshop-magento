/**
 * Copyright © 2021 Magebees. All rights reserved.
 * See LICENSE.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/element/image-uploader': {
                'Magebees_WebImages/js/form/element/image-uploader-mixin': true
            }
			
        }
    },
	map: {
        '*': {
            'Magento_Backend/js/media-uploader': 'Magebees_WebImages/js/media-uploader'
        }
    }
};