export const ABODE = {
    REQUEST_HOUSING: "ABODE_REQUEST_HOUSING",
    RECEIVE_HOUSING: "ABODE_RECEIVE_HOUSING",

    REQUEST_APARTMENT_TYPE: "ABODE_REQUEST_APARTMENT_TYPE",
    RECEIVE_APARTMENT_TYPE: "ABODE_RECEIVE_APARTMENT_TYPE",

    REQUEST_ROOM_TYPE: "ABODE_REQUEST_ROOM_TYPE",
    RECEIVE_ROOM_TYPE: "ABODE_RECEIVE_ROOM_TYPE",
};

const initialState = {
    housing: {
        items: [],
        item: {
            isFetching: false,
            error: false,
        },
        total_count: 0,
        isFetching: false,
        error: false,
    },
    room_type: {
        items: [],
        isFetching: false,
        error: false,
    },
    apartment_type: {
        items: [],
        isFetching: false,
        error: false,
    },
};

export default (state = initialState, action) => {
    const {type, payload} = action;

    switch (type) {
        case ABODE.REQUEST_HOUSING:
            return {
                ...state,
                housing: {
                    ...state.housing,
                    item: {
                        ...state.housing.item,
                        error: false,
                        isFetching: true,
                    },
                },
            };
        case ABODE.RECEIVE_HOUSING:
            return {
                ...state,
                housing: {
                    ...state.housing,
                    item: {
                        ...state.housing.item,
                        isFetching: false,
                        ...payload,
                    },
                },
            };
        case ABODE.REQUEST_APARTMENT_TYPE:
            return {
                ...state,
                apartment_type: {
                    ...state.apartment_type,
                    isFetching: true,
                    error: false,
                },
            };
        case ABODE.RECEIVE_APARTMENT_TYPE:
            return {
                ...state,
                apartment_type: {
                    ...state.apartment_type,
                    isFetching: false,
                    ...payload,
                },
            };
        case ABODE.REQUEST_ROOM_TYPE:
            return {
                ...state,
                room_type: {
                    ...state.room_type,
                    isFetching: true,
                    error: false,
                },
            };
        case ABODE.RECEIVE_ROOM_TYPE:
            return {
                ...state,
                room_type: {
                    ...state.room_type,
                    isFetching: false,
                    ...payload,
                },
            };
        default:
            return state;
    }
}