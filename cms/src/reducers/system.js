export const SYSTEM = {
    REQUEST_ME: "SYSTEM_REQUEST_ME",
    RECEIVE_ME: "SYSTEM_RECEIVE_ME",
};

const initialState = {
    user: {
        isFetching: false,
        error: false,
    },
};

export default (state = initialState, action) => {
    const { type, payload } = action;

    switch(type) {
    case SYSTEM.REQUEST_ME:
        return {
            ...state,
            user: {
                ...state.user,
                isFetching: true,
            }
        };
    case SYSTEM.RECEIVE_ME:
        return {
            ...state,
            user: {
                ...state.user,
                isFetching: false,
                ...payload,
            }
        };
    default:
        return state;
    }
};