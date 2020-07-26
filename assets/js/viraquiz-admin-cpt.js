( function( $ ) {
	
	"use strict";

	$(function(){

		var $body = $("body");
		var $editor = $(".quiz-editor");
		var i = $editor.find("div").length + 1;

		/* Add preloader to result images */
		function addResultImagePreloader( $results ) {

			if( ! $results ) {
				$results = $(".quiz-results").find(".quiz-results-item");
			}

			$results.each(function() {

				var $that = $(this);
				var result_image = $that.find("img:first");

				$that.find(".result-item-body").prepend('<div class="vrq-result-preloader"><p class="description">Loading resut image...</p><img src="' + VIRAQUIZ_vars.site_url + '/wp-admin/images/wpspin_light.gif"></div>');

				result_image.on("load", function() {
					$that.find(".vrq-result-preloader").hide();
				});

			});

		}

		addResultImagePreloader();

		
		/* Add user profile picture to quiz editor */
		$body.on("click", '.add-user-profile', function() {
			addUserProfile();
			dragInit();
			refreshLayersList();
			i++;
		});


		/* Add text to quiz editor */
		$body.on("click", '.add-text', function() {
			addText();
			dragInit();
			refreshLayersList();
			i++;
		});



		/* Add user profile callback */
		function addUserProfile() {
			var profile_pic = '<div class="profile-pic" data-layer="' + i + '" style="z-index: ' + i + ';"><img src="' + VIRAQUIZ_vars.assets_url + '/images/profile-picture.png"></div>';
			$editor.append(profile_pic);

		}	


		/* Add text callback */
		function addText() {
			var text = '<div class="quiz-text" data-layer="' + i + '" style="z-index: ' + i + ';"><textarea id="quiz-result-textarea-' + i + '" data-role="none" spellcheck="false" font-family-value="0">New text</textarea></div>';

			$editor.append(text);

			var $textarea = $("[data-layer=\"" + i + "\"]").find("textarea");
			var textarea_value = $textarea.val();
			$textarea.focus().val('').val(textarea_value);

			refreshTextLayerEditor(i);

		}

		/* Init jQuery draggable & resizable */
		function dragInit(){
			$(".quiz-editor > div").each(function() {

				var $that = $(this);

				//Add helpers for resize
				if( $that.find(".resize-corner-helper").length == 0 ) {
					for(var i = 1; i < 9; i++) {
						$that.append("<div class=\"resize-corner-helper hlp" + i + "\"></div>");
					}
				}


				
				if( $that.hasClass("quiz-text") ) {

					// Focus textarea on click mouseover mouseenter
					$that.on("click mouseover mouseenter", function() {
						$that.find("textarea").focus();
					});

					//Set textarea paret width and height
					var $textarea = $that.find("textarea");
					var text_length = $textarea.val().length;
					$textarea.attr("rows", 1).attr("cols", text_length);

					$that.css({
						height: $textarea.height()
					});
					$textarea.removeAttr("rows").removeAttr("cols");

				}

				
					$that.resizable({
						containment: "parent",
						handles: 'n, e, s, w, ne, se, sw, nw',
						minHeight: ( ! $that.hasClass("quiz-text") ? 50 : 30 ),
						minWidth: ( ! $that.hasClass("quiz-text") ? 50 : 100 ),
						cancel: "text",
						stop: function(e, ui) {
							var parent = ui.element.parent();
							ui.element.css({
								width: ui.element.width() / parent.width() * 100 + "%",
								height: ui.element.height() / parent.height() * 100 + "%"
							});

						}

					});

				    $that.on('resize', function (e) {
				        e.stopPropagation();
				    });

				if( ! $that.hasClass("ui-draggable") ) {

				$that.draggable({
					containment: "parent",
					cancel: "text",
				     stop: function( e, ui ) {
				     	
				        var $elm = $(this);
				        var $textarea =  $elm.find("textarea");
				        var textarea_value = $textarea.val();
				       	$textarea.focus().val('').val(textarea_value);

				        var pos = $elm.position();
				        var parent_sizes = {
				                height: $elm.parent().height(),
				                width: $elm.parent().width()
				            };
				        var elm_sizes = {
				        	height: $elm.height(),
				        	width: $elm.width()
				        };    

						$elm.css({
							"top"    : ((pos.top/parent_sizes.height) * 100) + "%",
							"left"   : ((pos.left/parent_sizes.width) * 100) + "%",
							"width"  : ((elm_sizes.width/parent_sizes.width) * 100) + "%",
							"height"  : ((elm_sizes.height/parent_sizes.height) * 100) + "%",
						});

					}

				});

				}

			});
		}



		/* Media Uploader */

		var mediaUploader;
		
		$body.on("click", ".add-image", function(e) {
			e.preventDefault();
			if( mediaUploader ){
				mediaUploader.open();
				return;
			}
			
			mediaUploader = wp.media.frames.file_frame = wp.media({
				title: 'Upload an image to quiz result',
				button: {
					text: 'Select image'
				},
				multiple: false
			});
			
			mediaUploader.on('select', function(){

				var attachment = mediaUploader.state().get('selection').first().toJSON();
				var new_image  = '<div class="quiz-image" data-layer="' + i + '" style="z-index: ' + i + ';"><img src="' + attachment.url + '" data-image_id="' + attachment.id + '"></div>';
				$editor.append(new_image);
				dragInit();
				refreshLayersList();
				i++;

			});
			
			mediaUploader.open();
			
		});



		/* Layers */
		/* Show layers list */
		$body.on("click", ".layers-btn", function() {
			showLayersList();
		});


		function showLayersList( type ) {

			if( $(".result-layers").length > 0 ) {
				return;
			}


			var layers_list = '';
			var $editor_divs = $(".quiz-editor > div");
			var total_layers = $editor_divs.length;
			var hidden_list = ( type == 'get-layers' ? 'hidden-result-layers' : '' );


			$editor_divs.each(function() {
				var $that = $(this);
				var delete_nr = $that.data("layer");

				if( $that.hasClass("profile-pic") ) {
					layers_list += '<li data-layer_indicator="' + delete_nr + '">Profile Picture <span class="dashicons dashicons-trash delete-layer" data-layerdel="' + delete_nr + '"></span></li>';
				}

				if( $that.hasClass("quiz-text") ) {

					var text = $that.find("textarea").val().replace(/(<([^>]+)>)/ig,"").split(' ').slice(0,3).join(' ') + '...';

					layers_list += '<li data-layer_indicator="' + delete_nr + '">' + text + ' <span class="dashicons dashicons-trash delete-layer" data-layerdel="' + delete_nr + '"></span></li>';

				}

				if( $that.hasClass("quiz-image") ) {
					layers_list += '<li data-layer_indicator="' + delete_nr + '">Media Image <span class="dashicons dashicons-trash delete-layer" data-layerdel="' + delete_nr + '"></span></li>';
				}
				
			});

			var layers = '<div class="result-layers ' + hidden_list + '"><div class="layers-header"><span>Layers (<span class="layers-number">' + total_layers + '</span>)</span><span class="dashicons dashicons-dismiss result-layers-remove"></span><hr/></div><ul>' + layers_list + '</ul></div>';

			$body.append(layers);

			$(".result-layers").draggable({
				containment: ".wrap",
				stop: function() {
					$(this).css("height", "auto");
				}
			});


			indicateLayer();
			makeLayersListSortable();

		}


		/* Sort layers list by z-index property */
		function sortByIndex(a, b) {

			var layer_indicator_a = $(a).data("layer_indicator");
			var index_a = $(".quiz-editor").find("[data-layer=\"" + layer_indicator_a + "\"]").css("z-index");

			var layer_indicator_b = $(b).data("layer_indicator");
			var index_b = $(".quiz-editor").find("[data-layer=\"" + layer_indicator_b + "\"]").css("z-index");

	   		return ( ( parseInt( index_a ) < parseInt( index_b ) ) ? 1 : -1 );

		}

		/* Refresh layers list after adding a new layer */
		function refreshLayersList() {

			var $result_layers_list = $(".result-layers");
			
			if( $result_layers_list.length == 0 ) {
				return;
			}

			var result_layers_list_top = $result_layers_list.position().top;
			var result_layers_list_left = $result_layers_list.position().left;

			$result_layers_list.remove();
			showLayersList();

			$(".result-layers").css({
				top : result_layers_list_top,
				left : result_layers_list_left
			});


		}

		/* Indicate layer in editor on "mouse enter" his corespondent in layers list */
		function indicateLayer() {

			$(".result-layers").find("li").each(function() {

				var $that = $(this);
				var curr_layer_indicator = $that.find("[data-layerdel]").data("layerdel");
				var curr_layer = $editor.find("[data-layer='" + curr_layer_indicator + "']");

				$that.on("mouseenter mouseleave", function() {
					curr_layer.toggleClass("indicate-layer");
				});

			});


		}


		/* Make layers list sortable, so we can set layers order */
		function makeLayersListSortable() {

			var $layers_list = $(".result-layers").find("ul");

			$layers_list.sortable({

		      placeholder: "ui-state-highlight",
		      stop: function() {

	            var $layers_list_items = $layers_list.find("li");
	            var layers_count = $layers_list_items.length;
	            var i = 0;

		      	$layers_list_items.each(function() {

		      		var index = layers_count - i;
		      		$(this).data("layer_index", index);
		      		i++;
		      	});

		      	setLayersOrder();

		      }

		    });

		    $(".result-layers ul li").sort(sortByIndex).appendTo($layers_list);

		}


		/* Set layers order by changing layer div z-index property value */
		function setLayersOrder() {

			$(".quiz-editor > div").each(function() {

				var curr_layer_number = $(this).data("layer");
				var layer_index = $(".result-layers").find("[data-layer_indicator=\"" + curr_layer_number + "\"]").data("layer_index");

				$(this).css("z-index", layer_index);
			});

		}



		/* Delete a layer */
		$body.on("click", ".delete-layer", function() {
			var $that = $(this);
			var layer_to_delete = $that.data("layerdel");
			$that.closest("li").remove();
			$editor.find("[data-layer='" + layer_to_delete + "']").remove();

			var $layers_number = $(".layers-number");
			var layers_number = parseInt ( $layers_number.text() ) - 1;
			$layers_number.text( layers_number );

		});
		
		
		/* Remove layers list */
		$body.on("click", ".result-layers-remove", function() {
			$(".result-layers").remove();
		});


		/* Text layer editor */
		function textLayerEditor( layer_number ) {

			if( $(".text-layer-editor").length > 0 ) {
				$(".text-layer-editor").remove();
			}

			var $text_layer = $("*[data-layer=\"" + layer_number + "\"]");
			var $text_layer_textarea = $text_layer.find("textarea");
			var text = $text_layer_textarea.val().replace(/(<([^>]+)>)/ig,"").split(' ').slice(0,5).join(' ') + '...';

			var text_editor_body = '<strong>Current text: &nbsp;&nbsp;&nbsp;</strong>' + text;
			text_editor_body += '<br/><br/><strong>Font size: </strong><div class="fontsize-slider"></div>';
			text_editor_body += '<br/><br/><strong><span class="editor-text-color">Color: </span></strong><input type="text" class="text-color-picker" value="' + rgba2hex( $text_layer_textarea.css("color") ) + '"/>';


			var fonts = VIRAQUIZ_vars.fonts;

			text_editor_body += '<br/><strong>Font family: </strong><select class="text-font-family" family-editor=' + layer_number + '>';

			for( var i = 0; i < fonts.length; i++ ) {
				var selected = ( ( $text_layer_textarea.attr("font-family-value") == i ) ? 'selected' : '' ); 
				text_editor_body += '<option value="' + i + '" ' + selected + '>' + fonts[i].name + '</option>';

			}

			text_editor_body += '</select>';
			text_editor_body += '<div class="vrq-text-center"><button class="delete-layer delete-text-layer-btn"  data-layerdel="' + layer_number + '"><span class="dashicons dashicons-trash"></span> Delete text layer</button></div>';
			text_editor_body += '<p class="vrq-info vrq-info-op description"><span class="dashicons dashicons-info"></span> <strong>Info! </strong> Text Shortcodes: <br /><strong>[firstname]</strong> - retrieves user first name <br /><strong>[lastname]</strong> - retrieves user last name <br /><strong>[fullname]</strong> - retrieves  user full name</p>';

			$("body").append("<div class=\"text-layer-editor\"><div class=\"text-layer-editor-header\"><span>Edit text layer</span> <span class=\"dashicons dashicons-dismiss text-layer-editor-remove\"></span><hr></div>" + text_editor_body + "</div>");


			$(".text-layer-editor").draggable({
				containment: ".wrap",
				stop: function() {
					$(this).css("height", "auto");
				}
			});


			$(".fontsize-slider").slider({
				min: 13,
				max: 90,
				value: parseInt( $text_layer_textarea.css("font-size") ),
				slide: function( e, ui ) {
					//console.log( ui.value );
					$text_layer_textarea.css("font-size", ui.value);
				}

			});


			$(".text-color-picker").wpColorPicker({
				//color: rgba2hex( $text_layer_textarea.css("color") ),
				//defaultColor: '#dd3333',
				mode: 'hsv',
				width: 200,
				change: function( e, ui ) {
					//console.log( ui.color.toString() );
					$text_layer_textarea.css("color", ui.color.toString());
				}
			});


			$("body").on("change", ".text-font-family", function() {

				if( layer_number != $(this).attr("family-editor") ) {
					return;
				}

				var family_val = parseInt( $(this).val() );
				var google_family = fonts[family_val].name.replace(/\s/g, '+');
				var google_family_id = fonts[family_val].name.replace(/\s/g, '-').toLowerCase();

				if( $("#" + google_family_id + "-css").length == 0 ) {
					$("#roboto-css").after('<link rel="stylesheet" id="' + google_family_id + '-css" href="https://fonts.googleapis.com/css?family=' + google_family + '">');
				}

				var new_font_family = "'" + fonts[family_val].name + "', " + fonts[family_val].family;

				$text_layer_textarea.css("font-family", new_font_family).attr("font-family-value", family_val);


			});



		}

		/* Refresh Text Layer Editor */
		function refreshTextLayerEditor( layer_number ) {

			var $text_editor = $(".text-layer-editor");
			
			if( $text_editor.length == 0 ) {
				textLayerEditor( layer_number );
				return;
			}

			var result_layers_list_top = $text_editor.position().top;
			var result_layers_list_left = $text_editor.position().left;

			$text_editor.remove();
			textLayerEditor( layer_number );

			$(".text-layer-editor").css({
				top : result_layers_list_top,
				left : result_layers_list_left
			});


		}



		/* Function to run when a text layer is deleted */
		$("body").on("click", ".delete-text-layer-btn", function() {
			$(".text-layer-editor").remove();
			refreshLayersList();
		});


		/* Open text editor when layer is clicked */
		$body.on("click", ".quiz-text", function() {
			refreshTextLayerEditor( $(this).data("layer") );
		});	


		/* Update layer text on text editor when the text on texarea is changed */
		$("body").on("keyup", "textarea", function() {
			var text_layer = $(this).closest(".quiz-text").data("layer");
			refreshTextLayerEditor( text_layer );
			refreshLayersList();

		});


		/* Remove text layer editor */
		$body.on("click", ".text-layer-editor-remove", function() {
			$(".text-layer-editor").remove();
		});



	    /* Set editor height according its width keeping 1200 / 630 ( FB Open Graph image) aspect ratio */
	    function setEditorHeight() {
	    	var $editor_wrapper = $(".quiz-editor-wrapper");
			var	editor_width = $editor_wrapper.width();
			var editor_height =  editor_width * 630 / 1200; 

			$editor_wrapper.css("height", editor_height + "px");
	    }
	    setEditorHeight();


	    /* Adapt editor height on window resize to keep aspect ratio */
	    $(window).on("resize" , function() {
	    	setEditorHeight();
	    });



		/* Save result */
		$body.on("click", ".save-result-button", function() {
			ajaxSaveResult();
		});

		function getLayersData(){

			var layers = [];
			var layers_list_opened = ( $(".result-layers").length != 0 );

			if( !layers_list_opened ) {
				showLayersList( 'get-layers' );
			}


			$(".result-layers").find("li").each(function() {

				var $that = $(this);
				var current_layer_indicator = $that.data("layer_indicator");
				$that = $(".quiz-editor").find("[data-layer=\"" + current_layer_indicator + "\"]");

				var current_layer = [];
				var layer_type = '';

				if( $that.hasClass("profile-pic") ){
					layer_type = 'profile';
				}

				if( $that.hasClass("quiz-image") ) {
					layer_type = 'media';
				}

				
				var layer_top = $that.position().top;
				var layer_left = $that.position().left;
				var layer_width = $that.width();
				var layer_height = $that.height();

				if( $that.hasClass("quiz-text") ) {

					layer_type = 'text';

					//text variables
					var $textarea = $that.find("textarea");
					var textarea_id = $textarea.attr("id");
					var textarea_value = applyLineBreaks( textarea_id );
					var text_width = getTextareaTextWidth( $("#" + textarea_id), textarea_value.split("[line_break]")[0] );
					var text_left_space = ( ( layer_width - Math.round(text_width) ) / 2 ) + 5;
					var text_position_left = layer_left + text_left_space;
					var text_color = rgba2hex( $textarea.css("color") );

					if( !text_color ) {
						text_color = '#000000';
					}

					var text_font_size = parseInt( $textarea.css("font-size") );
					
					if( !text_font_size ) {
						text_font_size = 25;
					}		


					current_layer.push({

						top: sizesCalculator( ( layer_top + 5 ), 'y' ),
						left: sizesCalculator( text_position_left ),
						type: layer_type,
						text: textarea_value,
						font_family: parseInt( $textarea.attr("font-family-value") ),
						color: text_color,
						fontsize: sizesCalculator( text_font_size )

					});				

				} else {

					current_layer.push({

						top: 	 sizesCalculator( layer_top, 'y' ),
						left: 	 sizesCalculator( layer_left ),
						width: 	 sizesCalculator( layer_width ),
						height:  sizesCalculator( layer_height, 'y' ),
						type: 	 layer_type,
						img_src: parseInt( $that.find("img").data("image_id") ),

					});

				}

				layers.push(current_layer);

			});

		
			var $result_layers_lst = $(".result-layers");		
			if( $result_layers_lst.hasClass("hidden-result-layers") ) {
				$result_layers_lst.remove();
			}

			layers = layers.reverse();

			return layers;

		}


		/* Get result genders */
		function getResultGenders() {

			var genders = 'both';
			var $male = $(".gender-male");
			var $female = $(".gender-female");

			if( $male.hasClass("gender-active") && !$female.hasClass("gender-active") ) {
				genders = 'male';
			}

			if( $female.hasClass("gender-active") && !$male.hasClass("gender-active") ) {
				genders = 'female';
			}


			return genders;

		}

		/* Activate / deactivate gender */
		$(".gender-male, .gender-female").on("click", function() {
			$(this).toggleClass("gender-active");
		});


		/* Ajax save quiz result */
		function ajaxSaveResult( method, delete_id ) {

			var layers = arrayToObject( getLayersData() );
			var $save_result = $(".save-result");
	      	var ajax_data = {

	                    nonce: VIRAQUIZ_vars.ajax_save_result_nonce,
	                    layers: layers,
	                    gender: getResultGenders(),
	                    post_id: parseInt( $("#post-id").val() ),
	                    action: 'viraquiz_save_quiz_results'

	                }; 

	        if( method == 'delete' ) {
	        	ajax_data['method'] = 'delete';
	        	ajax_data['delete_id'] = delete_id;
	        }    
	 

			$.ajax({

	                url: ajaxurl,
	                type: 'post',
	                data: ajax_data,
	                beforeSend: function () {
	                	$save_result.html("Saving result..");
	                },
	                success: function ( response ) {

	                    if( response.status == 'success' ) {

	                    	$(".quiz-results-list").html(response.html);

	                    	changeSaveCurrentResultNumber();

	                    	var $last_result = $(".quiz-results-item:last")
	                    	addResultImagePreloader( $last_result );
	                    	$last_result.find(".result-item-header").click();

	                    } else {
	                    	changeSaveCurrentResultNumber();
	                    }

	                    console.log(response.status);
	                },
	                error: function (response) {
	                    console.log(response);
	                }


	            });

		}



		/* Delete a quiz result */
		$("body").on("click", ".delete-quiz-result", function() {

			var delete_id = $(this).closest(".quiz-results-item").find(".result-item-header").attr("data-result");
			delete_id = parseInt( delete_id );

			if( !confirm( "Are you sure to delete result " + ( delete_id + 1 ) + "?" ) ) {
				return;
			}

			ajaxSaveResult( 'delete', delete_id );

			return;
		});



		
		/* Set current result number to "Save result button" */
		function changeSaveCurrentResultNumber() {

			var results_number = $(".quiz-results-item").length;
			var current_result = results_number + 1;

			$(".save-result").text("Save as result " + current_result);

		}
		changeSaveCurrentResultNumber();


		/* SlideDown result image when result title is clicked */
		function slideDownResult() {

			$("body").on("click", ".result-item-header", function() {

				var $that = $(this);
				var current_data_result = parseInt( $that.data("result") );

				slideDown( $that );


				if( ( current_data_result % 2 ) == 0  ) {
					var next_result = current_data_result + 1;
					slideDown( $("*[data-result=\"" + next_result + "\"]") );
				} else {
					var prev_result = current_data_result - 1;
					slideDown( $("*[data-result=\"" + prev_result + "\"]") );
				}


			});


			function slideDown( $that ) {

				var $result_header = $that;
				var $result_item = $result_header.closest(".quiz-results-item");
				var $result_body = $result_item.find(".result-item-body");

				$result_body.slideToggle();

			}


		}
		slideDownResult();



		/* Prevent saving quiz if the title is not set */
		$("input[name=\"publish\"]").on("click", function(e) {

			var title_length = $("input[name=\"post_title\"]").val().length;

			if( title_length == 0 ) {

				e.preventDefault();
				alert("Please set Quiz Title");

			}

		});


		/* Helper functions */
		/* Convert array to object */
		function arrayToObject(arr) {

		  var rv = {};
		  for (var i = 0; i < arr.length; ++i)
		    rv[i] = arr[i];
		  return rv;

		}

		/* Convert RGBA color to HEX color */
		function rgba2hex(color_value) {

				if ( ! color_value ) return false;

				var parts = color_value.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
				    length = color_value.indexOf('rgba') ? 3 : 2; 

				delete(parts[0]);
				
				for ( var i = 1; i <= length; i++ ) {
					parts[i] = parseInt( parts[i] ).toString(16);
					if ( parts[i].length == 1 ) parts[i] = '0' + parts[i];
				}

				return '#' + parts.join('').toUpperCase(); 
			}


		/* Add line breaks to textarea */
		function applyLineBreaks(textarea_id) {

			var oTextarea = document.getElementById(textarea_id);
			    if (oTextarea.wrap) {
			        oTextarea.setAttribute("wrap", "off");
			    }
			    else {
			        oTextarea.setAttribute("wrap", "off");
			        var newArea = oTextarea.cloneNode(true);
			        newArea.value = oTextarea.value;
			        oTextarea.parentNode.replaceChild(newArea, oTextarea);
			        oTextarea = newArea;
			    }

			    var strRawValue = oTextarea.value;
			    oTextarea.value = "";
			    var nEmptyWidth = oTextarea.scrollWidth;
			    var nLastWrappingIndex = -1;
			    for (var i = 0; i < strRawValue.length; i++) {
			        var curChar = strRawValue.charAt(i);
			        if (curChar == ' ' || curChar == '-' || curChar == '+')
			            nLastWrappingIndex = i;
			        oTextarea.value += curChar;
			        if (oTextarea.scrollWidth > nEmptyWidth) {
			            var buffer = "";
			            if (nLastWrappingIndex >= 0) {
			                for (var j = nLastWrappingIndex + 1; j < i; j++)
			                    buffer += strRawValue.charAt(j);
			                nLastWrappingIndex = -1;
			            }
			            buffer += curChar;
			            oTextarea.value = oTextarea.value.substr(0, oTextarea.value.length - buffer.length);
			            oTextarea.value += "\n" + buffer;
			        }
			    }
			    oTextarea.setAttribute("wrap", "");

			    return oTextarea.value.replace(new RegExp("\\n", "g"), "[line_break]");

		}

		/* Get the width in PX of text */
		function getTextWidth(text, font) {

		    var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
		    var context = canvas.getContext("2d");
		    context.font = font;
		    var metrics = context.measureText(text);
		    return metrics.width;

		}


		function getTextareaTextWidth($textarea, textarea_value) {

			var fontsize = $textarea.css("font-size");
			var font_family_value = parseInt( $textarea.attr("font-family-value") );
			var font_family = VIRAQUIZ_vars.fonts[font_family_value].name;
			var full_font_data = fontsize + ' ' + font_family;	

			return getTextWidth( textarea_value, full_font_data );

		}




		function sizesCalculator( size, axis ) {

			var editor_width = $editor.width();
			var editor_height = $editor.height();
			var width_reference = 880;
			var height_reference = 462;
			var calc_size;

			if( axis == 'y' ) {
				calc_size = height_reference * size / editor_height;
			} else {
				calc_size = width_reference * size / editor_width;
			}


			return Math.round( calc_size );
		}


	});


} )(jQuery);