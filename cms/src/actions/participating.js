import { PARTICIPATING } from "../reducers/participating";
import API from "../libs/api";

const request = new API();

export default {

    fetchCommentsByOrgId: (organization_id) => {

        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_COMMENTS });
            request.get(`comments`, {organization_id})
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_COMMENTS, payload }));
        };
    },

    fetchConferenceOrganizations: (data) => {

        return dispatch => {
            dispatch({ type: PARTICIPATING.REQUEST_CONFERENCE_ORGANIZATIONS });
            request.get(`conference_organization`, data)
                .then(payload => dispatch({ type: PARTICIPATING.RECEIVE_CONFERENCE_ORGANIZATIONS, payload }));
        };
    },

};