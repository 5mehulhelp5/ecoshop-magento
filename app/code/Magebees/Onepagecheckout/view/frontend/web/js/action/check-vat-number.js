define(
    [
        'ko',
        'jquery',
        'mage/storage',
        'mage/translate',
    ],
    function (
        ko,
        $,
        storage,
        $t
    ) {
        'use strict';
        return function (vatnumber) {
            
            if($.trim(vatnumber) == ""){
                $("#vatErr").removeClass("error-msg");
                $("#vatErr").html("");
                return;
            }

            return storage.post(
                'onepage/index/checkvatnumber',
                JSON.stringify(vatnumber),
                false
            ).done(
                function (response) {
                    if (response) {
                        $("#vatErr").addClass("error-msg");
						$("#vatErr").html(response);
                    }
                }
            ).fail(
                function (response) {
                    $("#vatErr").addClass("error-msg");
					$("#vatErr").html(response);
                }
            );
        };
    }
);