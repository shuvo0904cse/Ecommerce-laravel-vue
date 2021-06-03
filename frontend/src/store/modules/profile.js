import {PROFILE_REQUEST, PROFILE_ERROR, PROFILE_SUCCESS} from "../actions/profile";
import {apiCall, api_routes} from "@/utils/api";
import {AUTH_LOGOUT} from "../actions/auth";

const state = {
    profile: {},
};

const getters = {
    getProfile: state => state.profile
};

const actions = {
    [PROFILE_REQUEST]: ({commit, dispatch}) => {
        apiCall({url: api_routes.user.me})
            .then(resp => {
                commit(PROFILE_SUCCESS, resp);
            })
            .catch(err => {
                commit(PROFILE_ERROR);
                dispatch(AUTH_LOGOUT);
            });
    }
};

const mutations = {
    [PROFILE_SUCCESS]: (state, resp) => {
        state.status = "success";
        state.profile = resp.results;
    },
    [PROFILE_ERROR]: (state, resp) => {
        state.status = "error";
        state.profile = resp;
    },
    [AUTH_LOGOUT]: state => {
        state.profile = {};
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};