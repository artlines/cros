import React from 'react';
import {Route, Switch} from 'react-router-dom';
import routes from 'config/routes';

const Routes = () => (
    <Switch>
        {routes.map(route =>
            <Route
                path={route.path}
                component={route.component || null}
                exact={route.exact || false}
            />
        )}
    </Switch>
);

export default Routes;