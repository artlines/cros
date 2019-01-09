import React from 'react';
import PropTypes from 'prop-types';
import {Link} from 'react-router-dom';
import Drawer from '@material-ui/core/Drawer';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import routes from '../../config/routes';
import map from 'lodash/map';
import filter from 'lodash/filter';

class Sidebar extends React.PureComponent {

    render() {
        const { open, onClose, roles } = this.props;

        return (
            <Drawer open={open} onClose={onClose}>
                <List>
                    {map(filter(routes, r => r.menuItem), (route, i) => {

                        if (route.role && !roles.includes(route.role)) {
                            return null;
                        }

                        return (
                            <Link
                                key={i}
                                to={route.path}
                                style={{ textDecoration: 'none', color: 'inherit' }}
                                onClick={onClose}
                            >
                                <ListItem button>
                                    <ListItemIcon><route.menuItem.Icon/></ListItemIcon>
                                    <ListItemText primary={route.menuItem.title} />
                                </ListItem>
                            </Link>
                        );
                    })}
                </List>
            </Drawer>
        );
    }
}

Sidebar.propTypes = {
    /**
     * Is sidebar open?
     */
    open: PropTypes.bool.isRequired,
    /**
     * Handler to close sidebar
     */
    onClose: PropTypes.func.isRequired,
    /**
     * Defined user roles to prevent render not accessed sidebar item
     */
    roles: PropTypes.array.isRequired,
};

Sidebar.defaultProps = {
    open:       false,
    onClose:    () => {},
};

export default Sidebar;