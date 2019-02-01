import {applyMiddleware, createStore, compose, combineReducers} from "redux";
import reduxThunk from "redux-thunk";
import system from "./system";
import abode from "./abode";
import resettlement from "./resettlement";
import participating from "./participating";

const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

const store = createStore(
    combineReducers({
        system,
        abode,
        resettlement,
        participating,
    }),
    composeEnhancers(
        applyMiddleware(reduxThunk),
    ),
);

export default store;