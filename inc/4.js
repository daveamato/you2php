/*! videojs-resolution-switcher - 2015-7-26
 * Copyright (c) 2016 Kasper Moskwiak
 * Modified by Pierre Kraft and Derk-Jan Hartman
 * Licensed under the Apache-2.0 license. */

(function() {
  /* jshint eqnull: true*/
  /* global require */
  'use strict';
  var videojs = null;
  if(typeof window.videojs === 'undefined' && typeof require === 'function') {
    videojs = require('video.js');
  } else {
    videojs = window.videojs;
  }

  (function(window, videojs) {
    var videoJsResolutionSwitcher,
      defaults = {
        ui: true
      };

    /*
     * Resolution menu item
     */
    var MenuItem = videojs.getComponent('MenuItem');
    var ResolutionMenuItem = videojs.extend(MenuItem, {
      constructor: function(player, options){
        options.selectable = true;
        // Sets this.player_, this.options_ and initializes the component
        MenuItem.call(this, player, options);
        this.src = options.src;

        player.on('resolutionchange', videojs.bind(this, this.update));
      }
    } );
    ResolutionMenuItem.prototype.handleClick = function(event){
      MenuItem.prototype.handleClick.call(this,event);
      this.player_.currentResolution(this.options_.label);
    };
    ResolutionMenuItem.prototype.update = function(){
      var selection = this.player_.currentResolution();
      this.selected(this.options_.label === selection.label);
    };
    MenuItem.registerComponent('ResolutionMenuItem', ResolutionMenuItem);

    /*
     * Resolution menu button
     */
    var MenuButton = videojs.getComponent('MenuButton');
    var ResolutionMenuButton = videojs.extend(MenuButton, {
      constructor: function(player, options){
        this.label = document.createElement('span');
        options.label = 'Quality';
        // Sets this.player_, this.options_ and initializes the component
        MenuButton.call(this, player, options);
        this.el().setAttribute('aria-label','Quality');
        this.controlText('Quality');

        if(options.dynamicLabel){
          videojs.addClass(this.label, 'vjs-resolution-button-label');
          this.el().appendChild(this.label);
        }else{
          var staticLabel = document.createElement('span');
          videojs.addClass(staticLabel, 'vjs-menu-icon');
          this.el().appendChild(staticLabel);
        }
        player.on('updateSources', videojs.bind( this, this.update ) );
      }
    } );
    ResolutionMenuButton.prototype.createItems = function(){
      var menuItems = [];
      var labels = (this.sources && this.sources.label) || {};

      // FIXME order is not guaranteed here.
      for (var key in labels) {
        if (labels.hasOwnProperty(key)) {
          menuItems.push(new ResolutionMenuItem(
            this.player_,
            {
              label: key,
              src: labels[key],
              selected: key === (this.currentSelection ? this.currentSelection.label : false)
            })
          );
        }
      }
      return menuItems;
    };
    ResolutionMenuButton.prototype.update = function(){
      this.sources = this.player_.getGroupedSrc();
      this.currentSelection = this.player_.currentResolution();
      this.label.innerHTML = this.currentSelection ? this.currentSelection.label : '';
      return MenuButton.prototype.update.call(this);
    };
    ResolutionMenuButton.prototype.buildCSSClass = function(){
      return MenuButton.prototype.buildCSSClass.call( this ) + ' vjs-resolution-button';
    };
    MenuButton.registerComponent('ResolutionMenuButton', ResolutionMenuButton);

    /**
     * Initialize the plugin.
     * @param {object} [options] configuration for the plugin
     */
    videoJsResolutionSwitcher = function(options) {
      var settings = videojs.mergeOptions(defaults, options),
          player = this,
          groupedSrc = {},
          currentSources = {},
          currentResolutionState = {};

      /**
       * Updates player sources or returns current source URL
       * @param   {Array}  [src] array of sources [{src: '', type: '', label: '', res: ''}]
       * @returns {Object|String|Array} videojs player object if used as setter or current source URL, object, or array of sources
       */
      player.updateSrc = function(src){
        //Return current src if src is not given
        if(!src){ return player.src(); }

        // Only add those sources which we can (maybe) play
        src = src.filter( function(source) {
          try {
            return ( player.canPlayType( source.type ) !== '' );
          } catch (e) {
            // If a Tech doesn't yet have canPlayType just add it
            return true;
          }
        });
        //Sort sources
        this.currentSources = src.sort(compareResolutions);
        this.groupedSrc = bucketSources(this.currentSources);
        // Pick one by default
        var chosen = chooseSrc(this.groupedSrc, this.currentSources);
        this.currentResolutionState = {
          label: chosen.label,
          sources: chosen.sources
        };

        player.trigger('updateSources');
        player.setSourcesSanitized(chosen.sources, chosen.label);
        player.trigger('resolutionchange');
        return player;
      };

      /**
       * Returns current resolution or sets one when label is specified
       * @param {String}   [label]         label name
       * @param {Function} [customSourcePicker] custom function to choose source. Takes 2 arguments: sources, label. Must return player object.
       * @returns {Object}   current resolution object {label: '', sources: []} if used as getter or player object if used as setter
       */
      player.currentResolution = function(label, customSourcePicker){
        if(label == null) { return this.currentResolutionState; }

        // Lookup sources for label
        if(!this.groupedSrc || !this.groupedSrc.label || !this.groupedSrc.label[label]){
          return;
        }
        var sources = this.groupedSrc.label[label];
        // Remember player state
        var currentTime = player.currentTime();
        var isPaused = player.paused();

        // Hide bigPlayButton
        if(!isPaused && this.player_.options_.bigPlayButton){
          this.player_.bigPlayButton.hide();
        }

        // Change player source and wait for loadeddata event, then play video
        // loadedmetadata doesn't work right now for flash.
        // Probably because of https://github.com/videojs/video-js-swf/issues/124
        // If player preload is 'none' and then loadeddata not fired. So, we need timeupdate event for seek handle (timeupdate doesn't work properly with flash)
        var handleSeekEvent = 'loadeddata';
        if(this.player_.techName_ !== 'Youtube' && this.player_.preload() === 'none' && this.player_.techName_ !== 'Flash') {
          handleSeekEvent = 'timeupdate';
        }
        player
          .setSourcesSanitized(sources, label, customSourcePicker || settings.customSourcePicker)
          .one(handleSeekEvent, function() {
            player.currentTime(currentTime);
            player.handleTechSeeked_();
            if(!isPaused){
              // Start playing and hide loadingSpinner (flash issue ?)
              player.play().handleTechSeeked_();
            }
            player.trigger('resolutionchange');
          });
        return player;
      };

      /**
       * Returns grouped sources by label, resolution and type
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      player.getGroupedSrc = function(){
        return this.groupedSrc;
      };

      player.setSourcesSanitized = function(sources, label, customSourcePicker) {
        this.currentResolutionState = {
          label: label,
          sources: sources
        };
        if(typeof customSourcePicker === 'function'){
          return customSourcePicker(player, sources, label);
        }
        player.src(sources.map(function(src) {
          return {src: src.src, type: src.type, res: src.res};
        }));
        return player;
      };

      /**
       * Method used for sorting list of sources
       * @param   {Object} a - source object with res property
       * @param   {Object} b - source object with res property
       * @returns {Number} result of comparation
       */
      function compareResolutions(a, b){
        if(!a.res || !b.res){ return 0; }
        return (+b.res)-(+a.res);
      }

      /**
       * Group sources by label, resolution and type
       * @param   {Array}  src Array of sources
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      function bucketSources(src){
        var resolutions = {
          label: {},
          res: {},
          type: {}
        };
        src.map(function(source) {
          initResolutionKey(resolutions, 'label', source);
          initResolutionKey(resolutions, 'res', source);
          initResolutionKey(resolutions, 'type', source);

          appendSourceToKey(resolutions, 'label', source);
          appendSourceToKey(resolutions, 'res', source);
          appendSourceToKey(resolutions, 'type', source);
        });
        return resolutions;
      }

      function initResolutionKey(resolutions, key, source) {
        if(resolutions[key][source[key]] == null) {
          resolutions[key][source[key]] = [];
        }
      }

      function appendSourceToKey(resolutions, key, source) {
        resolutions[key][source[key]].push(source);
      }

      /**
       * Choose src if option.default is specified
       * @param   {Object} groupedSrc {res: { key: [] }}
       * @param   {Array}  src Array of sources sorted by resolution used to find high and low res
       * @returns {Object} {res: string, sources: []}
       */
      function chooseSrc(groupedSrc, src){
        var selectedRes = settings['default']; // use array access as default is a reserved keyword
        var selectedLabel = '';
        if (selectedRes === 'high') {
          selectedRes = src[0].res;
          selectedLabel = src[0].label;
        } else if (selectedRes === 'low' || selectedRes == null || !groupedSrc.res[selectedRes]) {
          // Select low-res if default is low or not set
          selectedRes = src[src.length - 1].res;
          selectedLabel = src[src.length -1].label;
        } else if (groupedSrc.res[selectedRes]) {
          selectedLabel = groupedSrc.res[selectedRes][0].label;
        }

        return {res: selectedRes, label: selectedLabel, sources: groupedSrc.res[selectedRes]};
      }

      function initResolutionForYt(player){
        // Map youtube qualities names
        var _yts = {
          highres: {res: 1080, label: '1080', yt: 'highres'},
          hd1080: {res: 1080, label: '1080', yt: 'hd1080'},
          hd720: {res: 720, label: '720', yt: 'hd720'},
          large: {res: 480, label: '480', yt: 'large'},
          medium: {res: 360, label: '360', yt: 'medium'},
          small: {res: 240, label: '240', yt: 'small'},
          tiny: {res: 144, label: '144', yt: 'tiny'},
          auto: {res: 0, label: 'auto', yt: 'auto'}
        };
        // Overwrite default sourcePicker function
        var _customSourcePicker = function(_player, _sources, _label){
          // Note that setPlayebackQuality is a suggestion. YT does not always obey it.
          player.tech_.ytPlayer.setPlaybackQuality(_sources[0]._yt);
          player.trigger('updateSources');
          return player;
        };
        settings.customSourcePicker = _customSourcePicker;

        // Init resolution
        player.tech_.ytPlayer.setPlaybackQuality('auto');

        // This is triggered when the resolution actually changes
        player.tech_.ytPlayer.addEventListener('onPlaybackQualityChange', function(event){
          for(var res in _yts) {
            if(res.yt === event.data) {
              player.currentResolution(res.label, _customSourcePicker);
              return;
            }
          }
        });

        // We must wait for play event
        player.one('play', function(){
          var qualities = player.tech_.ytPlayer.getAvailableQualityLevels();
          var _sources = [];

          qualities.map(function(q){
            _sources.push({
              src: player.src().src,
              type: player.src().type,
              label: _yts[q].label,
              res: _yts[q].res,
              _yt: _yts[q].yt
            });
          });

          player.groupedSrc = bucketSources(_sources);
          var chosen = {label: 'auto', res: 0, sources: player.groupedSrc.label.auto};

          this.currentResolutionState = {
            label: chosen.label,
            sources: chosen.sources
          };

          player.trigger('updateSources');
          player.setSourcesSanitized(chosen.sources, chosen.label, _customSourcePicker);
        });
      }

      player.ready(function(){
        if( settings.ui ) {
          var menuButton = new ResolutionMenuButton(player, settings);
          player.controlBar.resolutionSwitcher = player.controlBar.el_.insertBefore(menuButton.el_, player.controlBar.getChild('fullscreenToggle').el_);
          player.controlBar.resolutionSwitcher.dispose = function(){
            this.parentNode.removeChild(this);
          };
        }
        if(player.options_.sources.length > 1){
          // tech: Html5 and Flash
          // Create resolution switcher for videos form <source> tag inside <video>
          player.updateSrc(player.options_.sources);
        }

        if(player.techName_ === 'Youtube'){
         // tech: YouTube
         initResolutionForYt(player);
        }
      });

    };

    // register the plugin
    videojs.plugin('videoJsResolutionSwitcher', videoJsResolutionSwitcher);
  })(window, videojs);
})();



/*jQuery collapser v3.0.0 - (c) 2020 Aakash Chakravarthy*/
!function(f){var t="collapser",a={mode:"words",speed:"slow",truncate:10,ellipsis:" ... ",controlBtn:null,showText:"Show more",hideText:"Hide text",showClass:"show-class",hideClass:"hide-class",atStart:"hide",blockTarget:"next",blockEffect:"fade",lockHide:!1,changeText:!1,beforeShow:null,afterShow:null,beforeHide:null,afterHide:null};function o(e,t){var o=this;o.o=f.extend({},a,t),o.e=f(e),o.init()}o.prototype={init:function(){var e=this;e.mode=e.o.mode,e.remaining=null,e.ctrlButton=f.isFunction(e.o.controlBtn)?e.o.controlBtn.call(e.e):f('<a href="#" data-ctrl></a>'),"lines"==e.mode&&e.e.wrapInner("<div>");var t=f.isFunction(e.o.atStart)?e.o.atStart.call(e.e):e.o.atStart;"hide"==(t=void 0!==e.e.attr("data-start")?e.e.attr("data-start"):t)?e.hide(0):e.show(0)},show:function(e){var t=this,o=t.e;t.collapsed=!1,void 0===e&&(e=t.o.speed),f.isFunction(t.o.beforeShow)&&t.o.beforeShow.call(t.e,t);function a(){f.isFunction(t.o.afterShow)&&t.o.afterShow.call(t.e,t)}if(o.find("[data-ctrl]").remove(),"block"==t.mode)t.blockMode(o,"show",e,a);else{var n="lines"==t.mode?o.children("div"):o,i=n.height();if("lines"==t.mode)n.height("auto");else{var l=n.data("collHTML");void 0!==l&&n.html(l)}var s=n.height();n.height(i),n.animate({height:s},e,function(){n.height("auto"),a()}),o.removeClass(t.o.hideClass).addClass(t.o.showClass),f.isFunction(t.o.controlBtn)||o.append(t.ctrlButton),t.ctrlButton.html(t.o.hideText)}t.bindEvent(),t.o.lockHide&&t.ctrlButton.remove()},hide:function(e){var t=this,o=t.e;t.collapsed=!0,void 0===e&&(e=t.o.speed),f.isFunction(t.o.beforeHide)&&t.o.beforeHide.call(t.e,t);function a(){f.isFunction(t.o.afterHide)&&t.o.afterHide.call(t.e,t)}if(o.find("[data-ctrl]").remove(),"chars"==t.mode||"words"==t.mode){var n=o.html(),i=t.getCollapsedHTML(n,t.mode,t.o.truncate);if(i){o.data("collHTML",n),o.html(i);var l=o.text();t.remaining=l.split("words"==t.mode?" ":"").length-t.o.truncate}else t.remaining=0}if("lines"==t.mode){var s=o.children("div"),r=s.outerHeight();if(0==(c=s.find("[data-col-char]")).length){var c=f('<span style="display:none" data-col-char>.</span>');s.prepend(c)}var d=c.height(),h=d*t.o.truncate+d/4;r<=h?(h="auto",t.remaining=0):(t.remaining=parseInt(Math.ceil((r-h)/d)),console.log(t.remaining)),s.css({overflow:"hidden",height:h})}"block"==t.mode&&t.blockMode(o,"hide",e,a),a(),"block"!=t.mode&&(o.removeClass(t.o.showClass).addClass(t.o.hideClass),!f.isFunction(t.o.controlBtn)&&0<t.remaining&&o.append(t.ctrlButton),t.ctrlButton.html(t.o.showText)),t.bindEvent()},blockMode:function(e,t,o,a){var n=this,i=["fadeOut","slideUp","fadeIn","slideDown"],l="fade"==n.o.blockEffect?0:1,s="hide"==t?i[l]:i[2+l];f.isFunction(n.o.blockTarget)?n.o.blockTarget.call(n.e)[s](o,a):f.fn[n.o.blockTarget]&&f(e)[n.o.blockTarget]()[s](o,a),"show"==t?(e.removeClass(n.o.showClass).addClass(n.o.hideClass),n.o.changeText&&e.text(n.o.hideText)):(e.removeClass(n.o.hideClass).addClass(n.o.showClass),n.o.changeText&&e.text(n.o.showText))},getCollapsedHTML:function(e,t,o){for(var a=!1,n=0,i=0,l=!0,s=0;s<=e.length;s++){if(char=e.charAt(s),"<"==char&&(a=!0),">"==char&&(a=!1),n==o){i=s,l=!1;break}a||("words"==t&&" "==char&&n++,"chars"==t&&n++)}if(l)return!1;var r=e.slice(0,i);return this.balanceTags(r)+'<span class="coll-ellipsis">'+this.o.ellipsis+"</span>"},balanceTags:function(e){e.lastIndexOf("<")>e.lastIndexOf(">")&&(e=e.substring(0,e.lastIndexOf("<")));var t=e.match(/<[^>]+>/g),o=new Array;for(tag in t)t[tag].search("/")<=0?o.push(t[tag]):1==t[tag].search("/")&&o.pop();for(;0<o.length;){var a=o.pop();e+="</"+(a=a.substring(1,a.search(/[>]/)))+">"}return e},bindEvent:function(){var t=this;("block"==t.mode?t.e:t.ctrlButton).off("click").on("click",function(e){e.preventDefault(),t.collapsed?t.show():t.hide()})}},f.fn[t]=function(e){return this.each(function(){f.data(this,t)||f.data(this,t,new o(this,e))})}}(jQuery,(window,document));


function copytext1() {
  var copyText = document.getElementById("inputs");
  copyText.select();
  document.execCommand("Copy");
 }

