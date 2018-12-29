import { SYSTEM } from '../reducers/system';
import API from '../libs/api';

const request = new API();

export default {

    fetchUser: (id = null) => {

        return dispatch => {
            dispatch({ type: SYSTEM.REQUEST_ME });
            request.get(`users/me`)
                .then(res => console.log(res));
        }
    },

};