import {
    USER_SEARCH,
    UPDATE_USER_SEARCH,
    USER_CURRENT_PAGE,
    UPDATE_USER_CURRENT_PAGE,
    USER_SELECTED_ROLE,
    UPDATE_USER_SELECTED_ROLE,
    USER_LISTS,
    USER_RESPONSE,
    USER_SUCCESS,
    USER_ERROR,
    USER_STORE,
    USER_EDIT,
    USER_ADD,
    USER_UPDATE,
    USER_DELETE
} from "../actions/user";
import {apiCall, api_routes} from "@/utils/api";
import {AUTH_ERROR, AUTH_REQUEST, AUTH_SUCCESS} from "../actions/auth";

const state = {
    save_update_title: "Add",
    search: "",
    current_page: "",
    selected_role: "",
    pagination: {
        total: 0,
        per_page : 10,
        order_by: 'id',
        order_direction: 'asc'
    },
    action: "SAVE",
    users: [],
    fields: [
        { key: "name" },
        { key: "email" },
        { key: "role" },
        { key: "actions" }
    ],
    roles:[
        'ADMIN',
        'CUSTOMER'
    ],
    success: [],
    error: [],
    errors: []
};

const getters = {
    save_update_title: state => state.save_update_title,
    pagination: state => state.pagination,
    action: state => state.action,
    users: state => state.users,
    fields: state => state.fields,
    roles: state => state.roles,
    success: state => state.success,
    error: state => state.error,
    errors: state => state.errors,
};

const mutations = {
    [UPDATE_USER_SEARCH]: (state, payload) => {
         if (payload.search != state.search) {
            state.search = payload.search;
         }
    },

    [UPDATE_USER_CURRENT_PAGE]: (state, payload) => {
        if (payload.current_page != state.current_page) {
            state.current_page = payload.current_page;
        }
    },

    [UPDATE_USER_SELECTED_ROLE]: (state, payload) => {
        if (payload.selected_role != state.selected_role) {
            state.selected_role = payload.selected_role;
        }
    },

    [USER_RESPONSE]: (state, resp) => {
        state.users = resp.data;
        state.pagination.total = resp.meta.total;
    },

    [USER_EDIT]: (state) => {
        state.action = "UPDATE";
        state.save_update_title = "Update";
        state.errors = "";
        state.error = "";
        state.success = "";
    },

    [USER_ADD]: (state) => {
        state.action = "SAVE";
        state.save_update_title = "Add";
    },

    [USER_SUCCESS]: (state, resp) => {
        state.errors = "";
        state.error = "";
        state.success = resp.message;
    },

    [USER_ERROR]: (state, error) => {
        state.error = error.message;
        state.errors = error.errors;
        state.success = "";
    }
};

const actions = {
    [USER_SEARCH]: ({dispatch}) => {
        dispatch(USER_LISTS);
    },

    [USER_CURRENT_PAGE]: ({dispatch}) => {
        dispatch(USER_LISTS);
    },

    [USER_SELECTED_ROLE]: ({dispatch}) => {
        dispatch(USER_LISTS);
    },

    [USER_EDIT]: ({commit}, postData) => {
        commit(USER_EDIT, postData.user)
    },

    [USER_ADD]: ({commit}) => {
        commit(USER_ADD)
    },

    [USER_LISTS]: ({commit, state}) => {
        const params = "?page="+ state.current_page
        +'&per_page='+state.pagination.per_page
        +'&role='+state.selected_role
        +'&order_by='+state.pagination.order_by
        +'&order_direction='+state.pagination.order_direction
        +'&search='+state.search;
        apiCall({url: api_routes.user.user_lists+params, method: "get"})
            .then(resp => {
                commit(USER_RESPONSE, resp);
            })
            .catch(err => {

            });
    },

    [USER_STORE]: ({ commit, dispatch }, postData) => {
        return new Promise((resolve, reject) => {
            apiCall({ url: api_routes.user.user_store, data: postData.data, method: "post" })
                .then(resp => {
                    dispatch(USER_LISTS);
                    commit(USER_SUCCESS, resp);
                    resolve(resp);
                })
                .catch(err => {
                    commit(USER_ERROR, err);
                    reject(err);
                });
        });
    },

    [USER_UPDATE]: ({ commit, dispatch }, postData) => {
        return new Promise((resolve, reject) => {
            apiCall({ url: api_routes.user.user_update+"/"+postData.data.id, data: postData.data, method: "put" })
                .then(resp => {
                    dispatch(USER_LISTS);
                    dispatch(USER_ADD);
                    commit(USER_SUCCESS, resp);
                    resolve(resp);
                })
                .catch(err => {
                    commit(USER_ERROR, err);
                    reject(err);
                });
        });
    },

    [USER_DELETE]: ({commit, dispatch}, postData) => {
        apiCall({url: api_routes.user.user_delete+"/"+postData.data.id, method: "delete"})
            .then(resp => {
                dispatch(USER_LISTS);
                dispatch(USER_ADD);
                commit(USER_SUCCESS, resp);
            })
            .catch(err => {
                commit(USER_ERROR, err);
            });
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};