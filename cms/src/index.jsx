import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import "@babel/polyfill";
import store from "reducers/store";
import { BrowserRouter } from "react-router-dom";
import App from "App";

const Root = () => {
    return (
        <Provider store={store}>
            <BrowserRouter>
                <App/>
            </BrowserRouter>
        </Provider>
    );
};

ReactDOM.render(<Root/>, document.getElementById("root"));

module.hot.accept();