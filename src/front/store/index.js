import {createStore} from "vuex";
import createPersistedState from "vuex-persistedstate";
import {
  INVALIDATE_TOKEN,
  UPDATE_TOKEN
} from './mutations';
import {RENEW_TOKEN} from './actions';
import {updateToken} from '@/api';
import decode from 'jwt-decode';

let timer;

let store = createStore({
  state() {
    return {
      token: null,
      role: null,
      expiresAt: null,
      finishedLoading: false
    };
  },
  getters: {
    isRegistered(state) {
      return Boolean(state.token);
    }
  },
  mutations: {
    [UPDATE_TOKEN](state, token) {
      let decoded = decode(token);
      state.role = decoded.role;
      state.token = token;
      state.expiresAt = new Date(decoded.exp * 1000);
      state.finishedLoading = true;
    },
    [INVALIDATE_TOKEN](state) {
      state.token = null;
      state.role = null;
      state.expiresAt = null;
      state.finishedLoading = true;
    },
  },
  actions: {
    [RENEW_TOKEN]: function self({state, commit}) {
      if (state.token == null) {
        return;
      }
      let now = new Date();
      if (state.expiresAt < now) {
        commit(INVALIDATE_TOKEN);
        return;
      }
      updateToken({token: state.token})
        .then(
          token => {
            commit(UPDATE_TOKEN, token);
            // clearTimeout(timer);
            // timer = setTimeout(self, (process.env.INACTIVITY_TIMEOUT + 1) * 1000);
          },
          () => commit(INVALIDATE_TOKEN)
        );
    }
  },
  plugins: [createPersistedState({
    paths: ['token', 'role', 'expiresAt'],
    rehydrated(store) {
      if (store.state.expiresAt) {
        store.state.expiresAt = new Date(store.state.expiresAt);
        store.state.finishedLoading = true;
      }
    }
  })]
});

export default store;