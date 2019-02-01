import { PARTICIPATING } from "../reducers/participating";
import API from "../libs/api";

const request = new API();

export default {

    fetchConferenceOrganizations: (data = {}) => {

        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_CONFERENCE_ORGANIZATIONS });
            request.get(`conference_organization`, data)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_CONFERENCE_ORGANIZATIONS, payload }));
        };
    },

    fetchMembers: (query = {}) => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_MEMBERS });
            request.get(`conference_member`, query)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_MEMBERS, payload }));
        };
    },

    fetchInvoices: (query = {}) => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_INVOICES });
            request.get(`invoice`, query)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_INVOICES, payload }));
        };
    },

    fetchComments: (query = {}) => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_COMMENTS });
            request.get(`comment`, query)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_COMMENTS, payload }));
        };
    },

};