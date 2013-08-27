var SocialShare = {};

SocialShare.defaults = {
    clickEventType: 'mousedown'
    , callback: null        // allows for a callback function after each share item was clicked/successful
    , callbackCancel: null  // allows for a callback function if a share is cancelled (fb only right now)
    , text: ''          // general share text
    , twitterText: ''
    , pinText: ''
    , image: ''         // general share image
    , url: ''           // general share url
    , name: ''          // Used for the name param in facebook
    , twitterUrl: ''    // Allows for an over-ride url for twitter use
};

SocialShare.networks = {};
SocialShare.networks.FACEBOOK = 'Facebook';
SocialShare.networks.TWITTER = 'Twitter';
SocialShare.networks.PINTEREST = 'Pinterest';

SocialShare.options = {};

SocialShare.init = function (args) {
    SocialShare.options = jQuery.extend(this.defaults, args);
};


//
// A social sharing helper for all popular social networks
//  @param elements -- DOM elements to affect
//  @param method -- method name to call
//  @param args -- an functional arguments
//  @param options -- any additional options to use in place of the defaults 
//
var SocialShareHelper = function (elements, network, args)
{
    this.elements = elements;
    this.options = jQuery.extend(this.defaults, jQuery.extend(SocialShare.options, args) );
    this.network = network;
    this.attach(args);

}; SocialShareHelper.prototype = {
    defaults: {
        clickEventType: 'click'
        , callback: null        // allows for a callback function after each share item was clicked/successful
        , callbackCancel: null  // allows for a callback function if a share is cancelled (fb only right now)
        , text: ''          // general share text
        , image: ''         // general share image
        , url: ''           // general share url
        , name: ''          // Used for the name param in facebook
        , twitterUrl: ''    // Allows for an over-ride url for twitter use
    },

    attach: function (args) {

        //
        // attachs a callable method to the current $(this) element
        //
        if (this.network) {
            if (this['bind' + this.network + 'Share'])
                this['bind' + this.network + 'Share'](args);
            else if (console)
                console.log('SocialShareHelper: network "' + this.method + '" not found.');
        }
    },

    performCallback: function(args) {
        if (args.success) {
            if (this.options.callback) {
                this.options.callback(args);
            }
        } else {
            if (this.options.callbackCancel) {
                this.options.callbackCancel(args);
            }
        }
    },

    //
    // bind a facebook share to a DOM element
    //
    bindFacebookShare: function (args) {

        //
        // Initiates a share to facebook
        //
        args = $.extend(args, this.options);

        var self = this;
        this.elements.each(function (index, element) {
            $(element).unbind('click');
            $(element).bind('click', function () {
                FB.ui({
                    method: 'feed'
                    , link: args.url
                    , picture: args.image
                    , name: args.name
                    , caption: args.text
                }
                , function (response) {
                    self.performCallback({
                        success: (response && response.post_id) ? true : false
                        , network: SocialShare.networks.FACEBOOK
                    });
                });
            });
        });

        return this;
    },

    //
    // bind a twitter share to a DOM element
    //
   bindTwitterShare: function (args) {

       args = $.extend(args, this.options);

        var self = this;
        this.elements.each(function (index, element) {
            $(element).attr('target', '_blank');
            $(element).attr('href',
                "http://twitter.com/intent/tweet?source=webclient"
                + "&text=" + args.text + " " + args.url
            );
            $(element).bind('click', function () {
                self.performCallback({
                    success: true
                    , network: SocialShare.networks.TWITTER
                });
            });
        });

        return this;
    },

    
    //
    // bind a Pinterest share to a DOM element
    //
    bindPinterestShare: function (args) {

        args = $.extend(args, this.options);

        var self = this;
        this.elements.each(function (index, element) {
            $(element).attr('target', '_blank');
            $(element).attr('href',
                "http://pinterest.com/pin/create/button/"
                + "?url=" + args.url
                + "&media=" + args.image
                + "&description=" + args.text + " " + args.url
            );
            $(element).bind('click', function () {
                self.performCallback({
                    success: true
                    , network: SocialShare.networks.PINTEREST
                });
            });
        });

        return this;
    }
};

//
// jQuery Wrapper
//

//(function ($) {
    
    $.fn.bindFacebookShare = function (args) {
        new SocialShareHelper(this, SocialShare.networks.FACEBOOK, args);
        return this;
    };
    $.fn.bindTwitterShare = function (args) {
        new SocialShareHelper(this, SocialShare.networks.TWITTER, args);
        return this;
    };
    $.fn.bindPinterestShare = function (args) {
        new SocialShareHelper(this, SocialShare.networks.PINTEREST, args);
        return this;
    };

//})(jQuery);
