

const initialState = {
    isFetching: false,
    error: false,
    data: [],
};

export default (state = initialState, action) => {
    const { type, payload } = action;

    switch(type) {
        case '':

            break;
        default:
            return state;
    }
};