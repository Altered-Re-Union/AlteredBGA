/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * altered implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * altered.js
 *
 * altered user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

 var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
 var debug = isDebug ? console.info.bind(window.console) : function () {};
 
 define([
   'dojo',
   'dojo/_base/declare',
   'ebg/core/gamegui',
   'ebg/counter',
   g_gamethemeurl + 'modules/js/Core/game.js',
   g_gamethemeurl + 'modules/js/Core/modal.js',
   g_gamethemeurl + 'modules/js/Players.js',
   g_gamethemeurl + 'modules/js/Cards.js',
   g_gamethemeurl + 'modules/js/Meeples.js',
   g_gamethemeurl + 'modules/js/StarterDecks.js',
 ], function (dojo, declare) {
   function openFullscreen() {
     var docElm = document.documentElement;
     if (docElm.requestFullscreen) {
       docElm.requestFullscreen();
     } else if (docElm.mozRequestFullScreen) {
       docElm.mozRequestFullScreen();
     } else if (docElm.webkitRequestFullScreen) {
       docElm.webkitRequestFullScreen();
     } else if (docElm.msRequestFullscreen) {
       docElm.msRequestFullscreen();
     }
   }
 
  function closeFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  }

  function renderDeckDifficultyStars(stars) {
    const filledCount = Math.max(0, Math.min(5, Number(stars) || 0));
    return `<div class="deck-details-stars"><p>${_('Difficulty:')}</p> ${Array.from({ length: 5 }, (_unused, index) =>
      `<span class="deck-star${index < filledCount ? ' filled' : ''}"></span>`
    ).join('')}</div>`;
  }

  return declare('bgagame.altered', [customgame.game, altered.players, altered.cards, altered.meeples, altered.starterDecks], {
     constructor: function () {
       this._inactiveStates = ['selectPrecoDeck', 'firstDayManaSelection', 'newDayManaSelection', 'gameEnd'];
       this._notifications = [
         ['updateInformations', 10],
         ['mediumMessage', 1000],
         ['midMessage', 1200],
         ['clearTurn', 200],
         ['refreshUI', 200],
         ['refreshHand', 200],
         ['updateInitialPrecoDeckSelection', 200],
         ['vsScreen', 100],
         ['setupPlayer', 3000],
         ['updateFirstDayManaSelection', 200],
         ['nightCleanup', null],
         ['cleanupCards', null],
         ['newFirstPlayer', null],
         ['switchPlayer', null],
         ['startDusk', 1200],
         ['endDusk', 900],
         ['passTurn', 800],
 
         ['addMeeples', null],
         ['slideMeeples', null],
         ['looseMeeples', null],
         ['setTerrainMarker', null],
 
         ['pDrawCards', null],
         ['drawCards', null, (notif) => notif.args.player_id == this.player_id],
         ['publicDrawCards', null],
         ['pDiscardCards', null],
         ['publicDiscard', null],
         ['discardCards', null, (notif) => notif.args.player_id == this.player_id],
         // ['discardCardsOnDisplay', null],
         ['playCard', null],
         ['supportEffect', null],
         ['moveStormToken', null],
         ['moveToHand', null],
         ['silentKill', 200],
         ['spellCleanup', null],
         ['invokeToken', null],
         ['afterYou', 1000],
         ['pay', 100],
         ['gainCounter', 1400],
         ['useCounter', 1400],
         ['deleteCounter', 500],
         ['shuffleDeck', 100],
         ['winTieBreaker', 50],
         ['startTiebreak', null],
         ['targetCards', 500],
         ['moveCard', null],
         ['newPhase', 1000],
         ['putOnDeck', null],
 
         ['tap', 800],
         ['ready', 800],
         ['publicReadyMana', 800, (notif) => notif.args.player_id == this.player_id],
         ['privateReadyMana', 800],
         ['untap', 500],
         ['updateTotalMana', 200],
         ['roll', 3000],
         ['revealHand', 1000],
         ['refreshCard', 500],
         ['endReveal', 1000],
 
         // TODO??
         ['blockAllExpeditions', 100],
       ];
 
       // Fix mobile viewport (remove CSS zoom)
       this.default_viewport = 'width=1000';
       this.biggestHeightLandscape = 0;
       this.cardStatuses = {};
 
       this._loadingComplete = false;
       this._fakeIndex = -1;
       this._diceIndex = 1;
       this._undoPossible = true;
       this._beginner = false;
       this._demoDeck = false;
       this._deckFormat = 'STANDARD';
 
       this.draggables = [];
       this.draggableCards = [];
       this.draggedCard = null;
       this.posBeforeDrag = null;
       this.droppableZone = null;
     },
     notif_midMessage(n) {},
 
     getSettingsSections() {
       return {
         layout: _('Layout'),
         //        playerBoard: _('Player Board/Panel'),
         gameFlow: _('Game Flow'),
         //        other: _('Other'),
       };
     },
 
     getSettingsConfig() {
       return {
         ////////////////////
         ///    LAYOUT    ///
         boardHeight: {
           default: 100,
           name: _('Board height'),
           type: 'slider',
           sliderConfig: {
             step: 3,
             padding: 0,
             range: {
               min: [80],
               max: [120],
             },
           },
           section: 'layout',
         },
         fitTo: {
           default: (isMobile, isTouchDevice) => (isTouchDevice ? 1 : 0),
           name: _('Lock mode fitting direction'),
           type: 'select',
           values: {
             0: _('Fit to height'),
             1: _('Fit to width'),
           },
           section: 'layout',
         },
         displayFullArt: {
           default: 0,
           name: _('Display full art'),
           type: 'select',
           values: {
             0: _('No'),
             1: _('Yes'),
           },
           section: 'layout',
         },
 
         // handLocation: {
         //   default: (isMobile, isTouchDevice) => (isTouchDevice ? 1 : 3),
         //   name: _('Hand of cards'),
         //   type: 'select',
         //   values: {
         //     0: _('On the board'),
         //     1: _('In a floating collapsible container'),
         //     2: _('In a floating collapsible container, opened when entering the table'),
         //   },
         //   section: 'layout',
         // },
         // cardScale: {
         //   default: 40,
         //   name: _('Card size in hand'),
         //   type: 'slider',
         //   sliderConfig: {
         //     step: 3,
         //     padding: 0,
         //     range: {
         //       min: [30],
         //       max: [80],
         //     },
         //   },
         //   section: 'layout',
         // },
 
         //////////////////////
         /// BOARD / PANELS ///
 
         //////////////////////
         ///// GAME FLOW //////
         confirmMode: { type: 'pref', prefId: 103, section: 'gameFlow' },
         confirmUndoableMode: { type: 'pref', prefId: 104, section: 'gameFlow' },
         restartButtons: {
           default: 1,
           name: _('Restart turn buttons'),
           type: 'select',
           attribute: 'undoButtons',
           values: {
             0: _('Only "Restart turn" button'),
             1: _('"Restart turn" and "Undo last step" buttons'),
             2: _('Only "Undo last step" button'),
           },
           section: 'gameFlow',
         },
         playerUndo: { type: 'pref', prefId: 152, section: 'gameFlow' },
 
         //////////////////////
         /////// OTHER ////////
         // sortableHand: {
         //   default: (isMobile, isTouchDevice) => (isTouchDevice ? 1 : 0),
         //   name: _('Sortable hand using dragndrop'),
         //   type: 'select',
         //   values: {
         //     0: _('Enabled'),
         //     1: _('Disabled'),
         //   },
         //   section: 'other',
         // },
       };
     },
 
     isFloatingHand() {
       return [1, 2].includes(parseInt(this.settings.handLocation));
     },
 
     openHand() {
       if (this.isFloatingHand()) {
         $('floating-hand-wrapper').dataset.open = 'hand';
       }
     },
 
     onLeavingFastMode() {
       this.updateLayout();
 
       this.orderedPlayers.forEach((player, i) => {
         let handContainer = $(`hand-${player.id}`);
         this.adjustHand(handContainer, i == 0 ? 'bottom' : 'top');
 
         if (player.id == this.player_id) {
           this.clearHandTransform($(`mana-cards-${player.id}`));
         }
         this.clearHandTransform($(`board-discard-${player.id}`));
         this.clearHandTransform($(`board-reserve-${player.id}`));
         this.clearHandTransform($(`board-landmark-${player.id}`));
       });
     },
 
     notif_updateInformations(n) {
       debug('Notif: updating some informations', n);
       // Biome totals
       if (n.args.biomes !== undefined) {
         this.gamedatas.biomes = n.args.biomes;
         this.updateBiomeTotals();
       }
       // Will move
       if (n.args.movements !== undefined) {
         this.gamedatas.movements = n.args.movements;
         this.updateMovements();
       }
       // Blocked expedition
       if (n.args.blockedExpeditions !== undefined) {
         this.gamedatas.blockedExpeditions = n.args.blockedExpeditions;
         this.updateBlockedExpeditions();
       }
       // Powers-blocked expeditions
       if (n.args.powersBlockedExpeditions !== undefined) {
         this.gamedatas.powersBlockedExpeditions = n.args.powersBlockedExpeditions;
         this.updatePowersBlockedExpeditions();
       }
       // Defenders
       if (n.args.defenders !== undefined) {
         this.gamedatas.defenders = n.args.defenders;
         this.updateDefenders();
       }
       if (n.args.reserveSlots !== undefined) {
         this.gamedatas.reserveSlots = n.args.reserveSlots;
         this.updateReserveSlots();
       }
       if (n.args.landmarkSlots !== undefined) {
         this.gamedatas.landmarkSlots = n.args.landmarkSlots;
         this.updateLandmarkSlots();
       }
     },
 
     /**
      * Setup:
      *	This method set up the game user interface according to current game situation specified in parameters
      *	The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
      *
      * Params :
      *	- mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
      */
     setup(gamedatas) {
       debug('SETUP', gamedatas);
       // Create a new div for "anytime" buttons
       dojo.place("<div id='anytimeActions' style='display:inline-block;float:right'></div>", $('generalactions'), 'after');
       // Create a new div for "subtitle"
       dojo.place("<div id='pagesubtitle'></div>", 'maintitlebar_content');
       // Create a new div for overlays
       $('left-side-wrapper').insertAdjacentHTML('beforeend', '<div id="altered-overlay"></div>');
       // Create a new div for storm overlays
       $('left-side-wrapper').insertAdjacentHTML(
         'beforeend',
         `<div id="focus-storm-overlay">
           <div id="focus-storm-top"></div>
           <div id="focus-storm-middle"></div>
           <div id="focus-storm-bottom"></div>          
         </div>`
       );
       $('day-indicator-wheel-inner').dataset.phase = gamedatas.phase;
 
       // Experimental
       $('altered-main-container').insertAdjacentElement('beforeend', $('page-title'));
       $('left-side').insertAdjacentHTML(
         'beforeend',
         `<div id="bga-help_buttons">
           <button id="toggle-focus-mode" class="bga-help_button"></button>
         </div>`
       );
 
       $('toggle-focus-mode').addEventListener('click', () => {
         document.body.classList.toggle('focus-board');
         if (document.body.classList.contains('focus-board')) {
           window.scrollTo(0, 0);
           this.biggestHeightLandscape = 0;
           // openFullscreen();
         } else {
           // closeFullscreen();
         }
         this.onGameUiWidthChange();
       });
 
       this.setupInfoPanel();
       this.initPreferences();
 
       // this.inherited(arguments);
       this.setupBoard();
       this.setupPlayers();
       this.setupCards();
       this.setupMeeples();
       // this.setupOAuth();
       // this.setupSortableHand();
 
       // Updatable informations
       this.updateBiomeTotals();
       this.updateMovements();
       this.updateBlockedExpeditions();
       this.updatePowersBlockedExpeditions();
       this.updateDefenders();
       this._undoPossible = gamedatas.undo;
       this._beginner = gamedatas.beginner;
       this._deckFormat = gamedatas.deckFormat || 'STANDARD';
       this._demoDeck = this._deckFormat === 'DEMO';
 
       this.inherited(arguments);
       this._tryCacheAccountConfiguredFromGamestateArgs();
     },
 
     openOverlay() {
       $('altered-overlay').classList.add('active');
       $('altered-board-overlay').classList.add('active');
       this.centerOverlay();
     },
     closeOverlay() {
       this.closeCurrentTooltip(false);
       $('altered-overlay').classList.remove('active');
       $('altered-board-overlay').classList.remove('active');
     },
     centerOverlay() {
       let h = $('page-title').getBoundingClientRect()['height'];
       $('altered-board-overlay').style.paddingTop = h + 'px';
     },
 
     addToggleOverlayButton() {
       let getText = () => ($('altered-overlay').classList.contains('active') ? _('Close overlay') : _('Open overlay'));
 
       this.addSecondaryActionButton('btnToggleOverlay', getText(), () => {
         $('altered-overlay').classList.toggle('active');
         $('altered-board-overlay').classList.toggle('active');
         $('btnToggleOverlay').innerHTML = getText();
       });
     },
 
     closeOverlayIfOpened() {
       this.closeOverlay();
       this.onChangeHandLocationSetting();
       $('altered-overlay-content').innerHTML = '';
     },
 
     setupSortableHand() {
       // TODO
       // if ($(`hand-${this.player_id}`)) {
       //   let that = this;
       //   this._sortableHand = Sortable.create($(`hand-${this.player_id}`), {
       //     onStart(evt) {
       //       let cardId = evt.item.id;
       //       let tooltip = that.tooltips[cardId];
       //       tooltip.close();
       //       if (tooltip.showTimeout != null) clearTimeout(tooltip.showTimeout);
       //       that._dragndropMode = true;
       //     },
       //     onEnd(evt) {
       //       that._dragndropMode = false;
       //       let cardIds = that._sortableHand.toArray();
       //       that.takeAction('actOrderCards', { cardIds: JSON.stringify(cardIds), lock: false }, false);
       //     },
       //     fallbackTolerance: 10,
       //     delay: 200,
       //     delayOnTouchOnly: true,
       //     touchStartThreshold: 10,
       //   });
       // }
     },
 
     /*
      * Create form to request access token from Google's OAuth 2.0 server.
      */
     setupOAuth() {
       // Google's OAuth 2.0 endpoint for requesting an access token
       let oauth2Endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
 
       // Create <form> element to submit parameters to OAuth 2.0 endpoint.
       let form = document.createElement('form');
       form.setAttribute('method', 'GET'); // Send as a GET request.
       form.setAttribute('action', oauth2Endpoint);
 
       // Parameters to pass to OAuth 2.0 endpoint.
       let params = {
         project_id: 'bga-altered',
         client_id: '516885558184-6amd2b1sma940uh2o7p3fbqocoh3qfkd.apps.googleusercontent.com',
         redirect_uri: 'https://localhost/index.php',
         response_type: 'code',
         scope: 'https://www.googleapis.com/auth/drive.metadata.readonly',
         include_granted_scopes: 'true',
         state: 'pass-through value',
       };
 
       let URL = oauth2Endpoint + '?';
       let urlParams = [];
       // Add form parameters as hidden input values.
       for (var p in params) {
         // var input = document.createElement('input');
         // input.setAttribute('type', 'hidden');
         // input.setAttribute('name', p);
         // input.setAttribute('value', params[p]);
         // form.appendChild(input);
         urlParams.push(p + '=' + params[p]);
       }
 
       let btn = document.createElement('a');
       btn.href = URL + urlParams.join('&');
       btn.target = '_blank';
 
       // let btn = document.createElement('button');
       btn.setAttribute('type', 'submit');
       btn.appendChild(document.createTextNode('OAuth'));
       btn.classList.add('bgabutton');
       btn.classList.add('bgabutton_red');
       // btn.style.position = 'absolute';
       // btn.style.top = '50%';
       // btn.style.left = '0px';
       btn.style.width = '100px';
       btn.style.zIndex = 1000;
       btn.style.fontSize = '20px';
       form.appendChild(btn);
 
       // Add form to page and submit it to open the OAuth 2.0 endpoint.
       // $('altered-board').appendChild(form);
       $('anytimeActions').appendChild(form);
 
       //      form.submit();
     },
 
     setupBoard() {
       let storm = this.gamedatas.storm;
       storm.forEach((stormCard, i) => {
         if (this.gamedatas.tieBreaker && i == 2) stormCard.cardId = 5;
 
         $('storm-container').insertAdjacentHTML(
           'beforeend',
           `<div class='storm-card-container' id='storm-card-container-${i}'>
             <div class='storm-card' data-id='${stormCard.cardId % 10}' data-flipped='${stormCard.rotated ? 1 : 0}'></div>
           </div>`
         );
       });
       if (this.gamedatas.tieBreaker) {
         $('ebd-body').dataset.tieBreaker = 1;
       }
 
       for (let i = 0; i < 8; i++) {
         $('storm-container').insertAdjacentHTML(
           'beforeend',
           `<div class='storm-space' id='storm-${i}'>
             <div class='storm-terrain-markers' id='storm-${i}-markers' data-x="${i}"></div>
             <div class='storm-slot' id='storm-${i}-opponent' data-x="${i}"></div>
             <div class='storm-slot' id='storm-${i}-player' data-x="${i}"></div>
           </div>`
         );
       }
 
       $('storm-container').insertAdjacentHTML('beforeend', '<div id="roll-dice-container"></div>');
 
       $('day-indicator-frame').insertAdjacentHTML('beforeend', `<div class="storm-text">${_('HERO')}</div>`);
       $('storm-end-frame').insertAdjacentHTML('beforeend', _('COMPANION'));
 
       $('day-indicator-frame').insertAdjacentHTML(
         'beforeend',
         `<div id='help-phases-button'>
           <svg><use href="#help-marker-svg" /></svg>
         </div>`
       );
       $('help-phases-button').addEventListener('click', () => this.openHelperModal());
     },
 
     // onChangeSortableHandSetting(v) {
     //   if (this._sortableHand) this._sortableHand.option('disabled', v == 1);
 
     //   this.ensureNoSortableHandOnTouchDevice();
     // },
 
     // ensureNoSortableHandOnTouchDevice() {
     //   if (this.isTouchDevice && this.settings && this.settings.sortableHand == 0 && this.isFloatingHand() && this._sortableHand) {
     //     this._sortableHand.option('disabled', true);
 
     //     this.showMessage(
     //       _(
     //         "Sortable hand with floating hand on touchscreen is disabled because it's buggy on many devices (can't click on card to select them). Sorry for the inconvenience."
     //       ),
     //       'info'
     //     );
     //   }
     // },
 
     onLoadingComplete() {
       this.updateLayout();
       document.fonts.ready.then(() => {
         document.querySelectorAll('.altered-card').forEach((oCard) => this.autofitCardFrame(oCard, true));
         this._loadingComplete = true;
       });
       this.inherited(arguments);
     },
 
     onScreenWidthChange() {
       if (this.settings) this.updateLayout();
     },
 
     onAddingNewUndoableStepToLog(notif) {
       if (!$(`log_${notif.logId}`)) return;
       let stepId = notif.msg.args.stepId;
       $(`log_${notif.logId}`).dataset.step = stepId;
       if ($(`dockedlog_${notif.mobileLogId}`)) $(`dockedlog_${notif.mobileLogId}`).dataset.step = stepId;
 
       if (
         this.gamedatas &&
         this.gamedatas.gamestate &&
         this.gamedatas.gamestate.args &&
         this.gamedatas.gamestate.args.previousSteps &&
         this.gamedatas.gamestate.args.previousSteps.includes(parseInt(stepId))
       ) {
         this.onClick($(`log_${notif.logId}`), () => this.undoToStep(stepId));
 
         if ($(`dockedlog_${notif.mobileLogId}`)) this.onClick($(`dockedlog_${notif.mobileLogId}`), () => this.undoToStep(stepId));
       }
     },
 
     undoToStep(stepId) {
       this.stopActionTimer();
       this.checkAction('actRestart');
       this.takeAction('actUndoToStep', { stepId }, false);
     },
 
     notif_clearTurn(n) {
       debug('Notif: restarting turn', n);
       this.cancelLogs(n.args.notifIds);
     },
 
     notif_refreshUI(n) {
       debug('Notif: refreshing UI', n);
 
       Object.keys(n.args.datas).forEach((value) => {
         this.gamedatas[value] = n.args.datas[value];
       });
       this.setupCards();
       this.setupMeeples();
       this.updatePlayersCounters();
     },
 
     notif_refreshHand(n) {
       debug('Notif: refreshing UI hand and mana', n);
       let cardIds = [];
       n.args.hand.forEach((card) => {
         if (!$(`card-${card.id}`)) {
           this.addCard(card);
         } else {
           $(`hand-${this.player_id}`).insertAdjacentElement('beforeend', $(`card-${card.id}`));
         }
         cardIds.push(card.id);
       });
       n.args.mana.forEach((card) => {
         if (!$(`card-${card.id}`)) {
           this.addCard(card);
         } else {
           $(`mana-cards-${this.player_id}`).insertAdjacentElement('beforeend', $(`card-${card.id}`));
         }
         cardIds.push(card.id);
       });
 
       // Destroy other cards
       [
         ...$(`mana-cards-${this.player_id}`).querySelectorAll('.altered-card'),
         ...$(`hand-${this.player_id}`).querySelectorAll('.altered-card'),
       ].forEach((oCard) => {
         if (!cardIds.includes(parseInt(oCard.getAttribute('data-id')))) {
           this.destroy(oCard);
         }
       });
     },
 
     onUpdateActionButtons(stateName, args) {
       // this.addPrimaryActionButton('test', 'test', () => this.testNotif());
       this.inherited(arguments);
     },
 
     testNotif() {
       let o = {
         uid: '658b59c2be6d0',
         type: 'vsScreen',
         log: '',
         args: {
           factions: {
             2322020: 'MU',
             2322021: 'BR',
           },
         },
         channelorig: '/table/t545303',
         gamenameorig: 'altered',
         time: 1703631299,
         move_id: 15,
         bIsTableMsg: true,
         table_id: '545303',
       };
       this.notif_vsScreen(o);
     },
 
     clearPossible() {
       dojo.empty('pagesubtitle');
 
       dojo.query('.selectedToDiscard').removeClass('selectedToDiscard');
       dojo.query('.selectedToKeep').removeClass('selectedToKeep');
 
       let toRemove = ['btnLaunchSpell'];
       toRemove.forEach((eltId) => {
         if ($(eltId)) $(eltId).remove();
       });
 
       if ($('popin_manaDisplay_subtitle')) {
         $('popin_manaDisplay_subtitle').remove();
       }
       if (this._manaModal && this._manaModal.isDisplayed()) {
         this._manaModal.hide();
       }
 
       this.disableAllDraging();
 
       this.inherited(arguments);
     },
 
     onEnteringState(stateName, args) {
       debug('Entering state: ' + stateName, args);
       if (this.isFastMode() && ![].includes(stateName)) return;
 
       if (args.type == 'activeplayer') {
         let pId1 = args.active_player,
           pId2 = this.getOpponent(pId1);
         $(`board-hero-${pId1}`).classList.add('active');
         $(`board-hero-${pId2}`).classList.remove('active');
       }
 
       if (args.args && args.args.descSuffix) {
         this.changePageTitle(args.args.descSuffix);
       }
 
       if (args.args && args.args.optionalAction) {
         let base = args.args.descSuffix ? args.args.descSuffix : '';
         this.changePageTitle(base + 'skippable');
       }
 
       // if (args.args && args.args.source) {
       //   if (this.gamedatas.gamestate.descriptionmyturn.search('{source}') === -1) {
       //     if (args.args.sourceId) {
       //       let card = this.getCardInfos(args.args.sourceId);
       //       let uid = this.registerCustomTooltip(this.tplCard(card, true));
 
       //       $('pagemaintitletext').insertAdjacentHTML(
       //         'beforeend',
       //         ` (<span class="ark-log-card-name" id="${uid}">${_(args.args.source)}</span>)`
       //       );
       //       this.attachRegisteredTooltips();
       //     } else {
       //       $('pagemaintitletext').insertAdjacentHTML('beforeend', ` (${_(args.args.source)})`);
       //     }
       //   }
       // }
 
       if (!this._inactiveStates.includes(stateName) && !this.isCurrentPlayerActive()) return;
 
        if (
        args.args &&
        (args.args.optionalAction || args.args.canPassWithoutSelection) && !args.args.automaticAction
      ) {
         this.addSecondaryActionButton(
           'btnPassAction',
           _('Pass'),
           () => this.takeAction('actPassOptionalAction'),
           'restartAction'
         );
       }
 
       // Undo last steps
       if (args.args && args.args.previousSteps) {
         args.args.previousSteps.forEach((stepId) => {
           let logEntry = $('logs').querySelector(`.log.notif_newUndoableStep[data-step="${stepId}"]`);
           if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));
 
           logEntry = document.querySelector(`.chatwindowlogs_zone .log.notif_newUndoableStep[data-step="${stepId}"]`);
           if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));
         });
       }
 
       // Restart turn button
       if (
         this._undoPossible &&
         args.args &&
         args.args.previousEngineChoices &&
         args.args.previousEngineChoices >= 1 &&
         !args.args.automaticAction
       ) {
         if (args.args && args.args.previousSteps) {
           let lastStep = Math.max(...args.args.previousSteps);
           if (lastStep > 0)
             this.addDangerActionButton('btnUndoLastStep', _('Undo last step'), () => this.undoToStep(lastStep), 'restartAction');
         }
 
         // Restart whole turn
         this.addDangerActionButton(
           'btnRestartTurn',
           _('Restart turn'),
           () => {
             this.stopActionTimer();
             this.takeAction('actRestart');
           },
           'restartAction'
         );
       }
 
       if (this.isCurrentPlayerActive() && args.args) {
         // Anytime buttons
         if (args.args.anytimeActions) {
           args.args.anytimeActions.forEach((action, i) => {
             let msg = action.desc;
             msg = msg.log ? this.fsr(msg.log, msg.args) : _(msg);
             msg = this.formatString(msg);
 
             this.addPrimaryActionButton(
               'btnAnytimeAction' + i,
               msg,
               () => this.takeAction('actAnytimeAction', { id: i }, false),
               'anytimeActions'
             );
           });
         }
       }
 
       // Call appropriate method
       var methodName = 'onEnteringState' + stateName.charAt(0).toUpperCase() + stateName.slice(1);
       if (this[methodName] !== undefined) this[methodName](args.args);
     },
 
     onEnteringStateGameEnd() {
       $('focus-storm-overlay').classList.remove('active');
     },
 
     notif_winTieBreaker(n) {
       debug('Notif: winning with tiebreaker', n);
       // TODO?
     },
 
     notif_newPhase(n) {
       debug('Notif: start new phase', n);
       let wheel = $('day-indicator-wheel-inner');
       let newVal = n.args.phaseId;
       let turn = parseInt(+wheel.dataset.phase / 5);
       if (newVal == 0) turn++;
       wheel.dataset.phase = turn * 5 + newVal;
 
       let angles = [0, -74, -140, -216, -290];
       wheel.style.transform = `rotate(${turn * -360 + angles[newVal]}deg)`;
     },
 
     notif_mediumMessage(n) {},
 
     /**
      * Caches `_private.accountConfigured` from current `gamedatas.gamestate.args` so checks work even when
      * `onEnteringStateFetchDecks` runs before `onEnteringStateSelectPrecoDeck` (e.g. restored client state).
      */
     _tryCacheAccountConfiguredFromGamestateArgs() {
       const a = this.gamedatas && this.gamedatas.gamestate && this.gamedatas.gamestate.args;
       if (a && a._private && typeof a._private.accountConfigured !== 'undefined') {
         this._cachedAccountConfiguredForApiDecks = !!a._private.accountConfigured;
       }
     },
      
     isSingletonDeckFormat() {
       return this._deckFormat.indexOf('SINGLETON') !== -1;
     },

     isDemoDeckFormat() {
       return this._deckFormat === 'DEMO';
     },

     allowCustomDeckFormat() {
       return !this._beginner && !this.isDemoDeckFormat();
     },
 
     /**
      * Deck picker / fetchDecks: whether this player's BGA account is linked / ready for custom API decks.
      * Sources: last selectPrecoDeck args, live gamestate args, cache (see setup + _tryCacheAccountConfiguredFromGamestateArgs).
      */
     isAccountConfiguredForDeckPicker() {
       const privFromLast = this._lastSelectPrecoDeckArgs && this._lastSelectPrecoDeckArgs._private;
       const privFromGs =
         this.gamedatas && this.gamedatas.gamestate && this.gamedatas.gamestate.args && this.gamedatas.gamestate.args._private;
       const priv = privFromLast || privFromGs;
       if (priv && typeof priv.accountConfigured !== 'undefined' && priv.accountConfigured !== null) {
         return !!priv.accountConfigured;
       }
       if (typeof this._cachedAccountConfiguredForApiDecks !== 'undefined') {
         return !!this._cachedAccountConfiguredForApiDecks;
       }
       const p = this.gamedatas && this.gamedatas.players && this.gamedatas.players[this.player_id];
       if (p) {
         if (typeof p.accountConfigured !== 'undefined' && p.accountConfigured !== null) {
           return !!p.accountConfigured;
         }
         if (typeof p.linked_game_account !== 'undefined') return !!p.linked_game_account;
         if (typeof p.isGameAccountLinked !== 'undefined') return !!p.isGameAccountLinked;
       }
       return true;
     },
 
     /**
      * When entering `fetchDecks`, optional `args.accountConfigured` from `clientState(..., args)` wins
      * (set to true only after passing the deck-picker check).
      */
     _resolveAccountConfiguredForApiDecksMode(fetchDecksClientArgs) {
       const a = fetchDecksClientArgs || {};
       if (typeof a.accountConfigured !== 'undefined') {
         return !!a.accountConfigured;
       }
       this._tryCacheAccountConfiguredFromGamestateArgs();
       return this.isAccountConfiguredForDeckPicker();
     },
 
     /**
      * Replaces overlay content when game account is not linked (same structure as "Choose your deck").
      * Adjust title/description strings here when finalizing copy.
      */
     showRandomDeckAssignedContent(args) {
      const { factionDisplayNames } = this._getDeckFactionBannerConfig();
      const randomFaction = args._private.randomDeck && args._private.randomDeck.faction;
      const factionLabel = randomFaction ? factionDisplayNames[randomFaction] || randomFaction : null;
      const message = factionLabel
        ? dojo.string.substitute(_('You have been assigned a random ${faction} deck'), { faction: factionLabel })
        : _('You have been assigned a random deck');

      $('altered-overlay-content').innerHTML = `
        <h2>${_('Random deck')}</h2>
        <p>${message}</p>
      `;
      this.openOverlay();
      this.addSecondaryActionButton('btnCancel', _('Cancel'), () =>
        this.takeAction('actCancelPrecoDeckSelection', {}, false)
      );
     },

     showRandomDeckAssignedContent(args) {
      const { factionDisplayNames } = this._getDeckFactionBannerConfig();
      const randomFaction = args._private.randomDeck && args._private.randomDeck.faction;
      const factionLabel = randomFaction ? factionDisplayNames[randomFaction] || randomFaction : null;
      const message = factionLabel
        ? dojo.string.substitute(_('You have been assigned a random ${faction} deck'), { faction: factionLabel })
        : _('You have been assigned a random deck');

      $('altered-overlay-content').innerHTML = `
        <h2>${_('Random deck')}</h2>
        <p>${message}</p>
      `;
      this.openOverlay();
      this.addSecondaryActionButton('btnCancel', _('Cancel'), () =>
        this.takeAction('actCancelPrecoDeckSelection', {}, false)
      );
     },

     showAccountNotConfiguredDeckPickerContent() {
       ['btnConfirm', 'btnConfirmFooter', 'btnCancel', 'btnCancelFooter', 'btnBackFromCustom', 'btnToggleOverlay'].forEach((id) => {
         if ($(id)) $(id).remove();
       });
       $('altered-overlay-content').innerHTML = `
         <h2>${_('There\'s a lot of things ongoing !')}</h2>
         <div id='deck-wizard' class='deck-wizard-step-2 account-not-configured-screen'>
           <div id='account-not-configured-desc'>
            <p>${_(`To retrieve your decks, you'll now need an account on one of the officially recognized platforms of altered Re:Union`)}.</p>
            <p>
             <a class="account-not-configured-link" href="https://altered.re/pages/login" target="_blank" rel="noopener noreferrer">
                ${_('Create an account')}
              </a>
              ${_(' to build or import your decks on any acknowledged deckbuilder.')}
            </p>
            <p>${_('Don\'t forget to connect your account on BGA afterward. You can do so here :')} <a class="account-not-configured-link" href="https://boardgamearena.com/preferences?section=account" target="_blank" rel="noopener noreferrer">BGA Accounts</a></p>
            <p>${_('If you think you are correctly connected to BGA with your altered Re:Union account, then you might not have any valid decks for the format you have selected.')}</p>
           </div>
         </div>
       `;
      this.openOverlay();
      if (!this.isSingletonDeckFormat()) {
        this.addSecondaryActionButton('btnBackFromCustom', _('Back'), () => {
          if ($('btnBackFromCustom')) $('btnBackFromCustom').remove();
          this._customDeckSelectedFaction = null;
          this.onEnteringStateSelectPrecoDeck(this._lastSelectPrecoDeckArgs);
        });
      }
      this.addToggleOverlayButton();
    },
 
     /**
      * Starts the deck list fetch from RE:Union. Account binding is validated by the fetch result
      * (see onEnteringStateFetchDecks): failure or empty deck list shows the account / linking message.
      */
    requestFetchDecksOrAccountConfigurationMessage() {
      this._customDeckSelectedFaction = null;
      this.clientState('fetchDecks', _('Connecting to RE:Union to fetch your decks'), {});
    },

    _getAllApiFactions() {
      return ['AX', 'BR', 'MU', 'LY', 'OR', 'YZ'];
    },

    _getCustomDeckBannerFactions() {
      return ['ALL', 'AX', 'BR', 'LY', 'MU', 'OD', 'YZ'];
    },

    _isAllFactionsBanner(faction) {
      return faction === 'ALL';
    },

    _apiFactionFromBannerFaction(faction) {
      return faction === 'OD' ? 'OR' : faction;
    },

    _apiFactionsFromBannerFaction(faction) {
      if (this._isAllFactionsBanner(faction)) {
        return this._getAllApiFactions();
      }
      return [this._apiFactionFromBannerFaction(faction)];
    },

    _bannerFactionFromApiFaction(faction) {
      const normalized = faction === 'OR' ? 'OD' : faction || 'AX';
      return normalized.length > 2 ? normalized.substring(0, 2) : normalized;
    },

    _bannerFactionFromRequestFactions(requestFactions) {
      if (!requestFactions || !Array.isArray(requestFactions)) return null;
      if (this._apiRequestFactionsMatch(requestFactions, this._getAllApiFactions())) {
        return 'ALL';
      }
      if (requestFactions.length === 1) {
        return this._bannerFactionFromApiFaction(requestFactions[0]);
      }
      return null;
    },

    _apiRequestFactionsMatch(actual, expected) {
      if (!actual || !expected || actual.length !== expected.length) return false;
      const sortedActual = [...actual].sort();
      const sortedExpected = [...expected].sort();
      return sortedActual.every((faction, index) => faction === sortedExpected[index]);
    },

    _getDeckFactionBannerConfig() {
      if (!this._deckFactionBannerConfig) {
        const bannerFactionMap = {
          AX: 'AXIOM',
          BR: 'BRAVOS',
          LY: 'LYRA',
          MU: 'MUNA',
          OD: 'ORDIS',
          YZ: 'YZMIR',
        };
        const factionDisplayNames = {
          ALL: _('All'),
          AX: _('Axiom'),
          BR: _('Bravos'),
          LY: _('Lyra'),
          MU: _('Muna'),
          OD: _('Ordis'),
          YZ: _('Yzmir'),
        };
        const bannerImagePath = (faction, isSelected) => {
          let canonicalFaction = bannerFactionMap[faction] || 'AXIOM';
          let themeUrl = typeof g_gamethemeurl !== 'undefined' ? g_gamethemeurl : '';
          return `${themeUrl}img/Factions/${canonicalFaction}-faction-banner${isSelected ? '-selected' : ''}.png`;
        };
        const preloadFactionBannerImages = () => {
          if (!this._preloadedFactionBanners) {
            this._preloadedFactionBanners = {};
          }
          const canonicalFactions = ['AXIOM', 'BRAVOS', 'LYRA', 'MUNA', 'ORDIS', 'YZMIR'];
          let themeUrl = typeof g_gamethemeurl !== 'undefined' ? g_gamethemeurl : '';
          canonicalFactions.forEach((canonicalFaction) => {
            [false, true].forEach((isSelected) => {
              const url = `${themeUrl}img/Factions/${canonicalFaction}-faction-banner${isSelected ? '-selected' : ''}.png`;
              if (this._preloadedFactionBanners[url]) return;
              const img = new Image();
              img.src = url;
              this._preloadedFactionBanners[url] = true;
            });
          });
        };
        preloadFactionBannerImages();
        this._deckFactionBannerConfig = { bannerFactionMap, factionDisplayNames, bannerImagePath };
      }
      return this._deckFactionBannerConfig;
    },

    _renderDeckFactionBanners(factions, selectedFaction, onSelect) {
      const { factionDisplayNames, bannerImagePath } = this._getDeckFactionBannerConfig();
      if (!$('deck-faction-banners')) return;
      $('deck-selected-faction-title').innerHTML =
        factionDisplayNames[selectedFaction] || selectedFaction;
      $('deck-faction-banners').innerHTML = '';
      factions.forEach((faction) => {
        let isSelectedFaction = selectedFaction == faction;
        if (this._isAllFactionsBanner(faction)) {
          $('deck-faction-banners').insertAdjacentHTML(
            'beforeend',
            `<div class='deck-faction-banner-option deck-faction-banner-all ${isSelectedFaction ? 'selected' : ''}' id='banner-faction-ALL'>
              <span class='deck-faction-banner-all-label'>${factionDisplayNames.ALL}</span>
            </div>`
          );
        } else {
          $('deck-faction-banners').insertAdjacentHTML(
            'beforeend',
            `<div class='deck-faction-banner-option ${isSelectedFaction ? 'selected' : ''}' id='banner-faction-${faction}'>
              <img src='${bannerImagePath(faction, isSelectedFaction)}' alt='${factionDisplayNames[faction] || faction}' />
              <span class='deck-faction-banner-name'>${factionDisplayNames[faction] || faction}</span>
            </div>`
          );
        }
        this.onClick(`banner-faction-${faction}`, () => {
          if (selectedFaction == faction) return;
          onSelect(faction);
        });
      });
    },

    ///////////////////////////////////////////////////////////
    //  ____
    // |  _ \ _ __ ___  ___ ___  ___
    // | |_) | '__/ _ \/ __/ _ \/ __|
    // |  __/| | |  __/ (_| (_) \__ \
    // |_|   |_|  \___|\___\___/|___/
    ///////////////////////////////////////////////////////////

    onEnteringStateSelectPrecoDeck(args) {
       this._awaitingAPIReturn = false;
       if (!args._private) return;
       if ($('btnBackFromCustom')) {
         $('btnBackFromCustom').remove();
       }
       if ($('btnBackToSources')) {
         $('btnBackToSources').remove();
       }
       this._lastSelectPrecoDeckArgs = args;
       if (typeof args.demoDeck !== 'undefined') {
         this._demoDeck = !!args.demoDeck;
       }
       if (args._private && typeof args._private.accountConfigured !== 'undefined') {
         this._cachedAccountConfiguredForApiDecks = !!args._private.accountConfigured;
       }
       let deckNumber = args._private.selection;
       if (deckNumber == 'API') {
         this.showAPIDeckDetails(args);
         return;
       }
       if (deckNumber == 'random') {
        this.showRandomDeckAssignedContent(args);
        return;
      }
       if (deckNumber != null && args._private.starterDeck) {
         this.showAPIDeckDetails({ _private: { API: args._private.starterDeck } });
         return;
       }
 
      if (this.isSingletonDeckFormat()) {
        const gsName = this.gamedatas.gamestate && this.gamedatas.gamestate.name;
        if (gsName === 'fetchDecks' || gsName === 'chooseFetchedDeck') {
          return;
        }
        if ($('overlay-deck-selection') || $('api-fetch-decks')) {
          return;
        }
        this.requestFetchDecksOrAccountConfigurationMessage();
        return;
      }

      let decks = args._private.decks || [];
       let previousDeck = decks.find((deck) => '' + deck.deckNumber == '' + deckNumber) || null;
       const getFactionGroup = (faction) => faction;
      let factions = [...new Set(decks.map((deck) => getFactionGroup(deck.faction)))];
      let defaultFaction = previousDeck ? getFactionGroup(previousDeck.faction) : null;
      const { factionDisplayNames } = this._getDeckFactionBannerConfig();
 
       let previousWizardState = this._deckWizardState || {};
       this._deckWizardState = {
         selectedFaction: previousWizardState.selectedFaction || defaultFaction,
         selectedDeckNum:
           previousWizardState.selectedDeckNum !== undefined
             ? previousWizardState.selectedDeckNum
             : previousDeck
               ? previousDeck.deckNumber
               : null,
       };
 
       const removeButton = (id) => {
         if ($(id)) $(id).remove();
       };
 
       const renderFooterAction = (selectedDeck) => {
         removeButton('btnConfirm');
         removeButton('btnConfirmFooter');
         removeButton('btnCancel');
         removeButton('btnCancelFooter');
 
         if (!selectedDeck) return;
 
         if (deckNumber === null || '' + deckNumber != '' + selectedDeck.deckNumber) {
           this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
             this.takeAction('actSelectPrecoDeck', { choice: selectedDeck.deckNumber }, false)
           );
           if ($('deck-wizard-footer')) {
             this.addPrimaryActionButton(
               'btnConfirmFooter',
               _('Confirm'),
               () => this.takeAction('actSelectPrecoDeck', { choice: selectedDeck.deckNumber }, false),
               'deck-wizard-footer'
             );
           }
         } else {
           this.addSecondaryActionButton('btnCancel', _('Cancel'), () =>
             this.takeAction('actCancelPrecoDeckSelection', {}, false)
           );
           if ($('deck-wizard-footer')) {
             this.addSecondaryActionButton(
               'btnCancelFooter',
               _('Cancel'),
               () => this.takeAction('actCancelPrecoDeckSelection', {}, false),
               'deck-wizard-footer'
             );
           }
         }
       };
 
       const renderStep2Preconfigured = () => {
 
         const starterDecks = this.getStarterDecks();
 
         let selectedDeck = null;
         if (
           this._deckWizardState.selectedFaction === null ||
           this._deckWizardState.selectedFaction === undefined
         ) {
           this._deckWizardState.selectedFaction = factions[0];
         }
         let filteredDecks = decks.filter(
           (deck) =>
             getFactionGroup(deck.faction) == this._deckWizardState.selectedFaction
         );
 
         $('altered-overlay-content').innerHTML = `
           <h2 class='deck-selection-title'>${_('Choose your deck')}</h2>
           <div id='deck-wizard' class='deck-wizard-step-2'>
             <div id='deck-selected-faction-title'></div>
             <div id='deck-faction-banners'></div>
             ${
               !this.allowCustomDeckFormat()
                 ? ''
                : `<div id='deck-source-toggle'>
              <button class='deck-source-toggle-button bgabutton bgabutton_blue' id='deck-source-custom'>${_('Custom')}</button>
            </div>`
             }
             <div id='overlay-deck-container'></div>
             <div id='overlay-deck-details'></div>
             <div id='deck-wizard-footer'></div>
           </div>
         `;
         let selectedFactionName = factionDisplayNames[getFactionGroup(this._deckWizardState.selectedFaction)] ||
             this._deckWizardState.selectedFaction;
        this._renderDeckFactionBanners(factions, this._deckWizardState.selectedFaction, (faction) => {
          this._deckWizardState.selectedFaction = faction;
          this._deckWizardState.selectedDeckNum = null;
          debug('Selected faction: ' + faction);
          renderStep2Preconfigured();
        });

        if (this.allowCustomDeckFormat()) {
            // this.onClick('deck-source-random', () => {
            //   this.takeAction('actSelectRandomDeck', { faction: 'ALL' }, false);
            // });
            this.onClick('deck-source-custom', () => this.requestFetchDecksOrAccountConfigurationMessage());
         }
 
         filteredDecks.forEach((deck) => {
           let deckLabel = selectedFactionName;
           if (deck.hero && deck.hero.properties && deck.hero.properties.name) {
             deckLabel = deck.hero.properties.name;
           } else {
             debug("No hero name found for deck: " + JSON.stringify(deck));
           }
           $('overlay-deck-container').insertAdjacentHTML(
             'beforeend',
             `<div id='deck-option-${deck.deckNumber}' class='deck-option'>
               <div id='deck-card-holder-${deck.deckNumber}' class='deck-card-holder'></div>
               <div class='deck-option-name'>${deckLabel}</div>
             </div>`
           );
           this.addCard(deck.hero, `deck-card-holder-${deck.deckNumber}`);
           $(`card-${deck.hero.id}`).classList.add('no-frame');
           $(`card-${deck.hero.id}`).dataset.deckNumber = deck.deckNumber;
         });
 
         const selectDeck = (deck) => {
           if (selectedDeck !== null) {
             $(`card-${selectedDeck.hero.id}`).classList.remove('selected');
           }
           selectedDeck = deck;
           this._deckWizardState.selectedDeckNum = deck.deckNumber;
           $(`card-${selectedDeck.hero.id}`).classList.add('selected');
 
           $('overlay-deck-details').innerHTML = '';
 
           const maybeSelectedStarter = starterDecks[getFactionGroup(deck.faction)].find((deck) => deck.deckId == selectedDeck.deckId);
 
           if (maybeSelectedStarter != null) {
             const detailFaction = getFactionGroup(deck.faction);
             const heroKey = (deck.hero && (deck.hero.type || deck.hero.id)) || deck.faction;
             const heroThumbnail =
               deck.hero && deck.hero.thumbnail !== undefined
                 ? deck.hero.thumbnail
                 : deck.hero && deck.hero.properties && deck.hero.properties.thumbnail !== undefined
                   ? deck.hero.properties.thumbnail
                   : 0;
             $('overlay-deck-details').insertAdjacentHTML(
               'beforeend',
               `<div class='deck-details' data-faction='${detailFaction}' data-hero='${heroKey}' data-thumbnail='${heroThumbnail}'>
               <div class='faction-banner' data-faction='${detailFaction}'></div>
               <h3>${deck.hero.properties.name} by ${maybeSelectedStarter.author}</h3>
               ${renderDeckDifficultyStars(maybeSelectedStarter.stars)}
               <div class='deck-details-content'>
                <p class='deck-details-hook'>${maybeSelectedStarter.hook || ''}</p>
                <p class='deck-details-description'><span class='deck-details-title'>${_('Gameplan:')}</span> ${maybeSelectedStarter.description || ''}</p>
                <p class='deck-details-keycards'><span class='deck-details-title'>${_('Key opening Hand Cards:')}</span> ${maybeSelectedStarter.keycards || ''}</p>
               </div>
             </div>`
             );
           }
 
           renderFooterAction(selectedDeck);
         };
 
         filteredDecks.forEach((deck) => {
           this.onClick(`deck-option-${deck.deckNumber}`, () => selectDeck(deck));
           this.onClick(`card-${deck.hero.id}`, () => selectDeck(deck));
         });
 
         if (this._deckWizardState.selectedDeckNum !== null) {
           let previouslySelected = filteredDecks.find(
             (deck) => '' + deck.deckNumber == '' + this._deckWizardState.selectedDeckNum
           );
           if (previouslySelected) {
             selectDeck(previouslySelected);
             $(`card-${previouslySelected.hero.id}`).classList.add('keep');
           } else {
             renderFooterAction(null);
           }
         } else {
           renderFooterAction(null);
         }
       };
 
       const renderWizard = () => {
         removeButton('btnConfirm');
         removeButton('btnConfirmFooter');
         removeButton('btnCancel');
         removeButton('btnCancelFooter');
         renderStep2Preconfigured();
       };
 
       renderWizard();
       this.openOverlay();
       this.addToggleOverlayButton();
     },
 
     onLeavingStateSelectPrecoDeck() {
       this._deckWizardState = null;
       this.closeOverlay();
       $('altered-overlay-content').innerHTML = '';
     },
 
     notif_updateInitialPrecoDeckSelection(n) {
       debug('Notif: update initial preco deck selection', n);
       this.clearPossible();
       this.updatePageTitle();
       this.onEnteringStateSelectPrecoDeck(n.args.args);
     },
 
     ////////////////////////
     // FETCHING DECKS
     ////////////////////////
     onEnteringStateFetchDecks(args) {
       args = args || {};
       this._customDeckSelectedFaction = null;
       this.addCancelStateBtn();
       this._awaitingAPIReturn = true;
 
       const requestPayload = Object.assign({}, args);
       delete requestPayload.accountConfigured;
 
       const onFetchDecksFailedOrEmpty = () => {
         if (!this._awaitingAPIReturn) return;
         this._awaitingAPIReturn = false;
         this.clearClientState();
         this.showAccountNotConfiguredDeckPickerContent();
       };

       this.takeAction('actLoadAPIDecks', { request: JSON.stringify(requestPayload), lock: false }, false)
         .then((response) => {
           if (!this._awaitingAPIReturn) return;
           const data = response && response.data;
           const decks = data && data.decks;
           if (!data || !Array.isArray(decks) || decks.length === 0) {
             onFetchDecksFailedOrEmpty();
             return;
           }
           this.clientState('chooseFetchedDeck', _('Choose one of your deck'), data);
         })
         .catch(() => {
           onFetchDecksFailedOrEmpty();
         });
 
       $('altered-overlay-content').innerHTML = '';
       $('altered-overlay-content').insertAdjacentHTML(
         'beforeend',
         `
         <h2>${_('Fetching your decks from Re:Union')}</h2>
         <div id='api-fetch-decks' class='fetching'>
           <div id='api-loader' class="spinning-loader"></div>
           <div id="api-error"></div>
         </div>`
       );
       this.openOverlay();
     },
 
    ////////////////////////
    // CHOOSE DECKS
    ////////////////////////
    reloadFetchedDecks(overrides = {}) {
      if (this._awaitingAPIReturn) return;

      this._apiRequest = Object.assign({}, this._apiRequest, overrides);
      const strRequest = JSON.stringify(this._apiRequest);

      this._awaitingAPIReturn = true;
      $('api-error').innerHTML = '';
      if ($('overlay-deck-selection')) {
        $('overlay-deck-selection').classList.add('fetching');
      }
      this.takeAction('actLoadAPIDecks', { request: strRequest, lock: false }, false).then((response) => {
        if (!this._awaitingAPIReturn) return;

        if ($('overlay-deck-selection')) {
          $('overlay-deck-selection').classList.remove('fetching');
        }
        let args = response.data;
        args.update = true;
        this.clientState('chooseFetchedDeck', _('Choose one of your deck'), args);
      });
    },

    selectCustomDeckFaction(faction) {
      this._customDeckSelectedFaction = faction;
      this._deckContentAPI = null;
      if ($('btnConfirmDeck')) {
        $('btnConfirmDeck').classList.add('disabled');
      }
      this.reloadFetchedDecks({
        factions: this._apiFactionsFromBannerFaction(faction),
        page: 1,
      });
    },

    changeDeckPage(page) {
      this.reloadFetchedDecks({ page });
    },
 
     onEnteringStateChooseFetchedDeck(args) {
       this.addCancelStateBtn();
       this._awaitingAPIReturn = false;
      this._apiRequest = args.request;

      const bannerFactions = this._getCustomDeckBannerFactions();
      let selectedFaction =
        this._customDeckSelectedFaction ||
        this._bannerFactionFromRequestFactions(args.request && args.request.factions) ||
        bannerFactions[0];
      this._customDeckSelectedFaction = selectedFaction;

      const expectedApiFactions = this._apiFactionsFromBannerFaction(selectedFaction);
      const requestMatches =
        args.request &&
        args.request.factions &&
        this._apiRequestFactionsMatch(args.request.factions, expectedApiFactions);

      let isUpdateOnly = args.update || false;
      const hasDeckSelectionChrome =
        $('overlay-deck-selection') && $('prev-page') && $('deck-list');

      if (!requestMatches && !isUpdateOnly) {
        $('altered-overlay-content').innerHTML = `
        <div id='overlay-deck-selection' class='fetching'>
          <h2>${_('Choose your deck')}</h2>
          <div id="api-loader" class='spinning-loader'></div>
          <div id="api-error"></div>
        </div>`;
        this.openOverlay();
        this._apiRequest.factions = expectedApiFactions;
        this.reloadFetchedDecks({ factions: expectedApiFactions, page: 1 });
        return;
      }
       if (!isUpdateOnly || !hasDeckSelectionChrome) {
         $('altered-overlay-content').innerHTML = '';
         $('altered-overlay-content').insertAdjacentHTML(
           'beforeend',
           `
         <div id='overlay-deck-selection'>
           <h2 class='deck-selection-title'>${_('Choose your deck')}</h2>
           <div id="api-loader" class='spinning-loader'></div>
           <div id="api-error"></div>
           <div id='deck-selected-faction-title'></div>
           <div id='deck-faction-banners'></div>
           <div id="deck-search-holder">
             <form>
               <input type="text" placeholder="..." id="deck-search-input" />
               <button type="submit">
                 <i class="fa6 fa6-search"></i>
               </button>
             </form>
           </div>
           <div id='deck-list'></div>
           <div id="pagination-holder">
             <div id="prev-page">
               <i class="fa6 fa6-arrow-left"></i>
               ${_('Previous')}
             </div>
             <ul id="deck-pages"></ul>
             <div id="next-page">
               ${_('Next')}
               <i class="fa6 fa6-arrow-right"></i>  
             </div>
           </div>
         </div>`
         );
       }

      if ($('deck-faction-banners')) {
        this._renderDeckFactionBanners(bannerFactions, selectedFaction, (faction) => {
          if (this._customDeckSelectedFaction === faction) return;
          this.selectCustomDeckFaction(faction);
        });
      }

       // PAGINATION
       let current = parseInt(args.pagination.current);
       let last = args.pagination.last == '' ? 1 : parseInt(args.pagination.last);
 
       let pages = [1];
       // Previous
       let previous = current - 1;
       $('prev-page').classList.toggle('disabled', args.pagination.previous === '');
       if (previous > 0 && !pages.includes(previous)) pages.push(previous);
       // Current
       if (!pages.includes(current)) pages.push(current);
       // next
       let next = current + 1;
       $('next-page').classList.toggle('disabled', args.pagination.next === '');
       if (next < last && !pages.includes(next)) pages.push(next);
       // Last
       if (!pages.includes(last)) pages.push(last);
 
       if (args.pagination.previous !== '') {
         this.onClick(`prev-page`, () => this.changeDeckPage(previous));
       }
       if (args.pagination.next !== '') {
         this.onClick(`next-page`, () => this.changeDeckPage(next));
       }
 
       $('deck-pages').innerHTML = '';
       for (let i = 0; i < pages.length; i++) {
         let page = pages[i],
           isCurrent = page == current;
         $('deck-pages').insertAdjacentHTML(
           'beforeend',
           `<li class='page-link ${isCurrent ? 'current' : ''}' id='page-${page}'>${page}</li>`
         );
         if (i + 1 < pages.length && pages[i + 1] > page + 1) {
           $('deck-pages').insertAdjacentHTML('beforeend', `<li class='separator'>...</li>`);
         }
 
         if (!isCurrent) {
           this.onClick(`page-${page}`, () => this.changeDeckPage(page));
         }
       }
 
       this.onClick('api-error', () => ($('api-error').innerHTML = ''));
 
       // Confirm button
       this._deckContentAPI = null;
       this.addPrimaryActionButton(
         'btnConfirmDeck',
         _('Confirm'),
         () => {
           this.takeAction('actConfirmAPIDeck', { method: 'post', deckContent: JSON.stringify(this._deckContentAPI) }, false);
         },
         'overlay-deck-selection'
       );
       $('btnConfirmDeck').classList.add('disabled');
       this.openOverlay();
 
       // Thumbnails
      const { factionDisplayNames } = this._getDeckFactionBannerConfig();
      const factionNames = Object.assign({ OR: _('Ordis') }, factionDisplayNames);
       let hand = _('hand');

       let selected = null;
       $(`deck-list`).innerHTML = '';
       args.decks.forEach((deck) => {
         let factionName = factionNames[deck.faction];
         $(`deck-list`).insertAdjacentHTML(
           'beforeEnd',
           `<div id='deck-${deck.apiId}' class='deck-thumbnail' data-faction="${deck.faction}" data-thumbnail="${deck.heroThumbnail}">
             <div class="spinning-loader"></div>
             <div class='deck-name'>${deck.deckName}</div>
             <div class='deck-hero-name'>${deck.heroName}</div>
             <div class='deck-properties'>
               <div class='deck-faction'>${factionName}</div>
               <div class='deck-card-count'>
                 <svg><use href="#cards-svg" /></svg>
                 ${deck.cardCount}
               </div>
             </div>
           </div>`
         );
 
         this.onClick(`deck-${deck.apiId}`, () => {
           if (this._awaitingAPIReturn) return;
 
           if (selected) {
             $(`deck-${selected}`).classList.remove('selected');
           }
           selected = deck.apiId;
           $(`deck-${selected}`).classList.add('fetching');
           $('btnConfirmDeck').classList.add('disabled');
 
           this._awaitingAPIReturn = true;
           $('api-error').innerHTML = '';
           this.takeAction('actGetDeckInfos', { deckNumber: JSON.stringify(deck.apiId), lock: false }, false).then((response) => {
             let deckContent = response.data;
             this._deckContentAPI = deckContent;
             this._awaitingAPIReturn = false;
             $(`deck-${selected}`).classList.remove('fetching');
             $(`deck-${selected}`).classList.add('selected');
             $('btnConfirmDeck').classList.remove('disabled');
           });
         });
       });
     },
 
     showAPIDeckDetails(args) {
       let deck = args._private.API;
       $('altered-overlay-content').innerHTML = '';
       $('altered-overlay-content').insertAdjacentHTML(
         'beforeend',
         `
         <h2>${_('Your deck:')} ${deck.deckName}</h2>
         <div id='overlay-APIdeck-details'>
           <div id="deck-hero"></div>
           <div id="deck-cards"></div>
         </div>
       `
       );
       this.openOverlay();
 
       this.addCard({ id: '-hero', properties: deck.cards.hero.card.properties }, 'deck-hero');
       $(`card--hero`).insertAdjacentHTML('beforeend', `<div class='faction-banner' data-faction='${deck.faction}'></div>`);
 
       Object.entries(deck.cards).forEach(([i, card]) => {
         if (i == 'hero') return;
 
         let id = 'preview-' + i;
         this.addCard({ id, properties: card.card.properties }, 'deck-cards');
         $(`card-${id}`).querySelector('.card-frame').dataset.copies = card.n;
       });
 
       this.addSecondaryActionButton('btnCancel', _('Cancel'), () => this.takeAction('actCancelPrecoDeckSelection', {}, false));
     },
 
     //////////////////////////////
     // HANDLING API ERRORS
     showMessage() {
       if (!this._awaitingAPIReturn || arguments[0].indexOf('API ERROR###') < 0) {
         return this.inherited(arguments);
       } else {
         let l = arguments[0].split('###');
         this.handleAPIError(l[1]);
         return true;
       }
     },
 
     handleAPIError(l) {
       this._awaitingAPIReturn = false;
       let fetchingElt = $('altered-overlay-content').querySelector('.fetching');
       if (fetchingElt) fetchingElt.classList.remove('fetching');
 
       // Initial deck list fetch: same UX as empty list (account / linking guidance)
       if (this.gamedatas.gamestate.name == 'fetchDecks') {
         this.clearClientState();
         this.showAccountNotConfiguredDeckPickerContent();
         return;
       }
 
       if ($('api-error')) {
         $('api-error').innerHTML = _(l);
       }
     },
 
     notif_vsScreen(n) {
       debug('Notif: VS screen', n);
       this.closeOverlayIfOpened();
       $('altered-overlay-content').innerHTML = '';
       // $('altered-overlay-content').insertAdjacentHTML(
       //   'beforeend',
       //   `<div id='vs-left'>
       //       MUNA
       //   </div>
       //   <div id='vs-container'></div>
       //   <div id='vs-right'>
       //     LYRA
       //   </div>`
       // );
     },
 
     //////////////////////////////////////////////////////
     //  _   _                 ____
     // | \ | | _____      __ |  _ \  __ _ _   _
     // |  \| |/ _ \ \ /\ / / | | | |/ _` | | | |
     // | |\  |  __/\ V  V /  | |_| | (_| | |_| |
     // |_| \_|\___| \_/\_/   |____/ \__,_|\__, |
     //                                    |___/
     //////////////////////////////////////////////////////
 
     onEnteringStateNewDayManaSelection(args) {
       if (!args._private) return;
 
       // first day, handle differently since it's multiactive
       if (!args.canPass) {
         this.onEnteringStateFirstDayManaSelection(args);
         return;
       }
 
       console.error('SHOULD NOT HAPPEN !!');
     },
 
     onEnteringStateFirstDayManaSelection(args) {
       debug('onEnteringStateFirstDayManaSelection');
       this.openHand();
       let n = args._private.n;
 
       let cardIds = null;
       if (!$('overlay-hand-container')) {
         $('altered-overlay-content').innerHTML = '';
         $('altered-overlay-content').insertAdjacentHTML(
           'beforeend',
           `
           <h2>${_('Choose your starting mana cards')}</h2>
           <p>${_('Selected cards will join your mana pool')}</p>
           <div id='overlay-hand-container'></div>
           <div id='overlay-new-day-counter-wrapper' class='invalid'>
             <span id='overlay-new-day-counter'>0</span>
             /
             ${n}
           </div>
         `
         );
 
         $('overlay-hand-container').insertAdjacentElement('beforeend', $(`hand-${this.player_id}`));
         this.clearHandTransform($(`hand-${this.player_id}`));
         this.openOverlay();
       }
       this.addToggleOverlayButton();
 
       // Already made a selection => allow to cancel it
       if (args._private.selection != null) {
         args._private.selection.forEach((cardId) => {
           $(`card-${cardId}`).classList.add('selectedToMana');
         });
         $('overlay-new-day-counter').innerHTML = 3;
         $('overlay-new-day-counter-wrapper').classList.remove('invalid');
 
         // Remove confirm button
         if ($('btnConfirmManaSelection')) $('btnConfirmManaSelection').remove();
 
         // Cancel buttons
         this.addSecondaryActionButton('actCancelFirstDayManaSelection', _('Cancel'), () =>
           this.takeAction('actCancelFirstDayManaSelection', {}, false)
         );
         $('altered-overlay-content').insertAdjacentHTML(
           'beforeend',
           `<a href="#" class="action-button bgabutton bgabutton_blue" id="btnCancelManaSelection">${_('Cancel')}</a>`
         );
         this.onClick('btnCancelManaSelection', () => {
           this.takeAction('actCancelFirstDayManaSelection', {}, false);
         });
       }
       // No selection yet => let the user click on it
       else {
         // Remove confirm button
         if ($('btnCancelManaSelection')) $('btnCancelManaSelection').remove();
 
         // Confirm button
         if (!$('btnConfirmManaSelection')) {
           $('altered-overlay-content').insertAdjacentHTML(
             'beforeend',
             `<a href="#" class="action-button bgabutton bgabutton_blue disabled" id="btnConfirmManaSelection">${_('Confirm')}</a>`
           );
           this.onClick('btnConfirmManaSelection', () => {
             this.takeAction('actFirstDayManaSelection', { cardIds: JSON.stringify(cardIds) });
           });
         }
 
         this.onSelectNCards(args._private.cards, {
           n,
           class: 'selectedToMana',
           confirmText: _('Confirm Mana'),
           updateCallback: (selectedElements) => {
             cardIds = selectedElements;
             $('overlay-new-day-counter').innerHTML = selectedElements.length;
             $('btnConfirmManaSelection').classList.toggle('disabled', selectedElements.length != n);
             $('overlay-new-day-counter-wrapper').classList.toggle('invalid', selectedElements.length != n);
           },
           callback: (selectedElements, ignoredElements) =>
             this.takeAction('actFirstDayManaSelection', { cardIds: JSON.stringify(selectedElements) }),
         });
       }
     },
 
     onLeavingStateFirstDayManaSelection() {
       this.closeOverlayIfOpened();
     },
 
     notif_updateFirstDayManaSelection(n) {
       this.clearPossible();
       this.updatePageTitle();
       this.onEnteringStateFirstDayManaSelection(n.args.args);
     },
 
     ////////////////////////////////////////
     //  _____             _
     // | ____|_ __   __ _(_)_ __   ___
     // |  _| | '_ \ / _` | | '_ \ / _ \
     // | |___| | | | (_| | | | | |  __/
     // |_____|_| |_|\__, |_|_| |_|\___|
     //              |___/
     ////////////////////////////////////////
 
     addActionChoiceBtn(choice, disabled = false) {
       if ($('btnChoice' + choice.id)) return;
 
       let desc = '';
       if (Array.isArray(choice.description)) {
         desc = choice.description.map((s) => this.translate(s)).join(' ');
       } else {
         desc = this.translate(choice.description);
       }
       desc = this.formatString(desc);
 
       // Add source if any
       let source = _(choice.source ? choice.source : '');
 
       if (source != '') {
         desc += ` (${source})`;
       }
 
       this.addSecondaryActionButton(
         'btnChoice' + choice.id,
         desc,
         disabled
           ? () => {}
           : () => {
               this.askConfirmation(choice.irreversibleAction, () => this.takeAction('actChooseAction', { id: choice.id }));
             }
       );
       if (disabled) {
         $(`btnChoice${choice.id}`).classList.add('disabled');
       }
       if (choice.description.args && choice.description.args.bonus_pentagon) {
         $(`btnChoice${choice.id}`).classList.add('withbonus');
       }
       if (choice.sourceId) {
         // TODO?
         let card = this.getCardInfos(choice.sourceId);
         if ($(`card-${card.id}`) !== null) {
           if (!this.isMobile()) {
             this.addCustomTippyTooltip(`btnChoice${choice.id}`, this.tplCardTooltip(card), {
               disablingParentClasses: ['mana-modal', 'no-tooltip'],
               forceRecreate: true,
             });
           }
           $(`btnChoice${choice.id}`).setAttribute('sourceId', choice.sourceId);
           $(`btnChoice${choice.id}`).addEventListener('mouseenter', function (event) {
             source = $(`card-${event.target.getAttribute('sourceId')}`).classList.toggle('selectable', true);
           });
           $(`btnChoice${choice.id}`).addEventListener('mouseleave', function (event) {
             source = $(`card-${event.target.getAttribute('sourceId')}`).classList.toggle('selectable', false);
           });
           // source = this.fsr('${card_name}', { i18n: ['card_name'], card_name: _(card.name), card_id: card.id });
         }
       }
     },
 
     onEnteringStateResolveChoice(args) {
       Object.values(args.choices).forEach((choice) => this.addActionChoiceBtn(choice, false));
       Object.values(args.allChoices).forEach((choice) => this.addActionChoiceBtn(choice, true));
     },
 
     onEnteringStateImpossibleAction(args) {
       this.addActionChoiceBtn(
         {
           choiceId: 0,
           description: args.desc,
         },
         true
       );
     },
 
     addConfirmTurn(args, action) {
       this.addPrimaryActionButton('btnConfirmTurn', _('Confirm'), () => {
         this.stopActionTimer();
         this.takeAction(action);
       });
 
       const OPTION_CONFIRM = 103;
       let n = args.previousEngineChoices;
       let timer = Math.min(10 + 2 * n, 20);
       this.startActionTimer('btnConfirmTurn', timer, this.prefs[OPTION_CONFIRM].value);
     },
 
     onEnteringStateConfirmTurn(args) {
       this.addConfirmTurn(args, 'actConfirmTurn');
     },
 
     onEnteringStateConfirmPartialTurn(args) {
       this.addConfirmTurn(args, 'actConfirmPartialTurn');
     },
 
     onEnteringStatePay(args) {
       payMana = (i) => {
         return () => this.takeAtomicAction('actPay', [i]);
       };
       for (i = 0; i <= Math.min(args.mana, args.maximum); i++) {
         this.addPrimaryActionButton('btnMana' + i, this.formatString('{' + i + '}'), payMana(i));
       }
     },
 
     askConfirmation(warning, callback) {
       if (warning === false || this.prefs[104].value == 0) {
         callback();
       } else {
         //        let msg = warning === true ? _('drawing card(s) from the deck or the discard') : warning;
         let msg =
           warning === true
             ? _(
                 "If you take this action, you won't be able to undo past this step because you will either roll a dice, draw card(s) from the deck or the discard, or someone else is going to make a decision"
               )
             : warning;
         this.confirmationDialog(
           msg,
           // this.fsr(
           //   _("If you take this action, you won't be able to undo past this step because of the following reason: ${msg}"),
           //   { msg }
           // ),
           () => {
             callback();
           }
         );
       }
     },
 
     // Generic call for Atomic Action that encode args as a JSON to be decoded by backend
     takeAtomicAction(action, args, warning = false) {
       if (!this.checkAction(action)) return false;
 
       this.askConfirmation(warning, () =>
         this.takeAction('actTakeAtomicAction', { actionName: action, actionArgs: JSON.stringify(args) }, false)
       );
     },
 
     /*########################
     ##########################
     ###### DRAG N DROP #######
     ##########################
     ########################*/
 
     /*
      * Turn off the draggable cards
      */
     disableAllDraging() {
       Object.keys(this.draggables).forEach((cardId) => ($(`card-${cardId}`).draggable = false));
       this.draggableCards = [];
       this.draggables = {};
       this.draggableCallback = () => {};
       dojo.query('.droppable').removeClass('droppable');
     },
 
     /*
      * (Init) and enable draggable for given cards
      * param : cards
      *    cardId1 : zones
      *    cardId2 : zones
      *    ...
      */
     makeCardsDraggable(cards, callback) {
       this.draggableCallback = callback;
       this.draggableCards = [];
       this.draggables = {};
       let dropzones = [];
       Object.entries(cards).forEach(([cardId, locations]) => {
        // No available zones
        if (locations.length == 0) return;
 
        let zones = {};
        locations.forEach((location) => {
          zones[location] = {
            el: $(`board-${location}-${this.player_id}`),
            playLocation: location,
          };
        });
        Object.values(zones).forEach((zone) => {
          if (!dropzones.some((z) => z.el == zone.el)) {
            dropzones.push(zone);
          }
        });
        this.draggableCards[cardId] = zones;
        if (!this.draggables[cardId]) this.initDraggableCard(cardId);
      });
 
       // Attach event to dropzones
      dropzones.forEach((zone) => {
        let dropzone = zone.el;
 
         // ENTER/OVER
         this.connect(dropzone, 'dragenter', (event) => {
           if (!dropzone.classList.contains('droppable')) return;
 
           event.preventDefault();
           dropzone.classList.add('dragged-over');
         });
         this.connect(dropzone, 'dragover', (event) => {
           if (!dropzone.classList.contains('droppable')) return;
 
           event.preventDefault();
           dropzone.classList.add('dragged-over');
         });
 
         // LEAVE
         this.connect(dropzone, 'dragleave', (event) => {
           if (!dropzone.classList.contains('droppable')) return;
 
           event.preventDefault();
           dropzone.classList.remove('dragged-over');
         });
 
         // DROP
         this.connect(dropzone, 'drop', (event) => {
           if (!dropzone.classList.contains('droppable')) return;
 
           this.onDropCard(dropzone, event);
         });
       });
     },
 
     /*
      * Init draggable : create the draggable object and listen event
      */
     initDraggableCard(cardId) {
       let id = `card-${cardId}`,
         oCard = $(id);
       this.connect(oCard, 'dragstart', (evt) => this.onStartDraggingCard(cardId, evt));
       this.connect(oCard, 'dragend', (evt) => this.onEndDraggingCard(cardId, evt));
       oCard.draggable = true;
       this.draggables[cardId] = oCard;
     },
 
     /*
      * When starting to drag => highlight the drop possibilities
      */
     onStartDraggingCard(cardId, event) {
       debug('DragStart', cardId);
       this.closeCurrentTooltip(false);
       this._dragndropMode = true;
       const selectedItem = event.target;
       this.wait(100).then(() => selectedItem.classList.add('drag-active'));
       event.dataTransfer.setData('text/plain', cardId);
       event.dataTransfer.effectAllowed = 'move';
       event.dataTransfer.dropEffect = 'move';
 
       Object.values(this.draggableCards[cardId]).forEach((zone) => {
        zone.el.classList.add('droppable');
       });
     },
 
     /*
      * When we stop dragging => clear highlights
      */
     onEndDraggingCard(cardId, event) {
       debug('DragStop', cardId);
       this._dragndropMode = false;
       const selectedItem = event.target;
       selectedItem.classList.remove('drag-active');
 
      Object.values(this.draggableCards[cardId]).forEach((zone) => {
        zone.el.classList.remove('droppable');
        zone.el.classList.remove('dragged-over');
      });
     },
 
    onDropCard(dropZone, event) {
      event.preventDefault();
      event.stopPropagation();

      const cardId = event.dataTransfer.getData('text/plain');
      let playLocation = null;
      Object.values(this.draggableCards[cardId] || {}).forEach((zone) => {
        if (zone.el == dropZone) {
          playLocation = zone.playLocation;
        }
      });
      if (playLocation == null) return;

      dropZone.classList.remove('dragged-over');
      debug('DragDrop', playLocation, cardId);
      this.draggableCallback(cardId, playLocation);
    },
 
     ///////////////////////////////////////
     //     _        _   _
     //    / \   ___| |_(_) ___  _ __  ___
     //   / _ \ / __| __| |/ _ \| '_ \/ __|
     //  / ___ \ (__| |_| | (_) | | | \__ \
     // /_/   \_\___|\__|_|\___/|_| |_|___/
     ///////////////////////////////////////
    isTempleLandmarkPlay(cardId) {
      const card = this.getCardInfos(parseInt(cardId));
      return (
        card?.properties?.costTemple > 0 &&
        !(card.properties?.subtypes || []).includes('landmark')
      );
    },

    getPlayLocationLabel(cardId, location, defaultLabel) {
      if (location == 'landmark' && this.isTempleLandmarkPlay(cardId)) {
        return _('Temple (Landmark)');
      }
      return defaultLabel;
    },

    clearLandmarkTempleHighlight() {
      const landmark = $(`board-landmark-${this.player_id}`);
      if (landmark) {
        landmark.classList.remove('selectable', 'droppable', 'dragged-over');
      }
    },

     unselectIfNeeded() {
       let oCard = $(`hand-${this.player_id}`).querySelector('.selected');
       if (!oCard) {
         oCard = $(`board-reserve-${this.player_id}`).querySelector('.selected');
       }
       if (!oCard) {
         oCard = $(`board-limbo-${this.player_id}`).querySelector('.selected');
       }
      this.clearLandmarkTempleHighlight();
       if (!oCard) return;
 
       oCard.style.transform = oCard.backup.transform;
       oCard.style.left = oCard.backup.left;
       oCard.style.top = oCard.backup.top;
       oCard.style.zIndex = null;
       //        this.wait(400).then(() => oCard.classList.remove('selected'));
     },
 
     onEnteringStateChooseAssignment(args) {
       let t = args._private;
 
       if (t.play) {
         Object.keys(t.play).forEach((cardId) => {
           // ALREADY SELECTED CARD
           if (cardId == args.cardId) {
             this.wait(250).then(() => {
               this.onClick('altered-board-me', () => {
                 this.unselectIfNeeded();
                 this.clearClientState();
               });
 
               this.onClick(`card-${cardId}`, () => {
                 this.unselectIfNeeded();
                 this.clearClientState();
               });
             });
           }
           // OTHER CARD
           else {
             this.onClick(`card-${cardId}`, () => {
               this.unselectIfNeeded();
               let supportPossible = t.hasOwnProperty('support') ? t.support.includes(parseInt(cardId)) : false;
               this.clientState(
                 'chooseAssignmentLocation',
                 supportPossible ? _('What do you want to do with that card?') : _('Where do you want to play that card?'),
                 {
                   play: t.play,
                   support: t.support,
                   tap: t.tap,
                   cardId,
                   supportPossible,
                 }
               );
             });
           }
         });
 
         // DRAG N DROP
         this.makeCardsDraggable(t.play, (cardId, location) => this.takeAtomicAction('actPlay', [cardId, location]));
       }
 
       if (t.support) {
         t.support.forEach((cardId) => {
           if (t.hasOwnProperty('play') && Object.keys(t.play).includes(`${cardId}`)) return;
           if ($(`card-${cardId}`).classList.contains('selected')) return;
           console.log('test2');
 
           this.onClick(`card-${cardId}`, () => {
             this.unselectIfNeeded();
 
             this.clientState('chooseAssignmentLocation', _('Where do you want to play that card?'), {
               play: t.play,
               support: t.support,
               tap: t.tap,
               cardId,
               supportPossible: t.hasOwnProperty('support') ? t.support.includes(parseInt(cardId)) : false,
             });
           });
         });
       }
 
       if (t.tap) {
         t.tap.forEach((cardId) => {
           this.unselectIfNeeded();
           this.onClick(`card-${cardId}`, () => this.takeAtomicAction('actTap', [cardId]));
         });
       }
       if (args.additionalAction == false) {
         // Pass turn button
         this.addDangerActionButton(
           'btnPass',
           _('Pass'),
           () => {
             this.unselectIfNeeded();
             this.takeAtomicAction('actPass', []);
           },
           'restartAction'
         );
       }
     },
 
     onEnteringStateChooseAssignmentLocation(args) {
       this.addSecondaryActionButton(
         'btnCancel',
         _('Cancel'),
         () => {
           this.unselectIfNeeded();
           this.clearClientState();
         },
         'restartAction'
       );
 
       this.addCancelStateBtn();
       if (!args.hasOwnProperty('clientState') || args.clientState == true) {
         // this.addCancelStateBtn();
         this.onEnteringStateChooseAssignment({
           cardId: args.cardId,
           _private: {
             play: args.play,
             support: args.support,
             tap: args.tap,
           },
         });
       }
 
       // Mark card as selected
       let cardId = args.cardId;
       oCard = $(`card-${cardId}`);
       oCard.classList.add('selected');
      let supportIcon = oCard.querySelector('.card-support-icon');
      if (args.supportPossible && supportIcon) {
        this.onClick(supportIcon, () => {
          this.takeAtomicAction('actSupport', [cardId]);
        });
      }
       // Backup previous pos and transform
       oCard.backup = {
         transform: oCard.style.transform,
         left: oCard.style.left || '0px',
         top: oCard.style.top || '0px',
       };
 
       // Slide it using css transition, unless parent is reserve
       let limbo = $(`board-limbo-${this.player_id}`);
       oCard.style.transform = 'scale(1.2) rotate(0rad) translateY(0px)';
 
       if (oCard.parentNode.classList.contains('player-board-reserve')) {
         this.slide(oCard, limbo, {
           duration: 100,
           changeParent: false,
           attach: false,
           zIndexKeep: true,
           phantom: false,
           clearPos: false,
         });
       } else {
         let oHand = $(`hand-${this.player_id}`);
         oCard.style.left = limbo.offsetLeft - oHand.offsetLeft + 'px';
         oCard.style.top = limbo.offsetTop - oHand.offsetTop + 'px';
       }
       let onChooseLocation = (location) => {
         return () => this.takeAtomicAction('actPlay', [cardId, location]);
       };
 
       const names = {
         stormLeft: _('Hero side'),
         stormRight: _('Companion side'),
         landmark: _('Landmark'),
         reserve: _('Reserve'),
         limbo: _('Spell'),
         stormLeft_scout: _('Scout Hero side'),
         stormRight_scout: _('Scout Companion side'),
         landmark_temple: _('Temple (Landmark)'),
       };
 
       if (args.play[cardId] != undefined) {
         args.play[cardId].forEach((location, i) => {
          const label = this.getPlayLocationLabel(cardId, location, names[location] ?? location);
          this.addPrimaryActionButton('btnLocation' + i, label, onChooseLocation(location));
           this.onClick(`board-${location}-${this.player_id}`, onChooseLocation(location));
           this.onClick(`board-${location}-${this.player_id}`, onChooseLocation(location));
 
           if (location == 'limbo') {
             this.wait(200).then(() => {
               if ($(`card-${cardId}`).classList.contains('selected')) {
                 this.addPrimaryActionButton(
                   'btnLaunchSpell',
                   this.formatSvgIcon('spell'),
                   onChooseLocation(location),
                   `board-limbo-${this.player_id}`
                 );
 
                 if ($(`card-${cardId}`).classList.contains('mini-card')) {
                   $('btnLaunchSpell').classList.add('on-mini-card');
                 }
               }
             });
           }
         });
       }
 
       if (args.supportPossible == true && supportIcon) {
        this.addPrimaryActionButton(
          'btnSupportAbility',
          _('Support ability') + supportIcon.innerHTML,
          () => this.takeAtomicAction('actSupport', [cardId])
        );
       }
     },
 
     onEnteringStateTarget(args) {
       let location = 'hand';
 
       if (args.manaOrbs == true) {
         this.addPrimaryActionButton('btnShowMana', _('Show mana cards'), () => this._manaModal.show());
         this._manaModal.show();
 
         if (!$('popin_manaDisplay_subtitle')) {
           $('popin_manaDisplay_title').insertAdjacentHTML('afterend', '<h3 id="popin_manaDisplay_subtitle"></h3>');
         }
         $('popin_manaDisplay_subtitle').innerHTML = $('pagemaintitletext').innerHTML;
 
         location = 'mana';
       }
 
       this.onSelectNCards(
         args.cardIds,
         {
           n: args.n,
           class: 'selectable',
           confirmText: _('Confirm target'),
           upTo: args.upTo,
           callback: (selectedElements, ignoredElements) => this.takeAtomicAction('actTarget', [selectedElements]),
           passCallback: () => this.takeAction('actPassOptionalAction'),
         },
         location
       );
 
       Object.keys(args.targetCosts).forEach((cardId) => {
         $(`card-${cardId}`).insertAdjacentHTML('beforeend', `<div class='tough-marker'>${args.targetCosts[cardId]}</div>`);
       });
     },
 
     onEnteringStateExchange(args) {
       handIds = args.handIds ?? [];
       reserveIds = args.reserveIds ?? [];
       const canPassWithoutSelection = args.canPassWithoutSelection === true;
       const canSelectHand = reserveIds.length > 0;
       const canSelectReserve = handIds.length > 0;
       let selectedHand = [];
       let selectedReserve = [];
 
       let cancelSelection = () => {
         selectedHand = [];
         selectedReserve = [];
         updateStatus();
       };
 
       let updateStatus = () => {
         if ($('btnConfirmChoice')) $('btnConfirmChoice').remove();
         if (selectedHand.length == 1 && selectedReserve.length == 1) {
           this.addPrimaryActionButton('btnConfirmChoice', _('Confirm'), () =>
             this.takeAtomicAction('actExchange', [selectedReserve[0], selectedHand[0]])
           );
         }
 
         if ($('btnCancelChoice')) $('btnCancelChoice').remove();
         if (selectedHand.length > 0 || selectedReserve.length > 0) {
           this.addSecondaryActionButton('btnCancelChoice', _('Cancel'), cancelSelection);
         }
 
         handIds.forEach((id) => {
           let elt = $('card-' + id);
           if (!elt) return;
           let selected = selectedHand.includes(id);
           elt.classList.toggle('selected', selected);
           elt.classList.toggle(
             'selectable',
             canSelectHand && (selected || selectedHand.length < 1)
           );
         });
 
         reserveIds.forEach((id) => {
           let elt = $('card-' + id);
           if (!elt) return;
           let selected = selectedReserve.includes(id);
           elt.classList.toggle('selected', selected);
           elt.classList.toggle(
             'selectable',
             canSelectHand && (selected || selectedHand.length < 1)
           );
         });
       };
 
      if (canPassWithoutSelection && !$('btnPassAction')) {
        this.addSecondaryActionButton(
          'btnPassAction',
          _('Pass'),
          () => this.takeAction('actPassOptionalAction'),
          'restartAction'
        );
      }

      if (canSelectHand) {
       handIds.forEach((id) => {
         let elt = 'card-' + id;
 
         this.onClick(elt, () => {
           let index = selectedHand.findIndex((t) => t == id);
 
           if (index === -1) {
             if (selectedHand.length >= 1) return;
             selectedHand.push(id);
           } else {
             selectedHand.splice(index, 1);
           }
           updateStatus();
         });
       });
      }

      if (canSelectReserve) {
       reserveIds.forEach((id) => {
         let elt = 'card-' + id;
 
         this.onClick(elt, () => {
           let index = selectedReserve.findIndex((t) => t == id);
 
           if (index === -1) {
             if (selectedReserve.length >= 1) return;
             selectedReserve.push(id);
           } else {
             selectedReserve.splice(index, 1);
           }
           updateStatus();
         });
       });
      }
 
      updateStatus();
     },
 
     onEnteringStateDiscardDo(args) {
       this.onEnteringStateTarget(args);
     },
 
     onLeavingStateTarget() {
       document.querySelectorAll('.tough-marker').forEach((o) => o.remove());
     },
 
     onEnteringStatePlayCard(args) {
       let cardId = args.cardId;
       $(`card-${cardId}`).classList.add('selected');
 
       let onChooseLocation = (location) => {
         return () => this.takeAtomicAction('actPlayCard', [cardId, location]);
       };
 
       const names = {
         stormLeft: _('Hero side'),
         stormRight: _('Companion side'),
         landmark: _('Landmark'),
         reserve: _('Reserve'),
         limbo: _('Spell'),
       };
 
       if (args._private.play[cardId] != undefined) {
         args._private.play[cardId].forEach((location, i) => {
           this.addPrimaryActionButton('btnLocation' + i, names[location], onChooseLocation(location));
           this.onClick(`board-${location}-${this.player_id}`, onChooseLocation(location));
         });
       }
       // this.clientState('chooseAssignmentLocation', _('Where do you want to play that card?'), {
       //   play: args._private.play,
       //   support: [],
       //   tap: [],
       //   cardId: args.cardId,
       //   supportPossible: false,
       //   clientState: false,
       // });
     },
 
     onEnteringStateInvokeToken(args) {
      const names = {
        stormLeft: _('Hero side'),
        stormRight: _('Companion side'),
        source: _('source'),
        initialSource: _('source'),
        oppositeSource: _('opposite of played card'),
        landmark: _('Landmark'),
      };

      let onChooseLocation = (location) => {
        return () => this.takeAtomicAction('actInvokeToken', [location]);
      };

      const invokePlayerId = args.invokePlayerId ?? this.player_id;
      const invokePlayer = this.gamedatas.players[invokePlayerId];
      const invokePlayerPrefix =
        args.invokeOnOpponent && invokePlayer ? invokePlayer.name + ' — ' : '';

      const bindStormLocation = (location, locationArg, labelSuffix) => {
        this.addPrimaryActionButton(
          'btnLocation' + locationArg,
          invokePlayerPrefix + (names[location] ?? location) + labelSuffix,
          onChooseLocation(locationArg)
        );
        if (location == 'stormLeft' || location == 'stormRight') {
          this.onClick(`board-${location}-${invokePlayerId}`, onChooseLocation(locationArg));
        }
      };

      if (args.allPlayers == true) {
        i = 0;
        this.forEachPlayer((player) => {
          args.locations.forEach((location, i) => {
            this.addPrimaryActionButton(
              'btnLocation' + player.id + i,
              player.name + ' ' + names[location],
              onChooseLocation(location + '-' + player.id)
            );
            if (location == 'stormLeft' || location == 'stormRight') {
              this.onClick(`board-${location}-${player.id}`, onChooseLocation(location + '-' + player.id));
            }
          });
        });
      } else {
        args.locations.forEach((location, i) => {
          const locationArg =
            location == 'stormLeft' || location == 'stormRight'
              ? location + '-' + invokePlayerId
              : location;
          bindStormLocation(location, locationArg, '');
        });
      }
    },
 
     onEnteringStateBlockExpedition(args) {
       const names = {
         stormLeft: _('Hero side'),
         stormRight: _('Companion side'),
         source: _('source'),
         oppositeSource: _('opposite of played card'),
       };
 
       let onChooseLocation = (location) => {
         return () => this.takeAtomicAction('actBlockExpedition', [location]);
       };
 
       this.forEachPlayer((player) => {
         ['stormLeft', 'stormRight'].forEach((location) => {
           this.onClick(`board-${location}-${player.id}`, onChooseLocation(`board-${location}-${player.id}`));
         });
       });
     },
 
     onEnteringStateTargetExpedition(args) {
       const names = {
         stormLeft: _('Hero side'),
         stormRight: _('Companion side'),
         source: _('source'),
         oppositeSource: _('opposite of played card'),
       };
 
       let elements = {};
       args.expeditions.forEach((ex) => {
         data = ex.split('-');
         $(`board-${data[1]}-${data[0]}`).classList.add('selectable');
         elements[ex] = $(`board-${data[1]}-${data[0]}`);
       });
 
       this.onSelectN({
         n: args.n,
         elements: elements,
         class: 'selectable',
         confirmText: _('Confirm target'),
         callback: (selectedElements, ignoredElements) => this.takeAtomicAction('actTargetExpedition', [selectedElements]),
       });
 
       // let onChooseLocation = (location) => {
       //   return () => this.takeAtomicAction('actTargetExpedition', [location]);
       // };
 
       // args.expeditions.forEach((ex) => {
       //   data = ex.split('-');
       //   this.onClick(`board-${data[1]}-${data[0]}`, onChooseLocation(`board-${data[1]}-${data[0]}`));
       // });
     },
 
         onEnteringStateTargetPlayer(args) {
      let targetPlayer = (player) => {
        return () => this.takeAtomicAction('actTargetPlayer', [player]);
      };

      this.forEachPlayer((player) => {
        if (args.opponentsOnly && player.id == this.player_id) {
          return;
        }
        this.addPrimaryActionButton('btnTargetr' + player.id, player.name, targetPlayer(player.id));
      });
    },
 
     onEnteringStateRollDie(args) {
       let chooseRollDie = (roll) => {
         return () => this.takeAtomicAction('actRollDie', [roll]);
       };
 
       args.rolls.forEach((roll, i) => {
         this.addPrimaryActionButton('btnRoll' + i, roll, chooseRollDie(roll));
       });
 
       // management of All In rare
       if (args.canDiscard == true) {
         this.onSelectNCards(args.cardIds, {
           n: 1,
           class: 'selectable',
           confirmText: _('Confirm discard to increase die by 2'),
           callback: (selectedElements, ignoredElements) => this.takeAtomicAction('actDiscardAdd', [selectedElements]),
         });
       }
     },
 
     onEnteringStateMoveExpedition(args) {
       if (args.forceExpedition !== null) {
         return;
       }
       let onChooseLocation = (expe) => {
         return () => this.takeAtomicAction('actMoveExpedition', [expe]);
       };
 
       args.expeditions.forEach((expe, i) => {
         let pId = expe[0],
           location = expe[1];
 
         this.onClick(`board-${location}-${pId}`, onChooseLocation(expe));
         if (pId == this.player_id) {
           desc = location == 'stormLeft' ? _('My Hero expedition') : _('My Companion expedition');
         } else {
           desc = location == 'stormLeft' ? _('Opponent Hero expedition') : _('Opponent Companion expedition');
         }
         this.addPrimaryActionButton('btnLocation' + i, desc, onChooseLocation(expe));
       });
     },
 
     onEnteringStateSpend(args) {
       let chooseSpend = (n) => {
         return () => this.takeAtomicAction('actSpend', [n]);
       };
       for (j = 1; j <= args.n; j++) {
         debug(j);
         this.addPrimaryActionButton('btnSpend' + j, j, chooseSpend(j));
       }
     },
 
     onEnteringStateMarkRegion(args) {
       let goToRegion = (clientArgs) => {
         this.clientState('markRegionExpedition', _('Select region to add the marker'), Object.assign({ regions: args.regions }, clientArgs));
       };

       if (args.create && args.regionTypes && args.regionTypes.length > 1) {
         args.regionTypes.forEach((type) => {
           this.addPrimaryActionButton('btnMarkType' + type, this.formatSvgIcon(type), () =>
             goToRegion({ markerType: type, create: true })
           );
         });
         return;
       }

       if (args.create && args.regionType) {
         goToRegion({ markerType: args.regionType, create: true });
         return;
       }

       let markerIds = Object.keys(args.markers || {});
       if (markerIds.length === 1) {
         goToRegion({ marker: args.markers[markerIds[0]] });
         return;
       }

       markerIds.forEach((id) => {
         mark = args.markers[id];
         this.addPrimaryActionButton('btnMark' + mark.id, this.formatSvgIcon(mark.type), () => goToRegion({ marker: mark }));
       });
     },
 
     onEnteringStateInteruptReveal(args) {
       this.addPrimaryActionButton('btnConfirmReveal', _('Confirm'), () => this.takeAtomicAction('actInteruptReveal', []));
     },
 
     onEnteringStateMarkRegionExpedition(args) {
       Object.keys(args.regions).forEach((id) => {
         storm = $(`storm-${id}`);
         storm.classList.add('selectable');
         this.onClick(storm, () => {
           let markerArg = args.create && args.markerType ? args.markerType : args.marker.id;
           this.takeAtomicAction('actMarkRegion', [markerArg, id]);
         });
       });
 
       this.addSecondaryActionButton(
         'btnCancel',
         _('Cancel'),
         () => {
           this.unselectIfNeeded();
           this.clearClientState();
         },
         'restartAction'
       );
     },
 
     onEnteringStateMoveRegionMarker(args) {
       Object.keys(args.markers).forEach((id) => {
         mark = args.markers[id];
         meep = $(`meeple-${id}`);
         meep.classList.add('selectable');
         this.onClick(meep, () => this.takeAtomicAction('actMoveRegionMarker', [id]));
       });
     },
 
     ////////////////////////////////////////////////////////////
     // _____                          _   _   _
     // |  ___|__  _ __ _ __ ___   __ _| |_| |_(_)_ __   __ _
     // | |_ / _ \| '__| '_ ` _ \ / _` | __| __| | '_ \ / _` |
     // |  _| (_) | |  | | | | | | (_| | |_| |_| | | | | (_| |
     // |_|  \___/|_|  |_| |_| |_|\__,_|\__|\__|_|_| |_|\__, |
     //                                                 |___/
     ////////////////////////////////////////////////////////////
 
     /**
      * Replace some expressions by corresponding html formating
      */
     formatIcon(name, n = null, lowerCase = true) {
       let type = lowerCase ? name.toLowerCase() : name;
       const NO_TEXT_ICONS = [];
       let noText = NO_TEXT_ICONS.includes(name);
       let text = n == null ? '' : `<span>${n}</span>`;
       return `${noText ? text : ''}<div class="icon-container icon-container-${type}">
             <div class="altered-icon icon-${type}">${noText ? '' : text}</div>
           </div>`;
     },
 
     // SVG ICONS AVAILABLE :
     formatSvgIcon(name) {
       let glyphs = {
         anchored: 1,
         artist: 5,
         btg: 1,
         card: 1,
         charge: 1,
         discard: 2,
         fleeting: 1,
         forest: 1,
         hand: 1,
         infinity: 2,
         'mana-0': 2,
         'mana-1': 2,
         'mana-2': 2,
         'mana-3': 2,
         'mana-4': 2,
         'mana-5': 2,
         'mana-6': 2,
         'mana-7': 2,
         'mana-8': 2,
         'mana-9': 2,
         'mana-X': 2,
         'mana-$1': 2, // Useful only for substitution into regex
         mountain: 1,
         ocean: 1,
         permanent: 1,
         played: 1,
         reserve: 8,
         spell: 1,
         sleep: 1,
         tap: 1,
       };
 
       let icon = `<i class='svgicon-${name}'>`;
       let nGlyphs = glyphs[name];
       if (nGlyphs > 1) {
         for (let i = 1; i <= nGlyphs; i++) {
           icon += `<span class="path${i}"></span>`;
         }
       }
       icon += '</i>';
       return icon;
       // let svgId = name + '-svg';
       // let viewBox = $(svgId).getAttribute('viewBox');
       // return `<div class='inline-icon'><svg viewBox="${viewBox}"><use href="#${svgId}" /></svg></div>`;
     },
 
     formatString(str, italicParenthesis = false) {
       const ICONS = ['BOOST', 'ANCHORED', 'FLEETING', 'ASLEEP'];
       ICONS.forEach((name) => {
         const regex = new RegExp('<' + name + ':([^>]+)>', 'g');
         str = str.replaceAll(regex, this.formatIcon(name, '<span>$1</span>'));
         str = str.replaceAll(new RegExp('<' + name + '>', 'g'), this.formatIcon(name));
       });
 
       const MARKERS_MAP = {
         J: 'played',
         j: 'played',
         H: 'hand',
         h: 'hand',
         R: 'reserve',
         r: 'reserve',
         D: 'discard',
         T: 'tap',
         V: 'forest',
         O: 'ocean',
         E: 'ocean',
         M: 'mountain',
         COUNTER: 'charge',
         I: 'infinity',
       };
       Object.keys(MARKERS_MAP).forEach((marker) => {
         const regex = new RegExp('{' + marker + '}', 'g');
 
         let svgs = MARKERS_MAP[marker];
         if (!Array.isArray(svgs)) svgs = [svgs];
         let svgStr = '';
         svgs.forEach((svg) => (svgStr += this.formatSvgIcon(svg)));
 
         str = str.replace(regex, svgStr);
       });
       // str = str.replace(/__([^_]+)__/g, '<span class="action-card-name-reference">$1</span>');
       str = str.replace(/\#(.+?)\#/g, '<span class="rare-marker">$1</span>');
       str = str.replace(/\[\[([^\]]+)\]\]/g, '<span class="effect-reference-emphasis">$1</span>');
       str = str.replace(/\[([^\]]+)\]/g, '<span class="effect-reference">$1</span>');
       str = str.replace(/\{([0-9X]+)\}/g, this.formatSvgIcon('mana-$1'));
       if (italicParenthesis) str = str.replace(/(\([^\)]+\))/g, '<span class="parenthesis">$1</span>');
 
       return str;
     },
 
     /**
      * Format log strings
      *  @Override
      */
     format_string_recursive(log, args) {
       try {
         if (log && args && !args.processed) {
           args.processed = true;
 
           log = this.formatString(_(log));
 
           if (args.card_name !== undefined && args.card_id !== undefined) {
             // let card = this.getCardInfos(args.card_id);
             // let uid = this.registerCustomTooltip(this.tplCard(card, true));
             // args.card_name = `<span class="ark-log-card-name" id="${uid}">${_(args.card_name)}</span>`;
             args.card_name = `<span class="altered-log-card-name">${_(args.card_name)}</span>`;
           }
 
           if (args.source !== undefined && args.sourceId !== undefined) {
             // let card = this.getCardInfos(args.card_id);
             // let uid = this.registerCustomTooltip(this.tplCard(card, true));
             // args.source = `<span class="ark-log-card-name" id="${uid}">${_(args.source)}</span>`;
             args.source = `<span class="altered-log-card-name">${_(args.source)}</span>`;
           }
 
           if (args.effect_desc !== undefined) {
             args.effect_desc = this.formatString(this.translate(args.effect_desc));
           }
 
           if (args.phase_icon !== undefined) {
             args.phase_icon = this.formatIcon(args.phase);
           }
           if (args.phase_icon2 !== undefined) {
             args.phase_icon2 = this.formatIcon(args.phase);
           }
 
           if (args.biome_icon !== undefined) {
             let icon = args.biome_name;
             if (icon == 'water') icon = 'ocean';
             args.biome_icon = this.formatSvgIcon(icon);
             args.biome_name = '';
           }
 
           if (args.mana_cost !== undefined) {
             args.mana_cost = this.formatSvgIcon('mana-' + args.mana_cost);
           }
         }
       } catch (e) {
         console.error(log, args, 'Exception thrown', e.stack);
       }
 
       return this.inherited(arguments);
     },
 
     //////////////////////////////////////////////////////
     //  ___        __         ____                  _
     // |_ _|_ __  / _| ___   |  _ \ __ _ _ __   ___| |
     //  | || '_ \| |_ / _ \  | |_) / _` | '_ \ / _ \ |
     //  | || | | |  _| (_) | |  __/ (_| | | | |  __/ |
     // |___|_| |_|_|  \___/  |_|   \__,_|_| |_|\___|_|
     //////////////////////////////////////////////////////
 
     setupInfoPanel() {
       dojo.place(this.tplInfoPanel(), 'player_boards', 'first');
       let chk = $('help-mode-chk');
       dojo.connect(chk, 'onchange', () => this.toggleHelpMode(chk.checked));
       this.addTooltip('help-mode-switch', '', _('Toggle help/safe mode.'));
 
       dojo.connect($('show-settings2'), 'onclick', () => this.toggleSettings());
       this.addTooltip('show-settings2', '', _('Display some settings about the game.'));
 
       this.setupHelperModal();
 
       this._settingsModal = new customgame.modal('showSettings', {
         class: 'altered_popin',
         closeIcon: 'fa-times',
         title: _('Settings'),
         closeAction: 'hide',
         scale: 0.9,
         breakpoint: 550,
         verticalAlign: 'flex-start',
         contentsTpl: `<div id='altered-settings'>
              <div id='altered-settings-header'></div>
              <div id="settings-controls-container"></div>
            </div>`,
       });
 
       // let handWrapper = $('floating-hand-wrapper');
       // $('floating-hand-button').addEventListener('click', () => {
       //   if (handWrapper.dataset.open && handWrapper.dataset.open == 'hand') {
       //     delete handWrapper.dataset.open;
       //   } else {
       //     handWrapper.dataset.open = 'hand';
       //   }
       // });
     },
 
     tplInfoPanel() {
       return `
    <div class='player-board' id="player_board_config">
      <div id="player_config" class="player_board_content">
        <div class="player_config_row">
          <div id="help-mode-switch">
            <input type="checkbox" class="checkbox" id="help-mode-chk" />
            <label class="label" for="help-mode-chk">
              <div class="ball"></div>
            </label><svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="question-circle" class="svg-inline--fa fa-question-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M256 8C119 8 8 119.08 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 422a46 46 0 1 1 46-46 46.05 46.05 0 0 1-46 46zm40-131.33V300a12 12 0 0 1-12 12h-56a12 12 0 0 1-12-12v-4c0-41.06 31.13-57.47 54.65-70.66 20.17-11.31 32.54-19 32.54-34 0-19.82-25.27-33-45.7-33-27.19 0-39.44 13.14-57.3 35.79a12 12 0 0 1-16.67 2.13L148.82 170a12 12 0 0 1-2.71-16.26C173.4 113 208.16 90 262.66 90c56.34 0 116.53 44 116.53 102 0 77-83.19 78.21-83.19 106.67z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M256 338a46 46 0 1 0 46 46 46 46 0 0 0-46-46zm6.66-248c-54.5 0-89.26 23-116.55 63.76a12 12 0 0 0 2.71 16.24l34.7 26.31a12 12 0 0 0 16.67-2.13c17.86-22.65 30.11-35.79 57.3-35.79 20.43 0 45.7 13.14 45.7 33 0 15-12.37 22.66-32.54 34C247.13 238.53 216 254.94 216 296v4a12 12 0 0 0 12 12h56a12 12 0 0 0 12-12v-1.33c0-28.46 83.19-29.67 83.19-106.67 0-58-60.19-102-116.53-102z"></path></g></svg>
          </div>
 
          <div id="show-settings">
            <svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
              <g>
                <path class="fa-secondary" fill="currentColor" d="M638.41 387a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4L602 335a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6 12.36 12.36 0 0 0-15.1 5.4l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 44.9c-29.6-38.5 14.3-82.4 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79zm136.8-343.8a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4l8.2-14.3a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6A12.36 12.36 0 0 0 552 7.19l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 45c-29.6-38.5 14.3-82.5 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79z" opacity="0.4"></path>
                <path class="fa-primary" fill="currentColor" d="M420 303.79L386.31 287a173.78 173.78 0 0 0 0-63.5l33.7-16.8c10.1-5.9 14-18.2 10-29.1-8.9-24.2-25.9-46.4-42.1-65.8a23.93 23.93 0 0 0-30.3-5.3l-29.1 16.8a173.66 173.66 0 0 0-54.9-31.7V58a24 24 0 0 0-20-23.6 228.06 228.06 0 0 0-76 .1A23.82 23.82 0 0 0 158 58v33.7a171.78 171.78 0 0 0-54.9 31.7L74 106.59a23.91 23.91 0 0 0-30.3 5.3c-16.2 19.4-33.3 41.6-42.2 65.8a23.84 23.84 0 0 0 10.5 29l33.3 16.9a173.24 173.24 0 0 0 0 63.4L12 303.79a24.13 24.13 0 0 0-10.5 29.1c8.9 24.1 26 46.3 42.2 65.7a23.93 23.93 0 0 0 30.3 5.3l29.1-16.7a173.66 173.66 0 0 0 54.9 31.7v33.6a24 24 0 0 0 20 23.6 224.88 224.88 0 0 0 75.9 0 23.93 23.93 0 0 0 19.7-23.6v-33.6a171.78 171.78 0 0 0 54.9-31.7l29.1 16.8a23.91 23.91 0 0 0 30.3-5.3c16.2-19.4 33.7-41.6 42.6-65.8a24 24 0 0 0-10.5-29.1zm-151.3 4.3c-77 59.2-164.9-28.7-105.7-105.7 77-59.2 164.91 28.7 105.71 105.7z"></path>
              </g>
            </svg>
          </div>
        </div>
      </div>
    </div>
    `;
     },
 
     updatePlayerOrdering() {
       this.inherited(arguments);
       dojo.place('player_board_config', 'player_boards', 'first');
     },
 
     onChangeCardScaleSetting(val) {
       // let scale = val / 100;
       // [...document.querySelectorAll('.player-board-hand')].forEach((elt) => {
       //   elt.style.setProperty('--alteredZooCardScale', scale);
       // });
       // $('floating-hand-wrapper').style.setProperty('--alteredZooCardScale', scale);
     },
 
     onChangeBoardHeightSetting(val) {
       this.updateLayout();
     },
 
     onChangeFitToSetting(val) {
       this.updateLayout();
     },
 
     onChangeDisplayFullArtSetting(val) {
       document.querySelectorAll('.altered-card').forEach((oCard) => {
         if (!oCard.classList.contains('card-back')) {
           this.destroy(oCard);
         }
       });
       this.setupCards();
     },
 
     updateLayout() {
       if (!this.settings) return;
       const ROOT = document.documentElement;
 
       const IS_FOCUS_MODE = document.body.classList.contains('focus-board');
       const WIDTH = $('altered-main-container').getBoundingClientRect()['width'];
       let HEIGHT =
         (IS_FOCUS_MODE
           ? document.body.clientHeight
           : window.outerHeight || document.documentElement.clientHeight || document.body.clientHeight) - (IS_FOCUS_MODE ? 0 : 62);
       const BOARD_WIDTH = 1401;
       const BOARD_HEIGHT = 1100;
 
       // TOOLTIP SIZE
       if (HEIGHT < 900) {
         let tooltipScale = HEIGHT / 700;
         ROOT.style.setProperty('--cardScaleTooltip', tooltipScale);
       }
 
       // if (HEIGHT < WIDTH && WIDTH < BOARD_WIDTH) {
       //   this.biggestHeightLandscape = Math.max(this.biggestHeightLandscape, HEIGHT);
       //   HEIGHT = this.biggestHeightLandscape;
       // }
 
       let heightS = IS_FOCUS_MODE ? 1 : this.settings.boardHeight / 100;
       if (HEIGHT < WIDTH) {
         heightS = Math.max(0.8, heightS);
       }
       let heightScale = (heightS * HEIGHT) / BOARD_HEIGHT;
       let widthScale = WIDTH / BOARD_WIDTH;
       let scale = Math.min(widthScale, heightScale);
 
       if (IS_FOCUS_MODE) {
         if (this.settings.fitTo == 1) scale = widthScale;
         else scale = heightScale;
       }
       ROOT.style.setProperty('--boardScale', scale);
 
       this.centerOverlay();
     },
 
     /////////////////////////////////////////
     //  _   _      _
     // | | | | ___| |_ __   ___ _ __ ___
     // | |_| |/ _ \ | '_ \ / _ \ '__/ __|
     // |  _  |  __/ | |_) |  __/ |  \__ \
     // |_| |_|\___|_| .__/ \___|_|  |___/
     //              |_|
     /////////////////////////////////////////
 
     openHelperModal() {
       this._helperModal.show();
     },
 
     setupHelperModal() {
       this._helperModal = new customgame.modal('helperModal', {
         class: 'altered_popin',
         closeIcon: 'fa-times',
         closeAction: 'hide',
         verticalAlign: 'flex-start',
         contentsTpl: `<div id='altered-helpers'>
           <div id='helper-phases'>
             <h2>${_('Phases of the day')}</h2>
 
             <h3>${this.formatIcon('morning')} ${_('Phase 1: Morning')}</h3>
             <ul>
               <li>${_('Change first player')}</li>
               <li>${_('Ready your cards')}</li>
               <li>${_('Draw two cards')}</li>
               <li>${_('Put a card in Mana')}</li>
             </ul>
 
             <h3>${this.formatIcon('noon')} ${_('Phase 2: Noon')}</h3>
             <ul>
               <li>${_('Apply "At Noon" effects')}</li>
             </ul>
 
             <h3>${this.formatIcon('afternoon')} ${_('Phase 3: Afternoon')}</h3>
             <h4>${_('Go back and forth taking turns')}</h4>
             <ul>
               <li>${_('You can activate Quick actions')} (${this.formatSvgIcon('discard')} & ${this.formatSvgIcon('tap')})</li>
             </ul>
 
             <h4>${_('Then either:')}</h4>
             <ul>
               <li>${_('Play a card from your hand or Reserve')}</li>
               <li>${_('Pass the turn and end your Afternoon')}</li>
             </ul>
 
             <h3>${this.formatIcon('dusk')} ${_('Phase 4: Dusk')}</h3>
             <ul>
               <li>${_('Compare statistics and check which expeditions move forward')}</li>
             </ul>
 
             <h3>${this.formatIcon('night')} ${_('Phase 5: Night')}</h3>
             <ul>
               <li>${_('Apply "At Night" effects')}</li>
               <li>${_('Rest: Characters go to Reserve')}</li>
               <li>${_('Keep up to 2 cards in Reserve and 2 in Landmarks, discard the rest')}</li>
             </ul>
           </div>
           <div id='helper-icons'>
             <h2>${_('Icons')}</h2>
 
             <div class='icon-item'>${_("When I'm played from anywhere...")}</div>
             <div class='icon-item'>${_("When I'm played from hand...")}</div>
             <div class='icon-item'>${_("When I'm played from your Reserve")}</div>
 
 
             <div class='icon-item'>${_('Exhaust')}</div>
             <div class='icon-item'>${_('Fleeting')}</div>
             <div class='icon-item'>${_('Anchored')}</div>
             <div class='icon-item'>${_('Asleep')}</div>
           </div>
         </div>`,
       });
     },
   });
 });
 