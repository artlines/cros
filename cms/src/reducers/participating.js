export const PARTICIPATING = {
    REQUEST_COMMENTS: "PARTICIPATING_REQUEST_COMMENTS",
    RECEIVE_COMMENTS: "PARTICIPATING_RECEIVE_COMMENTS",

    REQUEST_CONFERENCE_ORGANIZATIONS: "PARTICIPATING_REQUEST_CONFERENCE_ORGANIZATIONS",
    RECEIVE_CONFERENCE_ORGANIZATIONS: "PARTICIPATING_RECEIVE_CONFERENCE_ORGANIZATIONS",
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
    comment: {..._initialObjectState},
    conference_organization: {..._initialObjectState},
};

export default (state = initialState, action) => {
    const { type, payload } = action;

    switch(type) {
    case PARTICIPATING.REQUEST_COMMENTS:
        return {
            ...state,
            comment: {
                ...state.comment,
                isFetching: true,
            }
        };
    case PARTICIPATING.RECEIVE_COMMENTS:
        return {
            ...state,
            comment: {
                ...state.comment,
                isFetching: false,
                ...payload,
            }
        };

    case PARTICIPATING.REQUEST_CONFERENCE_ORGANIZATIONS:
        return {
            ...state,
            conference_organization: {
                ...state.conference_organization,
                isFetching: true,
            }
        };
    case PARTICIPATING.RECEIVE_CONFERENCE_ORGANIZATIONS:
        return {
            ...state,
            conference_organization: {
                ...state.conference_organization,
                isFetching: false,
                ...payload,
            }
        };
    default:
        return state;
    }
};