import {AUTH_REQUEST, AUTH_ERROR, AUTH_SUCCESS, AUTH_LOGOUT} from "../actions/auth";
import {apiCall, api_routes} from "../../utils/api";
import router from "./../../router";

const state = {
    token: localStorage.getItem("user-token") || "",
    status: "",
    auth_error: ""
};

const getters = {
    isAuthenticated: state => !!state.token,
    auth_error: state => state.auth_error
};

const actions = {
    [AUTH_REQUEST]: ({ commit, dispatch }, user) => {
        return new Promise((resolve, reject) => {
            commit(AUTH_REQUEST);
            apiCall({ url: api_routes.user.login, data: user, method: "post" })
                .then(resp => {
                    localStorage.setItem("user-token", resp.token);
                    commit(AUTH_SUCCESS, resp);
                    resolve(resp);
                })
                .catch(err => {
                    commit(AUTH_ERROR, err);
                    localStorage.removeItem("user-token");
                    reject(err);
                });
        });
    },
    [AUTH_LOGOUT]: ({ commit }) => {
        return new Promise(resolve => {
            commit(AUTH_LOGOUT);
            localStorage.removeItem("user-token");
            resolve();
        });
    }
};

const mutations = {
    [AUTH_REQUEST]: state => {
        state.status = "loading";
    },
    [AUTH_SUCCESS]: (state, resp) => {
        state.status = "success";
        state.token = resp.token;
    },
    [AUTH_ERROR]: (state, resp) => {
        state.status = "error";
        state.auth_error = resp.message;
    },
    [AUTH_LOGOUT]: state => {
        state.token = "";
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};