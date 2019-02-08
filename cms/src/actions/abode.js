import { ABODE } from "../reducers/abode";
import API from "../libs/api";

const request = new API();

export default {

    fetchHousing: id => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_HOUSING });
            request.get(`housing/${id}`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_HOUSING, payload }));
        };
    },

    fetchRooms: query => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_ROOMS });
            request.get(`room`, query)
                .then(payload => dispatch({ type: ABODE.RECEIVE_ROOMS, payload }));
        };
    },

    fetchApartments: query => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_APARTMENTS });
            request.get(`apartment`, query)
                .then(payload => dispatch({ type: ABODE.RECEIVE_APARTMENTS, payload }));
        };
    },

    fetchParticipationClasses: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_PARTICIPATION_CLASS });
            request.get(`participation_class`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_PARTICIPATION_CLASS, payload }));
        };
    },

    fetchApartmentTypes: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_APARTMENT_TYPE });
            request.get(`apartment_type`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_APARTMENT_TYPE, payload }));
        };
    },

    fetchRoomTypes: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_ROOM_TYPE });
            request.get(`room_type`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_ROOM_TYPE, payload }));
        };
    },

    fetchSummaryInformation: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_SUMMARY_INFORMATION });
            request.get(`room_type/summary_information`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_SUMMARY_INFORMATION, payload }));
        };
    },

};