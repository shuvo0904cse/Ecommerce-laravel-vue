import axios from 'axios'
import { settings } from "@/settings";

const API_SERVER = settings.API_SERVER;

export const api_routes = {
    user: {
        login: API_SERVER + "login",
        me: API_SERVER + "user",
        user_lists: API_SERVER + "user-lists",
        user_store: API_SERVER + "store-user",
        user_update: API_SERVER + "update-user",
        user_delete: API_SERVER + "delete-user",
    },
    product: {
        product_lists: API_SERVER + "product-lists",
        product_store: API_SERVER + "store-product",
        product_update: API_SERVER + "update-product",
        product_delete: API_SERVER + "delete-product",
    },
};

export const apiCall = ({ url, method, ...args }) =>
    new Promise((resolve, reject) => {
        let token = localStorage.getItem("user-token") || "";

        if (token)
            axios.defaults.headers.common["Authorization"] = "Bearer " + token;

        try {
            axios({
                method: method || "get",
                url: url,
                ...args
            })
                .then(resp => {
                    resolve(resp.data);
                })
                .catch(error => {
                    reject(handleError(error));
                });
        } catch (error) {
            reject(handleError(error));
        }
    });

// ***** Handle errors *****/
export function handleError(error) {
    if (error.response) {
        const status = error.response.status;
        var message = "";
        switch (status) {
            case 400:
                message = error.response.data;
                break;
            case 401:
                message = error.response.data;
                break;
            case 403:
                message = error.response.data;
                break;
            case 500:
                message = "Server Error";
                break;
            default:
                message = "Something went wrong. Please try again later.";
        }
        return message;
    }
}