export const SYSTEM = {
    REQUEST_ME: "SYSTEM_REQUEST_ME",
    RECEIVE_ME: "SYSTEM_RECEIVE_ME",
    REQUEST_MANAGERS: "SYSTEM_REQUEST_MANAGERS",
    RECEIVE_MANAGERS: "SYSTEM_RECEIVE_MANAGERS",
};

const initialState = {
    user: {
        isFetching: false,
        error: false,
    },
    users: {
        items: [],
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
    case SYSTEM.REQUEST_MANAGERS:
        return {
            ...state,
            users: {
                ...state.users,
                isFetching: true,
            }
        };
    case SYSTEM.RECEIVE_MANAGERS:
        return {
            ...state,
            users: {
                ...state.users,
                isFetching: false,
                ...payload,
            }
        };
    default:
        return state;
    }
};