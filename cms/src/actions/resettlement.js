import { RESETTLEMENT } from "../reducers/resettlement";
import API from "../libs/api";

const request = new API();

export default {

    fetchApartments: (housing_id) => {

        return dispatch => {
            dispatch({ type: RESETTLEMENT.REQUEST_APARTMENTS });
            request.get(`housing/${housing_id}/resettlement`)
                .then(payload => dispatch({ type: RESETTLEMENT.RECEIVE_APARTMENTS, payload }));
        };
    },

    fetchNotSettledMembers: (housing_id = null) => {

        return dispatch => {
            dispatch({ type: RESETTLEMENT.REQUEST_MEMBERS });
            request.get(`conference_member/not_settled`, { housing_id })
                .then(payload => dispatch({ type: RESETTLEMENT.RECEIVE_MEMBERS, payload }));
        };
    },

};