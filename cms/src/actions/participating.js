import { PARTICIPATING } from "../reducers/participating";
import API from "../libs/api";

const request = new API();

export default {

    fetchOrganizationDirectory: () => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_ORGANIZATION_DIRECTORY });
            request.get(`organization_directory`)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_ORGANIZATION_DIRECTORY, payload }));
        };
    },

    fetchConferences: () => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_CONFERENCES });
            request.get(`conference`)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_CONFERENCES, payload }));
        };
    },

    fetchConferenceArchive: (id) => {
        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_CONFERENCE });
            request.get(`conference/${id}/archive`)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_CONFERENCE, payload }));
        };
    },

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