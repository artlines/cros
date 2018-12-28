import {applyMiddleware, createStore, compose, combineReducers} from "redux";
import reduxThunk from "redux-thunk";
import session from './session';

const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

const store = createStore(
    combineReducers({
        session,
    }),
    composeEnhancers(
        applyMiddleware(reduxThunk),
    ),
);

export default store;