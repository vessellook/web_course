import {createStore} from "vuex";
import createPersistedState from "vuex-persistedstate";
import {
  ADD_TO_CART,
  HIDE_LOGIN_MODAL,
  INVALIDATE_TOKEN,
  REMOVE_FROM_CART,
  SHOW_LOGIN_MODAL,
  UPDATE_TOKEN
} from './mutations';
import decode from 'jwt-decode';

let store = createStore({
  state() {
    return {
      cart: {},
      token: null,
      role: null,
      isLoginModalShown: false,
    };
  },
  getters: {
    isRegistered(state) {return Boolean(state.token);}
  },
  mutations: {
    [ADD_TO_CART](state, {product, count}) {
      if (state.cart.hasOwnProperty(product.id)) {
        state.cart[product.id].count += count;
      } else {
        state.cart[product.id] = {product, count};
      }
    },
    [REMOVE_FROM_CART](state, product) {
      delete state.cart[product.id];
    },
    [UPDATE_TOKEN](state, token) {
      let decoded = decode(token);
      this.state.role = decoded.role;
      state.token = token;
    },
    [INVALIDATE_TOKEN](state) {
      state.token = null;
    },
    [SHOW_LOGIN_MODAL](state) {
      state.isLoginModalShown = true;
    },
    [HIDE_LOGIN_MODAL](state) {
      state.isLoginModalShown = false;
    },
  },
  plugins: [createPersistedState({paths: ['cart', 'token', 'role']})]
});


export default store;