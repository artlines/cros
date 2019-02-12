export const SYSTEM = {
    REQUEST_ME: "SYSTEM_REQUEST_ME",
    RECEIVE_ME: "SYSTEM_RECEIVE_ME",

    REQUEST_MANAGERS: "SYSTEM_REQUEST_MANAGERS",
    RECEIVE_MANAGERS: "SYSTEM_RECEIVE_MANAGERS",

    REQUEST_USERS: "SYSTEM_REQUEST_USERS",
    RECEIVE_USERS: "SYSTEM_RECEIVE_USERS",

    REQUEST_ROLES: "SYSTEM_REQUEST_ROLES",
    RECEIVE_ROLES: "SYSTEM_RECEIVE_ROLES",
};

const initialState = {
    user: {
        isFetching: false,
        error: false,
    },
    managers: {
        items: [],
        total_count: 0,
        isFetching: false,
        error: false,
    },
    users: {
        items: [],
        total_count: 0,
        isFetching: false,
        error: false,
    },
    roles: {
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
                managers: {
                    ...state.managers,
                    isFetching: true,
                }
            };
        case SYSTEM.RECEIVE_MANAGERS:
            return {
                ...state,
                managers: {
                    ...state.managers,
                    isFetching: false,
                    ...payload,
                }
            };
        case SYSTEM.REQUEST_USERS:
            return {
                ...state,
                users: {
                    ...state.users,
                    isFetching: true,
                }
            };
        case SYSTEM.RECEIVE_USERS:
            return {
                ...state,
                users: {
                    ...state.users,
                    isFetching: false,
                    ...payload,
                }
            };
        case SYSTEM.REQUEST_ROLES:
            return {
                ...state,
                roles: {
                    ...state.roles,
                    isFetching: true,
                }
            };
        case SYSTEM.RECEIVE_ROLES:
            return {
                ...state,
                roles: {
                    ...state.roles,
                    isFetching: false,
                    ...payload,
                }
            };
    default:
        return state;
    }
};