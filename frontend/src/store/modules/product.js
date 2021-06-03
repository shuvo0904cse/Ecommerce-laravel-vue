import {
    PRODUCT_SEARCH,
    UPDATE_PRODUCT_SEARCH,
    PRODUCT_CURRENT_PAGE,
    UPDATE_PRODUCT_CURRENT_PAGE,
    PRODUCT_SELECTED_ROLE,
    UPDATE_PRODUCT_SELECTED_ROLE,
    PRODUCT_LISTS,
    PRODUCT_RESPONSE,
    PRODUCT_SUCCESS,
    PRODUCT_ERROR,
    PRODUCT_STORE,
    PRODUCT_EDIT,
    PRODUCT_ADD,
    PRODUCT_UPDATE,
    PRODUCT_DELETE
} from "../actions/product";
import {apiCall, api_routes} from "@/utils/api";

const state = {
    product_save_update_title: "Add",
    search: "",
    current_page: "",
    selected_role: "",
    product_pagination: {
        total: 0,
        per_page : 10,
        order_by: 'id',
        order_direction: 'asc'
    },
    product_action: "SAVE",
    products: [],
    product_fields: [
        { key: "title" },
        { key: "code" },
        { key: "actions" }
    ],
    product_success: [],
    product_error: [],
    product_errors: []
};

const getters = {
    product_save_update_title: state => state.product_save_update_title,
    product_pagination: state => state.product_pagination,
    product_action: state => state.product_action,
    products: state => state.products,
    product_fields: state => state.product_fields,
    product_success: state => state.product_success,
    product_error: state => state.product_error,
    product_errors: state => state.product_errors,
};

const mutations = {
    [UPDATE_PRODUCT_SEARCH]: (state, payload) => {
         if (payload.search != state.search) {
            state.search = payload.search;
         }
    },

    [UPDATE_PRODUCT_CURRENT_PAGE]: (state, payload) => {
        if (payload.current_page != state.current_page) {
            state.current_page = payload.current_page;
        }
    },

    [UPDATE_PRODUCT_SELECTED_ROLE]: (state, payload) => {
        if (payload.selected_role != state.selected_role) {
            state.selected_role = payload.selected_role;
        }
    },

    [PRODUCT_RESPONSE]: (state, resp) => {
        state.products = resp.data;
        state.product_pagination.total = resp.meta.total;
    },

    [PRODUCT_EDIT]: (state) => {
        state.product_action = "UPDATE";
        state.product_save_update_title = "Update";
        state.product_errors = "";
        state.product_error = "";
        state.product_success = "";
    },

    [PRODUCT_ADD]: (state) => {
        state.product_action = "SAVE";
        state.product_save_update_title = "Add";
    },

    [PRODUCT_SUCCESS]: (state, resp) => {
        state.product_errors = "";
        state.product_error = "";
        state.product_success = resp.message;
    },

    [PRODUCT_ERROR]: (state, error) => {
        console.log("errorerror", error);
        state.product_error = error.message;
        state.product_errors = error.errors;
        state.product_success = "";
    }
};

const actions = {
    [PRODUCT_SEARCH]: ({dispatch}) => {
        dispatch(PRODUCT_LISTS);
    },

    [PRODUCT_CURRENT_PAGE]: ({dispatch}) => {
        dispatch(PRODUCT_LISTS);
    },

    [PRODUCT_SELECTED_ROLE]: ({dispatch}) => {
        dispatch(PRODUCT_LISTS);
    },

    [PRODUCT_EDIT]: ({commit}, postData) => {
        commit(PRODUCT_EDIT, postData.product)
    },

    [PRODUCT_ADD]: ({commit}) => {
        commit(PRODUCT_ADD)
    },

    [PRODUCT_LISTS]: ({commit, state}) => {
        const params = "?page="+ state.current_page
        +'&per_page='+state.product_pagination.per_page
        +'&order_by='+state.product_pagination.order_by
        +'&order_direction='+state.product_pagination.order_direction
        +'&search='+state.search;
        apiCall({url: api_routes.product.product_lists+params, method: "get"})
            .then(resp => {
                commit(PRODUCT_RESPONSE, resp);
            })
            .catch(err => {

            });
    },

    [PRODUCT_STORE]: ({ commit, dispatch }, postData) => {
        return new Promise((resolve, reject) => {
            apiCall({ url: api_routes.product.product_store, data: postData.data, method: "post" })
                .then(resp => {
                    dispatch(PRODUCT_LISTS);
                    commit(PRODUCT_SUCCESS, resp);
                    resolve(resp);
                })
                .catch(err => {
                    commit(PRODUCT_ERROR, err);
                    reject(err);
                });
        });
    },

    [PRODUCT_UPDATE]: ({ commit, dispatch }, postData) => {
        return new Promise((resolve, reject) => {
            apiCall({ url: api_routes.product.product_update+"/"+postData.data.id, data: postData.data, method: "put" })
                .then(resp => {
                    dispatch(PRODUCT_LISTS);
                    dispatch(PRODUCT_ADD);
                    commit(PRODUCT_SUCCESS, resp);
                    resolve(resp);
                })
                .catch(err => {
                    commit(PRODUCT_ERROR, err);
                    reject(err);
                });
        });
    },

    [PRODUCT_DELETE]: ({commit, dispatch}, postData) => {
        apiCall({url: api_routes.product.product_delete+"/"+postData.data.id, method: "delete"})
            .then(resp => {
                dispatch(PRODUCT_LISTS);
                dispatch(PRODUCT_ADD);
                commit(PRODUCT_SUCCESS, resp);
            })
            .catch(err => {
                commit(PRODUCT_ERROR, err);
            });
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};