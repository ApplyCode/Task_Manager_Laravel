/**
 * A simple event bus implementation for Vue.js.
 * @namespace eventBus
 * @property {Object} events - An object containing all the registered events and their callbacks.
 * @function $on - Registers a callback for a given event.
 * @param {string} event - The name of the event to register the callback for.
 * @param {Function} callback - The callback function to be executed when the event is emitted.
 * @function $off - Unregisters a callback for a given event.
 * @param {string} event - The name of the event to unregister the callback for.
 * @param {Function} callback - The callback function to be unregistered.
 * @function $emit - Emits an event and executes all the registered callbacks.
 * @param {string} event - The name of the event to emit.
 * @param {...*} args - Any number of arguments to be passed to the registered callbacks.
 */
import { ref } from 'vue';

const eventBus = {
  events: ref({}),
  $on(event, callback) {
    if (!this.events.value[event]) {
      this.events.value[event] = [];
    }
    this.events.value[event].push(callback);
  },
  $off(event, callback) {
    if (!this.events.value[event]) return;
    const index = this.events.value[event].indexOf(callback);
    if (index > -1) {
      this.events.value[event].splice(index, 1);
    }
  },
  $emit(event, ...args) {
    if (!this.events.value[event]) return;
    this.events.value[event].forEach(callback => {
      callback(...args);
    });
  }
};

export default eventBus;