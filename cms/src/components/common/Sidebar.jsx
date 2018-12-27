import React from 'react';
import PropTypes from 'prop-types';
import Drawer from '@material-ui/core/Drawer';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import InboxIcon from '@material-ui/icons/Inbox';
import MailIcon from '@material-ui/icons/Mail';

class Sidebar extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <Drawer open={this.state.sidebarOpen} onClose={this.toggleSidebar}>
                <List>
                    {['All mail', 'Trash', 'Spam'].map((text, index) => (
                        <ListItem button key={text}>
                            <ListItemIcon>{index % 2 === 0 ? <InboxIcon /> : <MailIcon />}</ListItemIcon>
                            <ListItemText primary={text} />
                        </ListItem>
                    ))}
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
};

Sidebar.defaultProps = {
    open:       false,
    onClose:    () => {},
};

export default Sidebar;