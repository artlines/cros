import React from "react";
import PropTypes from "prop-types";
import {Route, Switch, withRouter} from "react-router-dom";
import routes from "config/routes";

class Routes extends React.Component {
    render() {
        const { roles } = this.props;

        return (
            <Switch>
                {routes.map((route, i) => {

                    if (route.role && !roles.includes(route.role)) {
                        return null;
                    }

                    return (
                        <Route
                            key={i}
                            path={route.path}
                            component={route.component || null}
                            exact={route.exact || false}
                        />
                    );
                })}
            </Switch>
        );
    }
}

Routes.propTypes = {
    /**
     * Defined user roles to check and prevent render not accessed route
     */
    roles: PropTypes.array.isRequired,
};

export default withRouter(Routes);