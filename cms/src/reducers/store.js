import {applyMiddleware, createStore, compose, combineReducers} from "redux";
import reduxThunk from "redux-thunk";
import system from "./system";
import abode from "./abode";
import participating from "./participating";

const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

const store = createStore(
    combineReducers({
        system,
        abode,
        participating,
    }),
    composeEnhancers(
        applyMiddleware(reduxThunk),
    ),
);

export default store;