import "@babel/polyfill";
import "typeface-roboto";

import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import store from "reducers/store";

import {MuiThemeProvider} from "@material-ui/core/styles";
import theme from "./theme";

import App from "./App";

const Root = () => {
    return (
        <Provider store={store}>
            <MuiThemeProvider theme={theme}>
                <App/>
            </MuiThemeProvider>
        </Provider>
    );
};

ReactDOM.render(<Root/>, document.getElementById("root"));

module.hot.accept();