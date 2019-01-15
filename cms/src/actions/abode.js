import { ABODE } from '../reducers/abode';
import API from '../libs/api';

const request = new API();

export default {

    fetchHousing: id => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_HOUSING });
            request.get(`housing/${id}`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_HOUSING, payload }))
                .catch(err => console.log(`abode::fetchHousing`, err)); // TODO: doing something with error
        };
    },

    fetchRooms: query => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_ROOMS });
            request.get(`room`, query)
                .then(payload => dispatch({ type: ABODE.RECEIVE_ROOMS, payload }))
                .catch(err => console.log(`abode::fetchRooms`, err)); // TODO: doing something with error
        };
    },

    fetchApartmentTypes: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_APARTMENT_TYPE });
            request.get(`apartment_type`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_APARTMENT_TYPE, payload }));
                // TODO: doing something with error
        };
    },

    fetchRoomTypes: () => {
        return dispatch => {
            dispatch({ type: ABODE.REQUEST_ROOM_TYPE });
            request.get(`room_type`)
                .then(payload => dispatch({ type: ABODE.RECEIVE_ROOM_TYPE, payload }));
                // TODO: doing something with error
        };
    },

};