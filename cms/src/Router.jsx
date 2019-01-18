import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import {compose} from "redux";
import {Route, Switch, withRouter} from "react-router-dom";
import routes from "config/routes";
import Layout from "./components/common/Layout";

class Router extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { roles } = this.props;

        return (
            <Layout>
                <Switch>
                    {routes.map((route, i) => {

                        if (route.role && !roles.includes(route.role)) {
                            return null;
                        }

                        return (
                            <Route
                                key={i}
                                path={route.path}
                                component={route.Component || null}
                                exact={route.exact || false}
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