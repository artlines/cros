import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import {compose} from "redux";
import {Route, Switch, withRouter} from "react-router-dom";
import routes from "config/routes";
import Layout from "./components/common/Layout";

class Router extends React.PureComponent {
    constructor(props) {
        super(props);

        this.state = {
            title: ' ',
        };
    }

    setTitle = title => this.setState({title});

    render() {
        const { roles } = this.props;
        const { title } = this.state;

        return (
            <Layout title={title}>
                <Switch>
                    {routes.map((route, i) => {
                        const { role, exact, path, title, Component } = route;

                        if (role && !roles.includes(role)) {
                            return null;
                        }

                        return (
                            <Route
                                key={i}
                                path={path}
                                exact={exact || false}
                                render={props => {
                                    this.setTitle(title);

                                    return (<Component {...props}/>);
                                }}
                            />
                        );
                    })}
                </Switch>
            </Layout>
        );
    }
}

Router.propTypes = {
    /**
     * Defined user roles to check and prevent render not accessed route
     */
    roles: PropTypes.array.isRequired,
};

const mapStateToProps = state =>
    ({
        roles: state.system.user.roles,
    });

export default compose(
    withRouter,
    connect(mapStateToProps, null),
)(Router);