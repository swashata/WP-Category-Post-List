/**
 * iPanelThemes Plugin Framework
 *
 * This is a jQuery plugin which works on the plugin framework to populate the UI
 * Admin area
 *
 * @dependency jquery, jquery-ui-widget, jquery-ui-mouse, jquery-ui-button, jquery-touch-punch, jquery-ui-draggable,
 * jquery-ui-droppable, jquery-ui-sortable, jquery-ui-datepicker, jquery-ui-dialog, jquery-ui-tabs, jquery-ui-slider,
 * jquery-ui-spinner, jquery-ui-progressbar, jquery-timepicker-addon, jquery-print-element, jquery-mwheelIntent, jquery-mousewheel
 *
 * @author Swashata Ghosh <swashata@ipanelthemes.com>
 * @version 2
 * @license    GPL v3
 */

;(function ( $, window, document, undefined ) {
	"use strict";
	var pluginName = "initCPLUI",
	defaults = {
		applyUIOnly: false,
		callback: null
	};

	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		this.jElement = $(this.element);
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype = {
		init: function () {
			// Apply UI only if the settings say so
			if ( this.settings.applyUIOnly === true ) {
				this.initUIElements();
				this.initSDA( true );
				return;
			}

			// Otherwise apply everything

			// Call the UI elements
			this.initUIElements();

			// Call the delegation functions
			this.initUIElementsDelegated();

			// Call the SDA
			this.initSDA( false );
		},

		// Just a safe log to console
		debugLog: function( variable ) {
			try {
				if ( console && console.log ) {
					console.log( variable );
				}
			} catch( e ) {

			}
		},


		/**
		 * Initialize the Sortable/Deletable/Addable
		 *
		 * @method     initSDA
		 */
		initSDA: function( forUI ) {
			var that = this;
			this.jElement.find('.ipt_uif_sda').each(function() {
				// Initialize the SDA UI
				that.uiSDAinit.apply(this);

				// Initialize the sortables for SDA
				that.uiSDAsort.apply(this);
			});
			if ( forUI === true ) {
				return;
			}

			// Call the delegatory methods
			that.edSDAattachAdd();
			that.edSDAattachDel();
		},


		/**
		 * Initialize the static UI elements which are not/can not be delegated
		 *
		 * @method     initUIElements
		 */
		initUIElements: function() {
			// Check the selectors of every checkbox togglers
			this.uiCheckboxToggler();

			// Sliders
			this.uiApplySlider();

			// Progressbar
			this.uiApplyProgressBar();

			// Date and DateTime Picker
			this.uiApplyDateTimePicker();

			// Font selector
			this.uiApplyFontSelector();

			// Theme selector
			this.uiApplyThemeSelector();

			// Uploader
			this.uiApplyUploader();

			// WP Color Picker
			this.uiApplyIRIS();

			// Conditional input and select
			this.uiApplyConditionalInput();
			this.uiApplyConditionalSelect();

			// Collapsebox
			this.uiApplyCollapsible();

			// Tabs
			this.uiApplyTabs();

			// Show/Hide inits
			this.uiApplyUIInits();
		},

		uiApplyUIInits: function() {
			this.jElement.find( '.ipt_uif_ui_init_loader' ).hide();
			this.jElement.find( '.ipt_uif_ui_hidden_init' ).css( 'visibility', 'visible' ).fadeIn( 'fast' );
		},

		// Checkbox Toggler
		uiCheckboxToggler: function() {
			// Loop through every toggler and add listener to the selectors too
			var jElement = this.jElement;
			jElement.find('.ipt_uif_checkbox_toggler').each(function() {
				var _self = $(this);
				if ( _self.is(':checked') ) {
					$(_self.data('selector')).prop('checked', true);
				}
			});
		},

		// Sliders
		uiApplySlider: function() {
			this.jElement.find('.ipt_uif_slider').each(function() {
				var step, min, max, value, slider_range, slider_settings, second_value, first_input = $(this), second_input = null,
				count_div, slider_div, slider_div_duplicate;

				// Get the settings
				step = parseFloat( $(this).data('step') );
				if( isNaN( step ) )
					step = 1;

				min = parseFloat( $(this).data('min') );
				if( isNaN( min ) )
					min = 1;

				max = parseFloat( $(this).data('max') );
				if( isNaN( max ) )
					max = null;

				value = parseFloat( $(this).val() );
				if( isNaN( value ) )
					value = min;

				slider_range = $(this).hasClass('slider_range') ? true : false;

				slider_settings = {
					min: min,
					max: max,
					step: step,
					range: false
				};

				// Get the second input if necessary
				if ( slider_range ) {
					second_input = first_input.next('input');
					second_value = parseFloat( second_input.val() );
					if( isNaN( second_value ) ) {
						second_value = min;
					}
				}

				// Prepare the show count
				count_div = first_input.prev('div.ipt_uif_slider_count');

				// Append the div
				slider_div = $('<div />');
				slider_div.addClass(slider_range ? 'ipt_uif_slider_range' : 'ipt_uif_slider_single').addClass('ipt_uif_slider_div');

				// Remove the duplicate div
				// Here for legecy purpose
				if ( slider_range ) {
					slider_div_duplicate = second_input.next('div');
				} else {
					slider_div_duplicate = first_input.next('div');
				}
				if ( slider_div_duplicate.length ) {
					slider_div_duplicate.remove();
				}

				if ( slider_range ) {
					second_input.after(slider_div);
				} else {
					first_input.after(slider_div);
				}

				//Prepare the slide function
				if ( ! slider_range ) {
					slider_settings.slide = function( event, ui ) {
						first_input.val(ui.value);
						if ( count_div.length ) {
							count_div.find('span').text(ui.value);
						}
					};
					slider_settings.value = value;
				} else {
					//alert('atta boy');
					slider_settings.slide = function( event, ui ) {
						first_input.val(ui.values[0]);
						second_input.val(ui.values[1]);
						if( count_div.length ) {
							count_div.find('span.ipt_uif_slider_count_min').text(ui.values[0]);
							count_div.find('span.ipt_uif_slider_count_max').text(ui.values[1]);
						}
					};
					slider_settings.values = [value, second_value];
					slider_settings.range = true;
				}

				// Init the counter
				if ( count_div.length ) {
					if ( slider_range ) {
						count_div.find('span.ipt_uif_slider_count_min').text(value);
						count_div.find('span.ipt_uif_slider_count_max').text(second_value);
					} else {
						count_div.find('span').text(value);
					}
				}

				//Init the slider
				slider_div.slider( slider_settings );
			});
		},

		// Progress bar
		uiApplyProgressBar: function() {
			this.jElement.find('.ipt_uif_progress_bar').each(function() {
				//First get the start value
				var progress_self = $(this),
				start_value = progress_self.data('start') ? progress_self.data('start') : 0,
				//Add the value to the inner div
				value_div = progress_self.find('.ipt_uif_progress_value').addClass('code');
				value_div.html(start_value + '%');

				//Init the progressbar
				var progressbar = progress_self.progressbar({
					value : start_value,
					change : function(event, ui) {
						value_div.html($(this).progressbar('option', 'value') + '%');
					}
				});

				if(progress_self.next('.ipt_uif_button_container').find('.ipt_uif_button.progress_random_fun').length) {
					progress_self.next('.ipt_uif_button_container').find('.ipt_uif_button.progress_random_fun').on('click', function() {
						//this.preventDefault();
						var new_value = parseInt(Math.random()*100);
						progressbar.progressbar('option', 'value', new_value);
						return false;
					});
				}
			});
		},

		// Date and Datetime picker
		uiApplyDateTimePicker: function() {
			// Date picker
			this.jElement.find('.ipt_uif_datepicker input.ipt_uif_text').datepicker({
				dateFormat : 'yy-mm-dd',
				beforeShow : function() {
					$('body').addClass('ipt_uif_common');
				},
				onClose : function() {
					$('body').removeClass('ipt_uif_common');
				},
				showButtonPanel: true,
				closeText: WPCPLl10n.closeText,
				currentText: WPCPLl10n.currentText,
				monthNames: WPCPLl10n.monthNames,
				monthNamesShort: WPCPLl10n.monthNamesShort,
				dayNames: WPCPLl10n.dayNames,
				dayNamesShort: WPCPLl10n.dayNamesShort,
				dayNamesMin: WPCPLl10n.dayNamesMin,
				firstDay: WPCPLl10n.firstDay,
				isRTL: WPCPLl10n.isRTL,
				timezoneText : WPCPLl10n.timezoneText
			});

			// Date Time Picker
			this.jElement.find('.ipt_uif_datetimepicker input.ipt_uif_text').datetimepicker({
				dateFormat : 'yy-mm-dd',
				timeFormat : 'HH:mm:ss',
				beforeShow : function() {
					$('body').addClass('ipt_uif_common');
				},
				onClose : function() {
					$('body').removeClass('ipt_uif_common');
				},
				showButtonPanel: true,
				closeText: WPCPLl10n.closeText,
				currentText: WPCPLl10n.tcurrentText,
				monthNames: WPCPLl10n.monthNames,
				monthNamesShort: WPCPLl10n.monthNamesShort,
				dayNames: WPCPLl10n.dayNames,
				dayNamesShort: WPCPLl10n.dayNamesShort,
				dayNamesMin: WPCPLl10n.dayNamesMin,
				firstDay: WPCPLl10n.firstDay,
				isRTL: WPCPLl10n.isRTL,
				amNames : WPCPLl10n.amNames,
				pmNames : WPCPLl10n.pmNames,
				timeSuffix : WPCPLl10n.timeSuffix,
				timeOnlyTitle : WPCPLl10n.timeOnlyTitle,
				timeText : WPCPLl10n.timeText,
				hourText : WPCPLl10n.hourText,
				minuteText : WPCPLl10n.minuteText,
				secondText : WPCPLl10n.secondText,
				millisecText : WPCPLl10n.millisecText,
				microsecText : WPCPLl10n.microsecText,
				timezoneText : WPCPLl10n.timezoneText
			});
		},

		// Font Selector
		uiApplyFontSelector: function() {
			this.jElement.find('.ipt_uif_font_selector').each(function() {
				// Create the initial
				var select = $(this).find('select').eq(0),
				preview = $(this).find('.ipt_uif_font_preview').eq(0),
				selected = select.find('option:selected'),
				font_suffix = selected.data('fontinclude'),
				font_key = selected.val(),
				font_family = selected.text();

				//Attach the link
				if ( ! $('#ipt_uif_webfont_' + font_key).length ) {
					$('<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + font_suffix + '" id="ipt_uif_webfont_' + font_key + '" />').appendTo('head');
				}

				//Change the font family
				preview.css({fontFamily : font_family});
			});
		},

		uiApplyThemeSelector: function() {
			this.jElement.find('.ipt_uif_theme_selector').each(function() {
				var select = $(this),
				preview = select.next('.ipt_uif_theme_preview'),
				selected = select.find('option:selected'),
				colors = selected.data('colors'),
				newHTML = '', i;
				for ( i = 0; i < colors.length; i++ ) {
					newHTML += '<div style="background-color: #' + colors[i] + ';"></div>';
				}
				preview.html(newHTML);
			});
		},

		// WordPress media uploader init
		uiApplyUploader: function() {
			var that = this;
			this.jElement.find('.ipt_uif_upload').each(function() {
				var uploader = $(this),
				input = uploader.find('input'),
				preview = uploader.find('div.ipt_uif_upload_bg'),
				button = uploader.find('button.ipt_uif_upload_button'),
				cancel = uploader.find('button.ipt_uif_upload_cancel'),
				filename;

				if ( button.length && input.length ) {
					//Initialize
					filename = input.val();
					preview.hide();
					if ( that.testImage(filename) ) {
						preview.show().find('.ipt_uif_upload_preview').css({backgroundImage : 'url("' + filename + '")'}).show();
					} else if ( filename === '' ) {
						preview.hide().find('.ipt_uif_upload_preview').css({backgroundImage : 'none'});
						cancel.hide();
					}
				}
			});
		},

		// WP color picker
		uiApplyIRIS: function() {
			this.jElement.find('.ipt_uif_colorpicker').wpColorPicker();
		},

		// Conditional input
		uiApplyConditionalInput: function() {
			this.jElement.find('.ipt_uif_conditional_input').each(function() {
				// init vars
				var _self = $(this),
				inputs = _self.find('input'),
				shown = [], hidden = [], input_ids, i;

				// loop through and populate vars
				inputs.each(function() {
					input_ids = $(this).data('condid');
					if ( typeof ( input_ids ) == 'string' ) {
						input_ids = input_ids.split( ',' );
					} else {
						input_ids = [];
					}

					if ( $(this).is(':checked') ) {
						shown.push.apply( shown, input_ids );
					} else {
						hidden.push.apply( hidden, input_ids );
					}
				});

				// hide all that would be hidden
				for ( i = 0; i < hidden.length; i++ ) {
					$('#' + hidden[i]).stop( true, true ).hide();
				}

				// Now show all that would be shown
				for ( i = 0; i < shown.length; i++ ) {
					$('#' + shown[i]).stop( true, true ).show();
				}

			});
		},

		// Conditional Select
		uiApplyConditionalSelect: function() {
			this.jElement.find('.ipt_uif_conditional_select').each(function() {
				// Init the vars
				var _self = $(this),
				select = _self.find('select'),
				shown = [], hidden = [], input_ids, i;

				// Loop through and populate vars
				select.find('option').each(function() {
					input_ids = $(this).data('condid');
					if ( typeof ( input_ids ) == 'string' ) {
						input_ids = input_ids.split( ',' );
					} else {
						input_ids = [];
					}

					if ( $(this).is(':selected') ) {
						shown.push.apply( shown, input_ids );
					} else {
						hidden.push.apply( hidden, input_ids );
					}
				});

				// hide all that would be hidden
				for ( i = 0; i < hidden.length; i++ ) {
					$('#' + hidden[i]).stop( true, true ).hide();
				}

				// Now show all that would be shown
				for ( i = 0; i < shown.length; i++ ) {
					$('#' + shown[i]).stop( true, true ).show();
				}
			});
		},

		// Collapsible
		uiApplyCollapsible: function() {
			this.jElement.find('.ipt_uif_collapsible_handle_anchor').each(function() {
				var self = $(this),
				collapse_wrap = self.closest('.ipt_uif_collapsible'),
				collapse_box = collapse_wrap.find('> .ipt_uif_collapsed');
				if ( collapse_wrap.data('opened') == 1 ) {
					collapse_box.show();
					self.addClass('ipt_uif_collapsible_open');
				} else {
					collapse_box.hide();
					self.removeClass('ipt_uif_collapsible_open');
				}
			});
		},

		// Tabs
		uiApplyTabs: function() {
			this.jElement.find('.ipt_uif_tabs').each(function() {
				// Apply UI tabs
				$(this).tabs({
					collapsible: $(this).data('collapsible')
				});

				//Fix for vertical tabs
				if ( $(this).hasClass('vertical') ) {
					$(this).addClass('ui-tabs-vertical ui-helper-clearfix');
					$(this).find('> ul > li').removeClass('ui-corner-top').addClass('ui-corner-left');
				}
			});
		},

		// SDA initiator
		uiSDAinit: function() {
			var self = $(this),
			submit_button = self.find('> .ipt_uif_sda_foot button.ipt_uif_sda_button'),

			// Get some variables
			vars = {
				sort : self.data('draggable') == 1 ? true : false,
				add : self.data('addable') == 1 ? true : false,
				del : self.data('addable') == 1 ? true : false,
				count : (submit_button.length && submit_button.data('count') ? submit_button.data('count') : 0),
				key : (submit_button.length && submit_button.data('key') ? submit_button.data('key') : '__KEY__'),
				confirmDel : (submit_button.length && submit_button.data('confirm') ? submit_button.data('confirm') : 'Are you sure you want to delete? This can not be undone.'),
				confirmTitle : (submit_button.length && submit_button.data('confirmtitle') ? submit_button.data('confirmtitle') : 'Confirmation of Deletion')
			};

			var totalItems = self.find( '> .ipt_uif_sda_body > .ipt_uif_sda_elem' ).length;
			if ( 0 == totalItems ) {
				self.addClass( 'ipt-uif-sda-empty' );
			} else {
				self.removeClass( 'ipt-uif-sda-empty' );
			}

			// store the data
			self.data( 'iptSDAdata', vars );
		},

		// SDA List make sortable
		uiSDAsort: function() {
			var self = $(this),
			sdaData = self.data('iptSDAdata');
			if ( sdaData.sort === true ) {
				self.find('> .ipt_uif_sda_body').sortable({
					items : 'div.ipt_uif_sda_elem',
					placeholder : 'ipt_uif_sda_highlight',
					handle : 'div.ipt_uif_sda_drag',
					distance : 5,
					axis : 'y',
					start: function( event, ui ) {
						ui.placeholder.height( ui.item.outerHeight() );
					},
					helper : 'original',
					cursor: 'move',
					appendTo: self.closest( '.ipt_uif_sda_body' )
					// items : 'div.ipt_uif_sda_elem',
					// placeholder : 'ipt_uif_sda_highlight',
					// handle : 'div.ipt_uif_sda_drag',
					// distance : 5,
					// axis : 'y',
					// helper : 'original'
				});
			}
		},

		/**
		 * Initialize event delegated functionalities
		 * Needs to be initialized only once
		 *
		 * @method     initUIElementsDelegated
		 */
		initUIElementsDelegated: function() {
			var _self = this;
			// Initialize the help toggler
			this.edApplyHelp();

			// Initialize the checkbox toggler
			this.edCheckboxToggler();

			// Initialize the slider listener
			this.edSliderInput();

			// Initialize the datetime Now
			this.edDateTimeNow();

			// Initialize the print element
			this.edApplyPrintElement();

			// Initialize the font selector
			this.edApplyFontSelector();

			// Initialize the theme selector
			this.edApplyThemeSelector();

			// Initialize the uploader
			this.edApplyUploader();

			// Initialize conditional input and select
			this.edApplyConditionalInput();
			this.edApplyConditionalSelect();

			// Initialize collapsible
			this.edApplyCollapsible();

			// Initialize delete confirmer
			this.edApplyDeleteConfirm();

			// Initialize Dismiss Message
			this.edApplyMessageDismiss();
		},

		// Dismiss Message
		edApplyMessageDismiss: function() {
			this.jElement.on( 'click', '.ipt_uif_message_dismiss', function( e ) {
				e.preventDefault();
				$( this ).closest( '.ipt_uif_message' ).fadeOut( 'fast' );
			} );
		},

		// Help Toggler
		edApplyHelp: function(e) {
			this.jElement.on( 'click', '.ipt_uif_msg', function(e) {
				e.preventDefault();
				var trigger = $(this).find('.ipt_uif_msg_icon'),
				title = trigger.attr('title'),
				temp, dialog_content;

				if( undefined === title || '' === title ) {
					if( undefined !== ( temp = trigger.parent().parent().siblings('th').find('label').html() ) ) {
						title = temp;
					} else {
						title = initCPLUI.L10n.help;
					}
				}

				dialog_content = $('<div>'  + trigger.next('.ipt_uif_msg_body').html() + '</div>');
				var buttons = {};
				buttons[initCPLUI.L10n.got_it] = function() {
					$(this).dialog("close");
				};
				dialog_content.dialog({
					autoOpen: true,
					buttons: buttons,
					modal: true,
					minWidth: 600,
					closeOnEscape: true,
					title: title,
					//appendTo : '.ipt_uif_common',
					create : function(event, ui) {
						$('body').addClass('ipt_uif_common');
					},
					close : function(event, ui) {
						$('body').removeClass('ipt_uif_common');
					}
				});
			} );
		},

		// Checkbox Toggler
		edCheckboxToggler: function() {
			// Apply the delegated listen to the change event
			this.jElement.on( 'change', '.ipt_uif_checkbox_toggler', function() {
				var selector = $($(this).data('selector')),
				self = $(this);
				if(self.is(':checked')) {
					selector.prop('checked', true).trigger('change');
				} else {
					selector.prop('checked', false).trigger('change');
				}
			} );
		},

		// Slider input event
		edSliderInput: function() {
			// Listen to the first input change
			this.jElement.on( 'change', '.ipt_uif_slider', function() {
				var _self = $(this), second_input, slider, count_div = _self.prev('.ipt_uif_slider_count');
				// If it is a range
				if ( _self.hasClass('slider_range') ) {
					second_input = _self.next('.ipt_uif_slider_range_max');
					slider = second_input.next('.ipt_uif_slider_div');
					slider.slider({
						values : [parseFloat(_self.val()), parseFloat(second_input.val())]
					});
					count_div.find('span.ipt_uif_slider_count_min').text( parseFloat( _self.val() ) );
				// If it is a slider
				} else {
					slider = _self.next('.ipt_uif_slider_div');
					slider.slider({
						value : parseFloat(_self.val())
					});
					count_div.find('span').text(parseFloat(_self.val()));
				}
			} );

			// Listen to the second input change
			this.jElement.on( 'change', '.ipt_uif_slider_range_max', function() {
				var _self = $(this),
				first_input = _self.prev('.ipt_uif_slider'),
				slider = _self.next('.ipt_uif_slider_div'),
				count_div = first_input.prev('.ipt_uif_slider_count');
				slider.slider({
					values : [parseFloat(first_input.val()), parseFloat(_self.val())]
				});
				count_div.find('span.ipt_uif_slider_count_max').text( parseFloat( _self.val() ) );
			} );
		},

		// DateTime NOW Button
		edDateTimeNow: function() {
			this.jElement.on( 'click', '.ipt_uif_datepicker_now', function() {
				$(this).nextAll('.ipt_uif_text').val('NOW');
			} );
		},

		// Print element
		edApplyPrintElement: function() {
			this.jElement.on( 'click', '.ipt_uif_printelement', function() {
				$('#' + $(this).data('printid')).printElement({
					leaveOpen:true,
					printMode:'popup',
					pageTitle : document.title
				});
			} );
		},

		// Font selector
		edApplyFontSelector: function() {
			this.jElement.on( 'change keyup', '.ipt_uif_font_selector select', function(e) {
				var select = $(this),
				preview = select.closest('.ipt_uif_font_selector').find('.ipt_uif_font_preview').eq(0),
				selected = select.find('option:selected'),
				font_suffix = selected.data('fontinclude'),
				font_key = selected.val(),
				font_family = selected.text();

				// Attach the link
				if ( ! $('#ipt_uif_webfont_' + font_key).length ) {
					$('<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + font_suffix + '" id="ipt_uif_webfont_' + font_key + '" />').appendTo('head');
				}

				//Change the font family
				preview.css({fontFamily : font_family});
			} );
		},

		// Theme Selector
		edApplyThemeSelector: function() {
			this.jElement.on( 'change keyup', '.ipt_uif_theme_selector', function() {
				var select = $(this),
				preview = select.next('.ipt_uif_theme_preview'),
				selected = select.find('option:selected'),
				colors = selected.data('colors'),
				newHTML = '', i;
				preview.html('');
				for (i = 0; i < colors.length; i++) {
					newHTML += '<div style="background-color: #' + colors[i] + ';"></div>';
				}
				preview.html(newHTML);
			} );
		},

		edApplyUploader: function() {
			//.ipt_uif_upload
			var that = this;

			// do for the preview
			this.jElement.on( 'click', '.ipt_uif_upload .ipt_uif_upload_preview', function() {
				var uploader = $(this).closest( '.ipt_uif_upload' ),
				input = uploader.find('input');

				tb_show('', input.val() + '?TB_iframe=true');
			} );

			// Do for the cancel
			this.jElement.on( 'click', '.ipt_uif_upload .ipt_uif_upload_cancel', function(e) {
				e.preventDefault();

				var uploader = $(this).closest( '.ipt_uif_upload' ),
				input = uploader.find('input'),
				preview = uploader.find('div.ipt_uif_upload_bg'),
				button = uploader.find('button.ipt_uif_upload_button'),
				cancel = uploader.find('button.ipt_uif_upload_cancel'),
				download = uploader.find('a');

				// Remove the input value
				input.val('');
				preview.hide();
				download.hide();
				cancel.hide();
			} );

			// Do for the text
			this.jElement.on( 'change', '.ipt_uif_upload .ipt_uif_text', function() {
				var uploader = $( this ).closest( '.ipt_uif_upload' ),
				preview = uploader.find('div.ipt_uif_upload_bg');

				preview.show().find('.ipt_uif_upload_preview').css({
					backgroundImage: 'url("' + $( this ).val() + '")'
				});
			} );


			// Do for the upload button
			this.jElement.on( 'click', '.ipt_uif_upload button.ipt_uif_upload_button', function(e) {
				e.preventDefault();

				var uploader = $(this).closest( '.ipt_uif_upload' ),
				input = uploader.find('input'),
				preview = uploader.find('div.ipt_uif_upload_bg'),
				button = uploader.find('button.ipt_uif_upload_button'),
				cancel = uploader.find('button.ipt_uif_upload_cancel'),
				filename,
				ipt_uif_wp_media_frame = uploader.data('iptUIFwpMediaFrame'),
				// Set the reference variables
				wp_media_reference = {
					input : input,
					preview : preview,
					self : button,
					cancel: cancel
				};

				// If wp_media already exists
				if ( ipt_uif_wp_media_frame ) {
					ipt_uif_wp_media_frame.open();
					return;
				}

				// It was not present
				// So let us create one
				ipt_uif_wp_media_frame = wp.media.frames.ipt_uif_wp_media_frame = wp.media({
					title : input.data('title'),
					button : {
						text : input.data('select')
					},
					multiple : false
				});

				// Bind the select event
				ipt_uif_wp_media_frame.on( 'select', function() {
					var attachment = ipt_uif_wp_media_frame.state().get('selection').first().toJSON(),
					associated_title_elem;

					wp_media_reference.preview.hide();

					if ( that.testImage(attachment.url) ) {
						wp_media_reference.preview.show().find('.ipt_uif_upload_preview').css({backgroundImage : 'url("' + attachment.url + '")'});
					} else if (attachment.url === '') {
						wp_media_reference.preview.hide().find('.ipt_uif_upload_preview').css({backgroundImage : 'none'});
					}

					//Change the input value
					wp_media_reference.input.val(attachment.url);

					//Check to see if title is associated
					associated_title_elem = wp_media_reference.input.data('settitle');
					if ( associated_title_elem !== undefined && $( '#' + associated_title_elem ).length ) {
						$('#' + associated_title_elem).val(attachment.title);
					}

					// Show the cancel button
					wp_media_reference.cancel.show();
				} );

				// open it
				ipt_uif_wp_media_frame.open();

				// save it
				uploader.data( 'iptUIFwpMediaFrame', ipt_uif_wp_media_frame );
			} );

		},

		// Conditional Input
		edApplyConditionalInput: function() {
			this.jElement.on( 'change', '.ipt_uif_conditional_input', function(e) {
				// init vars
				var _self = $(this),
				inputs = _self.find('input'),
				shown = [], hidden = [], input_ids, i;

				// loop through and populate vars
				inputs.each(function() {
					input_ids = $(this).data('condid');
					if ( typeof ( input_ids ) == 'string' ) {
						input_ids = input_ids.split( ',' );
					} else {
						input_ids = [];
					}

					if ( $(this).is(':checked') ) {
						shown.push.apply( shown, input_ids );
					} else {
						hidden.push.apply( hidden, input_ids );
					}
				});

				// hide all that would be hidden
				for ( i = 0; i < hidden.length; i++ ) {
					$('#' + hidden[i]).stop( true, true ).hide();
				}

				// Now show all that would be shown
				for ( i = 0; i < shown.length; i++ ) {
					$('#' + shown[i]).stop( true, true ).fadeIn('fast');
				}

			} );
		},

		// Conditional Select
		edApplyConditionalSelect: function() {
			this.jElement.on( 'change keyup', '.ipt_uif_conditional_select', function(e) {
				// Init the vars
				var _self = $(this),
				select = _self.find('select'),
				shown = [], hidden = [], input_ids, i;

				// Loop through and populate vars
				select.find('option').each(function() {
					input_ids = $(this).data('condid');
					if ( typeof ( input_ids ) == 'string' ) {
						input_ids = input_ids.split( ',' );
					} else {
						input_ids = [];
					}

					if ( $(this).is(':selected') ) {
						shown.push.apply( shown, input_ids );
					} else {
						hidden.push.apply( hidden, input_ids );
					}
				});

				// hide all that would be hidden
				for ( i = 0; i < hidden.length; i++ ) {
					$('#' + hidden[i]).stop( true, true ).hide();
				}

				// Now show all that would be shown
				for ( i = 0; i < shown.length; i++ ) {
					$('#' + shown[i]).stop( true, true ).fadeIn('fast');
				}
			} );
		},

		// Collapsible
		edApplyCollapsible: function() {
			this.jElement.on( 'click', '.ipt_uif_collapsible_handle_anchor', function(e) {
				var self = $(this),
				collapse_box = self.closest('.ipt_uif_collapsible').find('> .ipt_uif_collapsed');
				self.toggleClass('ipt_uif_collapsible_open');
				collapse_box.slideToggle('normal');
			} );
		},

		// Delete confirmer
		edApplyDeleteConfirm: function() {
			this.jElement.on( 'click', '.wp-list-table a.delete', function(e) {
				e.preventDefault();
				var self = $(this);
				$('<div>' + initCPLUI.L10n.delete_msg + '</div>').dialog({
					autoOpen : true,
					modal : true,
					minWidth : 400,
					closeOnEscape : true,
					title : initCPLUI.L10n.delete_title,
					buttons : {
						Confirm : function() {
							window.location.href = self.attr('href');
							$(this).dialog('close');
						},
						Cancel : function() {
							$(this).dialog('close');
						}
					},
					//appendTo : '.ipt_uif_common',
					create : function(event, ui) {
						$('body').addClass('ipt_uif_common');
					},
					close : function(event, ui) {
						$('body').removeClass('ipt_uif_common');
					}
				});
			} );
		},

		// Delete button functionality for SDA
		edSDAattachDel: function() {
			var that = this;
			this.jElement.on( 'click', '.ipt_uif_sda_del', function(e) {
				e.preventDefault();
				var self = $(this),
				vars = self.closest('.ipt_uif_sda').data('iptSDAdata'),
				dialog = $('<p>' + vars.confirmDel + '</p>');
				dialog.dialog({
					autoOpen : true,
					modal : true,
					minWidth : 400,
					closeOnEscape : true,
					title : vars.confirmTitle,
					buttons : {
						Confirm : function() {
							that.edSDAdel.apply(self);
							$(this).dialog('close');
						},
						Cancel : function() {
							$(this).dialog('close');
						}
					},
					//appendTo : '.ipt_uif_common',
					create : function(event, ui) {
						$('body').addClass('ipt_uif_common');
					},
					close : function(event, ui) {
						$('body').removeClass('ipt_uif_common');
					}
				});
			} );
		},
		edSDAdel: function() {
			var target = $(this).closest( '.ipt_uif_sda_elem' ),
			sdaItem = $( this ).closest( '.ipt_uif_sda' );
			target.slideUp('normal');
			target.css('background-color', '#ffaaaa').animate({'background-color' : '#ffffff'}, 'normal', function() {
				target.stop().remove();
				var totalItems = sdaItem.find( '> .ipt_uif_sda_body > .ipt_uif_sda_elem' ).length;
				if ( 0 == totalItems ) {
					sdaItem.addClass( 'ipt-uif-sda-empty' );
				} else {
					sdaItem.removeClass( 'ipt-uif-sda-empty' );
				}
				sdaItem.trigger( 'SDADelete.eform' );
			});
		},

		// Add button functionality for SDA
		edSDAattachAdd: function() {
			//.ipt_uif_sda_foot button.ipt_uif_sda_button
			var that = this;
			this.jElement.on( 'click', '.ipt_uif_sda_foot button.ipt_uif_sda_button', function(e) {
				e.preventDefault();
				var self = $(this),
				sdaItem = self.closest('.ipt_uif_sda'),
				vars = sdaItem.data('iptSDAdata'),
				add_string = sdaItem.find('> .ipt_uif_sda_data').text(),
				count = vars.count++,
				re = new RegExp( that.quote(vars.key), 'g' ), new_div, old_color;

				// Modify the element HTML
				add_string = $('<div></div>').html(add_string).text();
				add_string = add_string.replace( re, count );

				// Add the element HTML to a new DOM
				new_div = $('<div class="ipt_uif_sda_elem"></div>').append(add_string);

				// Append to the SDA body
				sdaItem.find('> .ipt_uif_sda_body').append(new_div);

				// Apply the UI framework
				new_div.initCPLUI({
					applyUIOnly: true
				});

				new_div.find( 'input, textarea, select' ).eq( 0 ).focus();

				// Animate the color
				old_color = new_div.css('background-color');

				new_div.hide().slideDown('fast').css('background-color', '#aaffaa').animate( {'background-color' : old_color}, 'normal', function() {
					sdaItem.trigger( 'SDAAdd.eform' );
				} );

				self.data( 'count', vars.count );
				self.attr( 'data-count', vars.count );

				var totalItems = sdaItem.find( '> .ipt_uif_sda_body > .ipt_uif_sda_elem' ).length;
				if ( 0 == totalItems ) {
					sdaItem.addClass( 'ipt-uif-sda-empty' );
				} else {
					sdaItem.removeClass( 'ipt-uif-sda-empty' );
				}
			} );
		},


		/**
		 * Other functions
		 *
		 * @internal
		 */
		testImage : function(filename) {
			return (/\.(gif|jpg|jpeg|tiff|png)$/i).test(filename);
		},

		quote : function(str) {
			return str.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
		},

		stripTags: function( string ) {
			var tempDOM = $('<div />'),
			stripped = '';
			tempDOM.html(string);
			stripped = tempDOM.text();
			tempDOM.remove();
			return stripped;
		},

		yourOtherFunction: function () {
			// some logic
		}
	};

	var methods = {
		init: function( options ) {
			return this.each(function() {
				if ( !$.data( this, "plugin_" + pluginName ) ) {
					$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
				}
			});
		},
		reinitTBAnchors : function() {
			var tbWindow = $('#TB_window'), width = $(window).width(), H = $(window).height(), W = ( 1024 < width ) ? 1024 : width, adminbar_height = 0;

			if ( $('body.admin-bar').length )
					adminbar_height = 28;

			if ( tbWindow.length ) {
					tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
					$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
					$('#TB_ajaxContent').width( W - 80 ).height( H - 95 - adminbar_height );
					tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
					if ( typeof document.body.style.maxWidth != 'undefined' )
							tbWindow.css({'top': 20 + adminbar_height + 'px','margin-top':'0'});
			}

			return $('a.thickbox').each( function() {
					var href = $(this).attr('href');
					if ( ! href ) return;
					href = href.replace(/&width=[0-9]+/g, '');
					href = href.replace(/&height=[0-9]+/g, '');
					$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
			});
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[ pluginName ] = function ( method ) {
		if( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof( method ) == 'object' || !method ) {
			methods.init.apply(this, arguments);
		} else {
			$.error( 'Method ' + method + ' does not exist on jQuery.' + pluginName );
		}

		// chain jQuery functions
		return this;
	};

})( jQuery, window, document );

jQuery(document).ready(function($) {
	$('.wp-cpl-backoffice').initCPLUI();
	$(document).initCPLUI('reinitTBAnchors');
});
