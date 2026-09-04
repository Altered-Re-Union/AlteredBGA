define(['dojo', 'dojo/_base/declare', g_gamethemeurl + 'modules/js/cardsData.js'], (dojo, declare) => {
  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  const isObject = (obj) => {
    return typeof obj === 'object' && obj !== null && !Array.isArray(obj);
  };

  const HERO = 'hero';
  const CHARACTER = 'character';
  const PERMANENT = 'permanent';
  const SPELL = 'spell';
  const TOKEN = 'token';
  let CARDS_DATA = {};

  return declare('altered.cards', null, {
    getCardInfos(cardId) {
      let card = { id: cardId };
      this.loadSaveCard(card);
      return card;
    },
    // getCardName(cardId) {
    //   let card = this.getCardInfos(cardId);
    //   return this.fsr('${card_name}', { i18n: ['card_name'], card_name: _(card.name), card_id: card.id });
    // },

    setupCards() {
      // This function is refreshUI compatible
      let cardIds = this.gamedatas.cards.map((card) => {
        if (!$(`card-${card.id}`)) {
          this.addCard(card);
        }

        let o = $(`card-${card.id}`);
        if (!o) return null;

        let container = this.getCardContainer(card);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
          if (!container.classList.contains('player-hand')) {
            o.style.transform = '';
            o.style.left = '0px';
            o.style.top = '0px';
            o.style.position = '';
          }

          if (container.classList.contains('mana-modal')) {
            this.tooltips[o.id].disable();
          } else {
            this.tooltips[o.id].enable();
          }
        }

        // Update tapped state
        o.classList.toggle('tapped', card.properties && card.properties.tapped == true);

        // Update counters
        if (card.properties && card.properties.extraDatas && card.properties.extraDatas.counterName) {
          o.dataset.counter = card.properties.extraDatas.counter;
        } else {
          delete o.dataset.counter;
        }

        // Minimize card except in hand and in discard
        let isFull =
          container.classList.contains('player-hand') ||
          container.classList.contains('player-board-discard') ||
          container.classList.contains('player-board-limbo') ||
          container.classList.contains('mana-modal') ||
          card.location.indexOf('reveal-') >= 0;
        o.classList.toggle('mini-card', !isFull);

        return card.id;
      });
      document.querySelectorAll('.altered-card').forEach((oCard) => {
        if (
          !cardIds.includes(parseInt(oCard.getAttribute('data-id'))) &&
          !oCard.classList.contains('card-back') &&
          !oCard.parentNode.classList.contains('player-hand') &&
          !oCard.parentNode.classList.contains('mana-modal')
        ) {
          this.destroy(oCard);
        }
      });
    },

    /**
     * Smarter buffering of card details (to not resend them over notification again)
     */
    loadSaveCard(card) {
      let cardInfos = CARDS_DATA[card.id];
      for (let key in cardInfos) {
        card[key] = cardInfos[key];
      }
    },

    addCard(card, container = null) {
      let ob = $(`card-${card.id}`);
      // if it already exist with the id
      if (ob) {
        this.destroy(ob);
      }

      if (card.fake) {
        this.place('tplFakeCard', card, container);
        return;
      }

      CARDS_DATA[card.id] = card;
      // this.loadSaveCard(card);
      if (container === null) {
        container = this.getCardContainer(card);
      }

      let o = this.place('tplCard', card, container);
      if (o !== undefined) {
        this.addCustomTippyTooltip(o.id, this.tplCardTooltip(card), {
          disablingParentClasses: ['mana-modal', 'no-tooltip'],
          forceRecreate: true,
        });
        if (this._loadingComplete) {
          this.autofitCardFrame(o);
        }
      }
      if (card.location == 'discard') {
        this.bringDiscardCounterToFront(card.pId);
      }
    },

    addFakeCard(container) {
      let id = this._fakeIndex--;
      container.insertAdjacentHTML('beforeend', this.tplFakeCard({ id }));
      return `card-${id}`;
    },

    getCardContainer(card) {
      let t = card.location.split('_');
      let type = card.properties.type;
      if (card.location == 'hand') {
        return $(`hand-${card.pId}`);
      } else if (['stormLeft', 'stormRight', 'reserve', 'permanent', 'landmark', 'limbo', 'discard'].includes(card.location)) {
        return $(`board-${card.location}-${card.pId}`);
      } else if (type == HERO) {
        return $(card.location);
      } else if (card.location == 'mana') {
        return $(`mana-cards-${card.pId}`);
      } else if (card.location == 'reveal-' + card.pId) {
        return $(card.location);
      }

      return $('test-cards');
    },

    isCardInDiscardPile(pId, cardId) {
      let oCard = $(`card-${cardId}`);
      let discard = $(`board-discard-${pId}`);
      return !!(oCard && discard && discard.contains(oCard));
    },

    incDiscardCount(pId, delta) {
      if (!this._playerCounters[pId]?.discardCount) return;
      this._playerCounters[pId].discardCount.incValue(delta);
    },

    bringDiscardCounterToFront(pId) {
      let zone = $(`board-discard-zone-${pId}`);
      let holder = zone?.querySelector('.discard-counter-holder');
      if (zone && holder) {
        zone.appendChild(holder);
      }
    },

    updateDiscardCountFromCards(cards) {
      cards.forEach((card) => {
        if (card.location == 'hand') return;
        let wasInDiscard = this.isCardInDiscardPile(card.pId, card.id);
        if (card.location == 'discard') {
          this.incDiscardCount(card.pId, 1);
        } else if (wasInDiscard) {
          this.incDiscardCount(card.pId, -1);
        }
      });
    },

    adjustHand(container, pos = 'bottom') {
      // let items = [...container.querySelectorAll('.altered-card'), ...container.querySelectorAll('.flip-container')];
      let items = [...container.childNodes].filter((t) => !t.classList.contains('draggable-mirror'));
      console.log(items);
      let n = items.length;
      const THRESHOLD = 8;
      if (n < THRESHOLD) n = n % 2 == 0 ? THRESHOLD : THRESHOLD + 1;

      let a = Math.min(450, n * 50); // X-DISTANCE BETWEEN MAX LEFT CARD AND CENTER
      let b = n < THRESHOLD ? 80 : 60; // Y-DISTANCE BETWEEN LOWEST CARD AND UPPEST CARD
      let r = (a * a + b * b) / (2 * b); // RADIUS OF THE CIRCLE
      let halfAngle = Math.asin(a / r);
      let alpha = (2 * halfAngle) / (n - 1);

      items.forEach(async (item, i) => {
        if (item.animationDelay) await this.wait(item.animationDelay);
        delete item.animationDelay;

        // Virtual index (useful if less than THRESHOLD cards)
        let j = i;
        if (items.length < THRESHOLD) j += parseInt((n - items.length) / 2);

        // Angle
        let itemAngle = -halfAngle + alpha * j;
        let dy = item.offsetHeight / 2 - b - (Math.cos(itemAngle) * r - (r - 80));
        if (pos == 'top') {
          dy = -dy - item.offsetHeight;
          itemAngle = -itemAngle;
        }
        item.style.transform = `rotate(${itemAngle}rad) translateY(${dy}px)`;

        // Origin
        item.style.transformOrigin = `${pos} center`;

        // Position
        let x = (j - n / 2) * 0.8 * item.offsetWidth;
        //        item.style.left = `calc(50% ${x < 0 ? '- ' : ' +'} ${Math.abs(x)}px)`;
        item.style.left = `${x}px`;
        item.style.top = '0px';

        let removeSpeed = () => {
          delete item.dataset.animationSpeed;
          item.removeEventListener('transitionend', removeSpeed);
        };
        if (item.dataset.animationSpeed == 'none') {
          await this.wait(1);
          removeSpeed();
        } else {
          item.addEventListener('transitionend', removeSpeed);
        }
      });
    },

    clearHandTransform(container) {
      let items = [...container.querySelectorAll('.altered-card')];
      items.forEach((item, i) => {
        item.style.transform = `rotate(0rad) translateY(0px)`;
        item.style.left = '0px';
        item.style.top = '0px';
        item.style.position = null;
      });
    },

    setupDiscardModal(player) {
      let pId = player.id;
      this._discardModals[pId] = new customgame.modal('discardDisplay' + pId, {
        class: 'altered_discard_popin',
        autoShow: false,
        closeIcon: null,
        closeAction: 'hide',
        title: this.fsr(_('Discard of ${player_name}'), {
          player_name: player.name,
        }),
        verticalAlign: 'flex-start',
        contentsTpl: `<div class='discard-modal' id='discard-cards-${pId}'></div>`,
        scale: 0.9,
        breakpoint: 800,
        onStartShow: () => {
          this.closeCurrentTooltip(false);
          $(`discard-cards-${pId}`).insertAdjacentElement('beforeend', $(`board-discard-${pId}`));
          $(`board-discard-${pId}`).classList.add('no-tooltip');
        },
        onStartHide: () => {
          this.closeCurrentTooltip(false);
          $(`board-discard-zone-${pId}`).insertAdjacentElement('beforeend', $(`board-discard-${pId}`));
          $(`board-discard-${pId}`).classList.remove('no-tooltip');
        },
        onShow: () => this.closeCurrentTooltip(false),
      });
      $(`board-discard-zone-${pId}`).addEventListener('click', () => {
        this.closeCurrentTooltip(false);
        if (this._discardModals[pId].isDisplayed()) this._discardModals[pId].hide();
        else this._discardModals[pId].show();
      });
      $(`discard-cards-${pId}`).addEventListener('click', () => {
        this.closeCurrentTooltip(false);
        if (this._discardModals[pId].isDisplayed()) this._discardModals[pId].hide();
      });
    },

    setupManaModal(player) {
      let pId = player.id;
      this._manaModal = new customgame.modal('manaDisplay', {
        class: 'altered_mana_popin',
        autoShow: false,
        closeAction: 'hide',
        title: _('Your mana cards'),
        verticalAlign: 'flex-start',
        contentsTpl: `<div class='mana-modal' id='mana-cards-${pId}'></div>`,
        scale: 0.9,
        breakpoint: 800,
        onStartShow: () => this.closeCurrentTooltip(false),
        onStartHide: () => this.closeCurrentTooltip(false),

        onShow: () => this.closeCurrentTooltip(false),
      });
      $(`mana-gauge-${pId}`).parentNode.addEventListener('click', () => {
        this.closeCurrentTooltip(false);
        if (this._manaModal.isDisplayed()) this._manaModal.hide();
        else this._manaModal.show();
      });
      $('popin_manaDisplay_title').addEventListener('click', () => this._manaModal.hide());
    },

    openAllCardsModal() {
      let modal = new customgame.modal('showAllCards', {
        class: 'altered_popin',
        autoShow: true,
        closeIcon: null,
        contentsTpl: `<div id='all-cards-wrapper'></div>`,
      });

      this.takeAction('actDisplayAllCards', { lock: false }, false).then((response) => {
        let data = response.data;
        data.forEach((card, i) => {
          card.id = 'allcard-' + i;
          $('all-cards-wrapper').insertAdjacentHTML(
            'beforeend',
            `<div class='card-compare'>
              ${this.tplCard(card)}
              <div class='card-mockup' style='background-image:url("${g_gamethemeurl}misc/API/assets/${
                card.properties.uid
              }.jpg");'></div>
            </div>`
          );
        });
      });
    },

    /**
     * Prepare cards for selection : get cards corresponding to ids,
     *   and make other in the same "location" unselectable
     */
    prepareCardsForSelection(cardIds, location = 'hand') {
      let container = null;
      if (location == 'hand') {
        this.openHand();
        container = $(`hand-${this.player_id}`);
      } else if (location == 'scoringHand') {
        this.openScoringHand();
        container = $(`scoring-hand-${this.player_id}`);
      } else if (location == 'pool') {
        container = $('cards-pool');
      } else if (location == 'mana') {
        container = $(`mana-cards-${this.player_id}`);
        this._manaModal.show();
      } else if (location == 'choice') {
        this._cardsChoiceModal = new customgame.modal('chooseCards', {
          class: 'altered_popin',
          autoShow: true,
          closeIcon: 'fa-times',
          closeAction: 'hide',
          verticalAlign: 'flex-start',
          contentsTpl: `<div id='choose-cards'></div><div id='choose-cards-footer'></div>`,
          scale: 0.9,
          breakpoint: 800,
        });
        this.addPrimaryActionButton('btnShowCards', _('Show cards'), () => this._cardsChoiceModal.show());
        container = $('choose-cards');
      }

      let elements = {};
      cardIds.forEach((cardId) => {
        let oCard = $(`card-${cardId}`);
        if (!oCard) {
          console.error(cardId);
          let card = { id: cardId };
          // this.loadSaveCard(card);
          // this.addZooCard(card, container);
          oCard = $(`card-${cardId}`);
        }
        elements[cardId] = oCard;
        oCard.classList.remove('unselectable');
        oCard.classList.add('selectable');
      });

      let containers = [$(`hand-${this.player_id}`), $(`scoring-hand-${this.player_id}`), $('cards-pool'), $('choose-cards')];
      containers.forEach((container) => {
        if (container) {
          [...container.querySelectorAll('.ark-card:not(.unselectable):not(.selectable)')].forEach((elt) =>
            elt.classList.add('unselectable')
          );
        }
      });

      return elements;
    },

    closeChooseCardsModal() {
      if (this._cardsChoiceModal) {
        this._cardsChoiceModal.destroy();
        delete this._cardsChoiceModal;
      }
    },

    /**
     * onSelectNCards : syntaxic sugar for calling prepareCardsForSelection and then onSelectN
     *  + default behavior for keep/discard choice when selecting n/2 cards over a pool of n cards
     */
    onSelectNCards(cardIds, config, location = 'hand') {
      let elements = this.prepareCardsForSelection(cardIds, location);
      config.elements = elements;
      // let callback = config.confirmMsg
      //   ? (selectedElements) => {
      //       this.askConfirmation(config.confirmMsg(selectedElements), () => config.callback(selectedElements));
      //     }
      //   : config.callback;

      if (location == 'choice') {
        config.btnContainer = 'choose-cards-footer';
      }
      if (location == 'mana') {
        config.btnContainer = 'popin_manaDisplay_subtitle';
      }

      return this.onSelectN(config);
    },

    onEnteringStateDiscard(args) {
      if (args.destination == 'mana' && args.n == 1) {
        this.onEnteringStateManaDiscard(args);
        return;
      }

      this.onSelectNCards(args._private.cards, {
        n: args.n,
        class: 'selectable',
        confirmText: _('Confirm discard'),
        callback: (selectedElements, ignoredElements) => this.takeAtomicAction('actDiscard', [selectedElements]),
      });
    },

    onEnteringStateNightCleanup(args) {
      let nReserve = args.nReserve,
        nLandmarks = args.nLandmarks;

      let reserveSelector = null,
        landmarksSelector = null;

      let selectedReserve = [],
        selectedLandmarks = [];

      let updateButtons = () => {
        if ($('btnConfirmChoice')) $('btnConfirmChoice').remove();
        if (selectedReserve.length == nReserve && selectedLandmarks.length == nLandmarks) {
          this.addPrimaryActionButton('btnConfirmChoice', _('Confirm discard'), () =>
            this.takeAtomicAction('actNightCleanup', [selectedReserve, selectedLandmarks])
          );
        }

        if ($('btnCancelChoice')) $('btnCancelChoice').remove();
        if (selectedReserve.length > 0 || selectedLandmarks.length > 0) {
          this.addSecondaryActionButton('btnCancelChoice', _('Cancel'), () => {
            if (reserveSelector) reserveSelector.cancelSelection();
            if (landmarksSelector) landmarksSelector.cancelSelection();
          });
        }
      };

      if (nReserve > 0) {
        reserveSelector = this.onSelectNCards(args._private.reserveCards, {
          n: nReserve,
          class: 'selectable',
          confirmBtn: false,
          cancelBtn: false,
          updateCallback: (cIds) => {
            selectedReserve = cIds;
            updateButtons();
          },
        });
      }

      if (nLandmarks > 0) {
        landmarksSelector = this.onSelectNCards(args._private.landmarkCards, {
          n: nLandmarks,
          class: 'selectable',
          confirmBtn: false,
          cancelBtn: false,
          updateCallback: (cIds) => {
            selectedLandmarks = cIds;
            updateButtons();
          },
        });
      }
    },

    onEnteringStateManaDiscard(args) {
      let selectedCard = null;
      let unselectIfNeeded = () => {
        if (!selectedCard) return;
        let oCard = $(`card-${selectedCard}`);
        // oCard.style.transform = oCard.backup.transform;
        // oCard.style.left = oCard.backup.left;
        // oCard.style.top = oCard.backup.top;
        oCard.classList.remove('selectedToMana', 'selected');
        selectedCard = null;
        if ($('btnConfirm')) $('btnConfirm').remove();
      };

      this.onClick('altered-board-me', () => {
        unselectIfNeeded();
      });

      if ($(`btnPassAction`)) {
        this.onClick('btnPassAction', () => {
          unselectIfNeeded();
        });
      }

      let cards = args._private.cards;
      cards.forEach((cardId) => {
        if (!$(`card-${cardId}`)) {
          return;
        }
        this.onClick(`card-${cardId}`, () => {
          if (cardId == selectedCard) {
            unselectIfNeeded();
          } else {
            unselectIfNeeded();
            selectedCard = cardId;

            let oCard = $(`card-${selectedCard}`);
            oCard.classList.add('selectedToMana', 'selected');

            // Backup previous pos and transform
            oCard.backup = {
              transform: oCard.style.transform,
              left: oCard.style.left || '0px',
              top: oCard.style.top || '0px',
            };

            this.addPrimaryActionButton('btnConfirm', _('Confirm Mana'), () =>
              this.takeAtomicAction('actDiscard', [[selectedCard]])
            );
          }
        });
      });
    },

    onLeavingStateDiscard() {
      if (this.isSpectator) return;
      let oCard = $(`hand-${this.player_id}`).querySelector('.selectedToMana');
      console.log('Leaving discard', oCard);
      if (!oCard) return;

      oCard.style.transform = oCard.backup.transform;
      oCard.style.left = oCard.backup.left;
      oCard.style.top = oCard.backup.top;
      oCard.classList.remove('selectedToMana', 'selected');
    },

    /**
     * Update defenders
     */
    updateDefenders() {
      $('ebd-body')
        .querySelectorAll('.defender-marker')
        .forEach((e) => e.remove());

      Object.values(this.gamedatas.defenders).forEach((defendersBySide) => {
        Object.values(defendersBySide).forEach((defenderIds) => {
          defenderIds.forEach((cardId) =>
            $(`card-${cardId}`).insertAdjacentHTML('beforeend', '<i class="fa6 fa6-chess-rook defender-marker"></i>')
          );
        });
      });
    },

    /**
     * Private notification for the player drawing the card :
     *  create the cards and slide them in hand
     */
    notif_pDrawCards(n) {
      debug('Notif: private drawing cards', n);
      // this.closeChooseCardsModal();
      let counter = 'handCount';

      this._playerCounters[this.player_id]['deckCount'].incValue(-n.args.cards.length);
      if (this.isFastMode()) {
        n.args.cards.forEach((card) => {
          this.addCard(card);
        });
        this._playerCounters[this.player_id][counter].incValue(n.args.cards.length);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(-n.args.cards.length);
        return;
      }

      Promise.all(
        n.args.cards.map((card, i) => {
          let source = n.args.stealing ? $(`counter-${n.args.stealing}-${counter}`) : $(`board-deck-${this.player_id}`);
          this.addCard(card, source);

          let cardId = `card-${card.id}`;
          $(cardId).animationDelay = 100 * (n.args.cards.length - i);
          $(cardId).dataset.animationSpeed = 'medium';
          this.changeParent($(cardId), $(`hand-${n.args.player_id}`));
          return this.wait(100 * i + 700);

          // return this.wait(100 * i).then(() => {

          //   let to = null;
          //   let container = this.getCardContainer(card);
          //   if (!isVisible(container)) to = $('floating-hand-button');

          //   return this.slide(`card-${card.id}`, container, {
          //     from: source,
          //     duration: 1000,
          //     to,
          //   });
          // });
        })
      ).then(() => {
        this._playerCounters[this.player_id][counter].incValue(n.args.cards.length);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(-n.args.cards.length);

        this.notifqueue.setSynchronousDuration(100);
      });
    },

    /**
     * Public notification when drawing cards:
     *  ignore if current player is the one drawing card
     *  slide fakes cards from titlebar to player panel and increase hand count
     */
    notif_drawCards(n) {
      debug('Notif: public drawing cards', n);
      // this.closeChooseCardsModal();
      let counter = 'handCount';

      let nCards = n.args.n;
      this._playerCounters[n.args.player_id]['deckCount'].incValue(-nCards);
      if (this.isFastMode()) {
        this._playerCounters[n.args.player_id][counter].incValue(nCards);
        return;
      }

      Array.from(Array(nCards), (x, i) => i).map((i) => {
        let cardId = this.addFakeCard($(`board-deck-${n.args.player_id}`));
        $(cardId).animationDelay = 100 * (nCards - i);
        $(cardId).dataset.animationSpeed = 'medium';
        this.changeParent($(cardId), $(`hand-${n.args.player_id}`));
      });
      this.wait(100 * nCards + 1000).then(() => {
        this._playerCounters[n.args.player_id][counter].incValue(nCards);
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_publicDrawCards(n) {
      debug('Notif: public drawing cards public', n);
      // this.closeChooseCardsModal();
      let counter = 'handCount';
      let nInHand = 0;

      this._playerCounters[n.args.player_id]['deckCount'].incValue(-n.args.cards.length);
      if (this.isFastMode()) {
        n.args.cards.forEach((card) => {
          this.addCard(card);
          if (card.location == 'hand') nInHand++;
          if (card.properties.hasOwnProperty('tapped') && card.properties.tapped == true) {
            $(`card-${card.id}`).classList.add('tapped');
          }
          if (card.location.indexOf('reveal') == 0) {
            this._playerCounters[n.args.player_id]['deckCount'].incValue(1);
          }
          if (card.location == 'discard') {
            this.incDiscardCount(n.args.player_id, 1);
          }
        });
        this._playerCounters[n.args.player_id][counter].incValue(nInHand);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(-n.args.cards.length);
        return;
      }

      Promise.all(
        n.args.cards.map((card, i) => {
          if (card.location == 'hand') nInHand++;

          return this.wait(100 * i).then(() => {
            this.addCard(card);

            let to = null;
            let container = this.getCardContainer(card);
            if (!isVisible(container)) to = $('floating-hand-button');
            let source = n.args.stealing ? $(`counter-${n.args.stealing}-${counter}`) : $(`board-deck-${n.args.player_id}`);
            if (card.properties.hasOwnProperty('tapped') && card.properties.tapped == true) {
              $(`card-${card.id}`).classList.add('tapped');
            }
            if (card.location.indexOf('reveal') == 0) {
              this._playerCounters[n.args.player_id]['deckCount'].incValue(1);
            }
            if (card.location == 'discard') {
              this.incDiscardCount(n.args.player_id, 1);
            }
            return this.slide(`card-${card.id}`, container, {
              from: source,
              duration: 1000,
              to,
            });
          });
        })
      ).then(() => {
        this._playerCounters[n.args.player_id][counter].incValue(nInHand);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(-n.args.cards.length);

        this.notifqueue.setSynchronousDuration(100);
      });
    },

    /**
     * Private notification for the player discarding the card :
     *  slide them and destroy them
     */
    notif_pDiscardCards(n) {
      debug('Notif: private discarding cards', n);
      let counter = 'handCount';
      if (n.args.fromLocation && n.args.fromLocation.startsWith('deck')) {
        counter = 'deckCount';
      }
      let nonTappedMana = 0;
      let alreadyDiscarded = 0;
      if (n.args.hasOwnProperty('alreadyDiscarded')) {
        alreadyDiscarded = n.args.alreadyDiscarded;
      }
      this.closeOverlayIfOpened();

      if (this.isFastMode()) {
        n.args.cards.forEach((card) => {
          let o = $(`card-${card.id}`);
          if (o) {
            if (n.args.toMana) {
              $(`mana-cards-${this.player_id}`).insertAdjacentElement('beforeend', o);
              o.classList.remove('selectedToMana');
            } else {
              this.destroy(o);
            }
          }
          nonTappedMana += !card.properties.hasOwnProperty('tapped') || card.properties.tapped ? 1 : 0;
        });
        this._playerCounters[this.player_id][counter].incValue(-n.args.cards.length + alreadyDiscarded);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(n.args.cards.length);
        if (n.args.toMana) {
          this._playerCounters[this.player_id]['totalMana'].incValue(n.args.cards.length);
          this._playerCounters[this.player_id]['mana'].incValue(nonTappedMana);
        }
        return;
      }

      Promise.all(
        n.args.cards.map((card, i) => {
          // TO MANA
          if (n.args.toMana) {
            nonTappedMana += !card.properties.hasOwnProperty('tapped') || card.properties.tapped == false ? 1 : 0;
            let target = $(`mana-gauge-${this.player_id}`); //$(`counter-board-${this.player_id}-mana`);
            if (!$(`card-${card.id}`)) {
              this.addCard(card, `mana-cards-${this.player_id}`);

              let fakeCardId = this._fakeIndex--;
              let fakeCard = this.tplFakeCard({ id: fakeCardId });
              $(`board-deck-${this.player_id}`).insertAdjacentHTML('beforeend', fakeCard);
              return this.slide(`card-${fakeCardId}`, target, { destroy: true, container: 'altered-board-resizable' });
            } else {
              let oCard = $(`card-${card.id}`);
              oCard.classList.remove('selectedToMana');

              let fakeCardId = this._fakeIndex--;
              let fakeCard = this.tplFakeCard({ id: fakeCardId });
              return this.flipAndReplace(oCard, fakeCard)
                .then(() => {
                  let id = `card-${fakeCardId}`;
                  this.changeParent(id, target);
                  $(id).style.left = '0px';
                  $(id).style.top = '0px';
                  $(id).style.transform = '';
                  return this.wait(700);
                })
                .then(() => {
                  $(`mana-cards-${this.player_id}`).insertAdjacentElement('beforeend', oCard);
                  $(`card-${fakeCardId}`).remove();
                });
            }
          }
          // NORMAL
          else {
            let target = n.args.stealing ? $(`counter-${n.args.stealing}-${counter}`) : this.getVisibleTitleContainer();
            return this.slide(`card-${card.id}`, target, {
              delay: 100 * i,
              duration: 1000,
              destroy: true,
              phantom: false,
            });
          }
        })
      ).then(() => {
        this._playerCounters[this.player_id][counter].incValue(-n.args.cards.length + alreadyDiscarded);
        if (n.args.stealing) this._playerCounters[n.args.stealing][counter].incValue(n.args.cards.length);
        if (n.args.toMana) {
          this._playerCounters[this.player_id]['totalMana'].incValue(n.args.cards.length);
          this._playerCounters[this.player_id]['mana'].incValue(nonTappedMana);
          this.clearHandTransform($(`mana-cards-${this.player_id}`));
        }

        this.notifqueue.setSynchronousDuration(100);
      });
    },

    /**
     * Public notification when discarding cards:
     *  ignore if current player is the one discarding card
     *  slide fakes cards from player panel to titlebar and decrease hand count
     */
    notif_discardCards(n) {
      debug('Notif: public discarding hidden cards', n);
      this.closeOverlayIfOpened();
      if (n.args.player_id == this.player_id) {
        return;
      }

      let counter = 'handCount';
      if (n.args.fromLocation && n.args.fromLocation.startsWith('deck')) {
        counter = 'deckCount';
      }

      let nCards = n.args.n;
      let alreadyDiscarded = 0;
      if (n.args.hasOwnProperty('alreadyDiscarded')) {
        alreadyDiscarded = n.args.alreadyDiscarded;
      }
      let oCards = [...$(`hand-${n.args.player_id}`).querySelectorAll('.altered-card')];
      // FROM DECK
      if (n.args.fromLocation && n.args.fromLocation.startsWith('deck')) {
        oCards = [];
        for (let i = 0; i < nCards; i++) {
          let fakeCardId = this._fakeIndex--;
          let fakeCard = this.tplFakeCard({ id: fakeCardId });
          $(`board-deck-${n.args.player_id}`).insertAdjacentHTML('beforeend', fakeCard);
          oCards.push($(`card-${fakeCardId}`));
        }
      }

      if (this.isFastMode()) {
        this._playerCounters[n.args.player_id][counter].incValue(-nCards + alreadyDiscarded);
        if (n.args.toMana) {
          this._playerCounters[n.args.player_id]['totalMana'].incValue(nCards);
          this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
        }
        for (let i = 0; i < nCards; i++) {
          oCards[i].remove();
        }
        return;
      }

      Promise.all(
        Array.from(Array(nCards), (x, i) => i).map((i) => {
          return this.wait(100 * i).then(() => {
            let o = this.slide(
              oCards[i].id,
              n.args.toMana ? $(`counter-board-${n.args.player_id}-mana`) : this.getVisibleTitleContainer(),
              {
                duration: 1000,
                destroy: true,
                phantom: false,
                container: 'altered-board-resizable',
              }
            );
            oCards[i].style.transform = '';
            return o;
          });
        })
      ).then(() => {
        this._playerCounters[n.args.player_id][counter].incValue(-nCards + alreadyDiscarded);
        if (n.args.toMana) {
          this._playerCounters[n.args.player_id]['totalMana'].incValue(nCards);
          this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
        }

        this.notifqueue.setSynchronousDuration(200);
      });
    },

    notif_publicDiscard(n) {
      debug('Public discard', n);
      let pId = n.args.player_id;
      this.updateDiscardCountFromCards(n.args.cards);
      let oCards = [...$(`hand-${pId}`).querySelectorAll('.altered-card')];
      let indexCardReplacement = 0;

      if (this.isFastMode()) {
        [...n.args.cards].map((card, i) => {
          if (card.location == 'hand') return;
          if (n.args.hand === true) this._playerCounters[pId]['handCount'].incValue(-1);

          let id = `card-${card.id}`;
          if (card.location == 'destroy') {
            $(id).remove();
            return this.wait(1000);
          }

          if (!$(id)) {
            this.addCard(card);
          } else {
            let container = this.getCardContainer(card);
            if (container) {
              container.insertAdjacentElement('beforeend', $(id));
              if (n.args.hand === true || card.location == 'reserve') $(id).classList.add('mini-card');
              if (card.location == 'discard') {
                $(id).classList.remove('mini-card');
                $(id).classList.remove('tapped');
              }
              $(id).style.transform = '';
              $(id).style.transformOrigin = 'initial';
            } else if (card.location == 'mana') {
              $(id).remove();
            }
          }
        });
        n.args.cards.forEach((card) => {
          if (card.location == 'discard') this.bringDiscardCounterToFront(card.pId);
        });
        this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);
        this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
        return;
      }

      Promise.all(
        [...n.args.cards].map((card, i) => {
          return this.wait(200 * i).then(() => {
            if (card.location == 'hand') return;
            if (n.args.hand === true) this._playerCounters[pId]['handCount'].incValue(-1);

            let id = `card-${card.id}`;
            if (card.location == 'destroy') {
              this.fadeOutAndDestroy(id, 1000);
              return this.wait(1000);
            }

            let slideIt = () => {
              if (n.args.hand === true || card.location == 'reserve') {
                $(id).classList.add('mini-card');
                this.changeParent(id, `board-${card.location}-${card.pId}`);
              }
              if (card.location == 'discard') $(id).classList.remove('mini-card');

              this.updateStatusIfCard($(id));

              if (card.properties.hasOwnProperty('tapped') && card.properties.tapped == false) {
                $(id).classList.remove('tapped');
              }

              if (card.location == 'mana') {
                let container = this.getCardContainer(card);
                if (container) {
                  return this.slide(id, `counter-board-${card.pId}-mana`).then(() => {
                    $(container).insertAdjacentElement('beforeend', $(id));
                    $(id).classList.remove('mini-card');
                  });
                } else {
                  return this.slide(id, `counter-board-${card.pId}-mana`, { destroy: true });
                }
              } else {
                return this.slide(id, `board-${card.location}-${card.pId}`, {
                  clearTransform: true,
                }).then(() => {
                  if (card.location == 'discard') this.bringDiscardCounterToFront(card.pId);
                });
              }
            };

            if (!$(id)) {
              if (pId != this.player_id) {
                this.addCard(card, this.getVisibleTitleContainer());
                $(id).classList.remove('mini-card');
                if (n.args.hasOwnProperty('originalLocation') && n.args.originalLocation == 'mana') {
                  const last = $('mana-gauge-' + pId).lastChild;
                  return this.flipAndReplace(last, id).then(() => slideIt());
                } else {
                  return this.flipAndReplace(oCards[indexCardReplacement++], id).then(() => slideIt());
                }
              } else {
                console.error('Card that I own do not exists ! :', card);
              }
            } else {
              return slideIt();
            }
          });
        })
      ).then(() => {
        this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);
        this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_gainCounter(n) {
      debug('Notification: gain counter', n);

      let oCard = $(`card-${n.args.card.id}`);
      if (this.isFastMode()) {
        oCard.dataset.counter = n.args.value;
        return;
      }

      let tmpElt = `<div style='position:absolute' id='animation-counter'>${n.args.increase}</div>`;
      this.getVisibleTitleContainer().insertAdjacentHTML('beforebegin', tmpElt);
      this.slide('animation-counter', oCard, {
        from: this.getVisibleTitleContainer(),
        destroy: true,
        phantom: false,
        duration: 1200,
      }).then(() => {
        oCard.dataset.counter = n.args.value;
      });
    },

    notif_useCounter(n) {
      debug('Notification: use counter', n);
      // pay mana if necessary
      this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
      this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);

      let oCard = $(`card-${n.args.card.id}`);
      if (this.isFastMode()) {
        oCard.dataset.counter = n.args.value;
        return;
      }

      let tmpElt = `<div style='position:absolute' id='animation-counter'>${n.args.decrease}</div>`;
      this.getVisibleTitleContainer().insertAdjacentHTML('beforebegin', tmpElt);
      oCard.dataset.counter = n.args.value;
      this.slide('animation-counter', this.getVisibleTitleContainer(), {
        from: oCard,
        destroy: true,
        phantom: false,
        duration: 1200,
      });
    },

    notif_deleteCounter(n) {
      debug('Notification: delete counter');
      // TODO
    },

    notif_targetCards(n) {
      debug('Notification: target card', n);
      this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
    },

    notif_playCard(n) {
      debug('Notif: playing a card', n);
      // Update counters
      if (n.args.fromLocation == 'hand') {
        this._playerCounters[n.args.player_id]['handCount'].incValue(-1);
      }
      this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
      this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);

      // Slide the card
      let card = n.args.card;
      let id = `card-${card.id}`;

      if (this.isFastMode()) {
        if (!$(id)) {
          let fakeCard = $(`hand-${n.args.player_id}`).querySelector('.card-back:last-child');
          fakeCard.remove();
          this.addCard(card);
        } else {
          // if the card is tapped we remove it
          $(id).classList.remove('tapped');
          let container = this.getCardContainer(card);
          $(container).insertAdjacentElement('beforeend', $(id));
          $(id).style.left = '0px';
          $(id).style.top = '0px';
          $(id).style.transform = '';
        }
        if (card.location != 'limbo') $(id).classList.add('mini-card');
        return;
      }

      let slideIt = () => {
        let container = this.getCardContainer(card);

        if (card.location != 'limbo') $(id).classList.add('mini-card');
        else if (n.args.player_id == this.player_id) {
          this.changeParent(id, container);
          $(id).style.left = '0px';
          $(id).style.top = '0px';
          $(id).style.transform = '';
          if ($('btnLaunchSpell')) $('btnLaunchSpell').remove();

          this.wait(800).then(() => {
            this.notifqueue.setSynchronousDuration(100);
          });
          return;
        }

        let highlight = n.args.player_id == this.bottomPId ? 'highlighted-me' : 'highlighted-opponent';
        $(id).classList.add(highlight);
        this.slide(id, container, { clearTransform: true }).then(() => {
          $(id).classList.remove(highlight);
          this.notifqueue.setSynchronousDuration(100);
        });
      };

      if (!$(id)) {
        let fakeCard = $(`hand-${n.args.player_id}`).querySelector('.card-back:last-child');
        this.addCard(card, `hand-${n.args.player_id}`);
        this.flipAndReplace(fakeCard, id).then(slideIt);
      } else {
        // if the card is tapped we remove it
        $(id).classList.remove('tapped');
        if ($(id).querySelector('.card-support-icon')) {
          $(id).querySelector('.card-support-icon').classList.remove('selectable');
        }
        slideIt();
      }
    },

    notif_supportEffect(n) {
      debug('Notif : playing from support', n);
      let card = n.args.card;
      let id = `card-${card.id}`;
      if (!$(id)) {
        this.addCard(card, 'page-title');
      }
      let container = this.getCardContainer(card);
      $(id).classList.remove('mini-card');
      $(id).style.transform = '';
      $(id).style.transformOrigin = 'initial';

      this.incDiscardCount(card.pId, 1);
      this.slide(id, container).then(() => {
        this.bringDiscardCounterToFront(card.pId);
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_tap(n) {
      debug('Notif: tapping card', n);
      $(`card-${n.args.card.id}`).classList.remove('selectable');
      $(`card-${n.args.card.id}`).classList.add('tapped');
      if (n.args.cost > 0) {
        this._playerCounters[n.args.player_id]['mana'].toValue(n.args.mana);
        this._playerCounters[n.args.player_id]['totalMana'].toValue(n.args.totalMana);
      }
    },

    notif_ready(n) {
      debug('Notif: readying card', n);
      $(`card-${n.args.card.id}`).classList.remove('selectable');
      $(`card-${n.args.card.id}`).classList.remove('tapped');
    },

    notif_publicReadyMana(n) {
      debug('Notif: readying card mana', n);
      this._playerCounters[n.args.player_id]['mana'].setValue(n.args.mana);
    },

    notif_privateReadyMana(n) {
      this.notif_ready(n);
      this._playerCounters[n.args.player_id]['mana'].setValue(n.args.mana);
      $(`card-${n.args.card.id}`).classList.remove('tapped');
    },

    notif_untap(n) {
      debug('Notif: untapping card(s)', n);
      n.args.cardIds.forEach((cardId) => {
        if ($(`card-${cardId}`)) $(`card-${cardId}`).classList.remove('tapped');
      });
    },

    notif_shuffleDeck(n) {
      debug('Notif: shuffling deck', n);
      this._playerCounters[n.args.player_id]['deckCount'].incValue(n.args.n);
    },

    notif_spellCleanup(n) {
      debug('Notif: spell cleanup', n);
      n.args.deleted.forEach((meepleId) => {
        $(`meeple-${meepleId}`).remove();
      });

      // Slide the card
      let card = n.args.card;
      this.updateCardStatuses(card.id);
      let id = `card-${card.id}`;
      if (!$(id)) {
        this.addCard(card, 'page-title');
      }
      if (card.location == 'discard') {
        $(id).classList.remove('mini-card');
        $(id).style.transform = '';
        $(id).style.transformOrigin = 'initial';
      } else {
        $(id).classList.add('mini-card');
        $(id).style.transform = '';
        $(id).style.transformOrigin = 'initial';
        if (card.properties.hasOwnProperty('tapped') && card.properties.tapped == true) {
          $(id).classList.add('tapped');
        }
      }
      let container = this.getCardContainer(card);
      if (card.location == 'discard') {
        this.incDiscardCount(card.pId, 1);
      }
      this.slide(id, container).then(() => {
        if (card.location == 'discard') {
          this.bringDiscardCounterToFront(card.pId);
        }
        if (!this.isFastMode()) {
          this.notifqueue.setSynchronousDuration(100);
        }
      });
    },

    notif_invokeToken(n) {
      debug('Notif: invoke token', n);
      // Slide the card
      let card = n.args.card;
      let id = `card-${card.id}`;

      if (this.isFastMode()) {
        if (!$(id)) {
          this.addCard(card);
        } else {
          let container = this.getCardContainer(card);
          $(container).insertAdjacentElement('beforeend', $(id));
        }
        return;
      }

      // we slide it from the card triggering the effect
      if (!$(id)) {
        this.addCard(
          card,
          n.args.card2.location == 'hand' || n.args.card2.location.indexOf('deck') > -1 || n.args.card2.location == 'mana'
            ? 'page-title'
            : `card-${n.args.card2.id}`
        );
        $(id).classList.add('mini-card');
      }
      let container = this.getCardContainer(card);
      this.slide(id, container).then(() => {
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_moveCard(n) {
      debug('Notif: moving card');
      // Slide the card
      let card = n.args.card;
      let id = `card-${card.id}`;
      // we slide it from the card triggering the effect
      let container = this.getCardContainer(card);
      this.slide(id, container).then(() => {
        if (!this.isFastMode()) {
          this.notifqueue.setSynchronousDuration(100);
        }
      });
    },

    notif_nightCleanup(n) {
      debug('Notif: cleaning up played cards', n);
      let pId = n.args.player_id;
      n.args.cards.forEach((card) => (card.discard = true));
      this.incDiscardCount(pId, n.args.cards.length);

      n.args.meeples.forEach((meepleId) => {
        $(`meeple-${meepleId}`).remove();
      });

      if (this.isFastMode()) {
        [...n.args.cards, ...n.args.cards2].map((card, i) => {
          this.updateCardStatuses(card.id);
          let container = $(card.discard ? `board-discard-${card.pId}` : `board-reserve-${card.pId}`);
          container.insertAdjacentElement('beforeend', $(`card-${card.id}`));
          if (card.discard) $(`card-${card.id}`).classList.remove('mini-card');
        });
        this.bringDiscardCounterToFront(pId);

        [...n.args.cards3].map((card, i) => {
          return $(`card-${card.id}`).remove();
        });
        return;
      }

      Promise.all(
        [...n.args.cards, ...n.args.cards2].map((card, i) => {
          return this.wait(200 * i).then(() => {
            this.updateCardStatuses(card.id);
            return this.slide(`card-${card.id}`, card.discard ? `board-discard-${card.pId}` : `board-reserve-${card.pId}`).then(
              () => {
                if (card.discard) {
                  $(`card-${card.id}`).classList.remove('mini-card');
                  this.bringDiscardCounterToFront(card.pId);
                }
                debug(card);
                if (card.properties.hasOwnProperty('tapped') && card.properties.tapped == false)
                  $(`card-${card.id}`).classList.remove('tapped');
              }
            );
          });
        })
      )
        .then(
          Promise.all(
            [...n.args.cards3].map((card, i) => {
              return this.wait(200 * i).then(() => {
                return $(`card-${card.id}`).remove();
              });
            })
          )
        )
        .then(() => {
          this.notifqueue.setSynchronousDuration(100);
        });
    },

    notif_cleanupCards(n) {
      debug('Notif: updating status of all cleaned up cards', n);
      this.gamedatas.passedPlayers = [];

      if (this.isFastMode()) {
        [...n.args.cardIds].map((cardId, i) => {
          this.updateCardStatuses(cardId);
        });
        return;
      }

      Promise.all(
        [...n.args.cardIds].map((cardId, i) => {
          return this.wait(200 * i).then(() => {
            this.updateCardStatuses(cardId);
          });
        })
      ).then(() => {
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    /**
     * Public notification when someone take back a card in hand
     *  slide it to hand if current player
     *  slide it to player panel otherwise
     *  inc hand count
     *  multiple cards of multiple players can be moved like that
     */
    notif_moveToHand(n) {
      debug('Moving cards to hand', n);
      let playerInc = {};
      let discardDec = {};
      n.args.cards.forEach((card) => {
        if (card.location != 'destroy' && this.isCardInDiscardPile(card.pId, card.id)) {
          discardDec[card.pId] = (discardDec[card.pId] || 0) + 1;
        }
      });
      Object.keys(discardDec).forEach((player) => this.incDiscardCount(player, -discardDec[player]));

      Promise.all(
        [...n.args.cards].map((card) => {
          this.updateCardStatuses(card.id);
          let oCard = $(`card-${card.id}`);
          oCard.classList.remove('mini-card');
          playerInc[card.pId] = playerInc[card.pId] ?? 0 + 1;

          if (card.location == 'destroy') {
            this.fadeOutAndDestroy(oCard, 1000);
            return this.wait(1000);
          }

          if (this.player_id == card.pId) {
            oCard.dataset.animationSpeed = 'medium';
            this.changeParent(oCard, `hand-${card.pId}`);
            return this.wait(500);
          } else {
            let fakeCardId = this._fakeIndex--;
            let fakeCard = this.tplFakeCard({ id: fakeCardId });
            return this.flipAndReplace(oCard, fakeCard).then(() => {
              $(`card-${fakeCardId}`).dataset.animationSpeed = 'medium';
              this.changeParent(`card-${fakeCardId}`, `hand-${card.pId}`);
              return this.wait(500);
            });
          }
        })
      ).then(() => {
        Object.keys(playerInc).forEach((player) => {
          this._playerCounters[player]['handCount'].incValue(playerInc[player]);
        });
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_putOnDeck(n) {
      debug('Notif: put back on deck', n);

      let playerInc = {};
      let discardDec = {};
      n.args.cards.forEach((card) => {
        if (card.location != 'destroy' && this.isCardInDiscardPile(card.pId, card.id)) {
          discardDec[card.pId] = (discardDec[card.pId] || 0) + 1;
        }
      });
      Object.keys(discardDec).forEach((player) => this.incDiscardCount(player, -discardDec[player]));

      Promise.all(
        [...n.args.cards].map((card) => {
          let oCard = $(`card-${card.id}`);
          if (card.location == 'destroy') {
            this.fadeOutAndDestroy(oCard, 1000);
            return this.wait(1000);
          }

          playerInc[card.pId] = playerInc[card.pId] ?? 0 + 1;

          oCard.classList.remove('mini-card');
          let fakeCardId = this._fakeIndex--;
          let fakeCard = this.tplFakeCard({ id: fakeCardId });
          return this.flipAndReplace(oCard, fakeCard).then(() => {
            return this.slide(`card-${fakeCardId}`, `board-deck-${card.pId}`, { destroy: true });
          });
        })
      ).then(() => {
        Object.keys(playerInc).forEach((player) => {
          this._playerCounters[player]['deckCount'].incValue(playerInc[player]);
        });
        this.notifqueue.setSynchronousDuration(100);
      });
    },

    notif_revealHand(n) {
      debug('Notif: reveal hand', n);
      // Slide the card
      let card = n.args.card;
      let id = `card-${card.id}`;

      if (this.isFastMode()) {
        if (!$(id)) {
          let fakeCard = $(`hand-${n.args.player_id}`).querySelector('.card-back:last-child');
          fakeCard.remove();
          this.addCard(card);
        } else {
          let container = this.getCardContainer(card);
          this.slide(id, container).then(() => {
            if (!this.isFastMode()) {
              this.notifqueue.setSynchronousDuration(100);
            }
          });
        }
        this._playerCounters[n.args.player_id]['handCount'].incValue(-1);

        return;
      }

      let fakeCard = $(`hand-${n.args.player_id}`).querySelector('.card-back:last-child');

      if (!$(id)) {
        this.addCard(card, `hand-${n.args.player_id}`);
        this.flipAndReplace(fakeCard, id);
      } else {
        let container = this.getCardContainer(card);
        this.slide(id, container).then(() => {
          if (!this.isFastMode()) {
            this.notifqueue.setSynchronousDuration(100);
          }
        });
      }
      this._playerCounters[n.args.player_id]['handCount'].incValue(-1);
    },

    notif_refreshCard(n) {
      debug('refreshing one card', n);
      let card = n.args.card;
      let id = `card-${card.id}`;
      CARDS_DATA[card.id] = card;
    },

    notif_endReveal(n) {
      debug('Notif: end reveal', n);
      if (n.args.player_id != this.player_id) {
        n.args.cardsRevealed.forEach((cardId) => {
          this.fadeOutAndDestroy($(`card-${cardId}`));
        });
      } else {
        n.args.cardsRevealed.forEach((cardId) => {
          id = `card-${cardId}`;
          this.changeParent($(id), $(`hand-${n.args.player_id}`));
        });
      }
    },

    //////////////////////////////////////////////
    //  _____ ____  _
    // |_   _|  _ \| |
    //   | | | |_) | |
    //   | | |  __/| |___
    //   |_| |_|   |_____|
    //////////////////////////////////////////////

    tplFakeCard(card) {
      let uid = 'card-' + card.id;
      return `<div id="${uid}" class='altered-card fake-card card-back'>
        <div class='altered-card-wrapper' data-asset='back'>
        </div>
      </div>`;
    },

    tplCard(card) {
      let type = card.properties.type;
      let miniZones = ['reserve', 'stormLeft', 'stormRight', 'permanent', 'landmark'];
      let mini = miniZones.includes(card.location);
      if (type == HERO) {
        return this.tplHeroCard(card);
      } else if (type == CHARACTER) {
        return this.tplCharacterCard(card, false, mini);
      } else if (type == SPELL) {
        return this.tplSpellCard(card, false, mini);
      } else if (type == PERMANENT) {
        return this.tplPermanentCard(card, false, mini);
      } else if (type == TOKEN) {
        return this.tplTokenCard(card, false, mini);
      }

      console.error('No tpl yet', card);
      return '';
    },

    tplCardTooltip(card) {
      let type = card.properties.type;
      if (type == HERO) {
        return this.tplHeroCardTooltip(card);
      } else if (type == CHARACTER) {
        if (card.properties.token) return this.tplTokenCardTooltip(card);
        else return this.tplCharacterCardTooltip(card);
      } else if (type == SPELL) {
        return this.tplSpellCardTooltip(card);
      } else if (type == PERMANENT) {
        return this.tplPermanentCardTooltip(card);
      } else if (type == TOKEN) {
        return this.tplTokenCardTooltip(card);
      }

      return '';
    },

    getSupportIcon(properties) {
      if (properties.supportIcon === undefined || properties.supportIcon == '') return '';
      return `<div class='card-support-icon' data-faction='${properties.faction}'>
        ${this.formatSvgIcon(properties.supportIcon)}
      </div>`;
    },

    getFlavorTextIfFitting(effect, p) {
      let flavor = _(p.flavorText || '');
      // let maxSize = p.supportDesc == '' ? 250 : 180;
      let supportLength = p.hasOwnProperty('supportDesc') ? p.supportDesc.length : 0;
      let maxSize = p.supportDesc == '' ? 250 : Math.min(250 - supportLength, 180);

      if (flavor == '' || effect.length + flavor.length >= maxSize) return '';
      if (p.token) return '';
      return (effect == '' ? '' : '<hr/>') + `<span class='flavor-text'>${flavor}</span>`;
    },

    tplHeroCard(card, tooltip = false, mini = false) {
      let p = card.properties;
      let i = this.getCardFrontInfos(card, tooltip);
      let effect = this.replaceKeyWordsAndGetReminders(_(p.effectDesc) || '');
      let fullArt = card.properties.hasOwnProperty('fullArt') ? card.properties.fullArt : false;

      tplData = `<div id="card-${card.id}${tooltip ? 'tooltip' : ''}" data-id="${card.id}" 
          class='altered-card card-hero ${mini ? 'mini-card' : ''} '>
        <div class='altered-card-wrapper' data-asset='${p.asset.replace('_R1', '_R')}'>`;

      if (fullArt == false) {
        tplData += `
          <div class='card-frame' data-faction='${p.faction}' data-type='hero'></div>`;
      }
      if (fullArt == false || (fullArt && !tooltip)) {
        tplData += `
          <div class='card-name' style="font-size:${i.nameFontSize}">${_(p.name)}</div>
          <div class='card-typeline'>${_(p.typeline)}</div>

          <div class='card-text' style="font-size:${i.textFontSize}">
            <div class='card-qrcode-container'>
              <a href="https://altered.re/pages/card?ref=${p.uid}" target="_blank" class='card-qrcode'></a>
            </div>
            <div class='card-effect' style="padding-top:${i.textPaddingTop}">
              ${this.formatString(effect, true)}
            </div>
          </div>
          <div class='card-footer'><div class='setIcon' data-asset='${
            p.hasOwnProperty('setIcon') ? p.setIcon : 'core'
          }'></div>${this.formatSvgIcon('artist')} ${p.artist}</div>`;
      }
      tplData += `</div>
      </div>`;
      return tplData;
    },
    tplHeroCardTooltip(card) {
      return `<div id="card-${card.id}-tooltip" class='altered-card-tooltip'>
        <div class='card-tooltip-frame'>
          ${this.tplHeroCard(card, true, false)}
        </div>
        <div class='tooltip-explanation'>${this.getCardTooltipExplanation(card)}</div>
      </div>`;
    },

    getBiomesUISizes(p) {
      let biomes = [p.forest, p.mountain, p.ocean];
      biomes.sort();
      let mid = biomes[1];
      if (mid == 0) mid = biomes[2];

      let sizes = {};
      ['forest', 'mountain', 'ocean'].forEach((biome) => {
        let v = p[biome];
        let size = null;
        if (v == 0) size = 0;
        else if (v < mid) size = 1;
        else if (v == mid) size = 2;
        else if (v > mid) size = 3;
        sizes[biome] = size;
      });

      return sizes;
    },

    tplCharacterCard(card, tooltip = false, mini = false) {
      let p = card.properties;
      let i = this.getCardFrontInfos(card, tooltip);
      let sizes = this.getBiomesUISizes(p);

      let effect = this.replaceKeyWordsAndGetReminders(p.effectDesc || '');
      let flavor = this.getFlavorTextIfFitting(effect, p);
      //let reminders = effect.reminders.length > 0 ? '(' + effect.reminders.join('<br />') + ')' : '';
      let support = this.replaceKeyWordsAndGetReminders(p.supportDesc || '');
      let supportIcon = this.getSupportIcon(p);
      let fullArt = card.properties.hasOwnProperty('fullArt') ? card.properties.fullArt : false;
      let counter = '';
      if (p.extraDatas && p.extraDatas.counterName) {
        counter = ` data-counter='${p.extraDatas.counter}'`;
      }

      if (!p.hasOwnProperty('mainAsset')) {
        p.mainAsset = p.asset;
      }

      let showFrame = this.settings.displayFullArt == '0' || fullArt == false || mini;
      let showContent = showFrame || (fullArt && !tooltip);
      let changed = (name) => (p.changedStats && p.changedStats.includes(name) ? ' altered' : '');
      tplData = `<div id="card-${card.id}${tooltip ? 'tooltip' : ''}" data-id="${card.id}" 
        class='altered-card card-character ${p.hasOwnProperty('token') ? 'card-token' : ''} ${
          mini ? 'mini-card' : ''
        }' data-boost='${i.boost}' ${counter}>
        <div class='altered-card-wrapper' data-asset='${(mini || (this.settings.displayFullArt == '0' && fullArt)) && p.hasOwnProperty('mainAsset') ? p.mainAsset.replace('_R1', '_R') : p.asset.replace('_R1', '_R')}'>`;

      if (showFrame) {
        tplData += `<div class='card-frame' data-size='${i.frameSize}' data-faction='${p.faction}' 
              data-rarity='${p.rarity}' data-support='${p.supportDesc ? 1 : 0}' data-type='${
                p.hasOwnProperty('token') ? 'token' : 'character'
              }'></div>
          `;

        if (!p.hasOwnProperty('token')) {
          tplData += ` <div class='rarity-gem' data-rarity='${p.rarity}'></div>`;
        }
      }
      if (showContent && !p.hasOwnProperty('token')) {
        tplData += `
          <div class='card-costs-bg' data-faction='${p.faction}'></div>
          <div class='card-hand-cost ${changed('costHand')}'>${p.costHand}</div>
          <div class='card-reserve-cost ${changed('costReserve')}'>${p.costReserve}</div>`;
      }

      if (showContent) {
        tplData += `

          <div class='card-name' style="font-size:${i.nameFontSize}">${_(p.name)}</div>
          <div class='card-typeline'>${_(p.typeline)}</div>`;

        tplData += `
          <div class='card-forest ${changed('forest')}' data-size='${sizes.forest}' 
            data-initial='${p.forest}' data-boost='${i.boost}'>
            ${p.forest}
          </div>
          <div class='card-mountain ${changed('mountain')}' data-size='${sizes.mountain}' 
            data-initial='${p.mountain}' data-boost='${i.boost}'>
            ${p.mountain}
          </div>
          <div class='card-ocean ${changed('ocean')}' data-size='${sizes.ocean}' 
            data-initial='${p.ocean}' data-boost='${i.boost}'>
            ${p.ocean}
          </div>`;
      } else if (tooltip) {
        tplData += `
          <div class='card-forest card-fullArt' data-size='' 
            data-initial='' data-boost='${i.boost}'>
          </div>
          <div class='card-mountain card-fullArt' data-size='' 
            data-initial='' data-boost='${i.boost}'> 
          </div>
          <div class='card-ocean card-fullArt' data-size='' 
            data-initial='' data-boost='${i.boost}'> 
          </div>`;
      }
      if (showContent) {
        tplData += `
          <div class='card-text' style="font-size:${i.textFontSize}">
            <div class='card-qrcode-container'>
              <a href="https://altered.re/pages/card?ref=${p.uid}" target="_blank" class='card-qrcode'></a>
            </div>
            <div class='card-effect' style="padding-top:${i.textPaddingTop}">
              ${this.formatString(effect, true)}
              ${flavor}
            </div>
          </div>
          <div class='card-support'${i.supportFontSize ? ` style="font-size:${i.supportFontSize}"` : ''}>
            ${this.formatString(support, true)}
          </div>

          ${supportIcon}
          <div class='card-footer'><div class='setIcon' data-asset='${
            p.hasOwnProperty('setIcon') ? p.setIcon : 'core'
          }'></div>${this.formatSvgIcon('artist')} ${p.artist}</div>`;
      }
      tplData += `
        </div>

        <div class='altered-card-statuses'></div>
      </div>`;
      return tplData;
    },
    tplCharacterCardTooltip(card) {
      let rareExtraDetails = '';
      let p = card.properties;
      if (p.rarity == 2) {
        rareExtraDetails += 'Reference : ' + p.uid;
        if (isDebug && p.uEffects?.length) {
          rareExtraDetails +=
            '<br /><br />' +
            p.uEffects
              .map(
                (t, i) =>
                  `Effect ${i}: trigger ${t[0]} / condition ${t[1]} / output ${t[2]}<br />`
              )
              .join('<br /><br />');
        }
      }

      return `<div id="card-${card.id}-tooltip" class='altered-card-tooltip'>
        <div class='card-tooltip-frame'>
          ${this.tplCharacterCard(card, true, false)}
          ${rareExtraDetails}
        </div>
        <div class='tooltip-explanation'>${this.getCardTooltipExplanation(card)}</div>
      </div>`;
    },

    tplTokenCard(card, tooltip = false, mini = false) {
      let p = card.properties;
      let i = this.getCardFrontInfos(card, tooltip);
      let sizes = this.getBiomesUISizes(p);
      let effect = this.replaceKeyWordsAndGetReminders(_(p.effectDesc) || '');
      let flavor = this.getFlavorTextIfFitting(effect, p);

      return `<div id="card-${card.id}${tooltip ? 'tooltip' : ''}" data-id="${card.id}" 
        class='altered-card card-token ${mini ? 'mini-card' : ''}' data-boost='${i.boost}'>
        <div class='altered-card-wrapper' data-asset='${p.asset.replace('_R1', '_R')}'>
          <div class='card-frame' data-faction='${p.faction}' data-type='token'></div>
          <div class='card-name' style="font-size:${i.nameFontSize}">${_(p.name)}</div>
          <div class='card-typeline'>${_(p.typeline)}</div>

          <div class='card-forest' data-size='${sizes.forest}' data-initial='${p.forest}' data-boost='${i.boost}'>${
            p.forest
          }</div>
          <div class='card-mountain' data-size='${sizes.mountain}' data-initial='${p.mountain}' data-boost='${i.boost}'>${
            p.mountain
          }</div>
          <div class='card-ocean' data-size='${sizes.ocean}' data-initial='${p.ocean}' data-boost='${i.boost}'>${p.ocean}</div>

          <div class='card-text' style="font-size:${i.textFontSize}">
            <div class='card-qrcode-container'>
              <a href="https://altered.re/pages/card?ref=${p.uid}" target="_blank" class='card-qrcode'></a>
            </div>
            <div class='card-effect' style="padding-top:${i.textPaddingTop}">
              ${this.formatString(effect, true)}
              ${flavor}
            </div>
          </div>

          <div class='card-footer'><div class='setIcon' data-asset='${
            p.hasOwnProperty('setIcon') ? p.setIcon : 'core'
          }'></div>${this.formatSvgIcon('artist')} ${p.artist}</div>
        </div>

        <div class='altered-card-statuses'></div>
      </div>`;
    },
    tplTokenCardTooltip(card) {
      return `<div id="card-${card.id}-tooltip" class='altered-card-tooltip'>
        <div class='card-tooltip-frame'>
          ${this.tplTokenCard(card, true, false)}
        </div>
        <div class='tooltip-explanation'>${this.getCardTooltipExplanation(card)}</div>
      </div>`;
    },

    tplSpellCard(card, tooltip = false, mini = false) {
      let p = card.properties;
      let i = this.getCardFrontInfos(card, tooltip);
      let effect = this.replaceKeyWordsAndGetReminders(_(p.effectDesc) || '');
      // let flavor = this.getFlavorTextIfFitting(effect, p);
      let support = this.replaceKeyWordsAndGetReminders(_(p.supportDesc) || '');
      let flavor = this.getFlavorTextIfFitting(effect, p);
      let supportIcon = this.getSupportIcon(p);
      let fullArt = card.properties.hasOwnProperty('fullArt') ? card.properties.fullArt : false;

      let counter = '';
      if (p.extraDatas && p.extraDatas.counterName) {
        counter = ` data-counter='${p.extraDatas.counter}'`;
      }

      let changed = (name) => (p.changedStats && p.changedStats.includes(name) ? ' altered' : '');
      let showFrame = this.settings.displayFullArt == '0' || fullArt == false || mini;
      let showContent = showFrame || (fullArt && !tooltip);

      tplData = `<div id="card-${card.id}${tooltip ? 'tooltip' : ''}" data-id="${card.id}" 
        class='altered-card card-spell ${mini ? 'mini-card' : ''}' ${counter}>
        <div class='altered-card-wrapper' data-asset='${(mini || (this.settings.displayFullArt == '0' && fullArt)) && p.hasOwnProperty('mainAsset') ? p.mainAsset.replace('_R1', '_R') : p.asset.replace('_R1', '_R')}'>`;

      if (showFrame) {
        tplData += `
          <div class='card-frame' data-size='${i.frameSize}' data-faction='${p.faction}' 
              data-rarity='${p.rarity}' data-support='${p.supportDesc ? 1 : 0}' data-type='spell'></div>
          <div class='rarity-gem' data-rarity='${p.rarity}'></div>`;
      }
      if (showContent) {
        tplData += `
          <div class='card-costs-bg' data-faction='${p.faction}'></div>
          <div class='card-hand-cost ${changed('costHand')}'>${p.costHand}</div>
          <div class='card-reserve-cost ${changed('costReserve')}'>${p.costReserve}</div>
          <div class='card-name'style="font-size:${i.nameFontSize}">${_(p.name)}</div>
          <div class='card-typeline'>${_(p.typeline)}</div>

          <div class='card-text' style="font-size:${i.textFontSize}">
            <div class='card-qrcode-container'>
              <a href="https://altered.re/pages/card?ref=${p.uid}" target="_blank" class='card-qrcode'></a>
            </div>
            <div class='card-effect' style="padding-top:${i.textPaddingTop}">
              ${this.formatString(effect, true)}
              ${flavor}
            </div>
          </div>
          <div class='card-support'${i.supportFontSize ? ` style="font-size:${i.supportFontSize}"` : ''}>
            ${this.formatString(support, true)}
          </div>

          ${supportIcon}
          <div class='card-footer'><div class='setIcon' data-asset='${
            p.hasOwnProperty('setIcon') ? p.setIcon : 'core'
          }'></div>${this.formatSvgIcon('artist')} ${p.artist}</div>
        </div>`;
      }
      tplData += `
        <div class='altered-card-statuses'></div>
      </div>`;
      return tplData;
    },

    tplSpellCardTooltip(card) {
      let p = card.properties;
      return `<div id="card-${card.id}-tooltip" class='altered-card-tooltip'>
        <div class='card-tooltip-frame'>
          ${this.tplSpellCard(card, true, false)}
        </div>
        <div class='tooltip-explanation'>${this.getCardTooltipExplanation(card)}</div>
      </div>`;
    },

    tplPermanentCard(card, tooltip = false, mini = false) {
      let p = card.properties;
      let i = this.getCardFrontInfos(card, tooltip);
      let effect = this.replaceKeyWordsAndGetReminders(_(p.effectDesc) || '');
      let flavor = this.getFlavorTextIfFitting(effect, p);
      let changed = (name) => (p.changedStats && p.changedStats.includes(name) ? ' altered' : '');
      let supportIcon = this.getSupportIcon(p);
      let support = this.replaceKeyWordsAndGetReminders(_(p.supportDesc) || '');
      let isLandmark = card.properties.subtypes.includes('landmark');
      let isFeat = card.properties.subtypes.includes('feat');
      let fullArt = card.properties.hasOwnProperty('fullArt') ? card.properties.fullArt : false;
      let permDescription = isLandmark
        ? _('(Play me in your Landmark zone. I don’t gain Fleeting.)')
        : _('(Play me in an Expedition. If it moves forward, I go to Reserve during Rest.)');

      if (!p.hasOwnProperty('mainAsset')) {
        p.mainAsset = p.asset;
      }

      let counter = '';
      if (p.extraDatas && p.extraDatas.counterName) {
        counter = ` data-counter='${p.extraDatas.counter}'`;
      }

      let showFrame = this.settings.displayFullArt == '0' || fullArt == false || mini;
      let showContent = showFrame || (fullArt && !tooltip);

      tplData = `<div id="card-${card.id}${tooltip ? 'tooltip' : ''}" data-id="${card.id}" 
        class='altered-card card-permanent ${p.hasOwnProperty('token') ? 'card-token' : ''} ${
          mini ? 'mini-card' : ''
        }' ${counter}>
        <div class='altered-card-wrapper' data-asset='${(mini || (this.settings.displayFullArt == '0' && fullArt)) && p.hasOwnProperty('mainAsset') ? p.mainAsset.replace('_R1', '_R') : p.asset.replace('_R1', '_R')}'>`;

      if (showFrame) {
        tplData += `<div class='card-frame' data-size='${i.frameSize}' data-faction='${p.faction}' 
              data-rarity='${p.rarity}' data-support='${p.supportDesc ? 1 : 0}' data-isfeat='${isFeat ? 1 : 0}' data-type='${
                isLandmark ? (p.hasOwnProperty('token') ? 'permanent' : 'permanent') : 'gear'
              }'></div>
          `;
        if (!p.hasOwnProperty('token')) {
          tplData += ` <div class='rarity-gem' data-rarity='${p.rarity}'></div>`;
        }
      }
      if (showContent && !p.hasOwnProperty('token')) {
        tplData += `
          <div class='card-costs-bg' data-faction='${p.faction}'></div>
          <div class='card-hand-cost ${changed('costHand')}'>${p.costHand}</div>
          <div class='card-reserve-cost ${changed('costReserve')}'>${p.costReserve}</div>`;
      }
      if (showContent) {
        tplData += `
          <div class='card-name' style="font-size:${i.nameFontSize}">${_(p.name)}</div>
          <div class='card-typeline'>${_(p.typeline)}</div>

          <div class='card-subpermanent'>${permDescription}</div>
          <div class='card-text' style="font-size:${i.textFontSize}">
            <div class='card-qrcode-container'>
              <a href="https://altered.re/pages/card?ref=${p.uid}" target="_blank" class='card-qrcode'></a>
            </div>
            <div class='card-effect' style="padding-top:${i.textPaddingTop}">
              ${this.formatString(effect, true)}
              ${flavor}
            </div>
          </div>
          <div class='card-support'${i.supportFontSize ? ` style="font-size:${i.supportFontSize}"` : ''}>
            ${this.formatString(support, true)}
          </div>
          ${supportIcon}
          <div class='card-footer'><div class='setIcon' data-asset='${
            p.hasOwnProperty('setIcon') ? p.setIcon : 'core'
          }'></div>${this.formatSvgIcon('artist')} ${p.artist}</div>
        </div>
          `;
      }
      tplData += `
        <div class='altered-card-statuses'></div>
      </div>`;
      return tplData;
    },

    tplPermanentCardTooltip(card) {
      let p = card.properties;
      return `<div id="card-${card.id}-tooltip" class='altered-card-tooltip'>
        <div class='card-tooltip-frame'>
          ${this.tplPermanentCard(card, true, false)}
        </div>
        <div class='tooltip-explanation'>${this.getCardTooltipExplanation(card)}</div>
      </div>`;
    },

    getCardFrontInfos(card, tooltip) {
      if (
        tooltip &&
        (!card.properties.hasOwnProperty('fullArt') || (this.settings.displayFullArt == '0' && card.properties.type != 'hero'))
      ) {
        let oCard = $(`card-${card.id}`);
        let oSupport = oCard.querySelector('.card-support');
        return {
          frameSize: oCard.querySelector('.card-frame').dataset.size,
          textFontSize: oCard.querySelector('.card-text').style.fontSize,
          nameFontSize: oCard.querySelector('.card-name').style.fontSize,
          supportFontSize: oSupport ? oSupport.style.fontSize : '',
          boost: oCard.dataset.boost,
          textPaddingTop: oCard.querySelector('.card-effect').style.paddingTop,
        };
      } else if (tooltip) {
        let oCard = $(`card-${card.id}`);
        return {
          frameSize: 0,
          textFontSize: 0,
          nameFontSize: 0,
          boost: oCard.dataset.boost,
          textPaddingTop: 0,
        };
      }

      let infos = {
        frameSize: 1,
        textFontSize: '14px',
        nameFontSize: '16px',
        boost: 0,
        textPaddingTop: '0px',
      };
      if (card.type == 'hero') infos.nameFontSize = '22px';

      return infos;
    },

    autofitCardFrame(oCard, eraseExisting = false) {
      if (!oCard.querySelector('.card-effect')) return;

      let isMini = oCard.classList.contains('mini-card');
      if (isMini) oCard.classList.remove('mini-card');
      oCard.style.setProperty('--cardScale', 1);
      oCard.classList.add('force-frame');

      let card = this.getCardInfos(oCard.dataset.id);
      let i = this.getCardFrontInfos(card);

      let oSupport = oCard.querySelector('.card-support');
      if (eraseExisting) {
        oCard.querySelector('.card-name').style.fontSize = i.nameFontSize;
        oCard.querySelector('.card-text').style.fontSize = i.textFontSize;
        oCard.querySelector('.card-effect').style.paddingTop = i.textPaddingTop;
        if (oSupport) {
          oSupport.style.fontSize = '';
          oSupport.style.lineHeight = '';
        }
      }
      oCard.offsetHeight;

      // Fit support (completed / rare ability box)
      if (oSupport && oSupport.textContent.trim() && oSupport.clientHeight > 0) {
        const minSupportSize = 9;
        const isSupportSizeOk = () => oSupport.scrollHeight <= oSupport.clientHeight;
        for (let size = 13; size >= minSupportSize && !isSupportSizeOk(); size--) {
          oSupport.style.fontSize = `${size}px`;
        }
        if (!isSupportSizeOk()) {
          oSupport.style.lineHeight = '1.0';
        }
        oCard.offsetHeight;
      }

      // Fit effect
      let isEffectSizeOk = () => {
        let frameSize =
          oCard.querySelector('.card-text').offsetHeight -
          (oCard.querySelector('.card-support') ? oCard.querySelector('.card-support').offsetHeight : 0);
        let contentSize = oCard.querySelector('.card-effect').offsetHeight;
        return frameSize >= contentSize;
      };

      if (!isEffectSizeOk()) {
        let oFrame = oCard.querySelector('.card-frame');
        if (oFrame) oFrame.dataset.size = 2;
        oCard.offsetHeight;
        for (let i = 13; i >= 10 && !isEffectSizeOk(); i--) {
          oCard.querySelector('.card-text').style.fontSize = `${i}px`;
        }
      }

      // Fit name
      let oName = oCard.querySelector('.card-name');
      let isNameSizeOk = () => oName.offsetHeight + 3 > oName.scrollHeight;
      let d = parseInt(i.nameFontSize);
      for (let i = d; i >= d - 4 && !isNameSizeOk(); i--) {
        oCard.querySelector('.card-name').style.fontSize = `${i}px`;
      }

      // // Center text
      // const H = oCard.querySelector('.card-text').getBoundingClientRect()['height'];
      // let computePadding = () => (H - oCard.querySelector('.card-effect').getBoundingClientRect()['height']) / 2;

      // let current = 0;
      // let tooLow = 0,
      //   tooHigh = 1000;
      // for (let i = 0; i < 4; i++) {
      //   let padding = computePadding();
      //   if (padding > current && padding > tooLow) tooLow = padding;
      //   if (padding < current && padding < tooHigh) tooHigh = padding;
      //   oCard.querySelector('.card-effect').style.paddingTop = padding + 'px';
      //   current = padding;
      // }
      // let mean = parseInt((tooHigh + tooLow) / 2);
      // if (mean > H) mean = 0;
      // oCard.querySelector('.card-effect').style.paddingTop = mean + 'px';

      oCard.style.setProperty('--cardScale', null);
      oCard.classList.remove('force-frame');

      // Add back mini if needed
      if (isMini) oCard.classList.add('mini-card');

      this.updateCardTooltip(oCard.dataset.id);
    },

    getMeeplesOnCard(cardId) {
      if (!$(`card-${cardId}`)) return [];
      return [...$(`card-${cardId}`).querySelectorAll('.altered-meeple:not(.phantom)')];
    },

    updateStatusIfCard(elt) {
      if ($(elt).classList.contains('altered-card')) this.updateCardStatuses($(elt).dataset.id);
    },

    updateCardStatuses(cardId) {
      let container = $(`card-${cardId}`).querySelector('.altered-card-statuses');
      if (!container) return;
      container.innerHTML = '';

      const ICONS = ['fleeting', 'anchored', 'asleep', 'boost'];
      let boost = 0;
      this.getMeeplesOnCard(cardId).forEach((meeple) => {
        let type = meeple.dataset.type;
        if (!ICONS.includes(type)) return;

        if (type == 'boost') {
          boost++;
          return;
        }

        //        container.insertAdjacentHTML('beforeend', `<div class='card-status'>${this.formatSvgIcon(type)}</div>`);
        container.insertAdjacentHTML('beforeend', this.formatIcon(type));
      });

      if ($(`card-${cardId}`).querySelector('.card-forest') != null) {
        let p = {
          forest: parseInt($(`card-${cardId}`).querySelector('.card-forest').getAttribute('data-initial')) + boost,
          mountain: parseInt($(`card-${cardId}`).querySelector('.card-mountain').getAttribute('data-initial')) + boost,
          ocean: parseInt($(`card-${cardId}`).querySelector('.card-ocean').getAttribute('data-initial')) + boost,
        };
        let sizes = this.getBiomesUISizes(p);
        ['forest', 'mountain', 'ocean'].forEach((biome) => {
          let o = $(`card-${cardId}`).querySelector(`.card-${biome}`);
          o.setAttribute('data-size', sizes[biome]);
          o.innerHTML = p[biome];
        });

        $(`card-${cardId}`).dataset.boost = boost;
      }

      this.updateCardTooltip(cardId);
    },

    updateCardTooltip(cardId) {
      this.tooltips[`card-${cardId}`].setContent(this.tplCardTooltip(this.getCardInfos(cardId)));
    },

    tempGroupBy(meeples) {
      return meeples.reduce(function (rv, x) {
        (rv[x['dataset']['type']] ??= []).push(x);
        return rv;
      }, {});
    },

    getCardTooltipExplanation(card) {
      let explanation = '';
      // debug(this.getMeeplesOnCard(card.id));
      // let meeplesByTypes = this.getMeeplesOnCard(card.id).groupBy((oMeeple) => oMeeple.dataset.type);
      if (
        (this.settings.displayFullArt == 1 && card.properties.hasOwnProperty('fullArt') && card.properties.fullArt == true) ||
        (this.settings.displayFullArt == '0' &&
          card.properties.hasOwnProperty('fullArt') &&
          card.properties.fullArt == true &&
          card.properties.type == 'hero')
      ) {
        if (card.properties.hasOwnProperty('effectDesc')) {
          explanation += `<div class='explanation'>
            <p>
              ${this.formatString(card.properties.effectDesc)}
            </p>
          </div>`;
        }
        if (card.properties.hasOwnProperty('supportDesc')) {
          explanation += `<div class='explanation'>
            <p>
              ${this.formatString(card.properties.supportDesc)}
            </p>
          </div>`;
        }
      }
      let meeplesByTypes = this.tempGroupBy(this.getMeeplesOnCard(card.id));
      Object.keys(meeplesByTypes).forEach((type) => {
        let tooltipDesc = this.getMeepleTooltip({ type });
        if (tooltipDesc != null) {
          let n = meeplesByTypes[type].length;
          explanation += `<div class='explanation'>
            <div class='explanation-icon'>
              ${this.formatIcon(type)}
              ${n > 1 ? `x ${n}` : ''}
            </div>
            <p>
              ${tooltipDesc.map((t) => this.formatString(t)).join('<br/>')}
            </p>
          </div>`;
        }
      });

      return explanation;
    },

    // getAbilityDesc(ability, n, card) {
    //   let descs = {};

    //   let desc = descs[ability];
    //   let result = {
    //     title: desc[0],
    //     desc: n == 1 && desc.length > 2 ? desc[2] : desc[1],
    //   };

    //   if (n !== null) {
    //     result.title = this.fsr(result.title, { i18n: ['n'], n });
    //     result.desc = this.fsr(result.desc, { i18n: ['n'], n, icon: this.formatIcon(`action-card-${n}`) });
    //   } else {
    //     result.desc = this.formatString(result.desc);
    //   }
    //   return result;
    // },

    replaceKeyWordsAndGetReminders(str) {
      if (Array.isArray(str)) {
        return str.map((s) => this.replaceKeyWordsAndGetReminders(_(s))).join(' ');
      } else {
        let t = str.split('  ');
        if (t.length > 1) {
          return t.map((s) => this.replaceKeyWordsAndGetReminders(_(s))).join('<br />');
        }
      }

      // Unique empty condition/trigger
      if (str == '[]]') return '';
      // Separate unique effects
      if (str == '<BR>') return '<br />';

      // Ensure markers are using []
      str = str.replaceAll('<', '[').replaceAll('>', ']').replaceAll('\\', '');

      const KEYWORDS = {
        AFTER_YOU: {
          text: _('After You'),
          reminder: _('End your turn as if you had played a card. You may still play cards later this Day.'),
        },
        ANCHORED: {
          text: _('Anchored'),
          reminder: _("During Rest, I don't go to Reserve and I lose Anchored."),
        },
        ASLEEP: {
          text: _('Asleep'),
          reminder: _("During Dusk, ignore my statistics. During Rest, I don't go to Reserve and I lose Asleep."),
        },
        BB: {
          text: '',
          reminder: _('A boost is a +1/+1/+1 counter. Remove it when it leaves the Expedition zone.'),
        },
        BOODA: {
          text: _('Booda 2/2/2'),
        },
        BOOSTED: {
          text: _('Boosted'),
        },
        BOOSTED_CHA_P: {
          text: _('Boosted'),
        },
        BOOSTED_TKN_P: {
          text: _('Boosted'),
        },
        BRASSBUG: {
          text: _('Brassbug 2/2/2'),
        },
        DEFENDER: {
          text: _('Defender'),
          reminder: _("My Expedition can't advance during Dusk."),
        },
        DEFENDER_FS: {
          text: _('Defender'),
          reminder: _("My Expedition can't advance during Dusk."),
        },
        DEFENDER_CHA_P: {
          text: _('Defender'),
          reminder: _("My Expedition can't advance during Dusk."),
        },
        ETERNAL: {
          text: _('Eternal'),
          reminder: _("During Rest, I don't go to Reserve."),
        },
        ETERNAL_FS: {
          text: _('Eternal'),
          reminder: _("During Rest, I don't go to Reserve."),
        },
        FLEETING: {
          text: _('Fleeting'),
          reminder: _('Send me to Discard instead of Reserve after my effect resolves.'),
        },
        FLEETING_CHAR: {
          text: _('Fleeting'),
          reminder: _('If I would be sent to Reserve, discard me instead.'),
        },
        GIGANTIC: {
          text: _('Gigantic'),
          reminder: _('I am considered present in each of your Expeditions.'),
        },
        MAW: {
          text: _('Maw 0/0/0'),
        },
        ORDIS_RECRUIT: {
          text: _('Ordis Recruit 1/1/1'),
        },
        RESUPPLY: {
          text: _('Resupply'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        EXHAUSTED_RESUPPLY: {
          text: _('Exhausted Resupply'),
          reminder: _('Put the top card of your deck in Reserve, then exhaust it {T}.'),
        },
        EXHAUSTED_RESUPPLIES: {
          text: _('Exhausted Resupplies'),
          reminder: _('Put the top card of your deck in Reserve, then exhaust it {T}.'),
        },
        EXHAUSTED_RESUPPLY_LOW: {
          text: _('Exhausted Resupply'),
          reminder: _('Put the top card of your deck in Reserve, then exhaust it {T}.'),
        },
        EXHAUSTED_RESUPPLY_INF: {
          text: _('Exhausted Resupply'),
          reminder: _('Put the top card of your deck in Reserve, then exhaust it {T}.'),
        },
        RESUPPLY_INF: {
          text: _('Resupply'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        RESUPPLY_LOW: {
          text: _('Resupply'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        RESUPPLY_T: {
          text: _('Resupply'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        RESUPPLIES: {
          text: _('Resupplies'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        SABOTAGE: {
          text: _('Sabotage'),
          reminder: _('Discard up to one target card from a Reserve.'),
        },
        SABOTAGE_LOW: {
          text: _('Sabotage'),
          reminder: _('Discard up to one target card from a Reserve.'),
        },
        SABOTAGE_INF: {
          text: _('Sabotage'),
          reminder: _('Discard up to one target card from a Reserve.'),
        },
        SEASONED: {
          text: _('Seasoned'),
          reminder: _('I keep my boosts when I go to Reserve.'),
        },
        SEASONED_CHA_P: {
          text: _('Seasoned'),
          reminder: _('I keep my boosts when I go to Reserve.'),
        },
        SEASONED_ME_FS: {
          text: _('Seasoned'),
          reminder: _('I keep my boosts when I go to Reserve.'),
        },
        TOUGH_1: {
          text: _('Tough 1'),
          reminder: _("Your opponent's Spells and abilities that target me cost {1} more."),
        },
        TOUGH_2: {
          text: _('Tough 2'),
          reminder: _("Your opponent's Spells and abilities that target me cost {2} more."),
        },
        TOUGH_X: {
          text: _('Tough X'),
          reminder: _("Your opponent's Spells and abilities that target me cost {X} more."),
        },
        TOUGH_FS_X: {
          text: _('Tough X'),
          reminder: _("Your opponent's Spells and abilities that target me cost {X} more."),
        },
        COOLDOWN: {
          text: _('Cooldown'),
          reminder: _(
            "If I go to Reserve after my effect resolves, exhaust me {T}. Exhausted cards can't be played and have no Support abilities."
          ),
        },
        MANA_MOTH: {
          text: _('Mana Moth 2/2/2'),
        },
        DRAGON_SHADE: {
          text: _('Dragon Shade 5/5/5'),
        },
        // Bise
        AUGMENT: {
          text: _('Augment'),
          reminder: _("If it has counters, it gains 1 more. This can't target Hero cards."),
        },
        AUGMENT_IMP: {
          text: _('Augment'),
          reminder: _("If it has counters, it gains 1 more. This can't target Hero cards."),
        },
        SCOUT_1: {
          text: _('Scout'),
          reminder: _('You may play me from hand for 1 with "{h} Send me to Reserve".'),
        },
        SCOUT_2: {
          text: _('Scout'),
          reminder: _('You may play me from hand for 2 with "{h} Send me to Reserve".'),
        },
        SCOUT_3: {
          text: _('Scout'),
          reminder: _('You may play me from hand for 3 with "{h} Send me to Reserve".'),
        },
        SCOUT_4: {
          text: _('Scout'),
          reminder: _('You may play me from hand for 4 with "{h} Send me to Reserve".'),
        },
        // Cyclone
        AEROLITH: {
          text: _('Aerolith'),
        },
        HALUA: {
          text: _('Halua'),
        },
        RUSH: {
          text: _('Rush'),
        },
        TOUGH_CHA_P_5: {
          text: _('Tough 5'),
          reminder: _("Your opponent's Spells and abilities that target me cost {5} more."),
        },
        WOOLLYBACK: {
          text: _('Woollyback 1/1/1'),
        },
        ASCENDS: {
          text: _('Ascends'),
          reminder: _("Until Rest, it can move forward even if matched in its region's terrains by the opponent's Expedition."),
        },
        ASCEND: {
          text: _('Ascend'),
          reminder: _("Until Rest, it can move forward even if matched in its region's terrains by the opponent's Expedition."),
        },
        ASCEND_INF: {
          text: _('Ascend'),
          reminder: _("Until Rest, it can move forward even if matched in its region's terrains by the opponent's Expedition."),
        },
        TOUGH_CHA_P_1: {
          text: _('Tough 1'),
          reminder: _("Your opponent's Spells and abilities that target me cost {1} more."),
        },
        // Duster
        MANASEED: {
          text: _('Manaseed'),
        },
        IN_CONTACT: {
          text: _('In contact'),
          reminder: _("I'm In Contact if another player's Expedition is in my region."),
        },
        GIFT: {
          text: _('Gift'),
        },
        ANCHORED_FS: {
          text: _('Anchored'),
        },
        ANCHORED_CHA_P: {
          text: _('Anchored'),
          reminder: _("During Rest, I don't go to Reserve and I lose Anchored."),
        },
        ASLEEP_CHA_P: {
          text: _('Asleep'),
          reminder: _("During Dusk, ignore my statistics. During Rest, I don't go to Reserve and I lose Asleep."),
        },
        RESUPPLY_THEY_S: {
          text: _('Resupply'),
          reminder: _('Put the top card of your deck in Reserve.'),
        },
        // Eole
        COMPLETED_LOW: {
          text: _('Completed'),
        },
        ASCENDED_S: {
          text: _('Ascended'),
          reminder: _("Until Rest, it can move forward even if matched in its region's terrains by the opponent's Expedition."),
        },
        DUE_TO_ASCENSION: {
          text: _('Due to Ascension'),
          reminder: _('if it moved forward due to at least one matched stat.'),
        }
      };

      const regexParentheses = /\(([^)]+)\)/;
      const regex = new RegExp('\\$\\[([^\\]]+)\\]', 'g');

      if (str.match(regex) !== null) {
        const matches = [...str.matchAll(regex)];
        for (let i = matches.length - 1; i >= 0; i--) {
          const match = matches[i];
          const index = match.index;

          const keyword = match[1];
          if (!KEYWORDS[keyword]) {
            console.error('Cant substitute keyword, should not happen :', keyword);
            continue;
          }

          const replacement = `<span class="keyword ${keyword}">${KEYWORDS[keyword].text}</span>`;
          const reminder = '##REMINDER##' + KEYWORDS[keyword].reminder;

          str = str.slice(0, index) + replacement + str.slice(index + keyword.length + 3);

          const alreadyReminder = str.indexOf('##REMINDER##', index);
          if (alreadyReminder !== -1) {
            str = str.replace('##REMINDER##', reminder + ' ');
          } else {
            // Check if there is a string in parentheses before the end
            const textBeforeEnd = str.slice(index + replacement.length);
            const matchParentheses = textBeforeEnd.match(regexParentheses);

            if (matchParentheses) {
              let index2 = matchParentheses.index;
              // Concatenate reminder at the end of the inside of the parentheses
              str = str.slice(0, index + replacement.length + index2) + '(' + matchParentheses[1] + ' ' + reminder + ')';
            } else {
              // Add reminder at the end
              str = str + ' (' + reminder + ')';
            }
          }
        }
      }

      Object.keys(KEYWORDS).forEach((keyword) => {
        const regex2 = new RegExp('\\[' + keyword + '\\]', 'g');
        str = str.replaceAll(regex2, `<span class="keyword ${keyword}">${KEYWORDS[keyword].text}</span>`);
      });

      str = str.replace('##REMINDER##', '');

      return str;
    },
  });
});
