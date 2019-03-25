export const PROGRAM = {
    REQUEST_SPEAKERS: "PROGRAM_REQUEST_SPEAKERS",
    RECEIVE_SPEAKERS: "PROGRAM_RECEIVE_SPEAKERS",

    REQUEST_COMMITTEE: "PROGRAM_REQUEST_COMMITTEE",
    RECEIVE_COMMITTEE: "PROGRAM_RECEIVE_COMMITTEE",
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
    speaker: {..._initialObjectState},
    committee: {..._initialObjectState},
};

export default (state = initialState, action) => {
    const { type, payload } = action;

    switch(type) {
        case PROGRAM.REQUEST_SPEAKERS:
            return {
                ...state,
                speaker: {
                    ...state.speaker,
                    isFetching: true,
                    ...payload,
                },
            };
        case PROGRAM.RECEIVE_SPEAKERS:
            return {
                ...state,
                speaker: {
                    ...state.speaker,
                    isFetching: false,
                    ...payload,
                },
            };
        case PROGRAM.REQUEST_COMMITTEE:
            return {
                ...state,
                committee: {
                    ...state.committee,
                    isFetching: true,
                    ...payload,
                },
            };
        case PROGRAM.RECEIVE_COMMITTEE:
            return {
                ...state,
                committee: {
                    ...state.committee,
                    isFetching: false,
                    ...payload,
                },
            };
        default:
            return state;
    }
};