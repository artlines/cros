import "@babel/polyfill";
import 'typeface-roboto';

import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import store from "reducers/store";
import App from './App';

const Root = () => {
    return (
        <Provider store={store}>
            <App/>
        </Provider>
    );
};

ReactDOM.render(<Root/>, document.getElementById("root"));

module.hot.accept();