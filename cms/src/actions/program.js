import { PROGRAM } from "../reducers/program";
import API from "../libs/api";

const request = new API();

export default {

    fetchSpeakers: (data) => {
        const query = {...data, type: 'speaker'};

        return dispatch => {
            dispatch({ type: PROGRAM.REQUEST_SPEAKERS });
            request.get(`program_member`, query)
                .then(payload => dispatch({ type: PROGRAM.RECEIVE_SPEAKERS, payload }));
        };
    },

    fetchCommittee: (data) => {
        const query = {...data, type: 'committee'};

        return dispatch => {
            dispatch({ type: PROGRAM.REQUEST_COMMITTEE });
            request.get(`program_member`, query)
                .then(payload => dispatch({ type: PROGRAM.RECEIVE_COMMITTEE, payload }));
        };
    },

};