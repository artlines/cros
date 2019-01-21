export const PARTICIPATING = {
    REQUEST_COMMENTS: "PARTICIPATING_REQUEST_COMMENTS",
    RECEIVE_COMMENTS: "PARTICIPATING_RECEIVE_COMMENTS",

    REQUEST_MEMBERS: "PARTICIPATING_REQUEST_MEMBERS",
    RECEIVE_MEMBERS: "PARTICIPATING_RECEIVE_MEMBERS",

    REQUEST_INVOICES: "PARTICIPATING_REQUEST_INVOICES",
    RECEIVE_INVOICES: "PARTICIPATING_RECEIVE_INVOICES",

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
    invoice: {..._initialObjectState},
    member: {..._initialObjectState},
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

        case PARTICIPATING.REQUEST_MEMBERS:
            return {
                ...state,
                member: {
                    ...state.member,
                    isFetching: true,
                }
            };
        case PARTICIPATING.RECEIVE_MEMBERS:
            return {
                ...state,
                member: {
                    ...state.member,
                    isFetching: false,
                    ...payload,
                }
            };

        case PARTICIPATING.REQUEST_INVOICES:
            return {
                ...state,
                invoice: {
                    ...state.invoice,
                    isFetching: true,
                }
            };
        case PARTICIPATING.RECEIVE_INVOICES:
            return {
                ...state,
                invoice: {
                    ...state.invoice,
                    isFetching: false,
                    ...payload,
                }
            };
    default:
        return state;
    }
};