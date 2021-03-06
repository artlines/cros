export const ABODE = {
    REQUEST_HOUSING: "ABODE_REQUEST_HOUSING",
    RECEIVE_HOUSING: "ABODE_RECEIVE_HOUSING",

    REQUEST_HOUSINGS: "ABODE_REQUEST_HOUSINGS",
    RECEIVE_HOUSINGS: "ABODE_RECEIVE_HOUSINGS",

    REQUEST_PARTICIPATION_CLASS: "ABODE_REQUEST_PARTICIPATION_CLASS",
    RECEIVE_PARTICIPATION_CLASS: "ABODE_RECEIVE_PARTICIPATION_CLASS",

    REQUEST_APARTMENT_TYPE: "ABODE_REQUEST_APARTMENT_TYPE",
    RECEIVE_APARTMENT_TYPE: "ABODE_RECEIVE_APARTMENT_TYPE",

    REQUEST_ROOM_TYPE: "ABODE_REQUEST_ROOM_TYPE",
    RECEIVE_ROOM_TYPE: "ABODE_RECEIVE_ROOM_TYPE",

    REQUEST_ROOMS: "ABODE_REQUEST_ROOMS",
    RECEIVE_ROOMS: "ABODE_RECEIVE_ROOMS",

    REQUEST_APARTMENTS: "ABODE_REQUEST_APARTMENTS",
    RECEIVE_APARTMENTS: "ABODE_RECEIVE_APARTMENTS",

    REQUEST_ROOMS_SUMMARY_INFORMATION: "ABODE_REQUEST_ROOMS_SUMMARY_INFORMATION",
    RECEIVE_ROOMS_SUMMARY_INFORMATION: "ABODE_RECEIVE_ROOMS_SUMMARY_INFORMATION",

    REQUEST_APARTMENTS_SUMMARY_INFORMATION: "ABODE_REQUEST_APARTMENTS_SUMMARY_INFORMATION",
    RECEIVE_APARTMENTS_SUMMARY_INFORMATION: "ABODE_RECEIVE_APARTMENTS_SUMMARY_INFORMATION",
};

const _initialObjectState = {
    items: [],
    item: {
        isFetching: false,
        error: false,
    },
    total_count: 0,
    isFetching: false,
    error: false,
};

const initialState = {
    housing: {
        ..._initialObjectState
    },
    participation_class: {
        ..._initialObjectState
    },
    apartment_type: {
        ..._initialObjectState
    },
    room_type: {
        ..._initialObjectState
    },
    room: {
        ..._initialObjectState
    },
    apartment: {
        ..._initialObjectState
    },
    rooms_summary_information: {
        ..._initialObjectState
    },
    apartments_summary_information: {
        ..._initialObjectState
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
        case ABODE.REQUEST_HOUSINGS:
            return {
                ...state,
                housing: {
                    ...state.housing,
                    isFetching: true,
                    error: false,
                },
            };
        case ABODE.RECEIVE_HOUSINGS:
            return {
                ...state,
                housing: {
                    ...state.housing,
                    isFetching: false,
                    ...payload,
                },
            };
    case ABODE.REQUEST_ROOMS:
        return {
            ...state,
            room: {
                ...state.room,
                isFetching: true,
                error: false,
            },
        };
    case ABODE.RECEIVE_ROOMS:
        return {
            ...state,
            room: {
                ...state.room,
                isFetching: false,
                ...payload,
            },
        };
    case ABODE.REQUEST_APARTMENTS:
        return {
            ...state,
            apartment: {
                ...state.apartment,
                isFetching: true,
                error: false,
            },
        };
    case ABODE.RECEIVE_APARTMENTS:
        return {
            ...state,
            apartment: {
                ...state.apartment,
                isFetching: false,
                ...payload,
            },
        };
    case ABODE.REQUEST_PARTICIPATION_CLASS:
        return {
            ...state,
            participation_class: {
                ...state.participation_class,
                isFetching: true,
                error: false,
            },
        };
    case ABODE.RECEIVE_PARTICIPATION_CLASS:
        return {
            ...state,
            participation_class: {
                ...state.participation_class,
                isFetching: false,
                ...payload,
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
        case ABODE.REQUEST_ROOMS_SUMMARY_INFORMATION:
            return {
                ...state,
                rooms_summary_information: {
                    ...state.rooms_summary_information,
                    isFetching: true,
                    error: false,
                },
            };
        case ABODE.RECEIVE_ROOMS_SUMMARY_INFORMATION:
            return {
                ...state,
                rooms_summary_information: {
                    ...state.rooms_summary_information,
                    isFetching: false,
                    ...payload,
                },
            };
        case ABODE.REQUEST_APARTMENTS_SUMMARY_INFORMATION:
            return {
                ...state,
                apartments_summary_information: {
                    ...state.apartments_summary_information,
                    isFetching: true,
                    error: false,
                },
            };
        case ABODE.RECEIVE_APARTMENTS_SUMMARY_INFORMATION:
            return {
                ...state,
                apartments_summary_information: {
                    ...state.apartments_summary_information,
                    isFetching: false,
                    ...payload,
                },
            };
    default:
        return state;
    }
};