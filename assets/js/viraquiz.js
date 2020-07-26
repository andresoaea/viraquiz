(function( $ ) {

    "use strict";

	$(function() {

        /* Make 100% width all parents of .viraquiz-container div */
        $(".viraquiz-container").parentsUntil("body").each(function() {

            if( $(this).css("float") == 'none' ) {
                $(this).addClass("viraquiz viraquiz-full-width");            
            }

        });


        /* Make main quiz container full width if the sidebar doesn't have any element */
        if ( ! $(".viraquiz-sidebar").children().length > 0 ) {
            $(".viraquiz-main-column").css("width", "100%");
        } 


	
        /* Show preloader when "Continue with facebook" button is clicked */
        $("body").on("click", "#viraquiz-fb-login", function(e) {

            /* Stop generate quiz if Facebook App ID and App Secret are not set */
            if( VIRAQUIZ_fb_app.status == 'is-not-set' ) {

                e.preventDefault();
                alert("Facebook App is not properly configured. If you are admin, please config the Facebook App on the Viraquiz plugin settings section.");
                
                return;

            }

            /* Show preloader */
            $(".viraquiz-preloader").fadeIn("slow");

        });


        /* Hide preloader when result image is fully loaded */
        $(".viraquiz-result-image").on("load", function() {

            var $that = $(this);

            setTimeout(function() {

                $(".viraquiz-result-preloader").hide();
                $that.fadeIn("slow");

                setTimeout(function() {
                    $(".viraquiz-result-actions").fadeIn("slow");
                },300);

            },1000);

        });


        /* Auto scroll to quiz result */
        if( $(".viraquiz-result-preloader").length > 0 ) {

            $('html,body').stop().animate({
             scrollTop: $(".quiz-content").offset().top - 70 
            }, 'slow');

        }


        /* Ajax cont quiz Facebook shares */
		$("#vrq-share-on-facebook").on("click", function() {

			$.ajax({

                url: VIRAQUIZ_Vars.ajax.url,
                type: 'post',
                data: {

                    nonce: VIRAQUIZ_Vars.ajax.nonce,
                    post_id: VIRAQUIZ_Vars.post_id,
                    user_data: VIRAQUIZ_Vars.user_data,
                    action: 'viraquiz_count_shares'

                },
				success: function ( response ) {
                    if( response.status == 'success' ) {
                    	console.log( "Share counted successfully" );
                    } else {
                    	console.log( "Error. Share was not counted." );
                    }
                },
                error: function () {
                    console.log( "Error on ajax request... Share was not counted." );
                }


            });

		});



        /* Ajax pagination */
        $(document).on("click", ".vrq-load-more-quizzes-btn:not(.vrq-loading)", function(e) {
            e.preventDefault();    

            var $that = $(this);
            var page = $that.data('page');
            var exclude = $that.attr("exclude-quiz");
            var newPage = page + 1;
            
            $that.addClass('vrq-loading').find('.dashicons-update').addClass('vrq-spin');

            var $load_more_text = $that.find(".vrq-load-more-text");
            var load_more_original_text = $load_more_text.text();

            $load_more_text.text(VIRAQUIZ_ajax_pagination.strings.loading_more);

            $.ajax({
                
                url : VIRAQUIZ_ajax_pagination.url,
                type : 'post',
                data : {
                    
                    page : page,
                    nonce : VIRAQUIZ_ajax_pagination.nonce,
                    exclude : exclude,
                    action: 'viraquiz_load_more'
                    
                },
                error : function( response ) {
                   console.log( "Error on ajax request... No more quizzes loaded" );
                },
                success : function( response ) {
                    
                   if( response.status == 'error' ) {

                        console.log( "Error. No more quizzes loaded." );
                        return;

                   }

                    if( response.quizzes == 0 ) {

                        $('.vrq-page-limit:last').after( '<div class="vrq-text-center"><h3>' + VIRAQUIZ_ajax_pagination.strings.no_more_quizzes + '</h3></div>' );
                        $that.slideUp(320);
                        
                    } else {
                        
                        setTimeout(function() {

                                $('.vrq-page-limit:last').after( response.quizzes );
                            
                            if( newPage == 1 ) {
                                
                                $that.slideUp(320);
                                
                            } else {
                                
                               $that.data('page', newPage);
                               $that.removeClass('vrq-loading').find('.dashicons-update').removeClass('vrq-spin');
                               $load_more_text.text( load_more_original_text );
                                
                            }
                            

                            var time = 300;

                            $(".vrq-ajax-loaded-quizzes:last").find(".vrq-single-quiz").each(function() {
                                var $this = $(this);
                                setTimeout(function() {
                                    $this.slideDown(320);
                                }, time);

                                time += 300;
                            });

                            
                        }, 500);
                        
                    }
                    
                    
                }
                
            });
            
        });


	});


} )( jQuery )