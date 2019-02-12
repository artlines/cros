import { SYSTEM } from "../reducers/system";
import API from "../libs/api";

const request = new API();

export default {

    fetchMe: () => {

        return dispatch => {
            dispatch({ type: SYSTEM.REQUEST_ME });
            request.get(`users/me`)
                .then(payload => dispatch({ type: SYSTEM.RECEIVE_ME, payload }));
        };
    },

    fetchManagers: () => {

        return dispatch => {
            dispatch({ type: SYSTEM.REQUEST_MANAGERS });
            request.get(`users/managers`)
                .then(payload => dispatch({ type: SYSTEM.RECEIVE_MANAGERS, payload }));
        };
    },

    fetchUsers: (query = {}) => {

        return dispatch => {
            dispatch({ type: SYSTEM.REQUEST_USERS });
            request.get(`users`, query)
                .then(payload => dispatch({ type: SYSTEM.RECEIVE_USERS, payload }));
        };
    },

    fetchRoles: () => {

        return dispatch => {
            dispatch({ type: SYSTEM.REQUEST_ROLES });
            request.get(`users/roles`)
                .then(payload => dispatch({ type: SYSTEM.RECEIVE_ROLES, payload }));
        };
    },

};