(function(){ // protection des scopes globaux des autres scripts - module pattern

    var pathimg    = '/bundles/capsciencesserverviplayout/img/';
    var didacticielServer = "http://interactifs.cap-sciences.net/didacticiel/web/";

    jQuery(function(){


        getLocale();

        // formfieldAddBehaviour(jQuery('.validate input[type=text], .validate input[type=email], .validate input[type=password]'));
        formfieldAddBehaviour(jQuery('input[aria-required=true]'));

        didacticiel(jQuery('#didacticiel'));

    });

    /* -------------------- localisation -------------------- */

    var lang = {
        'fr' : {
            empty_capsciences_servervip_core_user_pseudo_son : 'Indiquez votre identifiant',
            empty__username : 'Indiquez votre identifiant',
            empty_capsciences_servervip_core_user_email : 'Indiquez votre email',
            empty_capsciences_servervip_core_user_password_son_first : 'Indiquez votre mot de passe',
            empty__password : 'Indiquez votre mot de passe',
            empty_capsciences_servervip_core_user_password_son_second : 'Confirmez votre mot de passe',
            empty_pseudonym : 'Indiquez votre identifiant',
            empty_password_member_already : 'Indiquez votre mot de passe',
            invalid_email : 'Adresse mail non valide',
            mandatory_newsletter_subscribe : 'Vous devez vous inscrire Ã  la newsletter',
            mandatory_eula_accept : 'Vous devez accepter les conditions générales',
            matchpasswords_password2 : 'Vos mots de passe doivent être identiques',
            showdidac_page : 'Afficher la page ',
            empty_form_pseudo_son:'Indiquez votre identifiant',
            empty_form_prenom: 'Indiquez votre prénom',
            empty_form_nom: 'Indiquez votre nom',
            empty_form_email: 'Indiquez votre email',
            empty_form_num_mobile: 'Indiquez votre numéro de mobile',
            empty_form_code_postal: 'Indiquez votre code postal',
            empty_form_ville: 'Indiquez votre ville'
        },
        'en' : {
            empty_capsciences_servervip_core_user_pseudo_son : 'Set your nickname',
            empty__username : 'Set your nickname',
            empty_capsciences_servervip_core_user_email : 'Set your email',
            empty_capsciences_servervip_core_user_password_son_first : 'Set your password',
            empty__password : 'Set your password',
            empty_capsciences_servervip_core_user_password_son_second : 'Confirm your password',
            empty_pseudonym : 'Set your nickname',
            empty_password_member_already : 'Set your password',
            invalid_email : 'Email Address not valid',
            mandatory_newsletter_subscribe : 'You have to subscribe to the newsletter',
            mandatory_eula_accept : 'You have to accept CGU',
            matchpasswords_password2 : 'password and confirmation must be same',
            showdidac_page : 'Show page ',
            empty_form_pseudo_son:'Set your nickname',
            empty_form_prenom: 'Set your first name',
            empty_form_nom: 'Set your last name',
            empty_form_email: 'Set your email',
            empty_form_num_mobile: 'Set your mobile phone',
            empty_form_code_postal: 'Set your postal code',
            empty_form_ville: 'Set your city'

        }
    };
    var locale = 'fr'; // default locale is fr

    /**
     getLocale() sets the global var 'locale' to the locale detected in the page
     @author stefd
     @return null
     */
    function getLocale() {
        if(jQuery('html').attr('lang').length>0) {
            locale = jQuery('html').attr('lang');
        } else if(jQuery('body').attr('lang').length>0) {
            locale = jQuery('body').attr('lang');
        }
    }

    /* -------------------- fin localisation -------------------- */

    /* --------------- gestion des champs de formulaires --------------- */

    /**
     formfieldAddBehaviour add behaviour for a form field if it is required : adds an alert if empty
     @param f : form field as a jQuery object
     @return null
     */
    function formfieldAddBehaviour(f) {
        if(f.length === 0) {
            return;
        }
        f.each(function(){
            jQuery(this).on('blur',function() {
                var field = jQuery(this);
                formfieldSetValid(field);
                // check if field is empty
                if( field.val().length === 0 ) {
                    formfieldSetInvalid(field,'empty_' + field.attr('id'));
                    // else check if email, validate email
                } else if( field.attr("type") == "email") {
                    var validmail = /^[a-z0-9]+([-_\.+]{1}[a-z0-9]+)*@[a-z0-9]+([-_\.]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$/;
                    if(!field.val().toString().match(validmail)) {
                        formfieldSetInvalid(field,'invalid_email');
                    }
                } else if( field.is(':checkbox') && !field.is(':checked') ) {
                    formfieldSetInvalid(field,'mandatory_' + field.attr('id'));
                }
                // finally check if it's a password confirmation field and check whether passwords match
                if( field.attr('id') == 'password2' && (jQuery('#password').val() !== field.val()) ) {
                    formfieldSetInvalid(field,'matchpasswords_' + field.attr('id'));
                }
            });
        });
    }

    /**
     formfieldSetInvalid
     helper function for formfieldAddBehaviour : make all the changes around a form field when invalid
     @author stefd
     @param field : form field as a jQuery object
     @param message : message to be displayed when invalid
     @return null
     */
    function formfieldSetInvalid(field,message) {
        message = (lang[locale][message]) ? lang[locale][message] : message;
        field.attr("aria-invalid","true");
        field.closest('div')
            .addClass('form-text-invalid')
            .find('.info-required')
            .remove()
            .end()
            .append('<div class="info-required" role="alert">' + message + '</div>');
    }

    /**
     formfieldSetValid
     helper function for formfieldAddBehaviour : make all the changes around a form field when valid
     @author stefd
     @param field : form field as a jQuery object
     @return null
     */
    function formfieldSetValid(field) {
        field.attr("aria-invalid","false");
        field.closest('div').removeClass('form-text-invalid')
            .find('.info-required').remove();
    }

    /* --------------- fin gestion des champs de formulaires --------------- */

    /* ----------------- didacticiel ------------------------------ */

    /**
     didacticiel
     gathers via Ajax the tutorials, populates divs with them
     adds a navigation system between the tutorials
     @author stefd
     @param d : jQuery object pointing to the tutorials container
     @return null
     */

    var didacPages = 0; // nb of pages to be fetched
    var didacAvailableLanguages = {}; // available languages on the server
    function didacticiel(d) {
        // d = #didacticiel jQuery object
        if(d.length === 0) {
            return;
        }

        /** via ajax: gather each tutorial + its lang
         */

        jQuery.ajax({
                        type: "GET",
                        crossDomain:true,
                        url:didacticielServer + "/config.json",
                        dataType: "json",
                        success: function(oJSON) {
                            /** if success for each:
                             1. create navigation button + add behaviours
                             2. insert at appropriate DOM positions
                          all this is done in didacPopulate
                             */
                            didacPages = oJSON.nb_page;
                            didacAvailableLanguages = oJSON.language;
                            didacPopulate(0,d);
                        },
                        error: function (o) {
                            var str = '';
                            for ( var i in o ) {
                                str += i + " : " + o[i] + "\n";
                            }
                            jQuery('body').append("<pre>" + "impossible de faire la req json\n" + str + "</pre>");


                        }
                    });




    }

    /**
     didacPopulate
     helper function for didacticiel: populates, one by one
     @author stefd
     @param page : current page to fetch via ajax and to inject into the dom
     @param container : container to inject into
     @return null

     */
    function didacPopulate(page,container) {
        jQuery.ajax({
                        type: "GET",
                        crossDomain:true,
                        url: didacticielServer + "/d" + page + "/" + locale + "/index.html",
                        dataType: "html",
                        success: function(oHTML) {
                            var parent = container.parent();
                            var classStr = (page == 0) ? 'didac active' : 'didac';
                            var imgStr = '<img src="' + pathimg + 'didac-off.png" alt="' + lang[locale]['showdidac_page'] + page + '">';
                            if(page == 0) {
                                imgStr = imgStr.replace(/-off/,'-on');
                            }
                            container.append( jQuery('<div class="' + classStr + '" id="didac' + page + '" />').append(oHTML) );

                            if(parent.find('ul#didac-nav').length === 0) {
                                parent.append('<ul id="didac-nav" />');
                            }
                            parent.find('ul#didac-nav')
                                .append( '<li><a href="#" data-show="didac' + page + '">' + imgStr + '</a></li>' );
                            parent.find('a[data-show=didac' + page + ']').on("click",function(e){
                                if(container.find('div.active').attr('id') != 'didac' + page) {

                                    container
                                        // 1. hide all the divs
                                        .find('div.active')
                                        .attr("tabindex","-1")
                                        .fadeOut()
                                        .removeClass('active')
                                        .end()
                                        // 2. show current one
                                        .find('div#didac' + page)
                                        .attr("tabindex","0")
                                        .fadeIn()
                                        .addClass('active')
                                        .focus()
                                        .end()
                                        // scroll container back to top
                                        .scrollTop(0);

                                    // 3. update nav images
                                    parent.find('ul#didac-nav img[src*=on]').each(function(){
                                        jQuery(this).attr( "src", jQuery(this).attr("src").replace(/-on/,'-off') );
                                    });
                                    jQuery(this).find('img').eq(0).attr( "src", jQuery(this).find('img').eq(0).attr("src").replace(/-off/,'-on') );

                                }
                            });

                            if(page < didacPages-1) {
                                didacPopulate(page+1,container);
                            }
                        }
                    });
    }

    /* ----------------- fin didacticiel ------------------------------ */

})(); // fin protection, cf premiÃ¨re ligne