import React from 'react';
import {connect} from 'react-redux';
import Router from './Router';
import system from './actions/system';
import {BrowserRouter} from "react-router-dom";

class App extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        this.props.loadInitialData();
    }

    render() {
        const { user: { isFetching, roles, error, id } } = this.props;

        if (!id) return null;

        return (id &&
            <BrowserRouter>
                <Router/>
            </BrowserRouter>
        );
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
    });

const mapDispatchToProps = dispatch =>
    ({
        loadInitialData: () => {
            dispatch(system.fetchMe());
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(App);