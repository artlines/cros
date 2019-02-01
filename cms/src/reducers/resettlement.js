import sortBy from "lodash/sortBy";

export const RESETTLEMENT = {
    REQUEST_APARTMENTS: "RESETTLEMENT_REQUEST_APARTMENTS",
    RECEIVE_APARTMENTS: "RESETTLEMENT_RECEIVE_APARTMENTS",
    REQUEST_MEMBERS: "RESETTLEMENT_REQUEST_MEMBERS",
    RECEIVE_MEMBERS: "RESETTLEMENT_RECEIVE_MEMBERS",
};

const initialState = {
    apartments: {
        isFetching: false,
        error: false,
        items: [],
    },
    members: {
        isFetching: false,
        error: false,
        items: [],
    },
};

export default (state = initialState, action) => {
    const { type, payload } = action;

    switch(type) {
    case RESETTLEMENT.REQUEST_MEMBERS:
        return {
            ...state,
            members: {
                ...state.members,
                isFetching: true,
            }
        };
    case RESETTLEMENT.RECEIVE_MEMBERS:
        const { items, ...other } = payload;

        return {
            ...state,
            members: {
                ...state.members,
                isFetching: false,
                items: [...sortBy(items, ["org_name", "first_name", "last_name"])],
                ...other,
            }
        };
    case RESETTLEMENT.REQUEST_APARTMENTS:
        return {
            ...state,
            apartments: {
                ...state.apartments,
                isFetching: true,
            }
        };
    case RESETTLEMENT.RECEIVE_APARTMENTS:
        return {
            ...state,
            apartments: {
                ...state.apartments,
                isFetching: false,
                ...payload,
            }
        };
    default:
        return state;
    }
};