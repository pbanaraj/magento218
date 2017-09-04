require(['jquery'],function($){

    // $(document).ready(function(){

    //    alert('ok');

    // });

    $(document).scroll(function() {
	    if( $(this).scrollTop() >= 100 ) {
	        $('#pin2').css({top:'0px',position:'fixed'});
	    } else {
                var px3=parseInt(97-$(this).scrollTop())
	        $('#pin2').css({top:+px3+'px',position:'fixed'});
	    }
	})

});