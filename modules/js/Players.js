define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
   const PLAYER_COUNTERS = ['mana', 'totalMana', 'handCount', 'deckCount', 'discardCount'];

  return declare('altered.players', null, {
    getPlayers() {
      return Object.values(this.gamedatas.players);
    },

    getPlayerFaction(pId) {
      return this.gamedatas.players[pId].faction;
    },

    isSolo() {
      return this.getPlayers().length == 1;
    },

    getOpponent(pId) {
      return this.orderedPlayers[0].id == pId ? this.orderedPlayers[1].id : this.orderedPlayers[0].id;
    },

    setupPlayers() {
      // Change No so that it fits the current player order view
      let currentNo = this.getPlayers().reduce((carry, player) => (player.id == this.player_id ? player.no : carry), 0);
      let nPlayers = Object.keys(this.gamedatas.players).length;
      this.forEachPlayer((player) => (player.order = (player.no + nPlayers - currentNo) % nPlayers));
      this.orderedPlayers = Object.values(this.gamedatas.players).sort((a, b) => a.order - b.order);
      this.bottomPId = this.orderedPlayers[0].id;
      this.topPId = this.orderedPlayers[1].id;
      this._discardModals = {};

      // Add player board and player panel
      this.orderedPlayers.forEach((player, i) => {
        let container = i == 0 ? 'altered-board-me' : 'altered-board-opponent';
        this.place('tplPlayerBoard', player, container);
        // Tooltips
        ['stormLeft', 'stormRight'].forEach((expe) => {
          this.addCustomTooltip(
            `powers-blocked-${expe}-${player.id}`,
            _('These powers are currently blocked in this expedition')
          );
          this.addCustomTooltip(`blocked-${expe}-${player.id}`, _("This expedition can't currently move forward"));
        });

        // Hand observer
        let handContainer = $(`hand-${player.id}`);
        let observer = new MutationObserver(() => {
          if (handContainer.parentNode.id == `player-board-hand-${player.id}`)
            this.adjustHand(handContainer, i == 0 ? 'bottom' : 'top');
          else this.clearHandTransform(handContainer);
        });
        observer.observe(handContainer, { childList: true });

        // Landmark observer
        let landmarkContainer = $(`board-landmark-${player.id}`);
        let observerLandmark = new MutationObserver(() => {
          this.displayWarningSizeLimitIfNeeded(player, 'landmark', landmarkContainer);
        });
        observerLandmark.observe(landmarkContainer, { childList: true });
        // Reserve observer
        let reserveContainer = $(`board-reserve-${player.id}`);
        let reserveLandmark = new MutationObserver(() => {
          this.displayWarningSizeLimitIfNeeded(player, 'reserve', reserveContainer);
        });
        reserveLandmark.observe(reserveContainer, { childList: true });

        // Keep discard counter above cards (compositor layer issue with translateZ)
        let discardContainer = $(`board-discard-${player.id}`);
        let discardObserver = new MutationObserver(() => {
          this.bringDiscardCounterToFront(player.id);
        });
        discardObserver.observe(discardContainer, { childList: true });
        this.bringDiscardCounterToFront(player.id);

        // Panels
        this.place('tplPlayerPanel', player, `overall_player_board_${player.id}`);
        // Tie breaker
        $('tie-breaker-attributes').insertAdjacentHTML(
          'beforeend',
          `<div class="total-biomes ${container}" id='tie-breaker-biomes-${player.id}'>
          <div class='total-forest'></div>
          <div class='total-mountain'></div>
          <div class='total-ocean'></div>
        </div>`
        );

        // Discard modal
        this.setupDiscardModal(player);
        // Mana modal
        if (player.id == this.player_id) {
          this.setupManaModal(player);
        }
      });

      this.setupPlayersCounters();
    },

    onChangeHandLocationSetting(v) {
      let hand = $(`hand-${this.player_id}`);
      if (hand) {
        let container = this.isFloatingHand() ? 'floating-hand' : `player-board-hand-${this.player_id}`;
        $(container).insertAdjacentElement('beforeend', hand);
        // $('floating-hand-wrapper').classList.toggle('active', this.isFloatingHand());
        hand.style.order = v == 1 ? 1 : 4;
        hand.childNodes.forEach((item) => (item.dataset.animationSpeed = 'none'));
        this.adjustHand(hand);

        if (v == 3) {
          this.openHand();
        }
      }

      // this.ensureNoSortableHandOnTouchDevice();
    },

    getZoneMaxLimit(player, zone) {
      // TODO: do something smart here :)
      if (zone == 'reserve') {
        return this.gamedatas.reserveSlots[player.id];
      } else if (zone == 'landmark') {
        return this.gamedatas.landmarkSlots[player.id];
      }
      return 2;
    },

    displayWarningSizeLimitIfNeeded(player, zone, container) {
      let limit = this.getZoneMaxLimit(player, zone);
      if (limit === null) {
        return;
      }

      container.classList.toggle('danger', container.childNodes.length > limit);
      $(`max-${zone}-indicator-${player.id}`).innerHTML = limit;
    },

    tplPlayerBoard(player) {
      let pId = player.id;
      return `<div class='altered-player-board' id='player-board-${pId}' data-faction='${player.faction}'>
          <div class='player-board-discard-zone' id='board-discard-zone-${player.id}'>
            <div class='player-board-discard' id='board-discard-${player.id}'></div>
            <div class='discard-counter-holder'>
              <div class='deck-counter' id="counter-${player.id}-discardCount">0</div>
            </div>
          </div>
          <div class='player-board-deck' id='board-deck-${player.id}'>
            <div class='deck-counter-holder'  id='reveal-${player.id}'>
              <div class='deck-counter' id="counter-${player.id}-deckCount"></div>
            </div>
          </div>
          <div class='player-board-grass'>
            <div class='player-board-reserve' id='board-reserve-${player.id}'></div>
            <div class='reserve-warning'>
              <i class="fa fa-warning"></i>
              ${_('Max reserve slots at night:')}
              <span id='max-reserve-indicator-${player.id}'></span>
            </div>

            <div class='player-board-separator'></div>

            <div class='player-board-landmarks' id='board-landmark-${player.id}'></div>
            <div id='board-landmark_temple-${pId}' class='landmark-temple-marker' title="${_('Temple (Landmark)')}">
              <svg viewBox="0 0 64 64" aria-hidden="true">
                <rect x='7' y='6' width='50' height='6' rx='2'></rect>
                <rect x='12' y='13' width='40' height='5' rx='1'></rect>
                <rect x='17' y='19' width='30' height='28'></rect>
                <rect class='flute' x='22' y='22' width='3' height='22'></rect>
                <rect class='flute' x='30.5' y='22' width='3' height='22'></rect>
                <rect class='flute' x='39' y='22' width='3' height='22'></rect>
                <rect x='12' y='48' width='40' height='5' rx='1'></rect>
                <rect x='7' y='54' width='50' height='6' rx='2'></rect>
              </svg>
            </div>
            <div class='landmark-warning'>
              <i class="fa fa-warning"></i>
              ${_('Max landmark slots at night:')}
              <span id='max-landmark-indicator-${player.id}'></span>
            </div>
          </div>

          <div class='player-board-mana-wrapper'>
            <div class='player-board-mana-gauge' id='mana-gauge-${pId}'></div>
            <div class='player-board-mana-indicator'>
              <h6>${this.formatIcon('first-player')} ${_('MANA')}</h6>
              <div class='mana-counters-wrapper'>
                <span class="mana-counter" id="counter-board-${pId}-mana"></span> 
                / 
                <span class="mana-counter" id="counter-board-${pId}-totalMana"></span>
              </div>
            </div>
          </div>
          <div id='board-stormLeft_scout-${pId}' class='player-board-storm storm-left scout '>
            <i class="fa6 fa-regular fa-binoculars scout-marker"></i>
          </div>
          <div id='board-stormRight_scout-${pId}' class='player-board-storm storm-right scout '>
            <i class="fa6 fa-regular fa-binoculars scout-marker"></i>
          </div>
          <div id='board-stormLeft_ascend-${pId}' class='player-board-storm storm-left ascend '>
          </div>
          <div id='board-stormRight_ascend-${pId}' class='player-board-storm storm-right ascend '>
          </div>
          <div class='player-board-storm storm-left' id='board-stormLeft-${pId}'>
            <div class="total-biomes">
              <div class='total-forest'></div>
              <div class='total-mountain'></div>
              <div class='total-ocean'></div>
              <div class='total-biomes-highlights'>
                <div class='total-forest-highlight'></div>
                <div class='total-mountain-highlight'></div>
                <div class='total-ocean-highlight'></div>
              </div>
              <div class='storm-blocked' id='blocked-stormLeft-${pId}'>
                <i class="fa6 fa6-times"></i>
                <i class="fa6 fa6-times"></i>
                <i class="fa6 fa6-times"></i>
              </div>
            </div>
            <div class='powers-blocked' id='powers-blocked-stormLeft-${pId}'>
              <i class="fa6 fa6-times"></i>
              ${this.formatSvgIcon('played') + ' ' + this.formatSvgIcon('hand') + ' ' + this.formatSvgIcon('reserve')}
              <i class="fa6 fa6-times"></i>
            </div>
          </div>
          <div class='player-board-hero' id='board-hero-${pId}'>
            <div class="altered-first-player-holder" id="firstPlayer-${player.id}"></div>
          </div>
          <div class='player-board-storm storm-right' id='board-stormRight-${pId}'>
            <div class="total-biomes">
              <div class='total-forest'></div>
              <div class='total-mountain'></div>
              <div class='total-ocean'></div>
              <div class='total-biomes-highlights'>
                <div class='total-forest-highlight'></div>
                <div class='total-mountain-highlight'></div>
                <div class='total-ocean-highlight'></div>
              </div>
              <div class='storm-blocked' id='blocked-stormRight-${pId}'>
                <i class="fa6 fa6-times"></i>
                <i class="fa6 fa6-times"></i>
                <i class="fa6 fa6-times"></i>
              </div>
            </div>
            <div class='powers-blocked' id='powers-blocked-stormRight-${pId}'>
              <i class="fa6 fa6-times"></i>
              ${this.formatSvgIcon('played') + ' ' + this.formatSvgIcon('hand') + ' ' + this.formatSvgIcon('reserve')}
              <i class="fa6 fa6-times"></i>
            </div>
          </div>

          <div class='player-board-hand' id='player-board-hand-${pId}'>
            <div class='player-board-limbo' id='board-limbo-${player.id}'></div>
            <div class='player-hand' id='hand-${player.id}'></div>
          </div>

          <div class='player-board-infos'>
            <div class='player-board-name'>${player.name}</div>

            <div class='handCount-holder'>
              <span class="player-handCount" id="counter-${player.id}-handCount-bis"></span>
    
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 296.664 296.664">
                <path d="M 58.355,226.748 V 69.414 c 0,-1.709 0.294,-3.391 0.526,-5.039 L 13.778,79.057 C 3.316,82.455 -2.42,93.797 0.979,104.258 l 48.639,149.633 c 2.738,8.428 10.639,13.816 19.075,13.816 2.035,0 4.109,-0.315 6.143,-0.975 l 12.796,-4.211 C 71.066,259.213 58.355,244.242 58.355,226.748 Z" />
                <path d="M 91.098,203.275 139.715,53.673 c 0.491,-1.512 1.078,-3.342 1.746,-4.342 H 94.688 c -11,0 -20.333,9.082 -20.333,20.082 v 157.334 c 0,11 9.333,20.584 20.333,20.584 h 15.969 C 94.061,239.332 85.361,220.932 91.098,203.275 Z" />
                <path d="M 282.848,79.057 180.134,45.684 c -2.034,-0.662 -4.102,-0.975 -6.138,-0.975 -8.436,0 -16.326,5.387 -19.064,13.814 l -48.617,149.633 c -3.399,10.463 2.379,21.803 12.841,25.203 l 102.713,33.373 c 2.034,0.66 4.102,0.975 6.138,0.975 8.436,0 16.326,-5.389 19.064,-13.816 L 295.689,104.258 C 299.088,93.797 293.31,82.455 282.848,79.057 Z" />
              </svg>
            </div>
          </div>
        </div>`;
    },

    tplPlayerPanel(player) {
      return `
      <div class='player-info'>
        <div class='mana-counter-holder'>
          <span class="mana-counter" id="counter-${player.id}-mana"></span>/<span class="mana-counter" id="counter-${
        player.id
      }-totalMana"></span>
          
          ${this.formatIcon('first-player')}
        </div>

        <div class='handCount-holder'>
          <span class="player-handCount" id="counter-${player.id}-handCount"></span>

          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 296.664 296.664">
            <path d="M 58.355,226.748 V 69.414 c 0,-1.709 0.294,-3.391 0.526,-5.039 L 13.778,79.057 C 3.316,82.455 -2.42,93.797 0.979,104.258 l 48.639,149.633 c 2.738,8.428 10.639,13.816 19.075,13.816 2.035,0 4.109,-0.315 6.143,-0.975 l 12.796,-4.211 C 71.066,259.213 58.355,244.242 58.355,226.748 Z" />
            <path d="M 91.098,203.275 139.715,53.673 c 0.491,-1.512 1.078,-3.342 1.746,-4.342 H 94.688 c -11,0 -20.333,9.082 -20.333,20.082 v 157.334 c 0,11 9.333,20.584 20.333,20.584 h 15.969 C 94.061,239.332 85.361,220.932 91.098,203.275 Z" />
            <path d="M 282.848,79.057 180.134,45.684 c -2.034,-0.662 -4.102,-0.975 -6.138,-0.975 -8.436,0 -16.326,5.387 -19.064,13.814 l -48.617,149.633 c -3.399,10.463 2.379,21.803 12.841,25.203 l 102.713,33.373 c 2.034,0.66 4.102,0.975 6.138,0.975 8.436,0 16.326,-5.389 19.064,-13.816 L 295.689,104.258 C 299.088,93.797 293.31,82.455 282.848,79.057 Z" />
          </svg>
        </div>
      </div>`; // + (player.id == this.player_id ? `<div id='open-all-cards-modal'>Show all cards</div>` : '')
    },

    notif_setupPlayer(n) {
      debug('Notif: setupPlayer', n);
      let pId = n.args.player_id;
      if (this.isFastMode()) {
        this.closeOverlay();
      }

      // Update faction
      let faction = n.args.faction;
      this.gamedatas.players[pId].faction = faction;
      $(`player-board-${pId}`).setAttribute('data-faction', faction);

      // Slide hero
      let hero = n.args.card;
      delete this.tooltips[`card-${hero.id}`];
      this.addCard(hero, `hand-${pId}`);

      this.slide(`card-${hero.id}`, `board-hero-${pId}`, { clearTransform: true, phantom: false }).then(() => {
        n.args.meeples.forEach((meeple) => {
          this.addMeeple(meeple, `card-${hero.id}`);
          this.slide(`meeple-${meeple.id}`, this.getMeepleContainer(meeple));
        });
      });

      let o = $(`card-${hero.id}`);
      if (hero.properties && hero.properties.extraDatas && hero.properties.extraDatas.counterName) {
        o.dataset.counter = hero.properties.extraDatas.counter;
      } else {
        delete o.dataset.counter;
      }

      // Deck count
      this._playerCounters[pId]['deckCount'].toValue(n.args.deckCount);
    },

    ////////////////////////////////////////////////////
    //   ____                  _
    //  / ___|___  _   _ _ __ | |_ ___ _ __ ___
    // | |   / _ \| | | | '_ \| __/ _ \ '__/ __|
    // | |__| (_) | |_| | | | | ||  __/ |  \__ \
    //  \____\___/ \__,_|_| |_|\__\___|_|  |___/
    //
    ////////////////////////////////////////////////////
    /**
     * Create all the counters for player panels
     */
    setupPlayersCounters() {
      this._playerCounters = {};
      // this._playerCountersMeeples = {};
      this.forEachPlayer((player) => {
        let pId = player.id;
        let c = {};

        // MANA
        c.totalMana = this.createCounter([`counter-${pId}-totalMana`, `counter-board-${pId}-totalMana`], player.totalMana, (v) =>
          this.onUpdateTotalManaCounter(pId, v)
        );
        c.mana = this.createCounter([`counter-${pId}-mana`, `counter-board-${pId}-mana`], player.mana, (v) =>
          this.onUpdateManaCounter(pId, v)
        );

        // HAND
        c.handCount = this.createCounter([`counter-${pId}-handCount`, `counter-${pId}-handCount-bis`], player.handCount, (v) =>
          this.onUpdateHandCountCounter(pId, v)
        );

        // DECK COUNT
        c.deckCount = this.createCounter([`counter-${pId}-deckCount`], player.deckCount);

        // DISCARD COUNT
        c.discardCount = this.createCounter([`counter-${pId}-discardCount`], player.discardCount ?? 0);

        this._playerCounters[pId] = c;
      });
    },

    /**
     * Update all the counters in player panels according to gamedatas, useful for reloading
     */
    updatePlayersCounters(anim = true) {
      this.forEachPlayer((player) => {
        PLAYER_COUNTERS.forEach((res) => {
          let value = player[res];
          this._playerCounters[player.id][res].goTo(value, anim);
        });
      });
      this.updatePassedPlayers();
    },

    onUpdateManaCounter(pId, v) {
      let container = $(`mana-gauge-${pId}`);
      [...container.querySelectorAll('.mana-gauge-slot')].forEach((slot, i) => slot.classList.toggle('available', i < v));
    },

    onUpdateTotalManaCounter(pId, v) {
      let container = $(`mana-gauge-${pId}`);
      let slots = [...container.querySelectorAll('.mana-gauge-slot')];
      for (let i = slots.length; i < v; i++) {
        container.insertAdjacentHTML('beforeend', `<div class='mana-gauge-slot'><div class='mana-gem'></div></div>`);
      }
      for (let i = v; i < slots.length; i++) {
        slots[i].remove();
      }
    },
    onUpdateHandCountCounter(pId, v) {
      if (pId == this.player_id) return;
      let container = $(`hand-${pId}`);
      let cards = [...container.querySelectorAll('.altered-card')];
      for (let i = cards.length; i < v; i++) {
        this.addFakeCard(container);
      }
    },

    updateBiomeTotals() {
      this.forEachPlayer((player) => {
        let pId = player.id;
        let opponentId = this.getOpponent(pId);
        let totals = { forest: 0, mountain: 0, ocean: 0 };

        // Update the total for each biome and each side
        ['stormLeft', 'stormRight'].forEach((storm) => {
          let container = $(`board-${storm}-${pId}`);
          p = this.gamedatas.biomes[pId][storm];

          // Update
          let sizes = this.getBiomesUISizes(p);
          ['forest', 'mountain', 'ocean'].forEach((biome) => {
            let o = container.querySelector(`.total-${biome}`);
            o.innerHTML = p[biome];
            o.dataset.size = sizes[biome];
            totals[biome] += p[biome];
          });
        });

        let container = $(`tie-breaker-biomes-${pId}`);
        ['forest', 'mountain', 'ocean'].forEach((biome) => {
          let o = container.querySelector(`.total-${biome}`);
          o.innerHTML = totals[biome];
        });
      });
    },

    updateMovements() {
      let willMove = {};

      this.forEachPlayer((player) => {
        let pId = player.id;
        willMove[pId] = {};

        // Update will move for each biome
        ['hero', 'companion'].forEach((type) => {
          let stormSide = type == 'hero' ? 'stormLeft' : 'stormRight';
          let container = $(`board-${stormSide}-${pId}`);

          let moving = false;
          ['forest', 'mountain', 'ocean'].forEach((biome) => {
            let o = container.querySelector(`.total-${biome}`);
            o.dataset.willProgress = this.gamedatas.movements[pId] && this.gamedatas.movements[pId][type][biome];
            if (o.dataset.willProgress == 2) moving = true;
          });

          willMove[pId][type] = moving;
        });
      });

      [...$('storm-container').querySelectorAll('.altered-meeple')].forEach((meeple) => {
        let pId = meeple.dataset.side == 'opponent' ? this.topPId : this.bottomPId;
        let type = meeple.dataset.type;
        let willProgress = willMove[pId][type];
        meeple.classList.toggle('willProgress', willProgress);
      });

      this.updateUselessStormCards();
    },

    updateUselessStormCards() {
      let minPos = 8,
        maxPos = 0;
      [...$('storm-container').querySelectorAll('.altered-meeple')].forEach((meeple) => {
        let type = meeple.dataset.type;
        let pos = +meeple.parentNode.dataset.x;
        if (type == 'hero') minPos = Math.min(minPos, pos);
        else maxPos = Math.max(maxPos, pos);
      });

      if (minPos == 8 && maxPos == 0) return;

      let minCard = Math.floor((minPos + 1) / 2),
        maxCard = Math.floor((maxPos + 1) / 2);
      for (let i = 0; i < 5; i++) {
        $(`storm-card-container-${i}`).classList.toggle('useless', i < minCard || i > maxCard);
      }
    },

    updatePassedPlayers(pId = null) {
      if (pId !== null) {
        this.gamedatas.passedPlayers.push(pId);
      }

      let skipped = this.gamedatas.passedPlayers;
      $('focus-storm-overlay').classList.toggle('mePassed', skipped.includes(this.bottomPId));
      $('focus-storm-overlay').classList.toggle('opponentPassed', skipped.includes(this.topPId));
    },

    updateReserveSlots(pId = null) {
      this.forEachPlayer((player) => {
        let pId = player.id;
        let reserveContainer = $(`board-reserve-${pId}`);
        this.displayWarningSizeLimitIfNeeded(player, 'reserve', reserveContainer);
      });
    },

    updateLandmarkSlots(pId = null) {
      this.forEachPlayer((player) => {
        let pId = player.id;
        let landmarkContainer = $(`board-landmark-${pId}`);
        this.displayWarningSizeLimitIfNeeded(player, 'landmark', landmarkContainer);
      });
    },

    notif_passTurn(n) {
      debug('Notif: someone is over', n);
      this.updatePassedPlayers(n.args.player_id);
    },

    updatePowersBlockedExpeditions() {
      Object.entries(this.gamedatas.powersBlockedExpeditions).forEach(([pId, blocked]) => {
        ['stormLeft', 'stormRight'].forEach((expe) => {
          $(`powers-blocked-${expe}-${pId}`).classList.toggle('active', blocked[expe]);
        });
      });
    },

    updateBlockedExpeditions() {
      Object.entries(this.gamedatas.blockedExpeditions).forEach(([pId, blocked]) => {
        ['stormLeft', 'stormRight'].forEach((expe) => {
          $(`blocked-${expe}-${pId}`).classList.toggle('active', blocked[expe]);
        });
      });
    },

    /**
     * Use this tpl for any counters that represent qty of meeples in "reserve", eg xtokens
     */
    tplResourceCounter(player, res, prefix = '') {
      return this.formatString(`
          <div class='player-resource resource-${res}'>
            <span id='${prefix}counter-${player.id}-${res}' 
              class='${prefix}resource-${res}'></span>${this.formatIcon(res)}
            <div class='reserve' id='${prefix}reserve-${player.id}-${res}'></div>
          </div>
        `);
    },

    notif_moveStormToken(n) {
      debug('Notif : moving storm token', n);
      // TODO
    },

    notif_afterYou(n) {
      debug('Notif: after you passing', n);
      // Update counters
      this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
      this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);
    },

    notif_pay(n) {
      debug('Notif: pay', n);
      this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
      this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);
    },

    notif_updateTotalMana(n) {
      debug('Notif: update total mana', n);
      Object.keys(n.args.manas).forEach((id) => {
        this._playerCounters[id]['mana'].toValue(n.args.manas[id]);
        this._playerCounters[id]['totalMana'].toValue(n.args.manas[id]);
      });
    },
  });
});
